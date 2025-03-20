<?php
/**
 * Admin Panel için gerekli fonksiyonlar
 */

// Veritabanı bağlantısını dahil et
require_once 'config.php';

/**
 * Admin dashboard için istatistikleri toplar
 * 
 * @return array İstatistikler dizisi
 */
function getAdminDashboardStats() {
    $conn = $GLOBALS['db']->getConnection();
    $stats = [];

    // Toplam kullanıcı sayısı
    $query = "SELECT COUNT(*) as total FROM users";
    $result = $conn->query($query);
    $stats['total_users'] = $result->fetch_assoc()['total'];

    // Toplam deposit miktarı
    $query = "SELECT COALESCE(SUM(amount), 0) as total FROM deposits WHERE status = 'confirmed'";
    $result = $conn->query($query);
    $stats['total_deposits'] = $result->fetch_assoc()['total'];

    // Toplam withdraw miktarı
    $query = "SELECT COALESCE(SUM(amount), 0) as total FROM withdrawals WHERE status = 'completed'";
    $result = $conn->query($query);
    $stats['total_withdrawals'] = $result->fetch_assoc()['total'];

    // Aktif ticket sayısı
    $query = "SELECT COUNT(*) as total FROM support_tickets WHERE status != 'closed'";
    $result = $conn->query($query);
    $stats['active_tickets'] = $result->fetch_assoc()['total'];
    
    // Bugünkü oyun sayısı
    $today = date('Y-m-d');
    $query = "SELECT COUNT(*) as total FROM game_attempts WHERE DATE(created_at) = '$today'";
    $result = $conn->query($query);
    $stats['today_games'] = $result->fetch_assoc()['total'];
    
    // Bugün dağıtılan ödül
    $query = "SELECT COALESCE(SUM(amount), 0) as total FROM transactions 
              WHERE type = 'game' AND DATE(created_at) = '$today'";
    $result = $conn->query($query);
    $stats['today_rewards'] = $result->fetch_assoc()['total'];
    
    // Toplam oyun sayısı
    $query = "SELECT COUNT(*) as total FROM game_attempts";
    $result = $conn->query($query);
    $stats['total_games'] = $result->fetch_assoc()['total'];
    
    // Toplam dağıtılan ödül
    $query = "SELECT COALESCE(SUM(amount), 0) as total FROM transactions WHERE type = 'game'";
    $result = $conn->query($query);
    $stats['total_rewards'] = $result->fetch_assoc()['total'];

    // Son kaydolan 5 kullanıcı
    $query = "SELECT id, username, created_at, balance 
              FROM users 
              ORDER BY created_at DESC 
              LIMIT 5";
    $result = $conn->query($query);
    $stats['recent_users'] = [];
    while ($row = $result->fetch_assoc()) {
        $stats['recent_users'][] = $row;
    }

    // Son 10 işlem
    $query = "SELECT t.id, t.user_id, t.type, t.amount, t.created_at, u.username 
              FROM transactions t
              JOIN users u ON t.user_id = u.id
              ORDER BY t.created_at DESC 
              LIMIT 10";
    $result = $conn->query($query);
    $stats['recent_transactions'] = [];
    while ($row = $result->fetch_assoc()) {
        $stats['recent_transactions'][] = $row;
    }

    // Haftalık istatistikler için veri hazırlığı (AJAX ile alınacak)
    // Bu kısım ajax/weekly_stats.php dosyasında işlenecek

    return $stats;
}

/**
 * Haftalık istatistikler için veri döndürür
 * 
 * @return array Haftalık istatistikler
 */
function getWeeklyStats() {
    $conn = $GLOBALS['db']->getConnection();
    $stats = [];
    
    // Son 7 gün için tarihler
    $dates = [];
    $new_users = [];
    $deposits = [];
    $withdrawals = [];
    
    // Son 7 günün tarihlerini oluştur
    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $dates[] = date('d M', strtotime($date));
        
        // Yeni kullanıcılar
        $query = "SELECT COUNT(*) as total FROM users WHERE DATE(created_at) = '$date'";
        $result = $conn->query($query);
        $new_users[] = $result->fetch_assoc()['total'];
        
        // Depositler
        $query = "SELECT COALESCE(SUM(amount), 0) as total FROM deposits WHERE DATE(created_at) = '$date' AND status = 'confirmed'";
        $result = $conn->query($query);
        $deposits[] = $result->fetch_assoc()['total'];
        
        // Withdrawlar
        $query = "SELECT COALESCE(SUM(amount), 0) as total FROM withdrawals WHERE DATE(created_at) = '$date' AND status = 'completed'";
        $result = $conn->query($query);
        $withdrawals[] = $result->fetch_assoc()['total'];
    }
    
    $stats['dates'] = $dates;
    $stats['new_users'] = $new_users;
    $stats['deposits'] = $deposits;
    $stats['withdrawals'] = $withdrawals;
    
    return $stats;
}

/**
 * Admin oturum kontrolü yapar
 * 
 * @return bool Admin oturumu varsa true, yoksa false döner
 */
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']);
}

/**
 * Admin eylemlerini loglar
 * 
 * @param string $action Eylem adı
 * @param string $description Eylem açıklaması
 * @param int|null $related_id İlişkili kayıt ID'si (opsiyonel)
 * @param string|null $related_type İlişkili kayıt tipi (opsiyonel)
 * @return bool Log başarılı ise true, değilse false döner
 */
function logAdminAction($action, $description, $related_id = null, $related_type = null) {
    $conn = $GLOBALS['db']->getConnection();
    
    if (!isAdminLoggedIn()) {
        return false;
    }
    
    $admin_id = $_SESSION['admin_id'];
    $ip_address = $_SERVER['REMOTE_ADDR'];
    
    $stmt = $conn->prepare("INSERT INTO admin_logs (admin_id, action, description, related_id, related_type, ip_address) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ississ", $admin_id, $action, $description, $related_id, $related_type, $ip_address);
    
    return $stmt->execute();
}

/**
 * Kullanıcı listesini getirir
 * 
 * @param int $limit Limit
 * @param int $offset Offset
 * @param string $search Arama terimi
 * @return array Kullanıcı listesi
 */
function getUsers($limit = 10, $offset = 0, $search = '') {
    $conn = $GLOBALS['db']->getConnection();
    
    $whereClause = '';
    if (!empty($search)) {
        $search = $conn->real_escape_string($search);
        $whereClause = "WHERE username LIKE '%$search%' OR email LIKE '%$search%' OR full_name LIKE '%$search%'";
    }
    
    $query = "SELECT * FROM users $whereClause ORDER BY created_at DESC LIMIT $offset, $limit";
    $result = $conn->query($query);
    
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    
    return $users;
}

/**
 * Kullanıcı sayısını getirir
 * 
 * @param string $search Arama terimi
 * @return int Kullanıcı sayısı
 */
function getUsersCount($search = '') {
    $conn = $GLOBALS['db']->getConnection();
    
    $whereClause = '';
    if (!empty($search)) {
        $search = $conn->real_escape_string($search);
        $whereClause = "WHERE username LIKE '%$search%' OR email LIKE '%$search%' OR full_name LIKE '%$search%'";
    }
    
    $query = "SELECT COUNT(*) as total FROM users $whereClause";
    $result = $conn->query($query);
    
    return $result->fetch_assoc()['total'];
}

/**
 * Kullanıcı detayını getirir
 * 
 * @param int $user_id Kullanıcı ID
 * @return array|null Kullanıcı bilgileri veya null
 */
function getUserDetails($user_id) {
    $conn = $GLOBALS['db']->getConnection();
    
    $user_id = (int) $user_id;
    $query = "SELECT * FROM users WHERE id = $user_id";
    $result = $conn->query($query);
    
    if ($result->num_rows === 0) {
        return null;
    }
    
    return $result->fetch_assoc();
}

/**
 * Kullanıcının işlemlerini getirir
 * 
 * @param int $user_id Kullanıcı ID
 * @param int $limit Limit
 * @param int $offset Offset
 * @return array İşlem listesi
 */
function getUserTransactions($user_id, $limit = 10, $offset = 0) {
    $conn = $GLOBALS['db']->getConnection();
    
    $user_id = (int) $user_id;
    $query = "SELECT * FROM transactions WHERE user_id = $user_id ORDER BY created_at DESC LIMIT $offset, $limit";
    $result = $conn->query($query);
    
    $transactions = [];
    while ($row = $result->fetch_assoc()) {
        $transactions[] = $row;
    }
    
    return $transactions;
}



/**
 * Kullanıcı bakiyesini günceller
 * 
 * @param int $user_id Kullanıcı ID
 * @param float $amount Miktar
 * @param string $type İşlem türü (add, subtract, set, add_referral, subtract_referral, set_referral)
 * @param string $note İşlem notu
 * @return bool|string Güncelleme başarılı ise true, değilse hata mesajı
 */
