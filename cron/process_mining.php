<?php
/**
 * Mining kazançlarını hesaplayan ve bakiyelere aktaran CRON görevi
 * Her saat başı çalıştırılmalıdır (0 * * * *)
 */

// CLI'dan çalıştırma kontrolü
if (php_sapi_name() !== 'cli' && !isset($_GET['secret_key'])) {
    die('Bu dosya yalnızca CLI üzerinden veya geçerli bir anahtar ile çalıştırılabilir.');
}

// Web'den çalıştırma durumunda anahtar kontrolü
if (isset($_GET['secret_key'])) {
    // Bu anahtarı güvenli ve benzersiz bir değer olarak değiştirin
    $secret_key = '394f65b9c4c1a6433c89ef3ff7b735a2';
    
    if ($_GET['secret_key'] !== $secret_key) {
        http_response_code(403);
        die('Erişim reddedildi.');
    }
}

// Gerekli dosyaları dahil et
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/functions.php';

// Başlangıç mesajı
echo "Mining işleme başlatılıyor: " . date('Y-m-d H:i:s') . "\n";

// Bağlantı değişkenini al
$conn = $GLOBALS['db']->getConnection();

// İşlem kaydı oluştur
createProcessLog();

// Aktif mining paketlerini al
$active_packages = getActiveMiningPackages();
echo "Toplam aktif paket sayısı: " . count($active_packages) . "\n";

// Her aktif paket için işlem yap
$today = date('Y-m-d');
$total_processed = 0;
$total_earnings = 0;

foreach ($active_packages as $package) {
    
    // Mining kazancını hesapla (saatlik)
    $daily_revenue = $package['daily_revenue'];
    $hourly_revenue = $daily_revenue / 24;
    $hourly_electricity_cost = ($package['electricity_cost'] * $package['hash_rate']) / 24;
    $hourly_net_revenue = $hourly_revenue - $hourly_electricity_cost;
    
    // Kazanç tablosuna kaydet
    if (addMiningEarning($package['id'], $package['user_id'], $package['hash_rate'], $hourly_revenue, $hourly_electricity_cost, $hourly_net_revenue)) {
        // Kullanıcı bakiyesine ekle
        updateUserBalance($package['user_id'], $hourly_net_revenue);
        
        // Mining paketi toplam kazancını güncelle
        updatePackageTotalEarnings($package['id'], $hourly_net_revenue);
        
        // İşlem logu
        echo "Paket ID: {$package['id']} - Kullanıcı ID: {$package['user_id']} - Kazanç: " . number_format($hourly_net_revenue, 6) . " USDT\n";
        
        $total_processed++;
        $total_earnings += $hourly_net_revenue;
    } else {
        echo "Paket ID: {$package['id']} - Kazanç işlenemedi!\n";
    }
}

// İşlem sonucu
echo "İşlem tamamlandı: " . date('Y-m-d H:i:s') . "\n";
echo "İşlenen paket sayısı: " . $total_processed . "\n";
echo "Toplam ödenen kazanç: " . number_format($total_earnings, 6) . " USDT\n";

/**
 * İşlem kaydı oluşturur
 * 
 * @return bool İşlem başarılı mı?
 */
function createProcessLog() {
    global $conn;
    
    $stmt = $conn->prepare("INSERT INTO mining_process_logs (process_time) VALUES (NOW())");
    
    if ($stmt) {
        return $stmt->execute();
    }
    
    return false;
}

/**
 * Aktif mining paketlerini getirir
 * 
 * @return array Aktif mining paketleri
 */
function getActiveMiningPackages() {
    global $conn;
    
    $packages = [];
    
    $query = "SELECT ump.*, mp.hash_rate, mp.electricity_cost, mp.daily_revenue_rate, 
             (mp.hash_rate * mp.daily_revenue_rate) as daily_revenue
             FROM user_mining_packages ump 
             JOIN mining_packages mp ON ump.package_id = mp.id 
             WHERE ump.status = 'active'";
    
    $result = $conn->query($query);
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Eğer daily_revenue yoksa hesapla
            if (!isset($row['daily_revenue']) || $row['daily_revenue'] === null) {
                $row['daily_revenue'] = $row['hash_rate'] * $row['daily_revenue_rate'];
            }
            $packages[] = $row;
        }
    }
    
    return $packages;
}

