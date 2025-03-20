<?php
/**
 * Admin paneli için ödeme yönetimi fonksiyonları
 */

// Tüm depozitleri getir
function getAllDeposits($status = null, $limit = null, $offset = 0) {
    global $db;
    
    $sql = "SELECT d.*, u.username FROM deposits d 
            INNER JOIN users u ON d.user_id = u.id";
    
    if ($status) {
        $sql .= " WHERE d.status = ?";
    }
    
    $sql .= " ORDER BY d.created_at DESC";
    
    if ($limit) {
        $sql .= " LIMIT ?, ?";
    }
    
    $stmt = $db->prepare($sql);
    
    if ($status && $limit) {
        $stmt->bind_param("sii", $status, $offset, $limit);
    } elseif ($status) {
        $stmt->bind_param("s", $status);
    } elseif ($limit) {
        $stmt->bind_param("ii", $offset, $limit);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Tüm çekimleri getir
function getAllWithdrawals($status = null, $limit = null, $offset = 0) {
    global $db;
    
    $sql = "SELECT w.*, u.username FROM withdrawals w 
            INNER JOIN users u ON w.user_id = u.id";
    
    if ($status) {
        $sql .= " WHERE w.status = ?";
    }
    
    $sql .= " ORDER BY w.created_at DESC";
    
    if ($limit) {
        $sql .= " LIMIT ?, ?";
    }
    
    $stmt = $db->prepare($sql);
    
    if ($status && $limit) {
        $stmt->bind_param("sii", $status, $offset, $limit);
    } elseif ($status) {
        $stmt->bind_param("s", $status);
    } elseif ($limit) {
        $stmt->bind_param("ii", $offset, $limit);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Para çekme talebini onayla
function approveWithdrawal($withdrawal_id, $admin_id, $tx_hash = null, $note = null) {
    global $db;
    
    // İşlemi bul
    $stmt = $db->prepare("SELECT * FROM withdrawals WHERE id = ? AND status = 'pending'");
    $stmt->bind_param("i", $withdrawal_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $withdrawal = $result->fetch_assoc();
    
    if (!$withdrawal) {
        return [
            'success' => false,
            'message' => 'Çekim talebi bulunamadı veya zaten işlendi.'
        ];
    }
    
    // Transaction başlat
    $db->begin_transaction();
    
    try {
        // Çekim durumunu güncelle
        $status = 'completed';
        $processed_at = date('Y-m-d H:i:s');
        
        $stmt = $db->prepare("UPDATE withdrawals SET status = ?, admin_id = ?, transaction_hash = ?, admin_note = ?, processed_at = ? WHERE id = ?");
        $stmt->bind_param("sisssi", $status, $admin_id, $tx_hash, $note, $processed_at, $withdrawal_id);
        $stmt->execute();
        
        // İşlem kaydını güncelle
        $stmt = $db->prepare("UPDATE transactions SET status = 'completed' WHERE related_id = ? AND type = 'withdraw'");
        $stmt->bind_param("i", $withdrawal_id);
        $stmt->execute();
        
        $db->commit();
        
        return [
            'success' => true,
            'message' => 'Çekim talebi onaylandı.'
        ];
    } catch (Exception $e) {
        $db->rollback();
        
        return [
            'success' => false,
            'message' => 'İşlem sırasında bir hata oluştu: ' . $e->getMessage()
        ];
    }
}

// Para çekme talebini reddet
function rejectWithdrawal($withdrawal_id, $admin_id, $note = null) {
    global $db;
    
    // İşlemi bul
    $stmt = $db->prepare("SELECT w.*, u.balance FROM withdrawals w 
                         INNER JOIN users u ON w.user_id = u.id 
                         WHERE w.id = ? AND w.status = 'pending'");
    $stmt->bind_param("i", $withdrawal_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $withdrawal = $result->fetch_assoc();
    
    if (!$withdrawal) {
        return [
            'success' => false,
            'message' => 'Çekim talebi bulunamadı veya zaten işlendi.'
        ];
    }
    
    // Transaction başlat
    $db->begin_transaction();
    
    try {
        // Çekim durumunu güncelle
        $status = 'cancelled';
        $processed_at = date('Y-m-d H:i:s');
        
        $stmt = $db->prepare("UPDATE withdrawals SET status = ?, admin_id = ?, admin_note = ?, processed_at = ? WHERE id = ?");
        $stmt->bind_param("sissi", $status, $admin_id, $note, $processed_at, $withdrawal_id);
        $stmt->execute();
        
        // Kullanıcı bakiyesini iade et
        $totalAmount = $withdrawal['amount'] + $withdrawal['fee'];
        $stmt = $db->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
        $stmt->bind_param("di", $totalAmount, $withdrawal['user_id']);
        $stmt->execute();
        
        // İşlem kaydını güncelle
        $stmt = $db->prepare("UPDATE transactions SET status = 'cancelled' WHERE related_id = ? AND type = 'withdraw'");
        $stmt->bind_param("i", $withdrawal_id);
        $stmt->execute();
        
        // İade işlemi ekle
        $current_balance = $withdrawal['balance'] + $totalAmount;
        $stmt = $db->prepare("INSERT INTO transactions (user_id, related_id, type, amount, before_balance, after_balance, status, description) 
                            VALUES (?, ?, 'other', ?, ?, ?, 'completed', ?)");
        $description = "Refund for rejected withdrawal #$withdrawal_id";
        $stmt->bind_param("iiddds", $withdrawal['user_id'], $withdrawal_id, $totalAmount, $withdrawal['balance'], $current_balance, $description);
        $stmt->execute();
        
        $db->commit();
        
        return [
            'success' => true,
            'message' => 'Çekim talebi reddedildi ve bakiye iade edildi.'
        ];
    } catch (Exception $e) {
        $db->rollback();
        
        return [
            'success' => false,
            'message' => 'İşlem sırasında bir hata oluştu: ' . $e->getMessage()
        ];
    }
}

// Ödeme istatistiklerini getir
function getPaymentStats() {
    global $db;
    
    $stats = [
        'total_deposits' => 0,
        'total_withdrawals' => 0,
        'monthly_deposits' => 0,
        'monthly_withdrawals' => 0,
        'pending_withdrawals' => 0,
        'deposit_count' => 0,
        'withdraw_count' => 0
    ];
    
    // Toplam yatırım
    $stmt = $db->prepare("SELECT SUM(amount) as total FROM deposits WHERE status = 'confirmed'");
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stats['total_deposits'] = $row['total'] ?: 0;
    
    // Toplam çekim
    $stmt = $db->prepare("SELECT SUM(amount) as total FROM withdrawals WHERE status = 'completed'");
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stats['total_withdrawals'] = $row['total'] ?: 0;
    
    // Bu ayki yatırımlar
    $start_date = date('Y-m-01 00:00:00');
    $end_date = date('Y-m-t 23:59:59');
    
    $stmt = $db->prepare("SELECT SUM(amount) as total FROM deposits WHERE status = 'confirmed' AND created_at BETWEEN ? AND ?");
    $stmt->bind_param("ss", $start_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stats['monthly_deposits'] = $row['total'] ?: 0;
    
    // Bu ayki çekimler
    $stmt = $db->prepare("SELECT SUM(amount) as total FROM withdrawals WHERE status = 'completed' AND processed_at BETWEEN ? AND ?");
    $stmt->bind_param("ss", $start_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stats['monthly_withdrawals'] = $row['total'] ?: 0;
    
    // Bekleyen çekimler
    $stmt = $db->prepare("SELECT SUM(amount) as total, COUNT(*) as count FROM withdrawals WHERE status = 'pending'");
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stats['pending_withdrawals'] = $row['total'] ?: 0;
    $stats['pending_withdraw_count'] = $row['count'] ?: 0;
    
    // İşlem sayıları
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM deposits WHERE status = 'confirmed'");
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stats['deposit_count'] = $row['count'] ?: 0;
    
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM withdrawals WHERE status = 'completed'");
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stats['withdraw_count'] = $row['count'] ?: 0;
    
    return $stats;
}
/**
 * Manuel para yatırma işlemi ekle
 * 
 * @param int $user_id Kullanıcı ID
 * @param float $amount Tutar
 * @param string $status Durum (confirmed, pending, cancelled)
 * @param string $payment_id Ödeme ID (opsiyonel)
 * @param string $notes Notlar (opsiyonel)
 * @return array ['success' => bool, 'deposit_id' => int|null, 'message' => string]
 */
function addManualDeposit($user_id, $amount, $status = 'confirmed', $payment_id = '', $notes = '') {
    global $conn;
    
    // Sonuç dizisi oluştur
    $result = [
        'success' => false,
        'deposit_id' => null,
        'message' => ''
    ];
    
    try {
        // Önce deposits tablo yapısını kontrol et
        $tableInfo = $conn->query("DESCRIBE deposits");
        if (!$tableInfo) {
            throw new Exception("Deposits tablo yapısı alınamadı: " . $conn->error);
        }
        
        $columns = [];
        while ($row = $tableInfo->fetch_assoc()) {
            $columns[] = $row['Field'];
        }
        
        // Temel sütunları içeren SQL oluştur
        $sql = "INSERT INTO deposits (user_id, amount, status";
        $values = "VALUES ($user_id, $amount, '$status'";
        
        // İsteğe bağlı sütunları ekle (eğer varsa)
        if (in_array('payment_id', $columns) && !empty($payment_id)) {
            $sql .= ", payment_id";
            $values .= ", '$payment_id'";
        }
        
        if (in_array('notes', $columns) && !empty($notes)) {
            $sql .= ", notes";
            $values .= ", '$notes'";
        }
        
        if (in_array('payment_method', $columns)) {
            $sql .= ", payment_method";
            $values .= ", 'manual'";
        }
        
        if (in_array('created_at', $columns)) {
            $sql .= ", created_at";
            $values .= ", NOW()";
        }
        
        if (in_array('updated_at', $columns)) {
            $sql .= ", updated_at";
            $values .= ", NOW()";
        }
        
        $sql .= ") " . $values . ")";
        
        // Yatırım kaydı ekle
        if (!$conn->query($sql)) {
            throw new Exception("Yatırım kaydı eklenirken hata: " . $conn->error);
        }
        
        $deposit_id = $conn->insert_id;
        
        // Sonuç dizisine deposit_id ekle
        $result['deposit_id'] = $deposit_id;
        
        // Eğer onaylandıysa bakiyeyi güncelle
        if ($status === 'confirmed') {
            // Önce users tablosunun yapısını kontrol edelim
            $tableInfo = $conn->query("DESCRIBE users");
            if (!$tableInfo) {
                throw new Exception("Users tablo yapısı alınamadı: " . $conn->error);
            }
            
            $userColumns = [];
            while ($row = $tableInfo->fetch_assoc()) {
                $userColumns[] = $row['Field'];
            }
            
            // Kullanıcı bilgilerini al
            $result_user = $conn->query("SELECT * FROM users WHERE id = $user_id");
            
            if (!$result_user) {
                throw new Exception("Kullanıcı bilgileri alınamadı: " . $conn->error);
            }
            
            $user = $result_user->fetch_assoc();
            if (!$user) {
                throw new Exception("Kullanıcı bulunamadı (ID: $user_id)");
            }
            
            // Bakiyeyi güncelle
            $current_balance = $user['balance'];
            $new_balance = $current_balance + $amount;
            
            // Bakiye güncelleme SQL'i oluştur
            $updateSql = "UPDATE users SET balance = $new_balance";
            
            // Updated_at varsa ekle
            if (in_array('updated_at', $userColumns)) {
                $updateSql .= ", updated_at = NOW()";
            }
            
            $updateSql .= " WHERE id = $user_id";
            
            if (!$conn->query($updateSql)) {
                throw new Exception("Kullanıcı bakiyesi güncellenemedi: " . $conn->error);
            }
            
            // İşlem kaydı ekle
            $tableInfo = $conn->query("DESCRIBE transactions");
            if (!$tableInfo) {
                throw new Exception("Transactions tablo yapısı alınamadı: " . $conn->error);
            }
            
            $transColumns = [];
            while ($row = $tableInfo->fetch_assoc()) {
                $transColumns[] = $row['Field'];
            }
            
            // Transaction SQL'i oluştur
            $transSql = "INSERT INTO transactions (";
            $transValues = "VALUES (";
            $first = true;
            
            // Gerekli ve mevcut sütunları ekle
            if (in_array('user_id', $transColumns)) {
                $transSql .= "user_id";
                $transValues .= "$user_id";
                $first = false;
            }
            
            if (in_array('type', $transColumns)) {
                if (!$first) { $transSql .= ", "; $transValues .= ", "; }
                $transSql .= "type";
                $transValues .= "'deposit'";
                $first = false;
            }
            
            if (in_array('amount', $transColumns)) {
                if (!$first) { $transSql .= ", "; $transValues .= ", "; }
                $transSql .= "amount";
                $transValues .= "$amount";
                $first = false;
            }
            
            // Tablo yapısına göre dinamik olarak sütunları ekle
            if (in_array('related_id', $transColumns)) {
                if (!$first) { $transSql .= ", "; $transValues .= ", "; }
                $transSql .= "related_id";
                $transValues .= "$deposit_id";
                $first = false;
            } else if (in_array('reference_id', $transColumns)) {
                if (!$first) { $transSql .= ", "; $transValues .= ", "; }
                $transSql .= "reference_id";
                $transValues .= "$deposit_id";
                $first = false;
            }
            
            if (in_array('before_balance', $transColumns)) {
                if (!$first) { $transSql .= ", "; $transValues .= ", "; }
                $transSql .= "before_balance";
                $transValues .= "$current_balance";
                $first = false;
            } else if (in_array('balance_before', $transColumns)) {
                if (!$first) { $transSql .= ", "; $transValues .= ", "; }
                $transSql .= "balance_before";
                $transValues .= "$current_balance";
                $first = false;
            }
            
            if (in_array('after_balance', $transColumns)) {
                if (!$first) { $transSql .= ", "; $transValues .= ", "; }
                $transSql .= "after_balance";
                $transValues .= "$new_balance";
                $first = false;
            } else if (in_array('balance_after', $transColumns)) {
                if (!$first) { $transSql .= ", "; $transValues .= ", "; }
                $transSql .= "balance_after";
                $transValues .= "$new_balance";
                $first = false;
            }
            
            if (in_array('status', $transColumns)) {
                if (!$first) { $transSql .= ", "; $transValues .= ", "; }
                $transSql .= "status";
                $transValues .= "'completed'";
                $first = false;
            }
            
            if (in_array('description', $transColumns)) {
                if (!$first) { $transSql .= ", "; $transValues .= ", "; }
                $transSql .= "description";
                $transValues .= "'Manuel para yatırma işlemi'";
                $first = false;
            }
            
            if (in_array('created_at', $transColumns)) {
                if (!$first) { $transSql .= ", "; $transValues .= ", "; }
                $transSql .= "created_at";
                $transValues .= "NOW()";
                $first = false;
            }
            
            if (in_array('updated_at', $transColumns)) {
                if (!$first) { $transSql .= ", "; $transValues .= ", "; }
                $transSql .= "updated_at";
                $transValues .= "NOW()";
            }
            
            $transSql .= ") " . $transValues . ")";
            
            if (!$conn->query($transSql)) {
                throw new Exception("İşlem kaydı eklenemedi: " . $conn->error);
            }
        }
        
        // Admin log kaydı ekle
        if (isset($_SESSION['admin_id'])) {
            $admin_id = $_SESSION['admin_id'];
            
            $tableInfo = $conn->query("DESCRIBE admin_logs");
            if ($tableInfo) {
                $logColumns = [];
                while ($row = $tableInfo->fetch_assoc()) {
                    $logColumns[] = $row['Field'];
                }
                
                $logSql = "INSERT INTO admin_logs (";
                $logValues = "VALUES (";
                $first = true;
                
                if (in_array('admin_id', $logColumns)) {
                    $logSql .= "admin_id";
                    $logValues .= "$admin_id";
                    $first = false;
                }
                
                if (in_array('action', $logColumns)) {
                    if (!$first) { $logSql .= ", "; $logValues .= ", "; }
                    $logSql .= "action";
                    $logValues .= "'add_deposit'";
                    $first = false;
                }
                
                if (in_array('description', $logColumns)) {
                    if (!$first) { $logSql .= ", "; $logValues .= ", "; }
                    $logSql .= "description";
                    $logValues .= "'Manuel para yatırma eklendi: Kullanıcı ID: $user_id, Tutar: $amount USDT'";
                    $first = false;
                }
                
                if (in_array('related_id', $logColumns)) {
                    if (!$first) { $logSql .= ", "; $logValues .= ", "; }
                    $logSql .= "related_id";
                    $logValues .= "$deposit_id";
                    $first = false;
                }
                
                if (in_array('related_type', $logColumns)) {
                    if (!$first) { $logSql .= ", "; $logValues .= ", "; }
                    $logSql .= "related_type";
                    $logValues .= "'deposit'";
                    $first = false;
                }
                
                if (in_array('created_at', $logColumns)) {
                    if (!$first) { $logSql .= ", "; $logValues .= ", "; }
                    $logSql .= "created_at";
                    $logValues .= "NOW()";
                }
                
                $logSql .= ") " . $logValues . ")";
                
                $conn->query($logSql); // Log hatası kritik değil, işlem devam edebilir
            }
        }
        
        // Başarılı sonuç
        $result['success'] = true;
        $result['message'] = "İşlem başarıyla tamamlandı!";
        
        return $result;
        
    } catch (Exception $e) {
        // Hata durumunda
        $result['message'] = $e->getMessage();
        return $result;
    }
}
/**
 * Admin profil işlemleri için fonksiyonlar
 */

// Admin detaylarını getir
function getAdminDetailsi($admin_id) {
    global $db;
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("SELECT * FROM admins WHERE id = ?");
    $stmt->bind_param('i', $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return null;
}

// Admin e-posta kontrolü
function isAdminEmailAvailable($email) {
    global $db;
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM admins WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return $row['count'] == 0;
}

// Admin profilini güncelle
function updateAdminProfile($admin_id, $full_name, $email) {
    global $db;
    $conn = $db->getConnection();
    
    // E-posta değişimi varsa kontrol et
    $current_admin = getAdminDetailsi($admin_id);
    
    if ($email !== $current_admin['email']) {
        // E-posta mevcutsa hata döndür
        if (!isAdminEmailAvailable($email)) {
            return [
                'success' => false,
                'message' => 'Bu e-posta adresi zaten kullanılmaktadır.'
            ];
        }
    }
    
    // Güncelleme sorgusu
    $stmt = $conn->prepare("UPDATE admins SET full_name = ?, email = ? WHERE id = ?");
    
    if ($stmt === false) {
        return [
            'success' => false,
            'message' => 'Veritabanı sorgusu hazırlanamadı: ' . $conn->error
        ];
    }
    
    $stmt->bind_param('ssi', $full_name, $email, $admin_id);
    
    if ($stmt->execute()) {
        return [
            'success' => true,
            'message' => 'Profil başarıyla güncellendi.'
        ];
    } else {
        return [
            'success' => false,
            'message' => 'Profil güncellenirken bir hata oluştu: ' . $stmt->error
        ];
    }
}

// Admin şifresini değiştir
function changeAdminPassword($admin_id, $new_password) {
    global $db;
    $conn = $db->getConnection();
    
    // Şifreyi hashle
    $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("UPDATE admins SET password = ? WHERE id = ?");
    
    if ($stmt === false) {
        return [
            'success' => false,
            'message' => 'Veritabanı sorgusu hazırlanamadı: ' . $conn->error
        ];
    }
    
    $stmt->bind_param('si', $password_hash, $admin_id);
    
    if ($stmt->execute()) {
        return [
            'success' => true,
            'message' => 'Şifre başarıyla değiştirildi.'
        ];
    } else {
        return [
            'success' => false,
            'message' => 'Şifre değiştirilirken bir hata oluştu: ' . $stmt->error
        ];
    }
}

// Admin giriş kayıtlarını getir
function getAdminLoginLogs($admin_id, $limit = null) {
    global $db;
    $conn = $db->getConnection();
    $logs = [];
    
    $query = "SELECT * FROM admin_login_logs WHERE admin_id = ? ORDER BY created_at DESC";
    
    if ($limit) {
        $query .= " LIMIT ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ii', $admin_id, $limit);
    } else {
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $admin_id);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $logs[] = $row;
        }
    }
    
    return $logs;
}

// Admin rolü adını getir
function getAdminRoleName($role) {
    $roles = [
        'admin' => 'Yönetici',
        'moderator' => 'Moderatör',
        'support' => 'Destek Ekibi'
    ];
    
    return $roles[$role] ?? 'Bilinmeyen';
}

// Admin log kaydı oluştur (bu fonksiyon başka yerde tanımlanmış olabilir)
if (!function_exists('addAdminLog')) {
    function addAdminLog($admin_id, $action, $description = null, $related_id = null, $related_type = null) {
        global $db;
        $conn = $db->getConnection();
        
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
        
        $stmt = $conn->prepare("INSERT INTO admin_logs (admin_id, action, description, related_id, related_type, ip_address, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param('ississ', $admin_id, $action, $description, $related_id, $related_type, $ip_address);
        return $stmt->execute();
    }
}

/**
 * Mining paketini getir
 * 
 * @param int $package_id Paket ID
 * @return array|false Paket bilgileri veya false
 */
function getMiningPackage($package_id) {
    global $db;
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("SELECT * FROM mining_packages WHERE id = ?");
    
    if (!$stmt) {
        return false;
    }
    
    $stmt->bind_param("i", $package_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return false;
}

/**
 * Mining paketini güncelle
 * 
 * @param int $package_id Paket ID
 * @param string $name Paket adı
 * @param float $hash_rate Hash rate
 * @param float $electricity_cost Elektrik maliyeti
 * @param float $daily_revenue_rate Günlük kazanç oranı
 * @param float $package_price Paket fiyatı
 * @param string $description Açıklama
 * @param int $is_active Aktif mi?
 * @return bool Güncelleme başarılı mı?
 */
function updateMiningPackage($package_id, $name, $hash_rate, $electricity_cost, $daily_revenue_rate, $package_price, $description, $is_active) {
    global $db;
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("UPDATE mining_packages 
                             SET name = ?, 
                                 hash_rate = ?, 
                                 electricity_cost = ?, 
                                 daily_revenue_rate = ?, 
                                 package_price = ?, 
                                 description = ?, 
                                 is_active = ? 
                             WHERE id = ?");
    
    if (!$stmt) {
        return false;
    }
    
    $stmt->bind_param("sddddsis", $name, $hash_rate, $electricity_cost, $daily_revenue_rate, $package_price, $description, $is_active, $package_id);
    $result = $stmt->execute();
    
    if ($result) {
        // Admin log kaydı oluştur
        addAdminLog($_SESSION['admin_id'], 'update_mining_package', "Mining paketi güncellendi: $name", $package_id, 'mining_package');
    }
    
    return $result;
}

/**
 * Paketin kullanıcı sayısını getir
 * 
 * @param int $package_id Paket ID
 * @return int Kullanıcı sayısı
 */
function getPackageUserCount($package_id) {
    global $db;
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("SELECT COUNT(DISTINCT user_id) as count FROM user_mining_packages WHERE package_id = ? AND status = 'active'");
    
    if (!$stmt) {
        return 0;
    }
    
    $stmt->bind_param("i", $package_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['count'];
    }
    
    return 0;
}

/**
 * Paketin toplam satış sayısını getir
 * 
 * @param int $package_id Paket ID
 * @return int Satış sayısı
 */
function getPackageTotalSales($package_id) {
    global $db;
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM user_mining_packages WHERE package_id = ?");
    
    if (!$stmt) {
        return 0;
    }
    
    $stmt->bind_param("i", $package_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['count'];
    }
    
    return 0;
}

/**
 * Tüm mining paketlerini getir
 * 
 * @param bool $active_only Sadece aktif paketler mi getirilsin?
 * @return array Paketlerin listesi
 */
function getAllMiningPackages($active_only = false) {
    global $db;
    $conn = $db->getConnection();
    
    $query = "SELECT * FROM mining_packages";
    
    if ($active_only) {
        $query .= " WHERE is_active = 1";
    }
    
    $query .= " ORDER BY package_price ASC";
    
    $result = $conn->query($query);
    $packages = [];
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $packages[] = $row;
        }
    }
    
    return $packages;
}

/**
 * Mining paketi oluştur
 * 
 * @param string $name Paket adı
 * @param float $hash_rate Hash rate
 * @param float $electricity_cost Elektrik maliyeti
 * @param float $daily_revenue_rate Günlük kazanç oranı
 * @param float $package_price Paket fiyatı
 * @param string $description Açıklama
 * @param int $is_active Aktif mi?
 * @return int|false Oluşturulan paketin ID'si veya false
 */
function createMiningPackage($name, $hash_rate, $electricity_cost, $daily_revenue_rate, $package_price, $description, $is_active) {
    global $db;
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("INSERT INTO mining_packages 
                             (name, hash_rate, electricity_cost, daily_revenue_rate, package_price, description, is_active, created_at) 
                             VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    
    if (!$stmt) {
        return false;
    }
    
    $stmt->bind_param("sddddsi", $name, $hash_rate, $electricity_cost, $daily_revenue_rate, $package_price, $description, $is_active);
    $result = $stmt->execute();
    
    if ($result) {
        $package_id = $conn->insert_id;
        // Admin log kaydı oluştur
        addAdminLog($_SESSION['admin_id'], 'create_mining_package', "Mining paketi oluşturuldu: $name", $package_id, 'mining_package');
        return $package_id;
    }
    
    return false;
}

/**
 * Mining paketini sil
 * 
 * @param int $package_id Paket ID
 * @return bool Silme başarılı mı?
 */
function deleteMiningPackage($package_id) {
    global $db;
    $conn = $db->getConnection();
    
    // Önce paketin kullanımda olup olmadığını kontrol et
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM user_mining_packages WHERE package_id = ?");
    
    if (!$stmt) {
        return false;
    }
    
    $stmt->bind_param("i", $package_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($row['count'] > 0) {
            // Paket kullanımda, silinemiyor
            return false;
        }
    }
    
    // Paket adını log için al
    $package_name = '';
    $stmt = $conn->prepare("SELECT name FROM mining_packages WHERE id = ?");
    
    if ($stmt) {
        $stmt->bind_param("i", $package_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $package_name = $row['name'];
        }
    }
    
    // Paketi sil
    $stmt = $conn->prepare("DELETE FROM mining_packages WHERE id = ?");
    
    if (!$stmt) {
        return false;
    }
    
    $stmt->bind_param("i", $package_id);
    $result = $stmt->execute();
    
    if ($result) {
        // Admin log kaydı oluştur
        addAdminLog($_SESSION['admin_id'], 'delete_mining_package', "Mining paketi silindi: $package_name", $package_id, 'mining_package');
        return true;
    }
    
    return false;
}

/**
 * Toplam mining kullanıcı sayısını getir
 * 
 * @return int Kullanıcı sayısı
 */
function getTotalMiningUsers() {
    global $conn;
    
    $result = $conn->query("SELECT COUNT(DISTINCT user_id) as count FROM user_mining_packages WHERE status = 'active'");
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['count'];
    }
    
    return 0;
}

/**
 * Toplam aktif paket sayısını getir
 * 
 * @return int Paket sayısı
 */
function getTotalActivePackages() {
    global $conn;
    
    $result = $conn->query("SELECT COUNT(*) as count FROM user_mining_packages WHERE status = 'active'");
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['count'];
    }
    
    return 0;
}

/**
 * Bugünkü toplam mining kazancını getir
 * 
 * @return float Kazanç miktarı
 */
function getTodayMiningEarnings() {
    global $conn;
    
    $today = date('Y-m-d');
    
    $result = $conn->query("SELECT SUM(net_revenue) as total FROM mining_earnings WHERE date = '$today'");
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['total'] ?: 0;
    }
    
    return 0;
}


/**
 * Site ayarlarını getirir
 * 
 * @return array Site ayarları
 */
function getSiteSettingsi() {
    global $db;
    $conn = $db->getConnection();
    $settings = [];
    
    $result = $conn->query("SELECT * FROM site_settings");
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    }
    
    return $settings;
}

/**
 * Site ayarlarını günceller
 * 
 * @param array $updates Güncellenecek ayarlar (key => value formatında)
 * @return bool Güncelleme başarılı mı?
 */
function updateSiteSettings($updates) {
    global $db;
    $conn = $db->getConnection();
    
    try {
        // İşlem başlat
        $conn->begin_transaction();
        
        foreach ($updates as $key => $value) {
            // Önce ayarın varlığını kontrol et
            $check = $conn->prepare("SELECT COUNT(*) as count FROM site_settings WHERE setting_key = ?");
            $check->bind_param('s', $key);
            $check->execute();
            $result = $check->get_result();
            $row = $result->fetch_assoc();
            
            if ($row['count'] > 0) {
                // Ayar varsa güncelle
                $stmt = $conn->prepare("UPDATE site_settings SET setting_value = ?, updated_at = NOW() WHERE setting_key = ?");
                $stmt->bind_param('ss', $value, $key);
            } else {
                // Ayar yoksa ekle
                $stmt = $conn->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?)");
                $stmt->bind_param('ss', $key, $value);
            }
            
            if (!$stmt->execute()) {
                throw new Exception("Ayar güncellenirken hata: " . $conn->error);
            }
        }
        
        // İşlemi tamamla
        $conn->commit();
        
        // Admin log kaydı oluştur
        addAdminLog($_SESSION['admin_id'], 'update_site_settings', "Site ayarları güncellendi");
        
        return true;
        
    } catch (Exception $e) {
        // Hata durumunda işlemi geri al
        $conn->rollback();
        
        // Hata logla
        error_log("Site ayarları güncellenirken hata: " . $e->getMessage());
        
        return false;
    }
}

/**
 * Ödeme ayarlarını getirir
 * 
 * @return array Ödeme ayarları
 */
function getPaymentSettings() {
    global $db;
    $conn = $db->getConnection();
    $settings = [];
    
    $result = $conn->query("SELECT * FROM payment_settings");
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    }
    
    return $settings;
}

/**
 * Ödeme ayarlarını günceller
 * 
 * @param array $updates Güncellenecek ayarlar (key => value formatında)
 * @return bool Güncelleme başarılı mı?
 */
function updatePaymentSettings($updates) {
    global $db;
    $conn = $db->getConnection();
    
    try {
        // İşlem başlat
        $conn->begin_transaction();
        
        foreach ($updates as $key => $value) {
            // Önce ayarın varlığını kontrol et
            $check = $conn->prepare("SELECT COUNT(*) as count FROM payment_settings WHERE setting_key = ?");
            $check->bind_param('s', $key);
            $check->execute();
            $result = $check->get_result();
            $row = $result->fetch_assoc();
            
            if ($row['count'] > 0) {
                // Ayar varsa güncelle
                $stmt = $conn->prepare("UPDATE payment_settings SET setting_value = ?, updated_at = NOW() WHERE setting_key = ?");
                $stmt->bind_param('ss', $value, $key);
            } else {
                // Ayar yoksa ekle
                $stmt = $conn->prepare("INSERT INTO payment_settings (setting_key, setting_value) VALUES (?, ?)");
                $stmt->bind_param('ss', $key, $value);
            }
            
            if (!$stmt->execute()) {
                throw new Exception("Ödeme ayarı güncellenirken hata: " . $conn->error);
            }
        }
        
        // İşlemi tamamla
        $conn->commit();
        
        // Admin log kaydı oluştur
        addAdminLog($_SESSION['admin_id'], 'update_payment_settings', "Ödeme ayarları güncellendi");
        
        return true;
        
    } catch (Exception $e) {
        // Hata durumunda işlemi geri al
        $conn->rollback();
        
        // Hata logla
        error_log("Ödeme ayarları güncellenirken hata: " . $e->getMessage());
        
        return false;
    }
}
/**
 * Mining istatistiklerini getir
 * 
 * @param string $start_date Başlangıç tarihi (YYYY-MM-DD)
 * @param string $end_date Bitiş tarihi (YYYY-MM-DD)
 * @param int $package_id Paket ID filtresi (0 = tüm paketler)
 * @param int $user_id Kullanıcı ID filtresi (0 = tüm kullanıcılar)
 * @return array İstatistikler
 */
function getMiningStats($start_date, $end_date, $package_id = 0, $user_id = 0) {
    global $db;
    $conn = $db->getConnection();
    
    try {
        // Tarih doğrulama
        if (!validateDate($start_date) || !validateDate($end_date)) {
            error_log("getMiningStats: Geçersiz tarih formatı - start_date: $start_date, end_date: $end_date");
            return [];
        }
        
        // SQL sorgusu hazırlama
        $sql = "
            SELECT 
                ms.date, 
                SUM(ms.hash_rate) as total_hash_rate,
                SUM(ms.revenue) as total_revenue,
                SUM(ms.electricity_cost) as total_electricity_cost,
                SUM(ms.net_revenue) as total_net_revenue,
                COUNT(DISTINCT ms.user_id) as user_count
            FROM mining_stats ms
            WHERE ms.date BETWEEN ? AND ?
        ";
        
        $params = [$start_date, $end_date];
        $types = "ss"; // s = string
        
        if ($package_id > 0) {
            $sql .= " AND ms.package_id = ?";
            $params[] = $package_id;
            $types .= "i"; // i = integer
        }
        
        if ($user_id > 0) {
            $sql .= " AND ms.user_id = ?";
            $params[] = $user_id;
            $types .= "i"; // i = integer
        }
        
        $sql .= " GROUP BY ms.date ORDER BY ms.date DESC";
        
        // SQL sorgusu hata ayıklama
        error_log("getMiningStats SQL: " . $sql);
        
        $stmt = $conn->prepare($sql);
        
        if ($stmt === false) {
            error_log("getMiningStats: SQL prepare hatası - " . $conn->error);
            return [];
        }
        
        // Parametreleri bağlama
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result === false) {
            error_log("getMiningStats: Execute hatası - " . $stmt->error);
            return [];
        }
        
        $stats = [];
        while ($row = $result->fetch_assoc()) {
            $stats[] = $row;
        }
        
        $stmt->close();
        return $stats;
        
    } catch (Exception $e) {
        error_log("getMiningStats hatası: " . $e->getMessage());
        return [];
    }
}

/**
 * Tarih formatı doğrulama (YYYY-MM-DD)
 * 
 * @param string $date Tarih
 * @return bool Geçerli ise true, değilse false
 */
function validateDate($date) {
    if (empty($date)) {
        return false;
    }
    
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

/**
 * Para yatırma işlemlerini getir
 * 
 * @param string $start_date Başlangıç tarihi (YYYY-MM-DD)
 * @param string $end_date Bitiş tarihi (YYYY-MM-DD)
 * @param string $status Durum filtresi (all, pending, confirmed, failed)
 * @param int $user_id Kullanıcı ID filtresi (0 = tüm kullanıcılar)
 * @return array Yatırım işlemleri listesi
 */
function getDepositsi($start_date, $end_date, $status = 'all', $user_id = 0) {
    global $db;
    $conn = $db->getConnection();
    
    $sql = "
        SELECT d.*, u.username
        FROM deposits d
        LEFT JOIN users u ON d.user_id = u.id
        WHERE 1=1
    ";
    
    $params = [];
    $types = "";
    
    // Tarih filtresi
    if (!empty($start_date)) {
        $sql .= " AND DATE(d.created_at) >= ?";
        $params[] = $start_date;
        $types .= "s";
    }
    
    if (!empty($end_date)) {
        $sql .= " AND DATE(d.created_at) <= ?";
        $params[] = $end_date;
        $types .= "s";
    }
    
    // Durum filtresi
    if (!empty($status) && $status !== 'all') {
        $sql .= " AND d.status = ?";
        $params[] = $status;
        $types .= "s";
    }
    
    // Kullanıcı filtresi
    if (!empty($user_id) && $user_id > 0) {
        $sql .= " AND d.user_id = ?";
        $params[] = $user_id;
        $types .= "i";
    }
    
    // Sıralama
    $sql .= " ORDER BY d.created_at DESC";
    
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        error_log("getDepositsi: SQL prepare hatası - " . $conn->error);
        return [];
    }
    
    // Parametreleri bağlama
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result === false) {
        error_log("getDepositsi: Execute hatası - " . $stmt->error);
        return [];
    }
    
    $deposits = [];
    while ($row = $result->fetch_assoc()) {
        $deposits[] = $row;
    }
    
    $stmt->close();
    return $deposits;
}

/**
 * İşlem kaydı ekle
 * 
 * @param int $user_id Kullanıcı ID
 * @param string $type İşlem tipi
 * @param float $amount Tutar
 * @param float $balance_before İşlem öncesi bakiye
 * @param float $balance_after İşlem sonrası bakiye
 * @param string $status Durum
 * @param int $reference_id Referans ID
 * @param string $description Açıklama
 * @return int|false Eklenen işlem ID veya false
 */
function addTransaction($user_id, $type, $amount, $balance_before, $balance_after, $status, $reference_id = null, $description = '') {
    global $conn;
    
    // Transactions tablosunu kontrol et
    $tableInfo = $conn->query("DESCRIBE transactions");
    if (!$tableInfo) {
        error_log("Transactions tablo yapısı alınamadı: " . $conn->error);
        return false;
    }
    
    $transColumns = [];
    while ($row = $tableInfo->fetch_assoc()) {
        $transColumns[] = $row['Field'];
    }
    
    // Transaction SQL'i oluştur
    $transSql = "INSERT INTO transactions (";
    $transValues = "VALUES (";
    $transParams = [];
    $transTypes = "";
    $first = true;
    
    // Gerekli ve mevcut sütunları ekle
    if (in_array('user_id', $transColumns)) {
        $transSql .= "user_id";
        $transValues .= "?";
        $transParams[] = $user_id;
        $transTypes .= "i";
        $first = false;
    }
    
    if (in_array('type', $transColumns)) {
        if (!$first) { $transSql .= ", "; $transValues .= ", "; }
        $transSql .= "type";
        $transValues .= "?";
        $transParams[] = $type;
        $transTypes .= "s";
        $first = false;
    }
    
    if (in_array('amount', $transColumns)) {
        if (!$first) { $transSql .= ", "; $transValues .= ", "; }
        $transSql .= "amount";
        $transValues .= "?";
        $transParams[] = $amount;
        $transTypes .= "d";
        $first = false;
    }
    
    if (in_array('related_id', $transColumns) && $reference_id !== null) {
        if (!$first) { $transSql .= ", "; $transValues .= ", "; }
        $transSql .= "related_id";
        $transValues .= "?";
        $transParams[] = $reference_id;
        $transTypes .= "i";
        $first = false;
    } else if (in_array('reference_id', $transColumns) && $reference_id !== null) {
        if (!$first) { $transSql .= ", "; $transValues .= ", "; }
        $transSql .= "reference_id";
        $transValues .= "?";
        $transParams[] = $reference_id;
        $transTypes .= "i";
        $first = false;
    }
    
    if (in_array('before_balance', $transColumns)) {
        if (!$first) { $transSql .= ", "; $transValues .= ", "; }
        $transSql .= "before_balance";
        $transValues .= "?";
        $transParams[] = $balance_before;
        $transTypes .= "d";
        $first = false;
    } else if (in_array('balance_before', $transColumns)) {
        if (!$first) { $transSql .= ", "; $transValues .= ", "; }
        $transSql .= "balance_before";
        $transValues .= "?";
        $transParams[] = $balance_before;
        $transTypes .= "d";
        $first = false;
    }
    
    if (in_array('after_balance', $transColumns)) {
        if (!$first) { $transSql .= ", "; $transValues .= ", "; }
        $transSql .= "after_balance";
        $transValues .= "?";
        $transParams[] = $balance_after;
        $transTypes .= "d";
        $first = false;
    } else if (in_array('balance_after', $transColumns)) {
        if (!$first) { $transSql .= ", "; $transValues .= ", "; }
        $transSql .= "balance_after";
        $transValues .= "?";
        $transParams[] = $balance_after;
        $transTypes .= "d";
        $first = false;
    }
    
    if (in_array('status', $transColumns)) {
        if (!$first) { $transSql .= ", "; $transValues .= ", "; }
        $transSql .= "status";
        $transValues .= "?";
        $transParams[] = $status;
        $transTypes .= "s";
        $first = false;
    }
    
    if (in_array('description', $transColumns) && !empty($description)) {
        if (!$first) { $transSql .= ", "; $transValues .= ", "; }
        $transSql .= "description";
        $transValues .= "?";
        $transParams[] = $description;
        $transTypes .= "s";
        $first = false;
    }
    
    if (in_array('created_at', $transColumns)) {
        if (!$first) { $transSql .= ", "; $transValues .= ", "; }
        $transSql .= "created_at";
        $transValues .= "NOW()";
        $first = false;
    }
    
    if (in_array('updated_at', $transColumns)) {
        if (!$first) { $transSql .= ", "; $transValues .= ", "; }
        $transSql .= "updated_at";
        $transValues .= "NOW()";
    }
    
    $transSql .= ") " . $transValues . ")";
    
    $stmt = $conn->prepare($transSql);
    if (!$stmt) {
        error_log("Transaction sorgusu hazırlanamadı: " . $conn->error);
        return false;
    }
    
    if (count($transParams) > 0) {
        $stmt->bind_param($transTypes, ...$transParams);
    }
    
    if (!$stmt->execute()) {
        error_log("İşlem kaydı eklenemedi: " . $stmt->error);
        $stmt->close();
        return false;
    }
    
    $transaction_id = $stmt->insert_id;
    $stmt->close();
    
    return $transaction_id;
}


    /**
     * TRC20 adresini doğrula
     * 
     * @param string $address TRC20 adresi
     * @return bool Geçerli ise true, değilse false
     */
    function validateTRC20Address($address) {
        // TRC20 adresleri 'T' ile başlar ve toplam 34 karakter uzunluğundadır
        if (strlen($address) !== 34 || $address[0] !== 'T') {
            return false;
        }
        
        // Sadece alfanümerik karakterler içermeli
        return preg_match('/^[A-Za-z0-9]+$/', $address);
    }

/**
 * Admin işlem log'u ekle
 * 
 * @param int $admin_id Admin ID
 * @param string $action İşlem tipi
 * @param string $description Açıklama
 * @return int|false İşlem ID veya hata durumunda false
 */
function addActionLog($admin_id, $action, $description) {
    global $db;
    $conn = $db->getConnection();
    
    try {
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
        
        $sql = "
            INSERT INTO admin_logs (
                admin_id, action, description, ip_address, created_at
            ) VALUES (
                ?, ?, ?, ?, NOW()
            )
        ";
        
        $stmt = $conn->prepare($sql);
        
        if ($stmt === false) {
            error_log("addActionLog: SQL prepare hatası - " . $conn->error);
            return false;
        }
        
        $stmt->bind_param("isss", $admin_id, $action, $description, $ip_address);
        
        $stmt->execute();
        
        if ($stmt->affected_rows > 0) {
            $log_id = $conn->insert_id;
            $stmt->close();
            return $log_id;
        } else {
            error_log("addActionLog: Execute hatası - " . $stmt->error);
            $stmt->close();
            return false;
        }
    } catch (Exception $e) {
        error_log("Admin log ekleme hatası: " . $e->getMessage());
        return false;
    }
}

/**
 * Kullanıcı detaylarını getir
 * 
 * @param int $user_id Kullanıcı ID
 * @return array|false Kullanıcı bilgileri veya bulunamazsa false
 */
function getUserDetailsi($user_id) {
    global $db;
    $conn = $db->getConnection();
    
    $sql = "SELECT * FROM users WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        error_log("getUserDetails: SQL prepare hatası - " . $conn->error);
        return false;
    }
    
    $stmt->bind_param("i", $user_id);
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user;
    } else {
        $stmt->close();
        return false;
    }
}

/**
 * Tüm kullanıcıları getir
 * 
 * @return array Kullanıcı listesi
 */
function getAllUsers() {
    global $db;
    $conn = $db->getConnection();
    
    $sql = "SELECT id, username, email, balance FROM users ORDER BY username ASC";
    
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        error_log("getAllUsers: SQL prepare hatası - " . $conn->error);
        return [];
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result === false) {
        error_log("getAllUsers: Execute hatası - " . $stmt->error);
        return [];
    }
    
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    
    $stmt->close();
    return $users;
}