function updateUserBalance($user_id, $amount, $type, $note = '') {
    $conn = $GLOBALS['db']->getConnection();
    
    $user_id = (int) $user_id;
    $amount = (float) $amount;
    
    if ($amount <= 0 && $type != 'subtract' && $type != 'subtract_referral') {
        return "Geçersiz miktar.";
    }
    
    try {
        $conn->begin_transaction();
        
        // Kullanıcı bilgilerini al
        $stmt = $conn->prepare("SELECT balance, referral_balance FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception("Kullanıcı bulunamadı.");
        }
        
        $user = $result->fetch_assoc();
        $old_balance = $user['balance'];
        $old_referral_balance = $user['referral_balance'];
        $new_balance = $old_balance;
        $new_referral_balance = $old_referral_balance;
        
        // İşlem türüne göre bakiye güncelleme
        switch ($type) {
            case 'add':
                $new_balance = $old_balance + $amount;
                $update_field = 'balance';
                $transaction_type = 'bonus';
                break;
                
            case 'subtract':
                if ($old_balance < $amount) {
                    throw new Exception("Yetersiz bakiye.");
                }
                $new_balance = $old_balance - $amount;
                $update_field = 'balance';
                $transaction_type = 'deduction';
                break;
                
            case 'set':
                $new_balance = $amount;
                $update_field = 'balance';
                $transaction_type = 'admin_adjustment';
                break;
                
            case 'add_referral':
                $new_referral_balance = $old_referral_balance + $amount;
                $update_field = 'referral_balance';
                $transaction_type = 'referral_bonus';
                break;
                
            case 'subtract_referral':
                if ($old_referral_balance < $amount) {
                    throw new Exception("Yetersiz referans bakiyesi.");
                }
                $new_referral_balance = $old_referral_balance - $amount;
                $update_field = 'referral_balance';
                $transaction_type = 'referral_deduction';
                break;
                
            case 'set_referral':
                $new_referral_balance = $amount;
                $update_field = 'referral_balance';
                $transaction_type = 'admin_referral_adjustment';
                break;
                
            default:
                throw new Exception("Geçersiz işlem türü.");
        }
        
        // Bakiye güncelleme
        if ($update_field == 'balance') {
            $stmt = $conn->prepare("UPDATE users SET balance = ? WHERE id = ?");
            $stmt->bind_param("di", $new_balance, $user_id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET referral_balance = ? WHERE id = ?");
            $stmt->bind_param("di", $new_referral_balance, $user_id);
        }
        
        if (!$stmt->execute()) {
            throw new Exception("Bakiye güncellenirken bir hata oluştu.");
        }
        
        // İşlem kaydı
        $description = "Admin tarafından bakiye düzenlemesi: $note";
        $before_balance = ($update_field == 'balance') ? $old_balance : $old_referral_balance;
        $after_balance = ($update_field == 'balance') ? $new_balance : $new_referral_balance;
        
        $stmt = $conn->prepare("INSERT INTO transactions (user_id, type, amount, before_balance, after_balance, status, description) VALUES (?, ?, ?, ?, ?, 'completed', ?)");
        $stmt->bind_param("isddds", $user_id, $transaction_type, $amount, $before_balance, $after_balance, $description);
        
        if (!$stmt->execute()) {
            throw new Exception("İşlem kaydedilirken bir hata oluştu.");
        }
        
        // Admin log
        if (isset($_SESSION['admin_id'])) {
            $admin_id = $_SESSION['admin_id'];
            $ip = $_SERVER['REMOTE_ADDR'];
            $log_description = "Kullanıcı #$user_id $transaction_type işlemi: $amount USDT. Not: $note";
            
            $stmt = $conn->prepare("INSERT INTO admin_logs (admin_id, action, description, related_id, related_type, ip_address) VALUES (?, 'update_balance', ?, ?, 'user', ?)");
            $stmt->bind_param("isis", $admin_id, $log_description, $user_id, $ip);
            $stmt->execute();
        }
        
        $conn->commit();
        return true;
        
    } catch (Exception $e) {
        $conn->rollback();
        return $e->getMessage();
    }
}



/**
 * Kullanıcı durumunu günceller
 * 
 * @param int $user_id Kullanıcı ID
 * @param string $status Yeni durum (active, blocked, pending)
 * @return bool|string Güncelleme başarılı ise true, değilse hata mesajı
 */
function updateUserStatus($user_id, $status) {
    $conn = $GLOBALS['db']->getConnection();
    
    $user_id = (int) $user_id;
    $allowed_statuses = ['active', 'blocked', 'pending'];
    
    if (!in_array($status, $allowed_statuses)) {
        return "Geçersiz durum değeri.";
    }
    
    try {
        $conn->begin_transaction();
        
        $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $user_id);
        
        if (!$stmt->execute()) {
            throw new Exception("Durum güncellenirken bir hata oluştu.");
        }
        
        // Admin log
        if (isset($_SESSION['admin_id'])) {
            $admin_id = $_SESSION['admin_id'];
            $ip = $_SERVER['REMOTE_ADDR'];
            $description = "Kullanıcı #$user_id durumu '$status' olarak güncellendi.";
            
            $stmt = $conn->prepare("INSERT INTO admin_logs (admin_id, action, description, related_id, related_type, ip_address) VALUES (?, 'update_user_status', ?, ?, 'user', ?)");
            $stmt->bind_param("isis", $admin_id, $description, $user_id, $ip);
            $stmt->execute();
        }
        
        $conn->commit();
        return true;
        
    } catch (Exception $e) {
        $conn->rollback();
        return $e->getMessage();
    }
}


/**
 * Kullanıcı adını ID'ye göre getirir
 * 
 * @param int $user_id Kullanıcı ID
 * @return string Kullanıcı adı
 */
function getUsernameById($user_id) {
    $conn = $GLOBALS['db']->getConnection();
    
    $user_id = (int) $user_id;
    $query = "SELECT username FROM users WHERE id = $user_id";
    $result = $conn->query($query);
    
    if ($result->num_rows === 0) {
        return "Kullanıcı #$user_id";
    }
    
    return $result->fetch_assoc()['username'];
}



/**
 * Kullanıcının VIP paketini getirir
 * 
 * @param int $user_id Kullanıcı ID
 * @return array|null VIP paket bilgileri veya null
 */
function getUserVipPackage($user_id) {
    $conn = $GLOBALS['db']->getConnection();
    
    $user_id = (int) $user_id;
    $query = "SELECT u.vip_level, vp.* 
              FROM users u
              JOIN vip_packages vp ON u.vip_level = vp.id
              WHERE u.id = $user_id";
    $result = $conn->query($query);
    
    if ($result->num_rows === 0) {
        return null;
    }
    
    return $result->fetch_assoc();
}


/**
 * Kullanıcının mining paketlerini getirir
 * 
 * @param int $user_id Kullanıcı ID
 * @return array Mining paketleri listesi
 */
function getUserMiningPackages($user_id) {
    $conn = $GLOBALS['db']->getConnection();
    
    $user_id = (int) $user_id;
    $query = "SELECT ump.*, mp.name 
              FROM user_mining_packages ump
              JOIN mining_packages mp ON ump.package_id = mp.id
              WHERE ump.user_id = $user_id
              ORDER BY ump.purchase_date DESC";
    $result = $conn->query($query);
    
    $packages = [];
    while ($row = $result->fetch_assoc()) {
        $packages[] = $row;
    }
    
    return $packages;
}

/**
 * Kullanıcının referans sayısını getirir
 * 
 * @param int $user_id Kullanıcı ID
 * @return int Referans sayısı
 */
function getUserReferralsCount($user_id) {
    $conn = $GLOBALS['db']->getConnection();
    
    $user_id = (int) $user_id;
    $query = "SELECT COUNT(*) as total FROM users WHERE referrer_id = $user_id";
    $result = $conn->query($query);
    
    return $result->fetch_assoc()['total'];
}



/**
 * Deposit listesini getirir
 * 
 * @param int $limit Limit
 * @param int $offset Offset
 * @param string $search Arama terimi
 * @return array Deposit listesi
 */
function getDeposits($limit = 10, $offset = 0, $search = '') {
    $conn = $GLOBALS['db']->getConnection();
    
    $whereClause = '';
    if (!empty($search)) {
        $search = $conn->real_escape_string($search);
        $whereClause = "WHERE d.payment_id LIKE '%$search%' OR u.username LIKE '%$search%'";
    }
    
    $query = "SELECT d.*, u.username 
              FROM deposits d
              JOIN users u ON d.user_id = u.id
              $whereClause
              ORDER BY d.created_at DESC 
              LIMIT $offset, $limit";
    $result = $conn->query($query);
    
    $deposits = [];
    while ($row = $result->fetch_assoc()) {
        $deposits[] = $row;
    }
    
    return $deposits;
}

/**
 * Withdrawal listesini getirir
 * 
 * @param int $limit Limit
 * @param int $offset Offset
 * @param string $search Arama terimi
 * @return array Withdrawal listesi
 */
function getWithdrawalsa($limit = 10, $offset = 0, $search = '') {
    $conn = $GLOBALS['db']->getConnection();
    
    $whereClause = '';
    if (!empty($search)) {
        $search = $conn->real_escape_string($search);
        $whereClause = "WHERE w.transaction_hash LIKE '%$search%' OR u.username LIKE '%$search%'";
    }
    
    $query = "SELECT w.*, u.username 
              FROM withdrawals w
              JOIN users u ON w.user_id = u.id
              $whereClause
              ORDER BY w.created_at DESC 
              LIMIT $offset, $limit";
    $result = $conn->query($query);
    
    $withdrawals = [];
    while ($row = $result->fetch_assoc()) {
        $withdrawals[] = $row;
    }
    
    return $withdrawals;
}

