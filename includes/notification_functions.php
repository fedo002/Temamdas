<?php
/**
 * notification_functions.php
 * 
 * Bildirim işlemleri için güvenli fonksiyonlar
 */

/**
 * Kullanıcının okunmamış bildirimlerini güvenli şekilde getirir
 * 
 * @param int $user_id Kullanıcı ID'si
 * @return array Bildirim bilgileri ve okunmamış bildirim sayısı
 */
function getUserNotifications($user_id) {
    $conn = $GLOBALS['db']->getConnection();
    
    // Veritabanı bağlantısı yoksa veya geçersizse boş veri döndür
    if (!isset($conn) || !$conn) {
        return [
            'notifications' => [],
            'unread_count' => 0
        ];
    }
    
    // Tablo varlığını kontrol et - sorgu hatası almamak için try/catch içinde
    try {
        $tableCheck = $conn->query("SHOW TABLES LIKE 'notifications'");
        if (!$tableCheck || $tableCheck->num_rows == 0) {
            // Notification tablosu mevcut değil, boş veri döndür
            return [
                'notifications' => [],
                'unread_count' => 0
            ];
        }
        
        // Kullanıcı bilgilerini güvenli şekilde al
        $userData = ['user_lang' => 'tr', 'vip_level' => 0]; // Varsayılan değerler
        
        // SQL sorgusu başarısızsa hatayı yakalamak için try/catch kullan
        $userQuery = "SELECT user_lang, vip_level FROM users WHERE id = ?";
        $userStmt = $conn->prepare($userQuery);
        
        if ($userStmt) {
            $userStmt->bind_param("i", $user_id);
            $userStmt->execute();
            $userResult = $userStmt->get_result();
            
            if ($userResult && $userResult->num_rows > 0) {
                $userData = $userResult->fetch_assoc();
            }
        }
        
        $userLang = $userData['user_lang'] ?? 'tr';
        $vipLevel = $userData['vip_level'] ?? 0;
        
        // Mining paketi kontrolü - sorgu hatası almamak için kontrol et
        $hasMining = false;
        $checkMiningTable = $conn->query("SHOW TABLES LIKE 'user_mining_packages'");
        
        if ($checkMiningTable && $checkMiningTable->num_rows > 0) {
            $miningQuery = "SELECT COUNT(*) as has_mining FROM user_mining_packages WHERE user_id = ? AND status = 'active'";
            $miningStmt = $conn->prepare($miningQuery);
            
            if ($miningStmt) {
                $miningStmt->bind_param("i", $user_id);
                $miningStmt->execute();
                $miningResult = $miningStmt->get_result();
                
                if ($miningResult && $miningResult->num_rows > 0) {
                    $miningData = $miningResult->fetch_assoc();
                    $hasMining = $miningData['has_mining'] > 0;
                }
            }
        }
        
        // Okunmamış bildirimleri getir
        $notifications = [];
        $unread_count = 0;
        
        // Güncellenmiş SQL sorgusu
        $query = "SELECT n.notification_id, nc.title, nc.content, nc.notification_type, n.created_at
         FROM notifications n
         JOIN notification_contents nc ON n.notification_id = nc.notification_id
         LEFT JOIN notification_read_status nrs ON n.notification_id = nrs.notification_id AND nrs.user_id = ?
         WHERE n.user_id = ? AND (nrs.read_status IS NULL OR nrs.read_status = 0)
         AND (
             (nc.notification_type = 'general') OR
             (nc.notification_type = 'vip' AND ? > 0) OR
             (nc.notification_type = 'mining' AND ? = 1)
         )
         AND (nc.language = ? OR nc.language = 'all')
         ORDER BY n.created_at DESC
         LIMIT 10";
        
        $stmt = $conn->prepare($query);
        
        if ($stmt) {
            $hasMiningInt = $hasMining ? 1 : 0;
            $stmt->bind_param("iiiis", $user_id, $user_id, $vipLevel, $hasMiningInt, $userLang);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result) {
                while($row = $result->fetch_assoc()) {
                    $notifications[] = $row;
                }
            }
            
            // Okunmamış bildirim sayısını getir
            $countQuery = "SELECT COUNT(*) as count
                        FROM notifications n
                        JOIN notification_contents nc ON n.notification_id = nc.notification_id
                        LEFT JOIN notification_read_status nrs ON n.notification_id = nrs.notification_id AND nrs.user_id = ?
                        WHERE n.user_id = ? AND (nrs.read_status IS NULL OR nrs.read_status = 0)
                        AND (
                            (nc.notification_type = 'general') OR
                            (nc.notification_type = 'vip' AND ? > 0) OR
                            (nc.notification_type = 'mining' AND ? = 1)
                        )
                        AND (nc.language = ? OR nc.language = 'all')";
            
            $countStmt = $conn->prepare($countQuery);
            
            if ($countStmt) {
                $countStmt->bind_param("iiiis", $user_id, $user_id, $vipLevel, $hasMiningInt, $userLang);
                $countStmt->execute();
                $countResult = $countStmt->get_result();
                
                if ($countResult && $countResult->num_rows > 0) {
                    $countData = $countResult->fetch_assoc();
                    $unread_count = $countData['count'];
                }
            }
        }
        
        return [
            'notifications' => $notifications,
            'unread_count' => $unread_count
        ];
        
    } catch (Exception $e) {
        // Hata durumunda boş veri döndür ve hatayı logla
        error_log("Bildirim fonksiyonu hatası: " . $e->getMessage());
        return [
            'notifications' => [],
            'unread_count' => 0
        ];
    }
}