/**
 * Mining istatistikleri özeti
 * 
 * @return array Özet istatistikler
 */
function getMiningStatsSummary() {
    global $db;
    $conn = $db->getConnection();
    
    $summary = [
        'active_packages' => 0,
        'paused_packages' => 0,
        'total_hash_rate' => 0,
        'total_daily_revenue' => 0,
        'daily_electricity_cost' => 0,
        'daily_net_revenue' => 0
    ];
    
    try {
        // Aktif ve duraklatılmış paket sayıları
        $sql = "
            SELECT 
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_count,
                SUM(CASE WHEN status = 'paused' THEN 1 ELSE 0 END) as paused_count
            FROM user_mining_packages
        ";
        
        $result = $conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $summary['active_packages'] = $row['active_count'] ?? 0;
            $summary['paused_packages'] = $row['paused_count'] ?? 0;
        }
        
        // Toplam hash rate ve günlük kazançlar
        $today = date('Y-m-d');
        $sql = "
            SELECT 
                SUM(hash_rate) as total_hash_rate,
                SUM(revenue) as total_revenue,
                SUM(electricity_cost) as total_electricity_cost,
                SUM(net_revenue) as total_net_revenue
            FROM mining_stats
            WHERE date = ?
        ";
        
        $stmt = $conn->prepare($sql);
        
        if ($stmt === false) {
            error_log("getMiningStatsSummary: SQL prepare hatası - " . $conn->error);
            return $summary;
        }
        
        $stmt->bind_param("s", $today);
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $summary['total_hash_rate'] = $row['total_hash_rate'] ?? 0;
            $summary['total_daily_revenue'] = $row['total_revenue'] ?? 0;
            $summary['daily_electricity_cost'] = $row['total_electricity_cost'] ?? 0;
            $summary['daily_net_revenue'] = $row['total_net_revenue'] ?? 0;
        }
        
        $stmt->close();
        
        return $summary;
        
    } catch (Exception $e) {
        error_log("getMiningStatsSummary hatası: " . $e->getMessage());
        return $summary;
    }
}