/**
 * Açık destek taleplerini getirir
 * 
 * @param int $limit Limit
 * @param int $offset Offset
 * @return array Destek talepleri listesi
 */
function getOpenTickets($limit = 10, $offset = 0) {
    $conn = $GLOBALS['db']->getConnection();
    
    $query = "SELECT t.*, u.username 
              FROM support_tickets t
              JOIN users u ON t.user_id = u.id
              WHERE t.status != 'closed'
              ORDER BY 
                CASE 
                  WHEN t.priority = 'high' THEN 1
                  WHEN t.priority = 'medium' THEN 2
                  WHEN t.priority = 'low' THEN 3
                END,
                t.created_at ASC
              LIMIT $offset, $limit";
    $result = $conn->query($query);
    
    $tickets = [];
    while ($row = $result->fetch_assoc()) {
        $tickets[] = $row;
    }
    
    return $tickets;
}

/**
 * Site ayarlarını getirir
 * 
 * @return array Site ayarları
 */
function getSiteSettings() {
    $conn = $GLOBALS['db']->getConnection();
    
    $query = "SELECT setting_key, setting_value FROM site_settings";
    $result = $conn->query($query);
    
    $settings = [];
    while ($row = $result->fetch_assoc()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    
    return $settings;
}

/**
 * Site ayarını günceller
 * 
 * @param string $key Ayar anahtarı
 * @param string $value Ayar değeri
 * @return bool Güncelleme başarılı ise true, değilse false
 */
function updateSiteSetting($key, $value) {
    $conn = $GLOBALS['db']->getConnection();
    
    $key = $conn->real_escape_string($key);
    $value = $conn->real_escape_string($value);
    
    $query = "UPDATE site_settings SET setting_value = '$value' WHERE setting_key = '$key'";
    return $conn->query($query);
}

/**
 * VIP paketlerini getirir
 * 
 * @return array VIP paketleri
 */
function getVipPackages() {
    $conn = $GLOBALS['db']->getConnection();
    
    $query = "SELECT * FROM vip_packages ORDER BY price ASC";
    $result = $conn->query($query);
    
    $packages = [];
    while ($row = $result->fetch_assoc()) {
        $packages[] = $row;
    }
    
    return $packages;
}

/**
 * Mining paketlerini getirir
 * 
 * @return array Mining paketleri
 */
function getMiningPackages() {
    $conn = $GLOBALS['db']->getConnection();
    
    $query = "SELECT * FROM mining_packages ORDER BY package_price ASC";
    $result = $conn->query($query);
    
    $packages = [];
    while ($row = $result->fetch_assoc()) {
        $packages[] = $row;
    }
    
    return $packages;
}

/**
 * Oyun ayarlarını getirir
 * 
 * @return array Oyun ayarları
 */
function getGameSettings() {
    $conn = $GLOBALS['db']->getConnection();
    
    $query = "SELECT setting_key, setting_value FROM game_settings";
    $result = $conn->query($query);
    
    $settings = [];
    while ($row = $result->fetch_assoc()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    
    return $settings;
}

/**
 * Oyun ayarını günceller
 * 
 * @param string $key Ayar anahtarı
 * @param string $value Ayar değeri
 * @return bool Güncelleme başarılı ise true, değilse false
 */
function updateGameSetting($key, $value) {
    $conn = $GLOBALS['db']->getConnection();
    
    $key = $conn->real_escape_string($key);
    $value = $conn->real_escape_string($value);
    
    $query = "UPDATE game_settings SET setting_value = '$value' WHERE setting_key = '$key'";
    return $conn->query($query);
}

/**
 * Admin bilgilerini getirir
 * 
 * @param int $admin_id Admin ID
 * @return array|null Admin bilgileri veya null
 */
function getAdminDetails($admin_id) {
    $conn = $GLOBALS['db']->getConnection();
    
    $admin_id = (int) $admin_id;
    $query = "SELECT id, username, email, role, status, last_login, created_at, full_name FROM admins WHERE id = $admin_id";
    $result = $conn->query($query);
    
    if ($result->num_rows === 0) {
        return null;
    }
    
    return $result->fetch_assoc();
}

/**
 * Admin şifresini günceller
 * 
 * @param int $admin_id Admin ID
 * @param string $current_password Mevcut şifre
 * @param string $new_password Yeni şifre
 * @return bool|string Güncelleme başarılı ise true, değilse hata mesajı
 */
function updateAdminPassword($admin_id, $current_password, $new_password) {
    $conn = $GLOBALS['db']->getConnection();
    
    $admin_id = (int) $admin_id;
    
    // Mevcut şifreyi kontrol et
    $query = "SELECT password FROM admins WHERE id = $admin_id";
    $result = $conn->query($query);
    
    if ($result->num_rows === 0) {
        return "Admin bulunamadı.";
    }
    
    $admin = $result->fetch_assoc();
    
    if (!password_verify($current_password, $admin['password'])) {
        return "Mevcut şifre yanlış.";
    }
    
    // Yeni şifreyi hashleme
    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT, ['cost' => 12]);
    
    // Şifreyi güncelle
    $query = "UPDATE admins SET password = '$hashed_password' WHERE id = $admin_id";
    
    if ($conn->query($query)) {
        return true;
    } else {
        return "Şifre güncellenirken bir hata oluştu.";
    }
}
/**
 * Referans sistemi için gerekli fonksiyonlar
 */

/**
 * Referans sistemi ayarlarını getirir
 * 
 * @return array Referans sistemi ayarları
 */
function getReferralSettings() {
    $conn = $GLOBALS['db']->getConnection();
    $settings = [];

    // Site ayarlarından referans ile ilgili olanları alıyoruz
    $query = "SELECT setting_key, setting_value FROM site_settings 
              WHERE setting_key IN ('referral_active', 'referral_tier1_rate', 'referral_tier2_rate')";
    $result = $conn->query($query);
    
    while ($row = $result->fetch_assoc()) {
        if ($row['setting_key'] == 'referral_active') {
            $settings['referral_active'] = (int)$row['setting_value'];
        } elseif ($row['setting_key'] == 'referral_tier1_rate') {
            $settings['tier1_rate'] = (float)$row['setting_value'];
        } elseif ($row['setting_key'] == 'referral_tier2_rate') {
            $settings['tier2_rate'] = (float)$row['setting_value'];
        }
    }
    
    // Diğer ayarları da alıyoruz (varsa)
    $query = "SELECT setting_key, setting_value FROM payment_settings 
              WHERE setting_key IN ('min_deposit_amount')";
    $result = $conn->query($query);
    
    while ($row = $result->fetch_assoc()) {
        if ($row['setting_key'] == 'min_deposit_amount') {
            $settings['min_deposit_amount'] = (float)$row['setting_value'];
        }
    }
    
    // Referral ile ilgili özel ayarlar - Eğer tabloda yoksa varsayılanlar
    $settings = array_merge([
        'referral_active' => 1,
        'tier1_rate' => 0.05,
        'tier2_rate' => 0.02,
        'min_deposit_required' => 1,
        'min_deposit_amount' => 10,
        'bonus_for_referrer' => 0,
        'bonus_amount' => 1
    ], $settings);
    
    return $settings;
}

/**
 * Referans sistemi ayarlarını günceller
 * 
 * @param array $updates Güncellenecek ayarlar
 * @return bool Güncelleme başarılı ise true, değilse false döner
 */
function updateReferralSettings($updates) {
    $conn = $GLOBALS['db']->getConnection();
    
    try {
        // İşlemi başlat
        $conn->begin_transaction();
        
        // Site ayarlarını güncelle
        if (isset($updates['referral_active'])) {
            $active = (int)$updates['referral_active'];
            $conn->query("UPDATE site_settings SET setting_value = '$active' WHERE setting_key = 'referral_active'");
        }
        
        if (isset($updates['tier1_rate'])) {
            $rate = (float)$updates['tier1_rate'];
            $conn->query("UPDATE site_settings SET setting_value = '$rate' WHERE setting_key = 'referral_tier1_rate'");
        }
        
        if (isset($updates['tier2_rate'])) {
            $rate = (float)$updates['tier2_rate'];
            $conn->query("UPDATE site_settings SET setting_value = '$rate' WHERE setting_key = 'referral_tier2_rate'");
        }
        
        // Diğer ayarları güncelle (özel bir tablo varsa)
        // Örnek olarak, bu ayarları veritabanına kaydetmek için bir SQL sorgusu buraya eklenebilir
        
        // Admin log tablosuna kaydet
        if (isset($_SESSION['admin_id'])) {
            $admin_id = $_SESSION['admin_id'];
            $ip = $_SERVER['REMOTE_ADDR'];
            $stmt = $conn->prepare("INSERT INTO admin_logs (admin_id, action, description, ip_address) VALUES (?, 'update_referral_settings', 'Referans sistemi ayarları güncellendi', ?)");
            $stmt->bind_param("is", $admin_id, $ip);
            $stmt->execute();
        }
        
        // İşlemi tamamla
        $conn->commit();
        return true;
    } catch (Exception $e) {
        // Hata durumunda geri al
        $conn->rollback();
        error_log("Referans ayarları güncellenirken hata: " . $e->getMessage());
        return false;
    }
}

/**
 * Referans sistemi istatistiklerini getirir
 * 
 * @return array Referans sistemi istatistikleri
 */
function getReferralStats() {
    $conn = $GLOBALS['db']->getConnection();
    $stats = [];
    
    // Toplam referans sayısı
    $query = "SELECT COUNT(*) as total FROM users WHERE referrer_id IS NOT NULL";
    $result = $conn->query($query);
    $stats['total_referrals'] = $result->fetch_assoc()['total'];
    
    // Toplam ödenen komisyon
    $query = "SELECT COALESCE(SUM(amount), 0) as total FROM referral_earnings WHERE status = 'approved'";
    $result = $conn->query($query);
    $stats['total_commission'] = $result->fetch_assoc()['total'];
    
    // Bu ay ödenen komisyon
    $month_start = date('Y-m-01');
    $query = "SELECT COALESCE(SUM(amount), 0) as total FROM referral_earnings 
              WHERE status = 'approved' AND created_at >= '$month_start'";
    $result = $conn->query($query);
    $stats['monthly_commission'] = $result->fetch_assoc()['total'];
    
    // En çok komisyon kazanan kullanıcı
    $query = "SELECT u.id, u.username, COALESCE(SUM(re.amount), 0) as total_commission 
              FROM users u
              LEFT JOIN referral_earnings re ON u.id = re.user_id AND re.status = 'approved'
              GROUP BY u.id
              ORDER BY total_commission DESC
              LIMIT 1";
    $result = $conn->query($query);
    
    if ($result->num_rows > 0) {
        $stats['top_referrer'] = $result->fetch_assoc();
    } else {
        $stats['top_referrer'] = [
            'id' => 0,
            'username' => 'Yok',
            'total_commission' => 0
        ];
    }
    
    // En çok referans yapan kullanıcılar
    $query = "SELECT u.id, u.username, 
              (SELECT COUNT(*) FROM users WHERE referrer_id = u.id) as total_referrals,
              (SELECT COUNT(*) FROM users WHERE referrer_id = u.id AND status = 'active') as active_referrals,
              COALESCE(SUM(re.amount), 0) as total_commission
              FROM users u
              LEFT JOIN referral_earnings re ON u.id = re.user_id AND re.status = 'approved'
              GROUP BY u.id
              HAVING total_referrals > 0
              ORDER BY total_referrals DESC, total_commission DESC
              LIMIT 10";
    $result = $conn->query($query);
    
    $stats['top_referrers'] = [];
    while ($row = $result->fetch_assoc()) {
        $stats['top_referrers'][] = $row;
    }
    
    return $stats;
}

/**
 * Referans ağacı istatistiklerini getirir (Pasta grafik için)
 * 
 * @return array Referans ağacı istatistikleri
 */
function getReferralTreeStats() {
    $conn = $GLOBALS['db']->getConnection();
    $stats = [];
    
    // Direkt referanslar (1. seviye)
    $query = "SELECT COUNT(*) as total FROM users WHERE referrer_id IS NOT NULL";
    $result = $conn->query($query);
    $stats['direct_count'] = $result->fetch_assoc()['total'];
    
    // İkinci seviye referanslar
    // Bu sorgu örnek amaçlıdır ve gerçek veritabanı yapısına göre uyarlanmalıdır
    $query = "SELECT COUNT(u2.id) as total 
              FROM users u1
              JOIN users u2 ON u1.referrer_id IS NOT NULL AND u2.referrer_id = u1.id";
    $result = $conn->query($query);
    $stats['indirect_count'] = $result->fetch_assoc()['total'];
    
    return $stats;
}

/**
 * Referans kazançlarını getirir
 * 
 * @param int $limit Limit
 * @param int $offset Offset
 * @param array $filters Filtreler (user_id, status, date_from, date_to)
 * @return array Referans kazançları listesi
 */
function getReferralEarnings($limit = 10, $offset = 0, $filters = []) {
    $conn = $GLOBALS['db']->getConnection();
    
    $whereClause = [];
    $params = [];
    $paramTypes = "";
    
    // Filtreler
    if (!empty($filters['user_id'])) {
        $whereClause[] = "re.user_id = ?";
        $params[] = $filters['user_id'];
        $paramTypes .= "i";
    }
    
    if (!empty($filters['status'])) {
        $whereClause[] = "re.status = ?";
        $params[] = $filters['status'];
        $paramTypes .= "s";
    }
    
    if (!empty($filters['date_from'])) {
        $whereClause[] = "re.created_at >= ?";
        $params[] = $filters['date_from'] . " 00:00:00";
        $paramTypes .= "s";
    }
    
    if (!empty($filters['date_to'])) {
        $whereClause[] = "re.created_at <= ?";
        $params[] = $filters['date_to'] . " 23:59:59";
        $paramTypes .= "s";
    }
    
    $whereClause = !empty($whereClause) ? "WHERE " . implode(" AND ", $whereClause) : "";
    
    $query = "SELECT re.*, 
              u1.username as referrer_username,
              u2.username as referred_username
              FROM referral_earnings re
              JOIN users u1 ON re.user_id = u1.id
              JOIN users u2 ON re.referred_user_id = u2.id
              $whereClause
              ORDER BY re.created_at DESC
              LIMIT ?, ?";
    
    $stmt = $conn->prepare($query);
    
    // Parametreleri bağla
    if (!empty($params)) {
        $params[] = $offset;
        $params[] = $limit;
        $paramTypes .= "ii";
        
        $bind_names[] = $paramTypes;
        for ($i = 0; $i < count($params); $i++) {
            $bind_name = 'bind' . $i;
            $$bind_name = $params[$i];
            $bind_names[] = &$$bind_name;
        }
        
        call_user_func_array(array($stmt, 'bind_param'), $bind_names);
    } else {
        $stmt->bind_param("ii", $offset, $limit);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $earnings = [];
    while ($row = $result->fetch_assoc()) {
        $earnings[] = $row;
    }
    
    return $earnings;
}

/**
 * Kullanıcının referans listesini getirir
 * 
 * @param int $user_id Kullanıcı ID
 * @param int $limit Limit
 * @param int $offset Offset
 * @return array Referans listesi
 */
function getUserReferrals($user_id, $limit = 10, $offset = 0) {
    $conn = $GLOBALS['db']->getConnection();
    
    $query = "SELECT u.*, 
              (SELECT COUNT(*) FROM users WHERE referrer_id = u.id) as referral_count,
              (SELECT COALESCE(SUM(amount), 0) FROM deposits WHERE user_id = u.id AND status = 'confirmed') as total_deposits
              FROM users u
              WHERE u.referrer_id = ?
              ORDER BY u.created_at DESC
              LIMIT ?, ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iii", $user_id, $offset, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $referrals = [];
    while ($row = $result->fetch_assoc()) {
        $referrals[] = $row;
    }
    
    return $referrals;
}

/**
 * Kullanıcının referans kazançlarını getirir
 * 
 * @param int $user_id Kullanıcı ID
 * @param int $limit Limit
 * @param int $offset Offset
 * @return array Referans kazançları
 */
function getUserReferralEarnings($user_id, $limit = 10, $offset = 0) {
    return getReferralEarnings($limit, $offset, ['user_id' => $user_id]);
}



/**
 * Kullanıcının işlem sayısını getirir
 * 
 * @param int $user_id Kullanıcı ID
 * @return int İşlem sayısı
 */
function getUserTransactionsCount($user_id) {
    $conn = $GLOBALS['db']->getConnection();
    
    $user_id = (int) $user_id;
    $query = "SELECT COUNT(*) as total FROM transactions WHERE user_id = $user_id";
    $result = $conn->query($query);
    
    return $result->fetch_assoc()['total'];
}

/**
 * VIP kullanıcıları getirir
 * 
 * @param int $limit Limit
 * @param int $offset Offset
 * @param string $search Arama terimi
 * @param int $vip_level VIP seviyesi
 * @param string $sort Sıralama alanı
 * @param string $order Sıralama yönü
 * @return array VIP kullanıcı listesi
 */
function getVipUsers($limit = 20, $offset = 0, $search = '', $vip_level = 0, $sort = 'created_at', $order = 'desc') {
    $conn = $GLOBALS['db']->getConnection();
    
    $whereClause = ["vip_level > 0"];
    $allowedSorts = ['created_at', 'username', 'balance', 'vip_level', 'total_deposit'];
    $allowedOrders = ['asc', 'desc'];
    
    // Arama filtresi
    if (!empty($search)) {
        $search = $conn->real_escape_string($search);
        $whereClause[] = "(username LIKE '%$search%' OR email LIKE '%$search%' OR id = '$search')";
    }
    
    // VIP seviyesi filtresi
    if ($vip_level > 0) {
        $whereClause[] = "vip_level = $vip_level";
    }
    
    // WHERE cümlesini oluştur
    $whereStr = 'WHERE ' . implode(' AND ', $whereClause);
    
    // Sıralama alanı kontrolü
    if (!in_array($sort, $allowedSorts)) {
        $sort = 'created_at';
    }
    
    // Sıralama yönü kontrolü
    if (!in_array($order, $allowedOrders)) {
        $order = 'desc';
    }
    
    $query = "SELECT * FROM users $whereStr ORDER BY $sort $order LIMIT $offset, $limit";
    $result = $conn->query($query);
    
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    
    return $users;
}

/**
 * VIP kullanıcı sayısını getirir
 * 
 * @param string $search Arama terimi
 * @param int $vip_level VIP seviyesi
 * @return int VIP kullanıcı sayısı
 */
function getVipUsersCount($search = '', $vip_level = 0) {
    $conn = $GLOBALS['db']->getConnection();
    
    $whereClause = ["vip_level > 0"];
    
    // Arama filtresi
    if (!empty($search)) {
        $search = $conn->real_escape_string($search);
        $whereClause[] = "(username LIKE '%$search%' OR email LIKE '%$search%' OR id = '$search')";
    }
    
    // VIP seviyesi filtresi
    if ($vip_level > 0) {
        $whereClause[] = "vip_level = $vip_level";
    }
    
    // WHERE cümlesini oluştur
    $whereStr = 'WHERE ' . implode(' AND ', $whereClause);
    
    $query = "SELECT COUNT(*) as total FROM users $whereStr";
    $result = $conn->query($query);
    
    return $result->fetch_assoc()['total'];
}

/**
 * VIP istatistiklerini getirir
 * 
 * @return array VIP istatistikleri
 */
function getVipStats() {
    $conn = $GLOBALS['db']->getConnection();
    $stats = [];
    
    // Toplam VIP kullanıcı sayısı
    $query = "SELECT COUNT(*) as total FROM users WHERE vip_level > 0";
    $result = $conn->query($query);
    $stats['total_vip_users'] = $result->fetch_assoc()['total'];
    
    // Toplam VIP geliri
    $query = "SELECT COALESCE(SUM(amount), 0) as total FROM transactions WHERE type = 'vip'";
    $result = $conn->query($query);
    $stats['total_vip_revenue'] = $result->fetch_assoc()['total'];
    
    // Bu ay VIP satın alan kullanıcı sayısı
    $month_start = date('Y-m-01');
    $query = "SELECT COUNT(DISTINCT user_id) as total FROM transactions 
              WHERE type = 'vip' AND created_at >= '$month_start'";
    $result = $conn->query($query);
    $stats['monthly_vip_users'] = $result->fetch_assoc()['total'];
    
    // Bu ay VIP geliri
    $query = "SELECT COALESCE(SUM(amount), 0) as total FROM transactions 
              WHERE type = 'vip' AND created_at >= '$month_start'";
    $result = $conn->query($query);
    $stats['monthly_vip_revenue'] = $result->fetch_assoc()['total'];
    
    return $stats;
}

/**
 * VIP paket adını ID'ye göre getirir
 * 
 * @param int $vip_level VIP seviyesi
 * @return string VIP paket adı
 */
function getVipPackageName($vip_level) {
    $conn = $GLOBALS['db']->getConnection();
    
    $vip_level = (int) $vip_level;
    $query = "SELECT name FROM vip_packages WHERE id = $vip_level";
    $result = $conn->query($query);
    
    if ($result->num_rows === 0) {
        return "VIP $vip_level";
    }
    
    return $result->fetch_assoc()['name'];
}

/**
 * Kullanıcı bilgilerini günceller
 * 
 * @param int $user_id Kullanıcı ID
 * @param array $data Güncellenecek veriler
 * @return bool|string Güncelleme başarılı ise true, değilse hata mesajı
 */
function updateUser($user_id, $data) {
    $conn = $GLOBALS['db']->getConnection();
    
    $user_id = (int) $user_id;
    
    // Zorunlu alanları kontrol et
    if (empty($data['username']) || empty($data['email'])) {
        return "Kullanıcı adı ve e-posta alanları zorunludur.";
    }
    
    try {
        $conn->begin_transaction();
        
        // Kullanıcı adı ve e-postanın benzersiz olduğunu kontrol et
        $stmt = $conn->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
        $stmt->bind_param("ssi", $data['username'], $data['email'], $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            throw new Exception("Bu kullanıcı adı veya e-posta adresi zaten kullanılıyor.");
        }
        
        // Kullanıcı bilgilerini güncelle
        $fields = [];
        $params = [];
        $types = "";
        
        // Temel alanlar
        $updateFields = [
            'username' => 's',
            'email' => 's',
            'full_name' => 's',
            'trc20_address' => 's',
            'status' => 's',
            'vip_level' => 'i'
        ];
        
        foreach ($updateFields as $field => $type) {
            if (isset($data[$field])) {
                $fields[] = "$field = ?";
                $params[] = $data[$field];
                $types .= $type;
            }
        }
        
        // Şifre güncellemesi
        if (!empty($data['password'])) {
            $hashed_password = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
            $fields[] = "password = ?";
            $params[] = $hashed_password;
            $types .= "s";
        }
        
        // Güncelleme zamanı
        $fields[] = "updated_at = NOW()";
        
        // Parametreleri bağla
        $params[] = $user_id;
        $types .= "i";
        
        $fieldsStr = implode(", ", $fields);
        $query = "UPDATE users SET $fieldsStr WHERE id = ?";
        
        $stmt = $conn->prepare($query);
        
        // Dinamik parametre bağlama
        $bind_names[] = $types;
        for ($i = 0; $i < count($params); $i++) {
            $bind_name = 'bind' . $i;
            $$bind_name = $params[$i];
            $bind_names[] = &$$bind_name;
        }
        
        call_user_func_array(array($stmt, 'bind_param'), $bind_names);
        
        if (!$stmt->execute()) {
            throw new Exception("Kullanıcı bilgileri güncellenirken bir hata oluştu.");
        }
        
        // Admin log
        if (isset($_SESSION['admin_id'])) {
            $admin_id = $_SESSION['admin_id'];
            $ip = $_SERVER['REMOTE_ADDR'];
            $description = "Kullanıcı #$user_id bilgileri güncellendi.";
            
            $stmt = $conn->prepare("INSERT INTO admin_logs (admin_id, action, description, related_id, related_type, ip_address) VALUES (?, 'update_user', ?, ?, 'user', ?)");
            $stmt->bind_param("isis", $admin_id, $description, $user_id, $ip);
            $stmt->execute();
        }
        
        $conn->commit();
        return true;
        
    } catch (Exception $e) {
        $conn->rollback();
        return $e->getMessage();
    }
}


/**
 * Kullanıcının VIP seviyesini günceller
 * 
 * @param int $user_id Kullanıcı ID
 * @param int $vip_level Yeni VIP seviyesi
 * @param string $note İşlem notu
 * @param bool $charge_user Kullanıcıdan ücret alınsın mı?
 * @return bool|string Güncelleme başarılı ise true, değilse hata mesajı
 */
function updateUserVipLevel($user_id, $vip_level, $note = '', $charge_user = false) {
    $conn = $GLOBALS['db']->getConnection();
    
    $user_id = (int) $user_id;
    $vip_level = (int) $vip_level;
    
    try {
        $conn->begin_transaction();
        
        // Kullanıcı bilgilerini al
        $stmt = $conn->prepare("SELECT vip_level, balance FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception("Kullanıcı bulunamadı.");
        }
        
        $user = $result->fetch_assoc();
        $old_vip_level = $user['vip_level'];
        
        // Eğer zaten aynı VIP seviyesi ise işlem yapma
        if ($old_vip_level == $vip_level) {
            throw new Exception("Kullanıcı zaten bu VIP seviyesinde.");
        }
        
        // Yeni VIP paketinin bilgilerini al
        $stmt = $conn->prepare("SELECT * FROM vip_packages WHERE id = ?");
        $stmt->bind_param("i", $vip_level);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception("VIP paketi bulunamadı.");
        }
        
        $vip_package = $result->fetch_assoc();
        $package_price = $vip_package['price'];
        
        // Ücret kontrolü
        if ($charge_user && $package_price > 0) {
            if ($user['balance'] < $package_price) {
                throw new Exception("Kullanıcının yetersiz bakiyesi var.");
            }
            
            // Bakiyeden düş
            $new_balance = $user['balance'] - $package_price;
            $stmt = $conn->prepare("UPDATE users SET balance = ? WHERE id = ?");
            $stmt->bind_param("di", $new_balance, $user_id);
            
            if (!$stmt->execute()) {
                throw new Exception("Bakiye güncellenirken bir hata oluştu.");
            }
            
            // İşlem kaydı
            $description = "VIP seviyesi $old_vip_level -> $vip_level (" . $vip_package['name'] . "): $note";
            $stmt = $conn->prepare("INSERT INTO transactions (user_id, type, amount, before_balance, after_balance, status, description) VALUES (?, 'vip', ?, ?, ?, 'completed', ?)");
            $stmt->bind_param("iddds", $user_id, $package_price, $user['balance'], $new_balance, $description);
            
            if (!$stmt->execute()) {
                throw new Exception("İşlem kaydedilirken bir hata oluştu.");
            }
        }
        
        // VIP seviyesini güncelle
        $stmt = $conn->prepare("UPDATE users SET vip_level = ? WHERE id = ?");
        $stmt->bind_param("ii", $vip_level, $user_id);
        
        if (!$stmt->execute()) {
            throw new Exception("VIP seviyesi güncellenirken bir hata oluştu.");
        }
        
        // Admin log
        if (isset($_SESSION['admin_id'])) {
            $admin_id = $_SESSION['admin_id'];
            $ip = $_SERVER['REMOTE_ADDR'];
            $log_description = "Kullanıcı #$user_id VIP seviyesi $old_vip_level -> $vip_level olarak güncellendi. Not: $note";
            
            $stmt = $conn->prepare("INSERT INTO admin_logs (admin_id, action, description, related_id, related_type, ip_address) VALUES (?, 'update_vip_level', ?, ?, 'user', ?)");
            $stmt->bind_param("isis", $admin_id, $log_description, $user_id, $ip);
            $stmt->execute();
        }
        
        $conn->commit();
        return true;
        
    } catch (Exception $e) {
        $conn->rollback();
        return $e->getMessage();
    }
}

/**
 * Kullanıcı istatistiklerini getirir
 * 
 * @return array İstatistikler
 */
function getUsersStats() {
    $conn = $GLOBALS['db']->getConnection();
    $stats = [];
    
    // Toplam kullanıcı sayısı
    $query = "SELECT COUNT(*) as total FROM users";
    $result = $conn->query($query);
    $stats['total_users'] = $result->fetch_assoc()['total'];
    
    // Aktif kullanıcı sayısı
    $query = "SELECT COUNT(*) as total FROM users WHERE status = 'active'";
    $result = $conn->query($query);
    $stats['active_users'] = $result->fetch_assoc()['total'];
    
    // VIP kullanıcı sayısı
    $query = "SELECT COUNT(*) as total FROM users WHERE vip_level > 0";
    $result = $conn->query($query);
    $stats['vip_users'] = $result->fetch_assoc()['total'];
    
    // Bugün kaydolan kullanıcı sayısı
    $today = date('Y-m-d');
    $query = "SELECT COUNT(*) as total FROM users WHERE DATE(created_at) = '$today'";
    $result = $conn->query($query);
    $stats['new_users_today'] = $result->fetch_assoc()['total'];
    
    return $stats;
}

/**
 * Yeni kullanıcı oluşturur
 * 
 * @param array $data Kullanıcı verileri
 * @return int|string Başarılı ise kullanıcı ID, değilse hata mesajı
 */
function createUser($data) {
    $conn = $GLOBALS['db']->getConnection();
    
    // Zorunlu alanları kontrol et
    if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
        return "Kullanıcı adı, e-posta ve şifre alanları zorunludur.";
    }
    
    try {
        $conn->begin_transaction();
        
        // Kullanıcı adı ve e-postanın benzersiz olduğunu kontrol et
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $data['username'], $data['email']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            throw new Exception("Bu kullanıcı adı veya e-posta adresi zaten kullanılıyor.");
        }
        
        // Şifreyi hashleme
        $hashed_password = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
        
        // Referans kodu oluştur
        $referral_code = generateReferralCode(8);
        
        // Kullanıcı oluştur
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, full_name, trc20_address, status, vip_level, balance, referral_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssisd", 
            $data['username'], 
            $data['email'], 
            $hashed_password,
            $data['full_name'] ?? '',
            $data['trc20_address'] ?? '',
            $data['status'] ?? 'active',
            $data['vip_level'] ?? 0,
            $data['balance'] ?? 0,
            $referral_code
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Kullanıcı oluşturulurken bir hata oluştu.");
        }
        
        $user_id = $conn->insert_id;
        
        // Eğer başlangıç bakiyesi varsa işlem kayıt et
        if (isset($data['balance']) && $data['balance'] > 0) {
            $description = "Admin tarafından başlangıç bakiyesi";
            $stmt = $conn->prepare("INSERT INTO transactions (user_id, type, amount, before_balance, after_balance, status, description) VALUES (?, 'admin_adjustment', ?, 0, ?, 'completed', ?)");
            $stmt->bind_param("idds", $user_id, $data['balance'], $data['balance'], $description);
            $stmt->execute();
        }
        
        // Admin log
        if (isset($_SESSION['admin_id'])) {
            $admin_id = $_SESSION['admin_id'];
            $ip = $_SERVER['REMOTE_ADDR'];
            $description = "Yeni kullanıcı oluşturuldu: " . $data['username'];
            
            $stmt = $conn->prepare("INSERT INTO admin_logs (admin_id, action, description, related_id, related_type, ip_address) VALUES (?, 'create_user', ?, ?, 'user', ?)");
            $stmt->bind_param("isis", $admin_id, $description, $user_id, $ip);
            $stmt->execute();
        }
        
        $conn->commit();
        return $user_id;
        
    } catch (Exception $e) {
        $conn->rollback();
        return $e->getMessage();
    }
}