/**
 * Bildirimi okundu olarak işaretler
 * 
 * @param int $notification_id Bildirim ID'si
 * @param int $user_id Kullanıcı ID'si
 * @return bool İşlem başarılı mı
 */
function markNotificationAsRead($notification_id, $user_id) {
    $conn = $GLOBALS['db']->getConnection();
    
    // Veritabanı bağlantısı kontrolü
    if (!isset($conn) || !$conn) {
        return false;
    }
    
    try {
        // Tablo varlığını kontrol et
        $tableCheck = $conn->query("SHOW TABLES LIKE 'notification_read_status'");
        if (!$tableCheck || $tableCheck->num_rows == 0) {
            return false;
        }
        
        // Önce bu bildirim için kayıt var mı kontrol et
        $checkQuery = "SELECT * FROM notification_read_status WHERE notification_id = ? AND user_id = ?";
        $checkStmt = $conn->prepare($checkQuery);
        
        if (!$checkStmt) {
            return false;
        }
        
        $checkStmt->bind_param("ii", $notification_id, $user_id);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        
        if ($checkResult && $checkResult->num_rows > 0) {
            // Kayıt varsa güncelle
            $updateQuery = "UPDATE notification_read_status SET read_status = 1, read_at = NOW() 
                        WHERE notification_id = ? AND user_id = ?";
            $updateStmt = $conn->prepare($updateQuery);
            
            if (!$updateStmt) {
                return false;
            }
            
            $updateStmt->bind_param("ii", $notification_id, $user_id);
            return $updateStmt->execute();
        } else {
            // Kayıt yoksa yeni ekle
            $insertQuery = "INSERT INTO notification_read_status (notification_id, user_id, read_status, read_at) 
                        VALUES (?, ?, 1, NOW())";
            $insertStmt = $conn->prepare($insertQuery);
            
            if (!$insertStmt) {
                return false;
            }
            
            $insertStmt->bind_param("ii", $notification_id, $user_id);
            return $insertStmt->execute();
        }
    } catch (Exception $e) {
        error_log("Bildirim işaretleme hatası: " . $e->getMessage());
        return false;
    }
}

/**
 * Tüm bildirimleri okundu olarak işaretler
 * 
 * @param int $user_id Kullanıcı ID'si
 * @return bool İşlem başarılı mı
 */
function markAllNotificationsAsRead($user_id) {
    $conn = $GLOBALS['db']->getConnection();
    
    // Veritabanı bağlantısı kontrolü
    if (!isset($conn) || !$conn) {
        return false;
    }
    
    try {
        // Tablo varlığını kontrol et
        $tableCheck = $conn->query("SHOW TABLES LIKE 'notifications'");
        if (!$tableCheck || $tableCheck->num_rows == 0) {
            return false;
        }
        
        // Kullanıcının tüm bildirimlerini al
        $notificationsQuery = "SELECT n.notification_id 
                            FROM notifications n
                            WHERE n.user_id = ?";
        
        $notificationsStmt = $conn->prepare($notificationsQuery);
        
        if (!$notificationsStmt) {
            return false;
        }
        
        $notificationsStmt->bind_param("i", $user_id);
        $notificationsStmt->execute();
        $notificationsResult = $notificationsStmt->get_result();
        
        if (!$notificationsResult) {
            return false;
        }
        
        // Her bildirim için okundu olarak işaretle
        $success = true;
        while($row = $notificationsResult->fetch_assoc()) {
            $notification_id = $row['notification_id'];
            
            // Her bildirimi tek tek okundu olarak işaretle
            if (!markNotificationAsRead($notification_id, $user_id)) {
                $success = false;
            }
        }
        
        return $success;
    } catch (Exception $e) {
        error_log("Tüm bildirimleri işaretleme hatası: " . $e->getMessage());
        return false;
    }
}
/**
 * Kullanıcının tüm bildirimlerini (okunmuş ve okunmamış) getirir
 * 
 * @param int $user_id Kullanıcı ID'si
 * @return array Bildirim bilgileri
 */