/**
 * Günlük mining istatistiklerini getir
 * 
 * @param string $date Tarih (YYYY-MM-DD)
 * @param int $package_id Paket ID filtresi (0 = tüm paketler)
 * @param int $user_id Kullanıcı ID filtresi (0 = tüm kullanıcılar)
 * @return array Günlük istatistikler
 */
function getDailyMiningStats($date, $package_id = 0, $user_id = 0) {
    global $db;
    $conn = $db->getConnection();
    
    $sql = "
        SELECT 
            SUM(ms.hash_rate) as total_hash_rate,
            SUM(ms.revenue) as total_revenue,
            SUM(ms.electricity_cost) as total_electricity_cost,
            SUM(ms.net_revenue) as total_net_revenue,
            COUNT(DISTINCT ms.user_id) as user_count
        FROM mining_stats ms
        WHERE ms.date = ?
    ";
    
    $params = [$date];
    $types = "s";
    
    if ($package_id > 0) {
        $sql .= " AND ms.package_id = ?";
        $params[] = $package_id;
        $types .= "i";
    }
    
    if ($user_id > 0) {
        $sql .= " AND ms.user_id = ?";
        $params[] = $user_id;
        $types .= "i";
    }
    
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        error_log("getDailyMiningStats: SQL prepare hatası - " . $conn->error);
        return [
            'total_hash_rate' => 0,
            'total_revenue' => 0,
            'total_electricity_cost' => 0,
            'total_net_revenue' => 0,
            'user_count' => 0
        ];
    }
    
    $stmt->bind_param($types, ...$params);
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result === false) {
        error_log("getDailyMiningStats: Execute hatası - " . $stmt->error);
        return [
            'total_hash_rate' => 0,
            'total_revenue' => 0,
            'total_electricity_cost' => 0,
            'total_net_revenue' => 0,
            'user_count' => 0
        ];
    }
    
    $row = $result->fetch_assoc();
    
    // Sonuç yoksa varsayılan değerleri döndür
    if (!$row) {
        return [
            'total_hash_rate' => 0,
            'total_revenue' => 0,
            'total_electricity_cost' => 0,
            'total_net_revenue' => 0,
            'user_count' => 0
        ];
    }
    
    $stmt->close();
    return $row;
}