/**
 * Benzersiz referans kodu üretir
 * 
 * @param int $length Kod uzunluğu
 * @return string Referans kodu
 */
function generateReferralCode($length = 8) {
    $conn = $GLOBALS['db']->getConnection();
    
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $unique = false;
    $code = '';
    
    while (!$unique) {
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $chars[random_int(0, strlen($chars) - 1)];
        }
        
        // Kodun benzersiz olduğunu kontrol et
        $stmt = $conn->prepare("SELECT id FROM users WHERE referral_code = ?");
        $stmt->bind_param("s", $code);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $unique = true;
        }
    }
    
    return $code;
}

/**
 * Destek taleplerini getiren fonksiyon
 * 
 * @param string $status_filter Durum filtresi (all, open, in_progress, closed)
 * @param string $priority_filter Öncelik filtresi (all, low, medium, high)
 * @param string $search Arama terimi
 * @return array Destek talepleri listesi
 */
function getSupportTickets($status_filter = 'all', $priority_filter = 'all', $search = '') {
    $conn = $GLOBALS['db']->getConnection();
    
    $whereClause = [];
    $params = [];
    $paramTypes = "";
    
    // Durum filtresi
    if ($status_filter != 'all') {
        $whereClause[] = "t.status = ?";
        $params[] = $status_filter;
        $paramTypes .= "s";
    }
    
    // Öncelik filtresi
    if ($priority_filter != 'all') {
        $whereClause[] = "t.priority = ?";
        $params[] = $priority_filter;
        $paramTypes .= "s";
    }
    
    // Arama filtresi
    if (!empty($search)) {
        $search = "%$search%";
        $whereClause[] = "(t.subject LIKE ? OR u.username LIKE ?)";
        $params[] = $search;
        $params[] = $search;
        $paramTypes .= "ss";
    }
    
    $whereStr = !empty($whereClause) ? "WHERE " . implode(" AND ", $whereClause) : "";
    
    $query = "SELECT t.*, u.username 
              FROM support_tickets t
              JOIN users u ON t.user_id = u.id
              $whereStr
              ORDER BY 
                CASE 
                  WHEN t.status = 'open' THEN 1
                  WHEN t.status = 'in_progress' THEN 2
                  ELSE 3
                END,
                CASE
                  WHEN t.priority = 'high' THEN 1
                  WHEN t.priority = 'medium' THEN 2
                  ELSE 3
                END,
                t.last_updated DESC, t.created_at DESC";
    
    if (!empty($params)) {
        $stmt = $conn->prepare($query);
        
        // Parametreleri bağla
        $bind_names[] = $paramTypes;
        for ($i = 0; $i < count($params); $i++) {
            $bind_name = 'bind' . $i;
            $$bind_name = $params[$i];
            $bind_names[] = &$$bind_name;
        }
        
        call_user_func_array(array($stmt, 'bind_param'), $bind_names);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $conn->query($query);
    }
    
    $tickets = [];
    while ($row = $result->fetch_assoc()) {
        $tickets[] = $row;
    }
    
    return $tickets;
}

