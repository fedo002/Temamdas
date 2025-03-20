<?php
/**
 * Site fonksiyonları
 */

// Site ayarlarını getir
function getSiteSettings() {
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

// VIP paketlerini getir
function getVIPPackages() {
    global $db;
    $conn = $db->getConnection();
    $packages = [];
    
    $result = $conn->query("SELECT * FROM vip_packages WHERE is_active = 1 ORDER BY price ASC");
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $packages[] = $row;
        }
    }
    
    return $packages;
}

// VIP paket detaylarını getir
function getVipDetails($vip_level) {
    global $db;
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("SELECT * FROM vip_packages WHERE id = ?");
    $stmt->bind_param('i', $vip_level);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    // Varsayılan değerleri döndür
    return [
        'id' => 0,
        'name' => 'Standart',
        'price' => 0,
        'daily_game_limit' => 5,
        'game_max_win_chance' => 0.15,
        'referral_rate' => 0.05,
        'mining_bonus_rate' => 0.00,
        'description' => 'Ücretsiz paket'
    ];
}

// Mining paketlerini getir
function getMiningPackages() {
    global $db;
    $conn = $db->getConnection();
    $packages = [];
    
    $result = $conn->query("SELECT * FROM mining_packages WHERE is_active = 1 ORDER BY package_price ASC");
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $packages[] = $row;
        }
    }
    
    return $packages;
}

function getUserDetails($user_id) {
    $conn = dbConnect(); // veya global $db; $conn = $db->getConnection();
    
    // Direkt sorgu
    $result = $conn->query("SELECT * FROM users WHERE id = {$user_id}");
    
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return null;
}

// Kullanıcı adının uygunluğunu kontrol et
function isUsernameAvailable($username) {
    global $db;
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return $row['count'] == 0;
}

// Email adresinin uygunluğunu kontrol et
function isEmailAvailable($email) {
    global $db;
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return $row['count'] == 0;
}

// Referans kodunun geçerliliğini kontrol et
function isValidReferralCode($code) {
    global $db;
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("SELECT id FROM users WHERE referral_code = ?");
    $stmt->bind_param('s', $code);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->num_rows > 0;
}

// Benzersiz referans kodu oluştur
function generateReferralCode($length = 8) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    
    return $randomString;
}

// Kullanıcı kaydı oluştur
function registerUser($username, $email, $password, $referral_code = null) {
    global $db;
    $conn = $db->getConnection();
    
    // Referans kontrolü
    $referrer_id = null;
    if (!empty($referral_code)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE referral_code = ?");
        $stmt->bind_param('s', $referral_code);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $referrer_id = $row['id'];
        }
    }
    
    // Benzersiz referans kodu oluştur
    do {
        $new_referral_code = generateReferralCode();
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE referral_code = ?");
        $stmt->bind_param('s', $new_referral_code);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
    } while ($row['count'] > 0);
    
    // Şifreyi hashle
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    // Kullanıcıyı kaydet
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, referral_code, referrer_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('ssssi', $username, $email, $password_hash, $new_referral_code, $referrer_id);
    
    if ($stmt->execute()) {
        $user_id = $conn->insert_id;
        
        return [
            'success' => true,
            'user_id' => $user_id
        ];
    } else {
        return [
            'success' => false,
            'message' => 'Kayıt sırasında bir hata oluştu: ' . $conn->error
        ];
    }
}

// Kullanıcı girişi
function loginUser($username_or_email, $password, $remember_me = false) {
    global $db;
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param('ss', $username_or_email, $username_or_email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            // Giriş başarılı
            $stmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $stmt->bind_param('i', $user['id']);
            $stmt->execute();
            
            if ($remember_me) {
                // Hatırlanacak oturum oluştur
                $token = generateRandomToken();
                $expires = date('Y-m-d H:i:s', time() + 2592000); // 30 gün
                
                $stmt = $conn->prepare("INSERT INTO remember_tokens (user_id, token, expires) VALUES (?, ?, ?)");
                $stmt->bind_param('iss', $user['id'], $token, $expires);
                $stmt->execute();
                
                // Çerez ayarla
                setcookie('remember_token', $token, time() + 2592000, '/');
            }
            
            return [
                'success' => true,
                'user_id' => $user['id'],
                'username' => $user['username']
            ];
        }
    }
    
    return [
        'success' => false,
        'message' => 'Geçersiz kullanıcı adı/e-posta veya şifre.'
    ];
}

// Rastgele token oluştur
function generateRandomToken($length = 32) {
    return bin2hex(random_bytes($length));
}

// Ödeme ayarlarını getir
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

// Kullanıcının yatırımlarını getir
function getUserDeposits($user_id, $limit = null) {
    global $db;
    $conn = $db->getConnection();
    $deposits = [];
    
    $query = "SELECT * FROM deposits WHERE user_id = ? ORDER BY created_at DESC";
    
    if ($limit) {
        $query .= " LIMIT ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ii', $user_id, $limit);
    } else {
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $user_id);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $deposits[] = $row;
        }
    }
    
    return $deposits;
}

