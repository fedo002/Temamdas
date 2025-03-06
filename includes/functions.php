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

// Kullanıcı bilgilerini getir
function getUserDetails($user_id) {
    global $db;
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
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

// TRC20 adresinin geçerliliğini kontrol et (basit kontrol)
function validateTRC20Address($address) {
    // TRC20 adresleri genellikle T ile başlar ve 34 karakter uzunluğundadır
    return strlen($address) === 34 && $address[0] === 'T';
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
    $daily_game_limit = $vip_details['daily_game_limit'] ?? 5; // Varsayılan 5 oyun
    
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

