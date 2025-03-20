<?php
/**
 * admin_notification_functions.php - Admin bildirim yönetimi için yardımcı fonksiyonlar
 */

/**
 * Yeni bir bildirim oluşturur.
 *
 * @param int $user_id Kullanıcı ID
 * @return int|false Bildirim ID veya hata durumunda false
 */
function createNotification($user_id) {
    $conn = $GLOBALS['db']->getConnection();
    
    $query = "INSERT INTO notifications (user_id) VALUES (?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    
    if ($stmt->execute()) {
        return $stmt->insert_id;
    }
    
    return false;
}

/**
 * Bildirime içerik ekler.
 *
 * @param int $notification_id Bildirim ID
 * @param string $language Dil kodu (tr, en, all, vb.)
 * @param string $notification_type Bildirim türü (general, vip, mining)
 * @param string $title Başlık
 * @param string $content İçerik
 * @return bool Başarılı mı
 */
function addNotificationContent($notification_id, $language, $notification_type, $title, $content) {
    $conn = $GLOBALS['db']->getConnection();
    
    $query = "INSERT INTO notification_contents (notification_id, language, notification_type, title, content) 
              VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    
    if ($stmt === false) {
        error_log("Prepare error: " . $conn->error);
        return false;
    }
    
    $stmt->bind_param("issss", $notification_id, $language, $notification_type, $title, $content);
    
    return $stmt->execute();
}

/**
 * Mining paketi olan kullanıcıları getirir.
 *
 * @return array Mining kullanıcılarının listesi
 */
function getMiningUsers() {
    $conn = $GLOBALS['db']->getConnection();
    
    $query = "SELECT DISTINCT u.id, u.username, u.email 
              FROM users u 
              JOIN user_mining_packages m ON u.id = m.user_id 
              WHERE m.status = 'active' 
              ORDER BY u.username";
    $result = $conn->query($query);
    
    $users = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }
    
    return $users;
}

/**
 * Admin için bildirimleri getirir.
 *
 * @param string $filter_type Bildirim türü filtresi
 * @param string $filter_date Tarih filtresi
 * @param string $filter_status Durum filtresi
 * @param int $limit Limit
 * @param int $offset Offset
 * @return array Bildirim listesi
 */
function getAdminNotifications($filter_type = '', $filter_date = '', $filter_status = '', $limit = 10, $offset = 0) {
    $conn = $GLOBALS['db']->getConnection();
    
    $query = "SELECT n.notification_id, n.user_id, n.created_at, 
                     nc.title, nc.content, nc.notification_type, nc.language,
                     u.username, u.email,
                     nrs.read_status, nrs.read_at
              FROM notifications n
              JOIN notification_contents nc ON n.notification_id = nc.notification_id
              JOIN users u ON n.user_id = u.id
              LEFT JOIN notification_read_status nrs ON n.notification_id = nrs.notification_id AND nrs.user_id = n.user_id
              WHERE 1=1";
    
    // Filtreler
    $params = [];
    $types = "";
    
    if ($filter_type) {
        $query .= " AND nc.notification_type = ?";
        $params[] = $filter_type;
        $types .= "s";
    }
    
    if ($filter_date) {
        switch ($filter_date) {
            case 'today':
                $query .= " AND DATE(n.created_at) = CURDATE()";
                break;
            case 'yesterday':
                $query .= " AND DATE(n.created_at) = CURDATE() - INTERVAL 1 DAY";
                break;
            case 'last_week':
                $query .= " AND n.created_at >= CURDATE() - INTERVAL 7 DAY";
                break;
            case 'last_month':
                $query .= " AND n.created_at >= CURDATE() - INTERVAL 30 DAY";
                break;
        }
    }
    
    if ($filter_status) {
        if ($filter_status === 'read') {
            $query .= " AND nrs.read_status = 1";
        } else if ($filter_status === 'unread') {
            $query .= " AND (nrs.read_status IS NULL OR nrs.read_status = 0)";
        }
    }
    
    // Sıralama ve limit
    $query .= " ORDER BY n.created_at DESC";
    $query .= " LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    $types .= "ii";
    
    $stmt = $conn->prepare($query);
    
    if ($stmt === false) {
        error_log("Prepare error: " . $conn->error);
        return [];
    }
    
    if ($types) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $notifications = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $notifications[] = $row;
        }
    }
    
    return $notifications;
}