/**
 * Mining kazancı zaten işlenmiş mi kontrol eder
 * 
 * @param int $package_id Mining paketi ID
 * @param string $date Tarih (Y-m-d)
 * @return bool İşlenmiş mi?
 */
function isEarningProcessed($package_id, $date) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM mining_earnings 
                          WHERE user_mining_id = ? AND date = ? AND HOUR(created_at) = HOUR(NOW())");
    
    if ($stmt) {
        $stmt->bind_param("is", $package_id, $date);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            return $row['count'] > 0;
        }
    }
    
    return false;
}

/**
 * Mining kazancı ekler
 * 
 * @param int $package_id Mining paketi ID
 * @param int $user_id Kullanıcı ID
 * @param float $hash_rate Hash rate
 * @param float $revenue Brüt kazanç
 * @param float $electricity_cost Elektrik maliyeti
 * @param float $net_revenue Net kazanç
 * @return bool İşlem başarılı mı?
 */
function addMiningEarning($package_id, $user_id, $hash_rate, $revenue, $electricity_cost, $net_revenue) {
    global $conn;
    
    $stmt = $conn->prepare("INSERT INTO mining_earnings 
                          (user_id, user_mining_id, hash_rate, revenue, electricity_cost, net_revenue, date, processed, created_at) 
                          VALUES (?, ?, ?, ?, ?, ?, CURDATE(), 1, NOW())");
    
    if ($stmt) {
        $stmt->bind_param("iidddd", $user_id, $package_id, $hash_rate, $revenue, $electricity_cost, $net_revenue);
        return $stmt->execute();
    }
    
    return false;
}

/**
 * Kullanıcı bakiyesini günceller
 * 
 * @param int $user_id Kullanıcı ID
 * @param float $amount Eklenecek miktar
 * @return bool İşlem başarılı mı?
 */
function updateUserBalance($user_id, $amount) {
    global $conn;
    
    // Kullanıcının mevcut bakiyesini al
    $stmt = $conn->prepare("SELECT balance FROM users WHERE id = ?");
    
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $current_balance = $row['balance'];
            $new_balance = $current_balance + $amount;
            
            // Bakiyeyi güncelle
            $update_stmt = $conn->prepare("UPDATE users SET balance = ? WHERE id = ?");
            
            if ($update_stmt) {
                $update_stmt->bind_param("di", $new_balance, $user_id);
                $success = $update_stmt->execute();
                
                if ($success) {
                    // İşlem kaydı oluştur
                    addMiningTransaction($user_id, $amount, $current_balance, $new_balance);
                }
                
                return $success;
            }
        }
    }
    
    return false;
}

/**
 * Mining işlem kaydı oluşturur
 * 
 * @param int $user_id Kullanıcı ID
 * @param float $amount Miktar
 * @param float $before_balance Önceki bakiye
 * @param float $after_balance Sonraki bakiye
 * @return bool İşlem başarılı mı?
 */
function addMiningTransaction($user_id, $amount, $before_balance, $after_balance) {
    global $conn;
    
    $stmt = $conn->prepare("INSERT INTO transactions 
                          (user_id, type, amount, before_balance, after_balance, status, description, created_at) 
                          VALUES (?, 'miningdeposit', ?, ?, ?, 'completed', 'Mining kazancı', NOW())");
    
    if ($stmt) {
        $stmt->bind_param("iddd", $user_id, $amount, $before_balance, $after_balance);
        return $stmt->execute();
    }
    
    return false;
}

/**
 * Mining paketi toplam kazancını günceller
 * 
 * @param int $package_id Mining paketi ID
 * @param float $amount Eklenecek miktar
 * @return bool İşlem başarılı mı?
 */
function updatePackageTotalEarnings($package_id, $amount) {
    global $conn;
    
    $stmt = $conn->prepare("UPDATE user_mining_packages 
                          SET total_earned = total_earned + ? 
                          WHERE id = ?");
    
    if ($stmt) {
        $stmt->bind_param("di", $amount, $package_id);
        return $stmt->execute();
    }
    
    return false;
}