// Kullanıcının çekimlerini getir
function getUserWithdrawals($user_id, $limit = null) {
    global $db;
    $conn = $db->getConnection();
    $withdrawals = [];
    
    $query = "SELECT * FROM withdrawals WHERE user_id = ? ORDER BY created_at DESC";
    
    if ($limit) {
        $query .= " LIMIT ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ii', $user_id, $limit);
    } else {
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $user_id);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $withdrawals[] = $row;
        }
    }
    
    return $withdrawals;
}

// Kullanıcının işlemlerini getir
function getUserTransactions($user_id, $limit = null) {
    global $db;
    $conn = $db->getConnection();
    $transactions = [];
    
    $query = "SELECT * FROM transactions WHERE user_id = ? ORDER BY created_at DESC";
    
    if ($limit) {
        $query .= " LIMIT ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ii', $user_id, $limit);
    } else {
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $user_id);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $transactions[] = $row;
        }
    }
    
    return $transactions;
}

// Kullanıcının mining paketlerini getir
function getUserMiningPackages($user_id) {
    global $db;
    $conn = $db->getConnection();
    $packages = [];
    
    $query = "SELECT ump.*, mp.name, mp.hash_rate, mp.electricity_cost, mp.daily_revenue_rate, mp.package_price 
              FROM user_mining_packages ump 
              JOIN mining_packages mp ON ump.package_id = mp.id 
              WHERE ump.user_id = ? AND ump.status = 'active'";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $packages[] = $row;
        }
    }
    
    return $packages;
}

// Kullanıcının günlük mining kazançlarını getir
function getUserDailyMiningEarnings($user_id) {
    global $db;
    $conn = $db->getConnection();
    
    $today = date('Y-m-d');
    $earnings = [
        'today' => 0,
        'total' => 0,
        'earnings_data' => []
    ];
    
    // Bugünkü kazanç
    $stmt = $conn->prepare("SELECT SUM(net_revenue) as today_earnings FROM mining_earnings WHERE user_id = ? AND date = ?");
    $stmt->bind_param('is', $user_id, $today);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $earnings['today'] = $row['today_earnings'] ?: 0;
    }
    
    // Toplam kazanç
    $stmt = $conn->prepare("SELECT SUM(net_revenue) as total_earnings FROM mining_earnings WHERE user_id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $earnings['total'] = $row['total_earnings'] ?: 0;
    }
    
    // Son 7 günlük veriler
    $stmt = $conn->prepare("SELECT date, SUM(net_revenue) as daily_earnings 
                           FROM mining_earnings 
                           WHERE user_id = ? 
                           GROUP BY date 
                           ORDER BY date DESC 
                           LIMIT 7");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $earnings['earnings_data'][] = [
                'date' => $row['date'],
                'earnings' => $row['daily_earnings']
            ];
        }
    }
    
    return $earnings;
}
function dailEearn($user_id) {
    global $db;
    $conn = $db->getConnection();
    
    $today = date('Y-m-d');
    $yesterday = date('Y-m-d', strtotime('-1 day'));
    
    $earn = [
        'today' => 0,
        'yesterday' => 0,
    ];
    
    $types = ['referral', 'game', 'miningdeposit'];
    $types_placeholder = implode(',', array_fill(0, count($types), '?'));
    
    // Bugünkü kazançlar
    $stmt = $conn->prepare("SELECT SUM(amount) as today_earn FROM transactions WHERE user_id = ? AND type IN ($types_placeholder) AND DATE(created_at) = ?");
    $params = array_merge([$user_id], $types, [$today]);
    $stmt->bind_param(str_repeat('s', count($params)), ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $earn['today'] = $row['today_earn'] ?: 0;
    }
    
    // Dün kazançlar
    $stmt = $conn->prepare("SELECT SUM(amount) as yesterday_earn FROM transactions WHERE user_id = ? AND type IN ($types_placeholder) AND DATE(created_at) = ?");
    $params = array_merge([$user_id], $types, [$yesterday]);
    $stmt->bind_param(str_repeat('s', count($params)), ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $earn['yesterday'] = $row['yesterday_earn'] ?: 0;
    }
    
    return $earn;
}

function dailyreff($user_id) {
    global $db;
    $conn = $db->getConnection();
    
    $today = date('Y-m-d');
    $yesterday = date('Y-m-d', strtotime('-1 day'));
    
    $reff = [
        'today' => 0,
        'yesterday' => 0,
    ];
    
    $type = 'referral';
    
    // Bugünkü referral kazançları
    $stmt = $conn->prepare("SELECT SUM(amount) as today_reff FROM transactions WHERE user_id = ? AND type = ? AND DATE(created_at) = ?");
    $stmt->bind_param('iss', $user_id, $type, $today);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $reff['today'] = $row['today_reff'] ?: 0;
    }
    
    // Dün referral kazançları
    $stmt = $conn->prepare("SELECT SUM(amount) as yesterday_reff FROM transactions WHERE user_id = ? AND type = ? AND DATE(created_at) = ?");
    $stmt->bind_param('iss', $user_id, $type, $yesterday);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $reff['yesterday'] = $row['yesterday_reff'] ?: 0;
    }
    
    return $reff;
}