/**
 * Destek talebi detaylarını getirir
 * 
 * @param int $ticket_id Talep ID
 * @return array|null Talep detayları
 */
function getTicketDetails($ticket_id) {
    $conn = $GLOBALS['db']->getConnection();
    
    $ticket_id = (int) $ticket_id;
    $query = "SELECT t.*, u.username, u.email
              FROM support_tickets t
              JOIN users u ON t.user_id = u.id
              WHERE t.id = $ticket_id";
    
    $result = $conn->query($query);
    
    if ($result->num_rows === 0) {
        return null;
    }
    
    $ticket = $result->fetch_assoc();
    
    // Mesajları da getir
    $query = "SELECT sm.*, u.username
              FROM support_messages sm
              LEFT JOIN users u ON sm.user_id = u.id
              WHERE sm.ticket_id = $ticket_id
              ORDER BY sm.created_at ASC";
    
    $result = $conn->query($query);
    
    $ticket['messages'] = [];
    while ($row = $result->fetch_assoc()) {
        $ticket['messages'][] = $row;
    }
    
    return $ticket;
}

/**
 * Destek talebine cevap ekler
 * 
 * @param int $ticket_id Talep ID
 * @param string $message Mesaj
 * @param int $user_id Kullanıcı ID (admin ID)
 * @param boolean $is_user_message Kullanıcı mesajı mı?
 * @return bool|string İşlem başarılı ise true, değilse hata mesajı
 */