/**
 * İşlem geçmişini getir
 * 
 * @param string $start_date Başlangıç tarihi (YYYY-MM-DD)
 * @param string $end_date Bitiş tarihi (YYYY-MM-DD)
 * @param string $type İşlem tipi filtresi (all, deposit, withdraw, ...)
 * @param int $user_id Kullanıcı ID filtresi (0 = tüm kullanıcılar)
 * @param string $status Durum filtresi (all, completed, pending, failed, cancelled)
 * @param int $limit Limit
 * @param int $offset Offset
 * @return array İşlem geçmişi listesi
 */
function getTransactions($start_date, $end_date, $type = 'all', $user_id = 0, $status = 'all', $limit = 50, $offset = 0) {
    global $db;
    $conn = $db->getConnection();
    
    $sql = "
        SELECT t.*, u.username
        FROM transactions t
        LEFT JOIN users u ON t.user_id = u.id
        WHERE 1=1
    ";
    
    $params = [];
    $types = "";
    
    // Tarih filtresi
    if (!empty($start_date)) {
        $sql .= " AND DATE(t.created_at) >= ?";
        $params[] = $start_date;
        $types .= "s";
    }
    
    if (!empty($end_date)) {
        $sql .= " AND DATE(t.created_at) <= ?";
        $params[] = $end_date;
        $types .= "s";
    }
    
    // İşlem tipi filtresi
    if (!empty($type) && $type !== 'all') {
        $sql .= " AND t.type = ?";
        $params[] = $type;
        $types .= "s";
    }
    
    // Kullanıcı filtresi
    if (!empty($user_id) && $user_id > 0) {
        $sql .= " AND t.user_id = ?";
        $params[] = $user_id;
        $types .= "i";
    }
    
    // Durum filtresi
    if (!empty($status) && $status !== 'all') {
        $sql .= " AND t.status = ?";
        $params[] = $status;
        $types .= "s";
    }
    
    // Sıralama
    $sql .= " ORDER BY t.created_at DESC";
    
    // Limit ve offset
    $sql .= " LIMIT ?, ?";
    $params[] = $offset;
    $params[] = $limit;
    $types .= "ii";
    
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        error_log("getTransactions: SQL prepare hatası - " . $conn->error);
        return [];
    }
    
    $stmt->bind_param($types, ...$params);
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result === false) {
        error_log("getTransactions: Execute hatası - " . $stmt->error);
        return [];
    }
    
    $transactions = [];
    while ($row = $result->fetch_assoc()) {
        $transactions[] = $row;
    }
    
    $stmt->close();
    return $transactions;
}