function saveDeposit($user_id, $amount, $status, $payment_id, $order_id) {
    global $db;
    $conn = $db->getConnection();
    
    if ($amount <= 0) {
        return false;
    }
    
    $stmt = $conn->prepare("INSERT INTO deposits (user_id, amount, status, payment_id, order_id, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param('idssi', $user_id, $amount, $status, $payment_id, $order_id);
    
    if ($stmt->execute()) {
        return $conn->insert_id; // Yatırımın ID'sini döndür
    } else {
        return false;
    }
}
function getUserDailyAttempts($user_id) {
    global $db;
    $conn = $db->getConnection();
    
    // Kullanıcı detaylarını al
    $user = getUserDetails($user_id);
    
    // Kullanıcı bulunamazsa
    if (!$user) {
        return [
            'error' => true,
            'message' => 'Kullanıcı bulunamadı.',
            'total_limit' => 0,
            'used_attempts' => 0,
            'remaining_attempts' => 0,
            'vip_level' => 0
        ];
    }
    
    // VIP detaylarını al
    $vip_details = getVipDetails($user['vip_level'] ?? 0);
    $daily_game_limit = $vip_details['daily_game_limit'] ?? 1; // Varsayılan 5 oyun
    
    // Bugünün tarihini al
    $today = date('Y-m-d');
    
    // Bugünkü oyun sayısını say
    $stmt = $conn->prepare("
        SELECT 
            COUNT(*) as game_count 
        FROM game_attempts 
        WHERE 
            user_id = ? AND 
            DATE(created_at) = ? AND 
            stage IN (1, 3)  # Sadece oynanan aşamaları say
    ");
    
    // Prepared statement hatası varsa
    if ($stmt === false) {
        return [
            'error' => true,
            'message' => 'Veritabanı sorgusu hazırlanamadı: ' . $conn->error,
            'total_limit' => $daily_game_limit,
            'used_attempts' => 0,
            'remaining_attempts' => $daily_game_limit,
            'vip_level' => $user['vip_level'] ?? 0
        ];
    }
    
    // bind_param ve execute işlemleri
    $stmt->bind_param('is', $user_id, $today);
    $stmt->execute();
    
    // Sonuç kontrolü
    $result = $stmt->get_result();
    
    if ($result === false) {
        return [
            'error' => true,
            'message' => 'Sorgu sonucu alınamadı: ' . $stmt->error,
            'total_limit' => $daily_game_limit,
            'used_attempts' => 0,
            'remaining_attempts' => $daily_game_limit,
            'vip_level' => $user['vip_level'] ?? 0
        ];
    }
    
    $row = $result->fetch_assoc();
    $used_attempts = $row['game_count'] ?? 0;
    
    // Kalan oyun hakları
    $remaining_attempts = max(0, $daily_game_limit - $used_attempts);
    
    return [
        'error' => false,
        'total_limit' => $daily_game_limit,
        'used_attempts' => $used_attempts,
        'remaining_attempts' => $remaining_attempts,
        'vip_level' => $user['vip_level'] ?? 0
    ];
}

// Oyun ayarlarını getir
function getGameSettings() {
    $conn = dbConnect();
    $settings = [];
    
    $result = $conn->query("SELECT * FROM game_settings");
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    }
    
    return $settings;
}
/**
 * Kullanıcı profilini güncelle (genişletilmiş versiyon)
 * 
 * @param int $user_id Kullanıcı ID
 * @param string $full_name Tam ad
 * @param string $phone Telefon numarası
 * @param string $phone_wp WhatsApp numarası
 * @param string $trc20_address TRC20 cüzdan adresi
 * @param string $telegram Telegram kullanıcı adı
 * @param int $profile_photo Profil fotoğrafı ID (0-24)
 * @return array İşlem sonucu
 */
function updateUserProfile($user_id, $full_name, $phone = null, $phone_wp = null, $trc20_address = null, $telegram = null, $profile_photo = 0) {
    global $db;
    $conn = $db->getConnection();
    
    // Geçerli kullanıcı verilerini al
    $current_user = getUserDetails($user_id);
    
    // Güncelleme sorgusu
    $query = "UPDATE users SET full_name = ?";
    $params = [$full_name];
    $types = "s";
    
    // Telefon numarası
    if ($phone !== null) {
        $query .= ", phone = ?";
        $params[] = $phone;
        $types .= "s";
    }
    
    // WhatsApp numarası
    if ($phone_wp !== null) {
        $query .= ", phone_wp = ?";
        $params[] = $phone_wp;
    $types .= "s";
}

// TRC20 adresi (sadece mevcut adres boşsa)
if ($trc20_address !== null && empty($current_user['trc20_address'])) {
    $query .= ", trc20_address = ?";
    $params[] = $trc20_address;
    $types .= "s";
}

// Telegram kullanıcı adı
if ($telegram !== null) {
    $query .= ", telegram = ?";
    $params[] = $telegram;
    $types .= "s";
}

// Profil fotoğrafı
if ($profile_photo !== null) {
    $query .= ", profile_photo = ?";
    $params[] = $profile_photo;
    $types .= "i";
}

$query .= " WHERE id = ?";
$params[] = $user_id;
$types .= "i";

// Debug log
error_log("Updating user profile. Query: " . $query);
error_log("Params: " . print_r($params, true));

$stmt = $conn->prepare($query);

if ($stmt === false) {
    error_log("Prepare error in updateUserProfile: " . $conn->error);
    return [
        'success' => false,
        'message' => 'Database query preparation failed: ' . $conn->error
    ];
}

$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    error_log("User profile updated successfully for ID: " . $user_id);
    return [
        'success' => true,
        'message' => 'Profile successfully updated.'
    ];
} else {
    error_log("Execute error in updateUserProfile: " . $stmt->error);
    return [
        'success' => false,
        'message' => 'Error updating profile: ' . $stmt->error
    ];
}
}