function addTicketReply($ticket_id, $message, $user_id, $is_user_message = false) {
    $conn = $GLOBALS['db']->getConnection();
    
    $ticket_id = (int) $ticket_id;
    $user_id = (int) $user_id;
    
    try {
        $conn->begin_transaction();
        
        // Mesajı ekle
        $stmt = $conn->prepare("INSERT INTO support_messages (ticket_id, user_id, message, is_user_message) VALUES (?, ?, ?, ?)");
        $is_user = $is_user_message ? 1 : 0;
        $stmt->bind_param("iisi", $ticket_id, $user_id, $message, $is_user);
        
        if (!$stmt->execute()) {
            throw new Exception("Mesaj eklenirken bir hata oluştu.");
        }
        
        // Talep durumunu güncelle (admin cevap veriyorsa "in_progress")
        if (!$is_user_message) {
            $stmt = $conn->prepare("UPDATE support_tickets SET status = 'in_progress', last_updated = NOW() WHERE id = ?");
            $stmt->bind_param("i", $ticket_id);
            
            if (!$stmt->execute()) {
                throw new Exception("Talep durumu güncellenirken bir hata oluştu.");
            }
        }
        
        // Admin log
        if (!$is_user_message && isset($_SESSION['admin_id'])) {
            $admin_id = $_SESSION['admin_id'];
            $ip = $_SERVER['REMOTE_ADDR'];
            $description = "Destek talebi #$ticket_id cevaplandı.";
            
            $stmt = $conn->prepare("INSERT INTO admin_logs (admin_id, action, description, related_id, related_type, ip_address) VALUES (?, 'reply_ticket', ?, ?, 'ticket', ?)");
            $stmt->bind_param("isis", $admin_id, $description, $ticket_id, $ip);
            $stmt->execute();
        }
        
        $conn->commit();
        return true;
        
    } catch (Exception $e) {
        $conn->rollback();
        return $e->getMessage();
    }
}