function getAllUserNotifications($user_id) {
    global $conn;
    
    // Veritabanı bağlantısı kontrolü
    if (!isset($conn) || !$conn) {
        if (isset($GLOBALS['db'])) {
            $conn = $GLOBALS['db']->getConnection();
        } else {
            return [
                'notifications' => [],
                'unread_count' => 0
            ];
        }
    }
    
    try {
        // Tablo varlığını kontrol et
        $tableCheck = $conn->query("SHOW TABLES LIKE 'notifications'");
        if (!$tableCheck || $tableCheck->num_rows == 0) {
            return [
                'notifications' => [],
                'unread_count' => 0
            ];
        }
        
        // Kullanıcı bilgilerini güvenli şekilde al
        $userData = ['user_lang' => 'tr', 'vip_level' => 0]; // Varsayılan değerler
        
        // SQL sorgusu başarısızsa hatayı yakalamak için try/catch kullan
        $userQuery = "SELECT user_lang, vip_level FROM users WHERE id = ?";
        $userStmt = $conn->prepare($userQuery);
        
        if ($userStmt) {
            $userStmt->bind_param("i", $user_id);
            $userStmt->execute();
            $userResult = $userStmt->get_result();
            
            if ($userResult && $userResult->num_rows > 0) {
                $userData = $userResult->fetch_assoc();
            }
        }
        
        $userLang = $userData['user_lang'] ?? 'tr';
        $vipLevel = $userData['vip_level'] ?? 0;
        
        // Mining paketi kontrolü
        $hasMining = false;
        $checkMiningTable = $conn->query("SHOW TABLES LIKE 'user_mining_packages'");
        
        if ($checkMiningTable && $checkMiningTable->num_rows > 0) {
            $miningQuery = "SELECT COUNT(*) as has_mining FROM user_mining_packages WHERE user_id = ? AND status = 'active'";
            $miningStmt = $conn->prepare($miningQuery);
            
            if ($miningStmt) {
                $miningStmt->bind_param("i", $user_id);
                $miningStmt->execute();
                $miningResult = $miningStmt->get_result();
                
                if ($miningResult && $miningResult->num_rows > 0) {
                    $miningData = $miningResult->fetch_assoc();
                    $hasMining = $miningData['has_mining'] > 0;
                }
            }
        }
        
        // Tüm bildirimleri getir (okunmuş ve okunmamış)
        $notifications = [];
        
        // SQL sorgusu - artık read_status kontrolü yok
        $query = "SELECT n.notification_id, nc.title, nc.content, nc.notification_type, n.created_at,
                 CASE WHEN nrs.read_status IS NULL OR nrs.read_status = 0 THEN 0 ELSE 1 END as is_read
         FROM notifications n
         JOIN notification_contents nc ON n.notification_id = nc.notification_id
         LEFT JOIN notification_read_status nrs ON n.notification_id = nrs.notification_id AND nrs.user_id = ?
         WHERE n.user_id = ?
         AND (
             (nc.notification_type = 'general') OR
             (nc.notification_type = 'vip' AND ? > 0) OR
             (nc.notification_type = 'mining' AND ? = 1)
         )
         AND (nc.language = ? OR nc.language = 'all')
         ORDER BY n.created_at DESC";
        
        $stmt = $conn->prepare($query);
        
        if ($stmt) {
            $hasMiningInt = $hasMining ? 1 : 0;
            $stmt->bind_param("iiiis", $user_id, $user_id, $vipLevel, $hasMiningInt, $userLang);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result) {
                while($row = $result->fetch_assoc()) {
                    $notifications[] = $row;
                }
            }
            
            // Okunmamış bildirim sayısını getir (bu mevcut implementasyonu koruyoruz)
            $countQuery = "SELECT COUNT(*) as count
                        FROM notifications n
                        JOIN notification_contents nc ON n.notification_id = nc.notification_id
                        LEFT JOIN notification_read_status nrs ON n.notification_id = nrs.notification_id AND nrs.user_id = ?
                        WHERE n.user_id = ? AND (nrs.read_status IS NULL OR nrs.read_status = 0)
                        AND (
                            (nc.notification_type = 'general') OR
                            (nc.notification_type = 'vip' AND ? > 0) OR
                            (nc.notification_type = 'mining' AND ? = 1)
                        )
                        AND (nc.language = ? OR nc.language = 'all')";
            
            $countStmt = $conn->prepare($countQuery);
            
            if ($countStmt) {
                $countStmt->bind_param("iiiis", $user_id, $user_id, $vipLevel, $hasMiningInt, $userLang);
                $countStmt->execute();
                $countResult = $countStmt->get_result();
                
                if ($countResult && $countResult->num_rows > 0) {
                    $countData = $countResult->fetch_assoc();
                    $unread_count = $countData['count'];
                }
            } else {
                $unread_count = 0;
            }
        }
        
        return [
            'notifications' => $notifications,
            'unread_count' => $unread_count
        ];
        
    } catch (Exception $e) {
        // Hata durumunda boş veri döndür ve hatayı logla
        error_log("Bildirim fonksiyonu hatası: " . $e->getMessage());
        return [
            'notifications' => [],
            'unread_count' => 0
        ];
    }
}