/**
 * Kullanıcı şifresini değiştir
 * 
 * @param int $user_id Kullanıcı ID
 * @param string $new_password Yeni şifre
 * @return array İşlem sonucu
 */
function changeUserPassword($user_id, $new_password) {
    global $db;
    $conn = $db->getConnection();
    
    // Şifreyi hashle
    $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
    
    // Debug log
    error_log("Changing password for user ID: " . $user_id);
    
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    
    if ($stmt === false) {
        error_log("Prepare error in changeUserPassword: " . $conn->error);
        return [
            'success' => false,
            'message' => 'Database query preparation failed: ' . $conn->error
        ];
    }
    
    $stmt->bind_param('si', $password_hash, $user_id);
    
    if ($stmt->execute()) {
        // Log user action
        if (function_exists('logUserAction')) {
            logUserAction($user_id, 'password_change', 'User changed their password', null, null);
        }
        
        error_log("Password changed successfully for user ID: " . $user_id);
        return [
            'success' => true,
            'message' => 'Password successfully changed.'
        ];
    } else {
        error_log("Execute error in changeUserPassword: " . $stmt->error);
        return [
            'success' => false,
            'message' => 'Error changing password: ' . $stmt->error
        ];
    }
}


/**
 * Kullanıcının VIP seviyesine göre izin verilen maksimum avatar numarasını döndürür
 * 
 * @param int $vip_level VIP seviyesi
 * @return int İzin verilen maksimum avatar numarası
 */
function getMaxAllowedAvatar($vip_level) {
    switch ($vip_level) {
        case 1: return 10;
        case 2: return 14;
        case 3: return 18;
        case 4: 
        case 5: return 24;
        default: return 5; // Standart üyeler için
    }
}

/**
 * Telefon numarasını formatlı şekilde döndürür
 * 
 * @param string $phone Telefon numarası
 * @return string Formatlı telefon numarası
 */
function formatPhoneNumber($phone) {
    // Telefon numarasını temizle (sadece rakamları al)
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    // Eğer numara uluslararası formatta değilse ve Türkiye numara uzunluğundaysa
    if (strlen($phone) == 10 && substr($phone, 0, 1) != '+') {
        return '+90' . $phone;
    }
    
    // Eğer numara + ile başlamıyorsa
    if (substr($phone, 0, 1) != '+' && strlen($phone) > 0) {
        return '+' . $phone;
    }
    
    return $phone;
}

/**
 * WhatsApp bağlantı linki oluşturur
 * 
 * @param string $phone WhatsApp telefon numarası
 * @param string $message Ön tanımlı mesaj (opsiyonel)
 * @return string WhatsApp bağlantı linki
 */
function getWhatsAppLink($phone, $message = '') {
    // Telefon numarasını formatla
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    // Mesaj parametresini kodla
    $encoded_message = urlencode($message);
    
    // WhatsApp API linki
    return "https://api.whatsapp.com/send?phone={$phone}&text={$encoded_message}";
}

/**
 * Telegram bağlantı linki oluşturur
 * 
 * @param string $username Telegram kullanıcı adı
 * @return string Telegram bağlantı linki
 */
function getTelegramLink($username) {
    // @ işaretini temizle
    $username = ltrim($username, '@');
    
    // Telegram profil linki
    return "https://t.me/{$username}";
}

/**
 * TRC20 adresinin geçerliliğini kontrol eder
 * 
 * @param string $address TRC20 cüzdan adresi
 * @return bool Adres geçerli mi?
 */
function validateTRC20Address($address) {
    // TRC20 adresleri genellikle T ile başlar ve 34 karakter uzunluğundadır
    if (empty($address)) {
        return false;
    }
    
    // T ile başlar mı?
    if (substr($address, 0, 1) !== 'T') {
        return false;
    }
    
    // 34 karakter uzunluğunda mı?
    if (strlen($address) !== 34) {
        return false;
    }
    
    // Base58Check karakterlerinden mi oluşuyor?
    if (!preg_match('/^[123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz]+$/', $address)) {
        return false;
    }
    
    return true;
}