/**
 * Toplam bildirim sayısını getirir.
 *
 * @param string $filter_type Bildirim türü filtresi
 * @param string $filter_date Tarih filtresi
 * @param string $filter_status Durum filtresi
 * @return int Bildirim sayısı
 */
function getTotalNotificationCount($filter_type = '', $filter_date = '', $filter_status = '') {
    $conn = $GLOBALS['db']->getConnection();
    
    $query = "SELECT COUNT(DISTINCT n.notification_id) as total
              FROM notifications n
              JOIN notification_contents nc ON n.notification_id = nc.notification_id
              JOIN users u ON n.user_id = u.id
              LEFT JOIN notification_read_status nrs ON n.notification_id = nrs.notification_id AND nrs.user_id = n.user_id
              WHERE 1=1";
    
    // Filtreler
    $params = [];
    $types = "";
    
    if ($filter_type) {
        $query .= " AND nc.notification_type = ?";
        $params[] = $filter_type;
        $types .= "s";
    }
    
    if ($filter_date) {
        switch ($filter_date) {
            case 'today':
                $query .= " AND DATE(n.created_at) = CURDATE()";
                break;
            case 'yesterday':
                $query .= " AND DATE(n.created_at) = CURDATE() - INTERVAL 1 DAY";
                break;
            case 'last_week':
                $query .= " AND n.created_at >= CURDATE() - INTERVAL 7 DAY";
                break;
            case 'last_month':
                $query .= " AND n.created_at >= CURDATE() - INTERVAL 30 DAY";
                break;
        }
    }
    
    if ($filter_status) {
        if ($filter_status === 'read') {
            $query .= " AND nrs.read_status = 1";
        } else if ($filter_status === 'unread') {
            $query .= " AND (nrs.read_status IS NULL OR nrs.read_status = 0)";
        }
    }
    
    $stmt = $conn->prepare($query);
    
    if ($stmt === false) {
        error_log("Prepare error: " . $conn->error);
        return 0;
    }
    
    if ($types) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return (int)$row['total'];
}

/**
 * Bildirimi siler.
 *
 * @param int $notification_id Bildirim ID
 * @return bool Başarılı mı
 */
function deleteNotification($notification_id) {
    $conn = $GLOBALS['db']->getConnection();
    
    // Önce bildirim içeriğini sil
    $query1 = "DELETE FROM notification_contents WHERE notification_id = ?";
    $stmt1 = $conn->prepare($query1);
    $stmt1->bind_param("i", $notification_id);
    $result1 = $stmt1->execute();
    
    // Sonra okuma durumunu sil
    $query2 = "DELETE FROM notification_read_status WHERE notification_id = ?";
    $stmt2 = $conn->prepare($query2);
    $stmt2->bind_param("i", $notification_id);
    $result2 = $stmt2->execute();
    
    // Son olarak bildirimi sil
    $query3 = "DELETE FROM notifications WHERE notification_id = ?";
    $stmt3 = $conn->prepare($query3);
    $stmt3->bind_param("i", $notification_id);
    $result3 = $stmt3->execute();
    
    return $result1 && $result2 && $result3;
}

/**
 * Toplu bildirim gönderir
 * 
 * @param array $user_ids Kullanıcı ID'leri
 * @param array $notification_data Bildirim verileri (title, content, type, language)
 * @return array Sonuç (success, count)
 */
function sendBulkNotifications($user_ids, $notification_data) {
    $conn = $GLOBALS['db']->getConnection();
    
    $success = true;
    $sent_count = 0;
    
    // İşlem başlatma
    $conn->begin_transaction();
    
    try {
        foreach ($user_ids as $user_id) {
            // Her kullanıcı için bildirim oluştur
            $notification_id = createNotification($user_id);
            
            if ($notification_id) {
                // Bildirim içeriklerini ekle
                $content_success = addNotificationContent(
                    $notification_id, 
                    $notification_data['language'], 
                    $notification_data['type'], 
                    $notification_data['title'], 
                    $notification_data['content']
                );
                
                if (!$content_success) {
                    $success = false;
                    break;
                }
                
                $sent_count++;
            } else {
                $success = false;
                break;
            }
        }
        
        if ($success) {
            $conn->commit();
        } else {
            $conn->rollback();
        }
    } catch (Exception $e) {
        $conn->rollback();
        $success = false;
    }
    
    return [
        'success' => $success,
        'count' => $sent_count
    ];
}