/**
 * İşlem geçmişi sayısını getir
 * 
 * @param string $start_date Başlangıç tarihi (YYYY-MM-DD)
 * @param string $end_date Bitiş tarihi (YYYY-MM-DD)
 * @param string $type İşlem tipi filtresi (all, deposit, withdraw, ...)
 * @param int $user_id Kullanıcı ID filtresi (0 = tüm kullanıcılar)
 * @param string $status Durum filtresi (all, completed, pending, failed, cancelled)
 * @return int Toplam kayıt sayısı
 */
function getTransactionCount($start_date, $end_date, $type = 'all', $user_id = 0, $status = 'all') {
    global $db;
    $conn = $db->getConnection();
    
    $sql = "
        SELECT COUNT(*) as total
        FROM transactions t
        WHERE 1=1
    ";
    
    $params = [];
    $types = "";
    
    // Tarih filtresi
    if (!empty($start_date)) {
        $sql .= " AND DATE(t.created_at) >= ?";
        $params[] = $start_date;
        $types .= "s";
    }
    
    if (!empty($end_date)) {
        $sql .= " AND DATE(t.created_at) <= ?";
        $params[] = $end_date;
        $types .= "s";
    }
    
    // İşlem tipi filtresi
    if (!empty($type) && $type !== 'all') {
        $sql .= " AND t.type = ?";
        $params[] = $type;
        $types .= "s";
    }
    
    // Kullanıcı filtresi
    if (!empty($user_id) && $user_id > 0) {
        $sql .= " AND t.user_id = ?";
        $params[] = $user_id;
        $types .= "i";
    }
    
    // Durum filtresi
    if (!empty($status) && $status !== 'all') {
        $sql .= " AND t.status = ?";
        $params[] = $status;
        $types .= "s";
    }
    
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        error_log("getTransactionCount: SQL prepare hatası - " . $conn->error);
        return 0;
    }
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result === false) {
        error_log("getTransactionCount: Execute hatası - " . $stmt->error);
        return 0;
    }
    
    $row = $result->fetch_assoc();
    $stmt->close();
    
    return $row['total'] ?? 0;
}
/**
 * Paket bazlı mining istatistiklerini getir
 * 
 * @param string $start_date Başlangıç tarihi (YYYY-MM-DD)
 * @param string $end_date Bitiş tarihi (YYYY-MM-DD)
 * @return array Paket bazlı istatistikler
 */