/**
 * Telegram kullanıcı adının geçerliliğini kontrol eder
 * 
 * @param string $username Telegram kullanıcı adı
 * @return bool Kullanıcı adı geçerli mi?
 */
function validateTelegramUsername($username) {
    // Boş ise geçerli kabul edelim (zorunlu alan değil)
    if (empty($username)) {
        return true;
    }
    
    // @ işaretini kaldır
    $username = ltrim($username, '@');
    
    // 5-32 karakter arasında olmalı
    if (strlen($username) < 5 || strlen($username) > 32) {
        return false;
    }
    
    // a-z, 0-9 ve _ karakterlerinden oluşmalı
    if (!preg_match('/^[a-z0-9_]+$/', $username)) {
        return false;
    }
    
    // Çift alt çizgi içermemeli
    if (strpos($username, '__') !== false) {
        return false;
    }
    
    // Alt çizgi ile başlamamalı veya bitmemeli
    if (substr($username, 0, 1) === '_' || substr($username, -1) === '_') {
        return false;
    }
    
    return true;
}

/**
 * Telefon numarasının geçerliliğini kontrol eder
 * 
 * @param string $phone Telefon numarası
 * @return bool Telefon numarası geçerli mi?
 */
function validatePhoneNumber($phone) {
    // Boş ise geçerli kabul edelim (zorunlu alan değil)
    if (empty($phone)) {
        return true;
    }
    
    // Telefon numarasını temizle
    $phone = preg_replace('/[^0-9+]/', '', $phone);
    
    // En az 10 karakter uzunluğunda olmalı (ülke kodu hariç)
    if (strlen($phone) < 10) {
        return false;
    }
    
    // + ile başlıyorsa, ikinci karakter 0 olmamalı
    if (substr($phone, 0, 1) === '+' && substr($phone, 1, 1) === '0') {
        return false;
    }
    
    return true;
}


/**
 * Kullanıcının profil resmini döndürür
 * 
 * @param int|array $user Kullanıcı ID veya kullanıcı dizisi
 * @param string $size Boyut (small, medium, large)
 * @return string Profil resmi URL'si
 */
function getUserProfilePhoto($user, $size = 'medium') {
    // Kullanıcı verisi dizi değilse, ID olarak algıla ve bilgileri getir
    if (!is_array($user)) {
        $user = getUserDetails($user);
    }
    
    // Kullanıcı yoksa veya profil fotosu 0 ise
    if (!$user || !isset($user['profile_photo']) || $user['profile_photo'] == 0) {
        // Varsayılan avatar (kullanıcı adının ilk harfi)
        return [
            'type' => 'initial',
            'initial' => strtoupper(substr($user['username'] ?? 'U', 0, 1)),
            'url' => null
        ];
    }
    
    // Profil fotoğrafı varsa, boyuta göre URL oluştur
    $photo_id = $user['profile_photo'];
    $base_path = 'assets/images/avatars/';
    
    switch ($size) {
        case 'small':
            $img_path = $base_path . "avatar{$photo_id}_small.png";
            break;
        case 'large':
            $img_path = $base_path . "avatar{$photo_id}_large.png";
            break;
        case 'medium':
        default:
            $img_path = $base_path . "avatar{$photo_id}.png";
            break;
    }
    
    return [
        'type' => 'image',
        'initial' => null,
        'url' => $img_path
    ];
}



/**
 * Kullanıcının tüm bilgilerini kontrol eder ve eksikleri/hataları döndürür
 * 
 * @param int $user_id Kullanıcı ID
 * @return array Kontrol sonuçları
 */
function checkUserProfileCompleteness($user_id) {
    $user = getUserDetails($user_id);
    $missing = [];
    $errors = [];
    
    if (!$user) {
        return [
            'complete' => false,
            'percentage' => 0,
            'missing' => ['all'],
            'errors' => ['Kullanıcı bulunamadı']
        ];
    }
    
    // Zorunlu alanları kontrol et
    if (empty($user['full_name'])) {
        $missing[] = 'full_name';
    }
    
    if (empty($user['trc20_address'])) {
        $missing[] = 'trc20_address';
    } elseif (!validateTRC20Address($user['trc20_address'])) {
        $errors[] = 'Geçersiz TRC20 adresi';
    }
    
    // Opsiyonel alanları kontrol et (eksik olabilir ama hatalı olmamalı)
    if (!empty($user['phone']) && !validatePhoneNumber($user['phone'])) {
        $errors[] = 'Geçersiz telefon numarası';
    }
    
    if (!empty($user['phone_wp']) && !validatePhoneNumber($user['phone_wp'])) {
        $errors[] = 'Geçersiz WhatsApp numarası';
    }
    
    if (!empty($user['telegram']) && !validateTelegramUsername($user['telegram'])) {
        $errors[] = 'Geçersiz Telegram kullanıcı adı';
    }
    
    // Tamamlanma yüzdesini hesapla
    $total_fields = 4; // full_name, trc20_address, phone/whatsapp, telegram
    $completed = $total_fields - count($missing);
    $percentage = round(($completed / $total_fields) * 100);
    
    return [
        'complete' => (count($missing) === 0 && count($errors) === 0),
        'percentage' => $percentage,
        'missing' => $missing,
        'errors' => $errors
    ];
}