/**
 * Destek talebi durumunu günceller
 * 
 * @param int $ticket_id Talep ID
 * @param string $status Yeni durum (open, in_progress, closed)
 * @return bool|string İşlem başarılı ise true, değilse hata mesajı
 */
function updateTicketStatus($ticket_id, $status) {
    $conn = $GLOBALS['db']->getConnection();
    
    $ticket_id = (int) $ticket_id;
    $allowed_statuses = ['open', 'in_progress', 'closed'];
    
    if (!in_array($status, $allowed_statuses)) {
        return "Geçersiz durum değeri.";
    }
    
    try {
        $conn->begin_transaction();
        
        $stmt = $conn->prepare("UPDATE support_tickets SET status = ?, last_updated = NOW() WHERE id = ?");
        $stmt->bind_param("si", $status, $ticket_id);
        
        if (!$stmt->execute()) {
            throw new Exception("Talep durumu güncellenirken bir hata oluştu.");
        }
        
        // Admin log
        if (isset($_SESSION['admin_id'])) {
            $admin_id = $_SESSION['admin_id'];
            $ip = $_SERVER['REMOTE_ADDR'];
            $description = "Destek talebi #$ticket_id durumu '$status' olarak güncellendi.";
            
            $stmt = $conn->prepare("INSERT INTO admin_logs (admin_id, action, description, related_id, related_type, ip_address) VALUES (?, 'update_ticket_status', ?, ?, 'ticket', ?)");
            $stmt->bind_param("isis", $admin_id, $description, $ticket_id, $ip);
            $stmt->execute();
        }
        
        $conn->commit();
        return true;
        
    } catch (Exception $e) {
        $conn->rollback();
        return $e->getMessage();
    }
}

/**
 * Destek talebi siler
 * 
 * @param int $ticket_id Talep ID
 * @return bool|string İşlem başarılı ise true, değilse hata mesajı
 */
function deleteTicket($ticket_id) {
    $conn = $GLOBALS['db']->getConnection();
    
    $ticket_id = (int) $ticket_id;
    
    try {
        $conn->begin_transaction();
        
        // Önce mesajları sil
        $stmt = $conn->prepare("DELETE FROM support_messages WHERE ticket_id = ?");
        $stmt->bind_param("i", $ticket_id);
        
        if (!$stmt->execute()) {
            throw new Exception("Mesajlar silinirken bir hata oluştu.");
        }
        
        // Talebi sil
        $stmt = $conn->prepare("DELETE FROM support_tickets WHERE id = ?");
        $stmt->bind_param("i", $ticket_id);
        
        if (!$stmt->execute()) {
            throw new Exception("Talep silinirken bir hata oluştu.");
        }
        
        // Admin log
        if (isset($_SESSION['admin_id'])) {
            $admin_id = $_SESSION['admin_id'];
            $ip = $_SERVER['REMOTE_ADDR'];
            $description = "Destek talebi #$ticket_id silindi.";
            
            $stmt = $conn->prepare("INSERT INTO admin_logs (admin_id, action, description, related_id, related_type, ip_address) VALUES (?, 'delete_ticket', ?, ?, 'ticket', ?)");
            $stmt->bind_param("isis", $admin_id, $description, $ticket_id, $ip);
            $stmt->execute();
        }
        
        $conn->commit();
        return true;
        
    } catch (Exception $e) {
        $conn->rollback();
        return $e->getMessage();
    }
}

/**
 * Destek talebi istatistiklerini getirir
 * 
 * @return array İstatistikler
 */
function getSupportStats() {
    $conn = $GLOBALS['db']->getConnection();
    $stats = [];
    
    // Talep durum dağılımı
    $query = "SELECT 
              COUNT(CASE WHEN status = 'open' THEN 1 END) as open,
              COUNT(CASE WHEN status = 'in_progress' THEN 1 END) as in_progress,
              COUNT(CASE WHEN status = 'closed' THEN 1 END) as closed
              FROM support_tickets";
    
    $result = $conn->query($query);
    $stats['distribution'] = $result->fetch_assoc();
    
    // Son 7 gün için yanıt süreleri
    $stats['response_times'] = [];
    $stats['dates'] = [];
    
    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $stats['dates'][] = date('d.m', strtotime($date));
        
        $query = "SELECT AVG(TIMESTAMPDIFF(HOUR, t.created_at, MIN(sm.created_at))) as avg_response_time
                  FROM support_tickets t
                  JOIN support_messages sm ON t.id = sm.ticket_id AND sm.is_user_message = 0
                  WHERE DATE(t.created_at) = '$date'
                  GROUP BY t.id";
        
        $result = $conn->query($query);
        
        if ($result->num_rows > 0) {
            $sum = 0;
            $count = 0;
            while ($row = $result->fetch_assoc()) {
                if ($row['avg_response_time'] !== null) {
                    $sum += $row['avg_response_time'];
                    $count++;
                }
            }
            $stats['response_times'][] = $count > 0 ? round($sum / $count, 1) : 0;
        } else {
            $stats['response_times'][] = 0;
        }
    }
    
    return $stats;
}

/**
 * Toplu destek talebi işlemlerini gerçekleştirir
 * 
 * @param array $ticket_ids Talep ID'leri
 * @param string $action İşlem (close, delete)
 * @return bool|string İşlem başarılı ise true, değilse hata mesajı
 */
function bulkTicketAction($ticket_ids, $action) {
    $conn = $GLOBALS['db']->getConnection();
    
    if (empty($ticket_ids)) {
        return "Hiç talep seçilmedi.";
    }
    
    try {
        $conn->begin_transaction();
        
        foreach ($ticket_ids as $ticket_id) {
            $ticket_id = (int) $ticket_id;
            
            if ($action === 'close') {
                $result = updateTicketStatus($ticket_id, 'closed');
                if ($result !== true) {
                    throw new Exception($result);
                }
            } elseif ($action === 'delete') {
                $result = deleteTicket($ticket_id);
                if ($result !== true) {
                    throw new Exception($result);
                }
            } else {
                throw new Exception("Geçersiz işlem.");
            }
        }
        
        $conn->commit();
        return true;
        
    } catch (Exception $e) {
        $conn->rollback();
        return $e->getMessage();
    }
}

/**
 * Oyun istatistiklerini getir
 * 
 * @return array İstatistik bilgileri
 */
function getGameStats() {
    global $db;
    $conn = $db->getConnection();
    $stats = [
        'today_games' => 0,
        'today_rewards' => 0,
        'total_games' => 0,
        'total_rewards' => 0
    ];
    
    // Bugünün tarihi
    $today = date('Y-m-d');
    
    // Bugünkü oyun sayısı
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM game_attempts WHERE DATE(created_at) = ?");
    if ($stmt) {
        $stmt->bind_param('s', $today);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $stats['today_games'] = $row['count'];
        }
    }
    
    // Bugün dağıtılan ödül miktarı
    $stmt = $conn->prepare("SELECT SUM(win_amount) as total FROM game_attempts WHERE DATE(created_at) = ?");
    if ($stmt) {
        $stmt->bind_param('s', $today);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $stats['today_rewards'] = $row['total'] ?: 0;
        }
    }
    
    // Toplam oyun sayısı
    $result = $conn->query("SELECT COUNT(*) as count FROM game_attempts");
    if ($result && $row = $result->fetch_assoc()) {
        $stats['total_games'] = $row['count'];
    }
    
    // Toplam dağıtılan ödül miktarı
    $result = $conn->query("SELECT SUM(win_amount) as total FROM game_attempts");
    if ($result && $row = $result->fetch_assoc()) {
        $stats['total_rewards'] = $row['total'] ?: 0;
    }
    
    return $stats;
}
/**
 * Oyun ayarlarını güncelle
 * 
 * @param array $updates Güncellenecek ayarlar
 * @return bool İşlem başarılı mı?
 */