function getPackageMiningStats($start_date, $end_date) {
    global $db;
    $conn = $db->getConnection();
    
    try {
        $sql = "
            SELECT 
                mp.id,
                mp.name,
                mp.hash_rate as package_hash_rate,
                COUNT(DISTINCT ump.user_id) as user_count,
                SUM(ump.quantity) as total_quantity,
                SUM(ump.quantity * mp.hash_rate) as total_hash_rate,
                (SELECT SUM(ms.revenue) / COUNT(DISTINCT ms.date) 
                    FROM mining_stats ms 
                    WHERE ms.package_id = mp.id 
                    AND ms.date BETWEEN ? AND ?) as daily_revenue,
                (SELECT SUM(ms.electricity_cost) / COUNT(DISTINCT ms.date) 
                    FROM mining_stats ms 
                    WHERE ms.package_id = mp.id 
                    AND ms.date BETWEEN ? AND ?) as daily_electricity_cost,
                (SELECT SUM(ms.net_revenue) / COUNT(DISTINCT ms.date) 
                    FROM mining_stats ms 
                    WHERE ms.package_id = mp.id 
                    AND ms.date BETWEEN ? AND ?) as daily_net_revenue,
                (SELECT SUM(ms.net_revenue) 
                    FROM mining_stats ms 
                    WHERE ms.package_id = mp.id 
                    AND ms.date BETWEEN ? AND ?) as period_net_revenue
            FROM mining_packages mp
            LEFT JOIN user_mining_packages ump ON mp.id = ump.package_id AND ump.status = 'active'
            GROUP BY mp.id, mp.name, mp.hash_rate
            ORDER BY mp.price ASC
        ";
        
        $stmt = $conn->prepare($sql);
        
        if ($stmt === false) {
            error_log("getPackageMiningStats: SQL prepare hatası - " . $conn->error);
            return [];
        }
        
        // Parametreleri bağlama - her alt sorgu için tekrarlanan parametreler
        $stmt->bind_param("ssssssss", 
            $start_date, $end_date, 
            $start_date, $end_date, 
            $start_date, $end_date, 
            $start_date, $end_date
        );
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result === false) {
            error_log("getPackageMiningStats: Execute hatası - " . $stmt->error);
            return [];
        }
        
        $stats = [];
        while ($row = $result->fetch_assoc()) {
            $stats[] = $row;
        }
        
        $stmt->close();
        return $stats;
        
    } catch (Exception $e) {
        error_log("getPackageMiningStats hatası: " . $e->getMessage());
        return [];
    }
}

/**
 * En çok kazanç sağlayan kullanıcıları getir
 * 
 * @param string $start_date Başlangıç tarihi (YYYY-MM-DD)
 * @param string $end_date Bitiş tarihi (YYYY-MM-DD)
 * @param int $limit Limit
 * @return array Kullanıcı listesi
 */