// VIP seviye adını getir
function getVipLevelName($vip_level) {
    global $db;
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("SELECT name FROM vip_packages WHERE id = ?");
    
    if ($stmt) {
        $stmt->bind_param('i', $vip_level);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['name'];
        }
    }
    
    // Varsayılan değer
    return $vip_level == 0 ? 'Standart' : 'Seviye ' . $vip_level;
}

// Kullanıcının referans kazançlarını getir
function getUserReferralEarnings($user_id, $limit = null) {
    global $db;
    $conn = $db->getConnection();
    $earnings = [];
    
    $query = "SELECT t.* FROM transactions t 
              WHERE t.user_id = ? AND t.type = 'referral'
              ORDER BY t.created_at DESC";
    
    if ($limit) {
        $query .= " LIMIT ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ii', $user_id, $limit);
    } else {
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $user_id);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $earnings[] = $row;
        }
    }
    
    return $earnings;
}

// Toplam referans kazancını getir
function getTotalReferralEarnings($user_id) {
    global $db;
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("SELECT SUM(amount) as total FROM transactions WHERE user_id = ? AND type = 'referral'");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['total'] ?: 0;
    }
    
    return 0;
}

// Kullanıcının referans ettiği kullanıcıları getir
function getReferredUsers($user_id, $limit = null) {
    global $db;
    $conn = $db->getConnection();
    $users = [];
    
    $query = "SELECT id, username, email, created_at, total_deposit, status 
              FROM users 
              WHERE referrer_id = ? 
              ORDER BY created_at DESC";
    
    if ($limit) {
        $query .= " LIMIT ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ii', $user_id, $limit);
    } else {
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $user_id);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }
    
    return $users;
}

// Referans edilen kullanıcı sayısını getir
function countReferredUsers($user_id) {
    global $db;
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM users WHERE referrer_id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['total'];
    }
    
    return 0;
}
/**
 * Kullanıcı aksiyonlarını loglar
 * 
 * @param int $user_id Kullanıcı ID
 * @param string $action Aksiyon tipi
 * @param string $description Açıklama
 * @param int $related_id İlişkili kayıt ID
 * @param string $related_type İlişkili kayıt tipi
 * @return bool İşlem başarılı mı?
 */
function logUserAction($user_id, $action, $description = null, $related_id = null, $related_type = null) {
    global $db;
    $conn = $db->getConnection();
    
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
    
    $stmt = $conn->prepare("INSERT INTO user_logs (user_id, action, description, related_id, related_type, ip_address) VALUES (?, ?, ?, ?, ?, ?)");
    
    if ($stmt) {
        $stmt->bind_param("ississ", $user_id, $action, $description, $related_id, $related_type, $ip_address);
        return $stmt->execute();
    }
    
    return false;
}
$notification_functions_file = __DIR__ . '/notification_functions.php';
if (file_exists($notification_functions_file)) {
    require_once $notification_functions_file;
}
/**
 * Yatırım durumunu günceller
 * 
 * @param string $payment_id Ödeme ID
 * @param string $status Yeni durum
 * @return bool İşlem başarılı mı?
 */
function updateDepositStatus($payment_id, $status) {
    global $db;
    $conn = $db->getConnection();
    
    // API durumunu veritabanı durumuna dönüştür
    $db_status = $status;
    if ($status == 'waiting') {
        $db_status = 'pending';
    }
    
    $stmt = $conn->prepare("UPDATE deposits SET status = ?, updated_at = NOW() WHERE payment_id = ?");
    if ($stmt === false) {
        return false;
    }
    
    $stmt->bind_param('ss', $db_status, $payment_id);
    return $stmt->execute();
}

/**
 * Ödeme ID'sine göre yatırım bilgisini getirir
 * 
 * @param string $payment_id Ödeme ID
 * @return array|null Yatırım bilgileri
 */
function getDepositByPaymentId($payment_id) {
    global $db;
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("SELECT * FROM deposits WHERE payment_id = ?");
    if ($stmt === false) {
        return null;
    }
    
    $stmt->bind_param('s', $payment_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return null;
}

/**
 * Kullanıcı bakiyesini arttırır
 * 
 * @param int $user_id Kullanıcı ID
 * @param float $amount Eklenecek miktar
 * @return bool İşlem başarılı mı?
 */
function addUserBalance($user_id, $amount) {
    global $db;
    $conn = $db->getConnection();
    
    // Önce mevcut bakiyeyi al
    $stmt = $conn->prepare("SELECT balance FROM users WHERE id = ? FOR UPDATE");
    if ($stmt === false) {
        return false;
    }
    
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $before_balance = $user['balance'];
        $after_balance = $before_balance + $amount;
        
        // Bakiyeyi güncelle
        $stmt = $conn->prepare("UPDATE users SET balance = balance + ?, total_deposit = total_deposit + ? WHERE id = ?");
        if ($stmt === false) {
            return false;
        }
        
        $stmt->bind_param('ddi', $amount, $amount, $user_id);
        return $stmt->execute();
    }
    
    return false;
}