function updateGameSettings($updates) {
    global $db;
    $conn = $db->getConnection();
    
    try {
        // İşlem başlat
        $conn->begin_transaction();
        
        foreach ($updates as $key => $value) {
            $stmt = $conn->prepare("UPDATE game_settings SET setting_value = ? WHERE setting_key = ?");
            
            if (!$stmt) {
                throw new Exception("SQL hatası: " . $conn->error);
            }
            
            $stmt->bind_param('ss', $value, $key);
            $stmt->execute();
            
            if ($stmt->error) {
                throw new Exception("SQL hatası: " . $stmt->error);
            }
            
            $stmt->close();
        }
        
        // İşlemi tamamla
        $conn->commit();
        
        // Admin log kaydı oluştur
        if (function_exists('addAdminLog') && isset($_SESSION['admin_id'])) {
            addAdminLog($_SESSION['admin_id'], 'update_game_settings', 'Oyun ayarları güncellendi.');
        }
        
        return true;
        
    } catch (Exception $e) {
        // Hata durumunda işlemi geri al
        $conn->rollback();
        
        // Hata logla
        error_log("Oyun ayarları güncellenirken hata: " . $e->getMessage());
        
        return false;
    }
}

/**
 * Belirli bir VIP paketini ID'ye göre getir
 * 
 * @param int $package_id Paket ID
 * @return array|false Paket bilgileri veya bulunamazsa false
 */
function getVipPackageById($package_id) {
    global $db;
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("SELECT * FROM vip_packages WHERE id = ?");
    
    if (!$stmt) {
        error_log("getVipPackageById prepare hatası: " . $conn->error);
        return false;
    }
    
    $stmt->bind_param("i", $package_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $package = $result->fetch_assoc();
        $stmt->close();
        return $package;
    }
    
    $stmt->close();
    return false;
}


/**
 * VIP paketini güncelle
 * 
 * @param int $package_id Paket ID
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
 * @return bool Güncelleme başarılı mı?
 */
function updateVipPackage($package_id, $name, $price, $duration_days, $daily_game_limit, $game_max_win_chance, $referral_rate, $mining_bonus_rate, $features, $description, $is_active) {
    global $db;
    $conn = $db->getConnection();
    
    try {
        $stmt = $conn->prepare("
            UPDATE vip_packages SET
                name = ?,
                price = ?,
                duration_days = ?,
                daily_game_limit = ?,
                game_max_win_chance = ?,
                referral_rate = ?,
                mining_bonus_rate = ?,
                features = ?,
                description = ?,
                is_active = ?,
                updated_at = NOW()
            WHERE id = ?
        ");
        
        if (!$stmt) {
            error_log("updateVipPackage prepare hatası: " . $conn->error);
            return false;
        }
        
        $stmt->bind_param(
            "sdiiiddssii", 
            $name, 
            $price, 
            $duration_days, 
            $daily_game_limit, 
            $game_max_win_chance, 
            $referral_rate, 
            $mining_bonus_rate, 
            $features, 
            $description, 
            $is_active,
            $package_id
        );
        
        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    } catch (Exception $e) {
        error_log("updateVipPackage hatası: " . $e->getMessage());
        return false;
    }
}

/**
 * VIP paketi satın alma işlemini gerçekleştirir
 * 
 * @param int $user_id Kullanıcı ID
 * @param int $package_id Paket ID
 * @return array İşlem sonucu ['success' => bool, 'message' => string]
 */
function purchaseVipPackage($user_id, $package_id) {
    global $db;
    $conn = $db->getConnection();
    
    // İşlem sonucu
    $result = [
        'success' => false,
        'message' => ''
    ];
    
    // Kullanıcı ve paket bilgilerini al
    $user = getUserById($user_id);
    $package = getVipPackageById($package_id);
    
    if (!$user) {
        $result['message'] = 'Kullanıcı bulunamadı.';
        return $result;
    }
    
    if (!$package) {
        $result['message'] = 'VIP paketi bulunamadı.';
        return $result;
    }
    
    if (!$package['is_active']) {
        $result['message'] = 'Bu VIP paketi şu anda satın alınamaz.';
        return $result;
    }
    
    // Kullanıcının bakiyesini kontrol et
    if ($user['balance'] < $package['price']) {
        $result['message'] = 'Yetersiz bakiye.';
        return $result;
    }
    
    // Transaction başlat
    $conn->begin_transaction();
    
    try {
        // Bitiş tarihini hesapla
        $start_date = date('Y-m-d H:i:s');
        $end_date = date('Y-m-d H:i:s', strtotime("+{$package['duration_days']} days"));
        
        // Kullanıcının mevcut VIP paketini kontrol et
        $active_vip = getUserActiveVip($user_id);
        
        if ($active_vip) {
            // Mevcut VIP aboneliğini pasif yap
            $stmt = $conn->prepare("UPDATE user_vip_packages SET is_active = 0, updated_at = NOW() WHERE id = ?");
            $stmt->bind_param("i", $active_vip['id']);
            $stmt->execute();
            $stmt->close();
        }
        
        // Yeni VIP paketi satın alma kaydını oluştur
        $stmt = $conn->prepare("
            INSERT INTO user_vip_packages (
                user_id, package_id, price, start_date, end_date, is_active, created_at, updated_at
            ) VALUES (
                ?, ?, ?, ?, ?, 1, NOW(), NOW()
            )
        ");
        
        $stmt->bind_param("iidss", $user_id, $package_id, $package['price'], $start_date, $end_date);
        $stmt->execute();
        $user_vip_id = $conn->insert_id;
        $stmt->close();
        
        // Kullanıcı bakiyesini güncelle
        $current_balance = $user['balance'];
        $new_balance = $current_balance - $package['price'];
        
        $stmt = $conn->prepare("UPDATE users SET balance = ?, vip_level = ?, vip_expires = ? WHERE id = ?");
        $stmt->bind_param("disi", $new_balance, $package_id, $end_date, $user_id);
        $stmt->execute();
        $stmt->close();
        
        // İşlem kaydı oluştur
        $transaction_type = 'vip';
        $transaction_desc = "VIP paketi satın alındı: " . $package['name'];
        
        $stmt = $conn->prepare("
            INSERT INTO transactions (
                user_id, type, amount, before_balance, after_balance, 
                status, related_id, description, created_at
            ) VALUES (
                ?, ?, ?, ?, ?, 'completed', ?, ?, NOW()
            )
        ");
        
        $amount = -$package['price']; // Negatif tutar (bakiyeden düşülecek)
        
        $stmt->bind_param("idddiss", 
            $user_id, 
            $transaction_type, 
            $amount, 
            $current_balance, 
            $new_balance, 
            $user_vip_id, 
            $transaction_desc
        );
        
        $stmt->execute();
        $stmt->close();
        
        // İşlemi tamamla
        $conn->commit();
        
        // Başarılı sonuç
        $result['success'] = true;
        $result['message'] = 'VIP paketi başarıyla satın alındı.';
        $result['user_vip_id'] = $user_vip_id;
        
    } catch (Exception $e) {
        // Hata durumunda rollback
        $conn->rollback();
        
        $result['message'] = 'İşlem sırasında bir hata oluştu: ' . $e->getMessage();
        error_log("purchaseVipPackage hatası: " . $e->getMessage());
    }
    
    return $result;
}

/**
 * Kullanıcının aktif VIP paketini getir
 * 
 * @param int $user_id Kullanıcı ID
 * @return array|false Aktif VIP paketi veya yoksa false
 */
function getUserActiveVip($user_id) {
    global $db;
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("
        SELECT uvp.*, vp.name, vp.daily_game_limit, vp.game_max_win_chance, vp.referral_rate, vp.mining_bonus_rate
        FROM user_vip_packages uvp
        JOIN vip_packages vp ON uvp.package_id = vp.id
        WHERE uvp.user_id = ? AND uvp.is_active = 1 AND uvp.end_date > NOW()
        ORDER BY uvp.id DESC
        LIMIT 1
    ");
    
    if (!$stmt) {
        error_log("getUserActiveVip prepare hatası: " . $conn->error);
        return false;
    }
    
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $vip = $result->fetch_assoc();
        $stmt->close();
        return $vip;
    }
    
    $stmt->close();
    return false;
}

/**
 * Kullanıcının VIP geçmişini getir
 * 
 * @param int $user_id Kullanıcı ID
 * @param int $limit Limit (0 = tümü)
 * @return array VIP geçmişi
 */
function getUserVipHistory($user_id, $limit = 0) {
    global $db;
    $conn = $db->getConnection();
    
    $query = "
        SELECT uvp.*, vp.name
        FROM user_vip_packages uvp
        JOIN vip_packages vp ON uvp.package_id = vp.id
        WHERE uvp.user_id = ?
        ORDER BY uvp.created_at DESC
    ";
    
    if ($limit > 0) {
        $query .= " LIMIT ?";
    }
    
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        error_log("getUserVipHistory prepare hatası: " . $conn->error);
        return [];
    }
    
    if ($limit > 0) {
        $stmt->bind_param("ii", $user_id, $limit);
    } else {
        $stmt->bind_param("i", $user_id);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $history = [];
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $history[] = $row;
        }
    }
    
    $stmt->close();
    return $history;
}