function getTopMiningUsers($start_date, $end_date, $limit = 10) {
    global $db;
    $conn = $db->getConnection();
    
    try {
        $sql = "
            SELECT 
                ms.user_id,
                u.username,
                SUM(ms.hash_rate) as hash_rate,
                SUM(ms.net_revenue) as net_revenue
            FROM mining_stats ms
            JOIN users u ON ms.user_id = u.id
            WHERE ms.date BETWEEN ? AND ?
            GROUP BY ms.user_id, u.username
            ORDER BY net_revenue DESC
            LIMIT ?
        ";
        
        $stmt = $conn->prepare($sql);
        
        if ($stmt === false) {
            error_log("getTopMiningUsers: SQL prepare hatası - " . $conn->error);
            return [];
        }
        
        $stmt->bind_param("ssi", $start_date, $end_date, $limit);
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result === false) {
            error_log("getTopMiningUsers: Execute hatası - " . $stmt->error);
            return [];
        }
        
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        
        $stmt->close();
        return $users;
        
    } catch (Exception $e) {
        error_log("getTopMiningUsers hatası: " . $e->getMessage());
        return [];
    }
}
/**
 * Yeni bir VIP paketi oluşturur
 * 
 * @param string $name Paket adı
 * @param float $price Paket fiyatı
 * @param int $duration_days Süre (gün)
 * @param int $daily_game_limit Günlük oyun limiti
 * @param float $game_max_win_chance Maksimum kazanma şansı (0-1 arası)
 * @param float $referral_rate Referans komisyon oranı (0-1 arası)
 * @param float $mining_bonus_rate Mining bonus oranı (0-1 arası)
 * @param string $features Paket özellikleri (JSON string)
 * @param string $description Açıklama
 * @param int $is_active Aktif mi? (1/0)
 * @return int|false Başarılı ise paket ID'si, değilse false
 */