/**
 * İşlem kaydı oluşturur
 * 
 * @param int $user_id Kullanıcı ID
 * @param string $type İşlem tipi
 * @param float $amount Miktar
 * @param string $description Açıklama
 * @param int $related_id İlişkili kayıt ID (opsiyonel)
 * @return int|bool Oluşturulan işlem ID'si veya başarısız ise false
 */
function createTransaction($user_id, $type, $amount, $description, $related_id = null) {
    global $db;
    $conn = $db->getConnection();
    
    // Kullanıcı bakiyesini al
    $stmt = $conn->prepare("SELECT balance FROM users WHERE id = ?");
    if ($stmt === false) {
        return false;
    }
    
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $after_balance = $user['balance'];
        $before_balance = $after_balance - $amount;
        
        // İşlem kaydı oluştur
        $status = 'completed';
        
        if ($related_id) {
            $stmt = $conn->prepare("INSERT INTO transactions (user_id, type, amount, description, related_id, before_balance, after_balance, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param('isdsidds', $user_id, $type, $amount, $description, $related_id, $before_balance, $after_balance, $status);
        } else {
            $stmt = $conn->prepare("INSERT INTO transactions (user_id, type, amount, description, before_balance, after_balance, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param('isdsids', $user_id, $type, $amount, $description, $before_balance, $after_balance, $status);
        }
        
        if ($stmt->execute()) {
            return $conn->insert_id;
        }
    }
    
    return false;
}

/**
 * Referans kazançlarını dağıtır (3 seviyeye kadar)
 * 
 * @param int $user_id Kullanıcı ID
 * @param float $amount İşlem tutarı
 * @param string $transaction_type İşlem tipi (deposit, game vb.)
 * @return bool İşlem başarılı mı?
 */
function distributeReferralEarnings($user_id, $amount, $transaction_type = 'deposit') {
    global $db;
    $conn = $db->getConnection();
    
    // Site ayarlarını al
    $settings = getSiteSettings();
    
    // Referans sisteminin aktif olup olmadığını kontrol et
    if (!isset($settings['referral_active']) || $settings['referral_active'] != '1') {
        return false;
    }
    
    // Kullanıcıyı al
    $user = getUserDetails($user_id);
    if (!$user) {
        return false;
    }
    
    // İşlem tipi için özel referans oranlarını kontrol et
    $tier1_rate_key = ($transaction_type == 'game') ? 'referral_gametier1_rate' : 'referral_tier1_rate';
    $tier2_rate_key = ($transaction_type == 'game') ? 'referral_gametier2_rate' : 'referral_tier2_rate';
    $tier3_rate_key = ($transaction_type == 'game') ? 'referral_gametier3_rate' : 'referral_tier3_rate';
    
    // Referans oranlarını al
    $tier1_rate = isset($settings[$tier1_rate_key]) ? floatval($settings[$tier1_rate_key]) : 0;
    $tier2_rate = isset($settings[$tier2_rate_key]) ? floatval($settings[$tier2_rate_key]) : 0;
    $tier3_rate = isset($settings[$tier3_rate_key]) ? floatval($settings[$tier3_rate_key]) : 0;
    
    // 1. seviye referans (direk referans eden)
    if ($user['referrer_id'] && $tier1_rate > 0) {
        $level1_user_id = $user['referrer_id'];
        $level1_earning = $amount * $tier1_rate;
        
        if ($level1_earning > 0) {
            // Kazancı ekle
            addReferralEarning($level1_user_id, $level1_earning, $user_id, 1, $transaction_type);
            
            // 2. seviye referans
            if ($tier2_rate > 0) {
                $level1_user = getUserDetails($level1_user_id);
                
                if ($level1_user && $level1_user['referrer_id']) {
                    $level2_user_id = $level1_user['referrer_id'];
                    $level2_earning = $amount * $tier2_rate;
                    
                    if ($level2_earning > 0) {
                        // Kazancı ekle
                        addReferralEarning($level2_user_id, $level2_earning, $user_id, 2, $transaction_type);
                        
                        // 3. seviye referans
                        if ($tier3_rate > 0) {
                            $level2_user = getUserDetails($level2_user_id);
                            
                            if ($level2_user && $level2_user['referrer_id']) {
                                $level3_user_id = $level2_user['referrer_id'];
                                $level3_earning = $amount * $tier3_rate;
                                
                                if ($level3_earning > 0) {
                                    // Kazancı ekle
                                    addReferralEarning($level3_user_id, $level3_earning, $user_id, 3, $transaction_type);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    
    return true;
}

/**
 * Referans kazancı ekler
 * 
 * @param int $user_id Kazanç eklenecek kullanıcı ID
 * @param float $amount Miktar
 * @param int $referred_user_id Referans edilen kullanıcı ID
 * @param int $tier Referans seviyesi (1, 2, 3)
 * @param string $transaction_type İşlem tipi (deposit, game vb.)
 * @return bool İşlem başarılı mı?
 */
function addReferralEarning($user_id, $amount, $referred_user_id, $tier, $transaction_type = 'deposit') {
    global $db;
    $conn = $db->getConnection();
    
    // İşlem başlat
    $conn->begin_transaction();
    
    try {
        // Kullanıcı bakiyesini al
        $stmt = $conn->prepare("SELECT balance, referral_balance FROM users WHERE id = ? FOR UPDATE");
        if ($stmt === false) {
            throw new Exception("SQL hatası (SELECT): " . $conn->error);
        }
        
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception("Kullanıcı bulunamadı.");
        }
        
        $user = $result->fetch_assoc();
        $before_balance = $user['balance'];
        $before_referral_balance = $user['referral_balance'];
        $after_balance = $before_balance + $amount;
        $after_referral_balance = $before_referral_balance + $amount;
        
        // Bakiyeleri güncelle
        $stmt = $conn->prepare("UPDATE users SET balance = balance + ?, referral_balance = referral_balance + ? WHERE id = ?");
        if ($stmt === false) {
            throw new Exception("SQL hatası (UPDATE): " . $conn->error);
        }
        
        $stmt->bind_param('ddi', $amount, $amount, $user_id);
        $stmt->execute();
        
        // Referans edilen kullanıcı adını al
        $referred_username = '';
        $stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
        if ($stmt === false) {
            throw new Exception("SQL hatası (SELECT username): " . $conn->error);
        }
        
        $stmt->bind_param('i', $referred_user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $referred_username = $row['username'];
        }
        
        // İşlem açıklaması
        $description = "Tier $tier Referral Earning: $referred_username";
        if ($transaction_type == 'game') {
            $description = "Game - " . $description;
        }
        
        // İşlem kaydı oluştur
        $type = 'referral';
        $status = 'completed';
        
        $stmt = $conn->prepare("INSERT INTO transactions (user_id, type, amount, description, related_id, before_balance, after_balance, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        if ($stmt === false) {
            throw new Exception("SQL hatası (INSERT): " . $conn->error);
        }
        
        $stmt->bind_param('isdsidds', $user_id, $type, $amount, $description, $referred_user_id, $before_balance, $after_balance, $status);
        $stmt->execute();
        
        // İşlemi tamamla
        $conn->commit();
        
        return true;
        
    } catch (Exception $e) {
        // Hata durumunda işlemi geri al
        $conn->rollback();
        
        // Hata logla
        error_log("Referans kazancı eklenirken hata: " . $e->getMessage());
        
        return false;
    }
}

function processWithdrawalWithFees($user_id, $amount, $address, $percentageFee, $networkFee) {
    global $conn;
    
    // Begin transaction
    $conn->begin_transaction();
    
    try {
        // Calculate total fee
        $totalFee = $percentageFee + $networkFee;
        
        // Create the withdrawal record
        $query = "INSERT INTO withdrawals (user_id, amount, fee, trc20_address, status, created_at) VALUES (?, ?, ?, ?, 'pending', NOW())";
        $stmt = $conn->prepare($query);
        
        if (!$stmt) {
            throw new Exception("SQL Error (INSERT withdrawal): " . $conn->error);
        }
        
        $stmt->bind_param('idds', $user_id, $amount, $totalFee, $address);
        $success = $stmt->execute();
        
        if (!$success) {
            throw new Exception("SQL Execution Error (INSERT withdrawal): " . $stmt->error);
        }
        
        $withdraw_id = $conn->insert_id;
        
        // Reduce user balance
        $totalDeduction = $amount + $totalFee;
        $query = "UPDATE users SET balance = balance - ? WHERE id = ? AND balance >= ?";
        $stmtBalance = $conn->prepare($query);
        
        if (!$stmtBalance) {
            throw new Exception("SQL Error (UPDATE balance): " . $conn->error);
        }
        
        $stmtBalance->bind_param('did', $totalDeduction, $user_id, $totalDeduction);
        $success = $stmtBalance->execute();
        
        if (!$success) {
            throw new Exception("SQL Execution Error (UPDATE balance): " . $stmtBalance->error);
        }
        
        if ($stmtBalance->affected_rows === 0) {
            throw new Exception("Yetersiz bakiye veya bakiye güncellenemedi.");
        }
        
        // Create transaction record
        $query = "INSERT INTO transactions (user_id, type, amount, related_id, status, created_at) VALUES (?, 'withdraw', ?, ?, 'pending', NOW())";
        $stmtTransaction = $conn->prepare($query);
        
        if (!$stmtTransaction) {
            throw new Exception("SQL Error (INSERT transaction): " . $conn->error);
        }
        
        $stmtTransaction->bind_param('idi', $user_id, $totalDeduction, $withdraw_id);
        $success = $stmtTransaction->execute();
        
        if (!$success) {
            throw new Exception("SQL Execution Error (INSERT transaction): " . $stmtTransaction->error);
        }
        
        // Commit transaction
        $conn->commit();
        
        return [
            'success' => true,
            'withdraw_id' => $withdraw_id
        ];
        
    } catch (Exception $e) {
        // Roll back on failure
        $conn->rollback();
        
        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}