function createVipPackage($name, $price, $duration_days, $daily_game_limit, $game_max_win_chance, $referral_rate, $mining_bonus_rate, $features, $description = '', $is_active = 1) {
    global $db;
    $conn = $db->getConnection();
    
    try {
        $stmt = $conn->prepare("
            INSERT INTO vip_packages (
                name, price, duration_days, daily_game_limit, 
                game_max_win_chance, referral_rate, mining_bonus_rate, 
                features, description, is_active, created_at, updated_at
            ) VALUES (
                ?, ?, ?, ?, 
                ?, ?, ?, 
                ?, ?, ?, NOW(), NOW()
            )
        ");
        
        $stmt->bind_param(
            "sdiiiddsssi", 
            $name, 
            $price, 
            $duration_days, 
            $daily_game_limit, 
            $game_max_win_chance, 
            $referral_rate, 
            $mining_bonus_rate, 
            $features, 
            $description, 
            $is_active
        );
        
        if ($stmt->execute()) {
            $package_id = $conn->insert_id;
            $stmt->close();
            return $package_id;
        } else {
            error_log("VIP paketi oluşturma hatası: " . $stmt->error);
            $stmt->close();
            return false;
        }
    } catch (Exception $e) {
        error_log("VIP paketi oluşturma hatası: " . $e->getMessage());
        return false;
    }
}
function getWithdrawals($start_date, $end_date, $status_filter = 'all', $user_filter = 0) {
    global $conn;  // Global veritabanı bağlantısını kullan
    
    // Temel sorgu
    $query = "SELECT w.*, u.username 
              FROM withdrawals w
              JOIN users u ON w.user_id = u.id
              WHERE w.created_at BETWEEN ? AND ?";
    
    // Durum filtresini ekle
    if ($status_filter !== 'all') {
        $query .= " AND w.status = '" . $conn->real_escape_string($status_filter) . "'";
    }
    
    // Kullanıcı filtresini ekle
    if ($user_filter > 0) {
        $query .= " AND w.user_id = " . (int)$user_filter;
    }
    
    // Son güncelleme tarihine göre sırala
    $query .= " ORDER BY w.created_at DESC";
    
    // Prepared statement kullan
    $stmt = $conn->prepare($query);
    
    if ($stmt === false) {
        throw new Exception("Sorgu hazırlanamadı: " . $conn->error);
    }
    
    // Tarih parametrelerini bağla
    $start_date_full = $start_date . ' 00:00:00';
    $end_date_full = $end_date . ' 23:59:59';
    $stmt->bind_param("ss", $start_date_full, $end_date_full);
    
    if (!$stmt->execute()) {
        throw new Exception("Sorgu çalıştırılamadı: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $withdrawals = [];
    
    while ($row = $result->fetch_assoc()) {
        $withdrawals[] = $row;
    }
    
    return $withdrawals;
}

/**
 * İşlem tipi istatistiklerini getir
 * 
 * @param string $start_date Başlangıç tarihi (YYYY-MM-DD)
 * @param string $end_date Bitiş tarihi (YYYY-MM-DD)
 * @param int $user_id Kullanıcı ID filtresi (0 = tüm kullanıcılar)
 * @return array İşlem tipi istatistikleri
 */
function getTransactionTypeStats($start_date, $end_date, $user_id = 0) {
    global $db;
    $conn = $db->getConnection();
    
    // İşlem tipleri
    $transaction_types = [
        'deposit', 'withdraw', 'referral', 'referral_transfer', 
        'game', 'mining', 'miningdeposit', 'vip', 'bonus', 'transfer', 'other'
    ];
    
    // Her bir işlem tipi için istatistikleri başlat
    $stats = [];
    foreach ($transaction_types as $type) {
        $stats[$type] = [
            'count' => 0,
            'amount' => 0
        ];
    }
    
    // SQL sorgusu hazırla
    $sql = "
        SELECT 
            type,
            COUNT(*) as count,
            SUM(amount) as amount
        FROM transactions
        WHERE status = 'completed'
    ";
    
    $params = [];
    $types = "";
    
    // Tarih filtresi
    if (!empty($start_date)) {
        $sql .= " AND DATE(created_at) >= ?";
        $params[] = $start_date;
        $types .= "s";
    }
    
    if (!empty($end_date)) {
        $sql .= " AND DATE(created_at) <= ?";
        $params[] = $end_date;
        $types .= "s";
    }
    
    // Kullanıcı filtresi
    if (!empty($user_id) && $user_id > 0) {
        $sql .= " AND user_id = ?";
        $params[] = $user_id;
        $types .= "i";
    }
    
    $sql .= " GROUP BY type";
    
    // Sorguyu çalıştır
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        error_log("getTransactionTypeStats: SQL prepare hatası - " . $conn->error);
        return $stats;
    }
    
    // Parametreleri bağla
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result === false) {
        error_log("getTransactionTypeStats: Execute hatası - " . $stmt->error);
        return $stats;
    }
    
    // Sonuçları işle
    while ($row = $result->fetch_assoc()) {
        if (isset($row['type']) && array_key_exists($row['type'], $stats)) {
            $stats[$row['type']]['count'] = $row['count'];
            $stats[$row['type']]['amount'] = $row['amount'];
        } else if (isset($row['type'])) {
            // Diğer işlem tipleri için
            $stats['other']['count'] += $row['count'];
            $stats['other']['amount'] += $row['amount'];
        }
    }
    
    $stmt->close();
    return $stats;
}


/**
 * Manuel para çekme işlemi ekle
 * 
 * @param int $user_id Kullanıcı ID
 * @param float $amount Çekim tutarı
 * @param float $fee İşlem ücreti
 * @param string $status Durum (pending, processing, completed, cancelled)
 * @param string $trc20_address TRC20 cüzdan adresi
 * @param string $transaction_hash İşlem hash (opsiyonel)
 * @param string $admin_note Admin notu (opsiyonel)
 * @return array ['success' => bool, 'withdrawal_id' => int|null, 'message' => string]
 */
function addManualWithdrawal($user_id, $amount, $fee, $status = 'pending', $trc20_address = '', $transaction_hash = '', $admin_note = '') {
    global $conn;
    
    // Sonuç dizisi oluştur
    $result = [
        'success' => false,
        'withdrawal_id' => null,
        'message' => ''
    ];
    
    try {
        // Önce users tablosunu kontrol et - kullanıcı var mı ve bakiye yeterli mi?
        $query = "SELECT * FROM users WHERE id = ?";
        $stmt = $conn->prepare($query);
        
        if (!$stmt) {
            throw new Exception("Kullanıcı sorgusu hazırlanamadı: " . $conn->error);
        }
        
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $userResult = $stmt->get_result();
        $user = $userResult->fetch_assoc();
        $stmt->close();
        
        if (!$user) {
            throw new Exception("Kullanıcı bulunamadı (ID: $user_id)");
        }
        
        // Toplam çekim tutarı (miktar + ücret)
        $total_amount = $amount + $fee;
        
        // Bakiye kontrolü
        if ($status === 'completed' && $user['balance'] < $total_amount) {
            throw new Exception("Kullanıcının bakiyesi yetersiz. Bakiye: " . number_format($user['balance'], 2) . " USDT, Gerekli: " . number_format($total_amount, 2) . " USDT");
        }
        
        // Withdrawals tablo yapısını kontrol et
        $tableInfo = $conn->query("DESCRIBE withdrawals");
        if (!$tableInfo) {
            throw new Exception("Withdrawals tablo yapısı alınamadı: " . $conn->error);
        }
        
        $columns = [];
        while ($row = $tableInfo->fetch_assoc()) {
            $columns[] = $row['Field'];
        }
        
        // Para çekme kaydı ekle - dinamik SQL oluştur
        $sql = "INSERT INTO withdrawals (user_id, amount, fee, status, trc20_address";
        $values = "VALUES (?, ?, ?, ?, ?";
        $params = [$user_id, $amount, $fee, $status, $trc20_address];
        $types = "iddss"; // integer, double, double, string, string
        
        if (!empty($transaction_hash) && in_array('transaction_hash', $columns)) {
            $sql .= ", transaction_hash";
            $values .= ", ?";
            $params[] = $transaction_hash;
            $types .= "s";
        }
        
        if (!empty($admin_note) && in_array('admin_note', $columns)) {
            $sql .= ", admin_note";
            $values .= ", ?";
            $params[] = $admin_note;
            $types .= "s";
        }
        
        if (in_array('created_at', $columns)) {
            $sql .= ", created_at";
            $values .= ", NOW()";
        }
        
        if (in_array('updated_at', $columns)) {
            $sql .= ", updated_at";
            $values .= ", NOW()";
        }
        
        $sql .= ") " . $values . ")";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Para çekme kaydı eklenemedi: " . $conn->error);
        }
        
        $stmt->bind_param($types, ...$params);
        if (!$stmt->execute()) {
            throw new Exception("Para çekme kaydı eklenirken hata: " . $stmt->error);
        }
        
        $withdrawal_id = $stmt->insert_id;
        $stmt->close();
        
        // Sonuç dizisine withdrawal_id ekle
        $result['withdrawal_id'] = $withdrawal_id;
        
        // Eğer durum "completed" ise bakiyeyi güncelle
        if ($status === 'completed') {
            // Bakiyeyi güncelle
            $current_balance = $user['balance'];
            $new_balance = $current_balance - $total_amount;
            
            $updateSql = "UPDATE users SET balance = ?";
            
            // updated_at varsa ekle
            if (in_array('updated_at', array_column(mysqli_fetch_all($conn->query("DESCRIBE users"), MYSQLI_ASSOC), 'Field'))) {
                $updateSql .= ", updated_at = NOW()";
            }
            
            $updateSql .= " WHERE id = ?";
            
            $stmt = $conn->prepare($updateSql);
            if (!$stmt) {
                throw new Exception("Kullanıcı bakiyesi güncellenemedi: " . $conn->error);
            }
            
            $stmt->bind_param("di", $new_balance, $user_id);
            if (!$stmt->execute()) {
                throw new Exception("Kullanıcı bakiyesi güncellenirken hata: " . $stmt->error);
            }
            $stmt->close();
            
            // Transactions tablosuna kayıt ekle
            // Önce tablo yapısını kontrol et
            $tableInfo = $conn->query("DESCRIBE transactions");
            if (!$tableInfo) {
                throw new Exception("Transactions tablo yapısı alınamadı: " . $conn->error);
            }
            
            $transColumns = [];
            while ($row = $tableInfo->fetch_assoc()) {
                $transColumns[] = $row['Field'];
            }
            
            // Transaction SQL'i oluştur
            $transSql = "INSERT INTO transactions (";
            $transValues = "VALUES (";
            $transParams = [];
            $transTypes = "";
            $first = true;
            
            // Gerekli ve mevcut sütunları ekle
            if (in_array('user_id', $transColumns)) {
                $transSql .= "user_id";
                $transValues .= "?";
                $transParams[] = $user_id;
                $transTypes .= "i";
                $first = false;
            }
            
            if (in_array('type', $transColumns)) {
                if (!$first) { $transSql .= ", "; $transValues .= ", "; }
                $transSql .= "type";
                $transValues .= "?";
                $transParams[] = 'withdraw';
                $transTypes .= "s";
                $first = false;
            }
            
            if (in_array('amount', $transColumns)) {
                if (!$first) { $transSql .= ", "; $transValues .= ", "; }
                $transSql .= "amount";
                $transValues .= "?";
                $transParams[] = -$total_amount; // Negative for withdrawal
                $transTypes .= "d";
                $first = false;
            }
            
            // Referans veya ilişkili ID
            if (in_array('related_id', $transColumns)) {
                if (!$first) { $transSql .= ", "; $transValues .= ", "; }
                $transSql .= "related_id";
                $transValues .= "?";
                $transParams[] = $withdrawal_id;
                $transTypes .= "i";
                $first = false;
            } else if (in_array('reference_id', $transColumns)) {
                if (!$first) { $transSql .= ", "; $transValues .= ", "; }
                $transSql .= "reference_id";
                $transValues .= "?";
                $transParams[] = $withdrawal_id;
                $transTypes .= "i";
                $first = false;
            }
            
            // Bakiye bilgileri
            if (in_array('before_balance', $transColumns)) {
                if (!$first) { $transSql .= ", "; $transValues .= ", "; }
                $transSql .= "before_balance";
                $transValues .= "?";
                $transParams[] = $current_balance;
                $transTypes .= "d";
                $first = false;
            } else if (in_array('balance_before', $transColumns)) {
                if (!$first) { $transSql .= ", "; $transValues .= ", "; }
                $transSql .= "balance_before";
                $transValues .= "?";
                $transParams[] = $current_balance;
                $transTypes .= "d";
                $first = false;
            }
            
            if (in_array('after_balance', $transColumns)) {
                if (!$first) { $transSql .= ", "; $transValues .= ", "; }
                $transSql .= "after_balance";
                $transValues .= "?";
                $transParams[] = $new_balance;
                $transTypes .= "d";
                $first = false;
            } else if (in_array('balance_after', $transColumns)) {
                if (!$first) { $transSql .= ", "; $transValues .= ", "; }
                $transSql .= "balance_after";
                $transValues .= "?";
                $transParams[] = $new_balance;
                $transTypes .= "d";
                $first = false;
            }
            
            if (in_array('status', $transColumns)) {
                if (!$first) { $transSql .= ", "; $transValues .= ", "; }
                $transSql .= "status";
                $transValues .= "?";
                $transParams[] = 'completed';
                $transTypes .= "s";
                $first = false;
            }
            
            if (in_array('description', $transColumns)) {
                if (!$first) { $transSql .= ", "; $transValues .= ", "; }
                $transSql .= "description";
                $transValues .= "?";
                $transParams[] = 'Manuel para çekme işlemi';
                $transTypes .= "s";
                $first = false;
            }
            
            if (in_array('created_at', $transColumns)) {
                if (!$first) { $transSql .= ", "; $transValues .= ", "; }
                $transSql .= "created_at";
                $transValues .= "NOW()";
                $first = false;
            }
            
            if (in_array('updated_at', $transColumns)) {
                if (!$first) { $transSql .= ", "; $transValues .= ", "; }
                $transSql .= "updated_at";
                $transValues .= "NOW()";
            }
            
            $transSql .= ") " . $transValues . ")";
            
            $stmt = $conn->prepare($transSql);
            if (!$stmt) {
                throw new Exception("İşlem kaydı eklenemedi: " . $conn->error);
            }
            
            if (count($transParams) > 0) {
                $stmt->bind_param($transTypes, ...$transParams);
            }
            
            if (!$stmt->execute()) {
                throw new Exception("İşlem kaydı eklenirken hata: " . $stmt->error);
            }
            $stmt->close();
        }
        
        // Admin log kaydı ekle
        if (isset($_SESSION['admin_id'])) {
            $admin_id = $_SESSION['admin_id'];
            
            // Admin logs tablosunu kontrol et
            $tableInfo = $conn->query("DESCRIBE admin_logs");
            if ($tableInfo) {
                $logColumns = [];
                while ($row = $tableInfo->fetch_assoc()) {
                    $logColumns[] = $row['Field'];
                }
                
                $logSql = "INSERT INTO admin_logs (";
                $logValues = "VALUES (";
                $logParams = [];
                $logTypes = "";
                $first = true;
                
                if (in_array('admin_id', $logColumns)) {
                    $logSql .= "admin_id";
                    $logValues .= "?";
                    $logParams[] = $admin_id;
                    $logTypes .= "i";
                    $first = false;
                }
                
                if (in_array('action', $logColumns)) {
                    if (!$first) { $logSql .= ", "; $logValues .= ", "; }
                    $logSql .= "action";
                    $logValues .= "?";
                    $logParams[] = 'add_withdrawal';
                    $logTypes .= "s";
                    $first = false;
                }
                
                if (in_array('description', $logColumns)) {
                    if (!$first) { $logSql .= ", "; $logValues .= ", "; }
                    $logSql .= "description";
                    $logValues .= "?";
                    $logParams[] = "Manuel para çekme eklendi: Kullanıcı ID: $user_id, Tutar: $amount USDT, Ücret: $fee USDT";
                    $logTypes .= "s";
                    $first = false;
                }
                
                if (in_array('related_id', $logColumns)) {
                    if (!$first) { $logSql .= ", "; $logValues .= ", "; }
                    $logSql .= "related_id";
                    $logValues .= "?";
                    $logParams[] = $withdrawal_id;
                    $logTypes .= "i";
                    $first = false;
                }
                
                if (in_array('related_type', $logColumns)) {
                    if (!$first) { $logSql .= ", "; $logValues .= ", "; }
                    $logSql .= "related_type";
                    $logValues .= "?";
                    $logParams[] = 'withdrawal';
                    $logTypes .= "s";
                    $first = false;
                }
                
                if (in_array('created_at', $logColumns)) {
                    if (!$first) { $logSql .= ", "; $logValues .= ", "; }
                    $logSql .= "created_at";
                    $logValues .= "NOW()";
                }
                
                $logSql .= ") " . $logValues . ")";
                
                $stmt = $conn->prepare($logSql);
                if ($stmt) {
                    if (count($logParams) > 0) {
                        $stmt->bind_param($logTypes, ...$logParams);
                    }
                    $stmt->execute();
                    $stmt->close();
                }
            }
        }
        
        // Başarılı sonuç
        $result['success'] = true;
        $result['message'] = "Para çekme işlemi başarıyla eklendi.";
        
        return $result;
    } catch (Exception $e) {
        // Hata durumunda
        $result['message'] = $e->getMessage();
        return $result;
    }
}