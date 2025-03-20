<?php
// process_deposit.php dosyasının en başına, session_start() satırından önce ekleyin
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Bir log dosyasına da hataları yazabilirsiniz
ini_set('log_errors', 1);
ini_set('error_log', 'deposit_error.log');
session_start();
require_once '../../includes/config.php';
require_once '../../includes/admin_functions.php';

// Admin oturum kontrolü
if(!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

// Bağlantı değişkenini al
$conn = $GLOBALS['db']->getConnection();

// İşlem parametrelerini al
$action = isset($_GET['action']) ? $_GET['action'] : '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Geçerli bir işlem ve ID kontrolü
if (empty($action) || $id <= 0) {
    setFlashMessage('error', 'Geçersiz istek parametreleri.');
    header('Location: deposits.php');
    exit;
}

/**
 * Debug mesajı yazdır
 * 
 * @param string $message Debug mesajı
 * @param mixed $data Ekstra veri (opsiyonel)
 * @return void
 */
function debug($message, $data = null) {
    $debugInfo = date('Y-m-d H:i:s') . " - " . $message;
    
    if ($data !== null) {
        $debugInfo .= "\nData: " . print_r($data, true);
    }
    
    error_log($debugInfo);
    
    // Geliştirme ortamında ekrana da yazdırabilirsiniz
    if (defined('DEBUG_MODE') && DEBUG_MODE) {
        echo "<pre>DEBUG: $debugInfo</pre>";
    }
}

// Dosyanın başında tanımlayın
define('DEBUG_MODE', true); // Geliştirme tamamlandığında false yapın

// Yatırım bilgilerini getir
$deposit = getDepositById($id);

if (!$deposit) {
    setFlashMessage('error', 'Yatırım bulunamadı.');
    header('Location: deposits.php');
    exit;
}

// İşlem durumunu kontrol et
if ($deposit['status'] !== 'pending' && $action !== 'view') {
    setFlashMessage('error', 'Sadece bekleyen işlemler üzerinde değişiklik yapılabilir.');
    header('Location: deposits.php');
    exit;
}

// İşlemi gerçekleştir
$result = false;
$message = '';

switch ($action) {
    case 'confirm':
        // Yatırımı onayla
        $result = confirmDeposit($id);
        $message = $result ? 'Yatırım başarıyla onaylandı.' : 'Yatırım onaylanırken bir hata oluştu.';
        break;
        
    case 'cancel':
        // Yatırımı iptal et
        $result = cancelDeposit($id);
        $message = $result ? 'Yatırım başarıyla iptal edildi.' : 'Yatırım iptal edilirken bir hata oluştu.';
        break;
        
    default:
        $message = 'Geçersiz işlem.';
        break;
}

// İşlem sonucuna göre yönlendir
if ($result) {
    setFlashMessage('success', $message);
} else {
    setFlashMessage('error', $message);
}

// Geri yönlendir
header('Location: deposits.php');
exit;


/**
 * Para yatırma işlemini onayla (basit versiyon)
 */
function confirmDeposit($deposit_id) {
    global $conn;
    
    // Yatırım bilgilerini getir
    $deposit = getDepositById($deposit_id);
    
    if (!$deposit) {
        echo "Yatırım bulunamadı!";
        return false;
    }
    
    // Durumu kontrol et
    if ($deposit['status'] !== 'pending') {
        echo "Bu işlem zaten onaylanmış veya iptal edilmiş!";
        return false;
    }
    
    // Kullanıcı bilgisini direkt sorgula
    $user_id = $deposit['user_id'];
    $result = $conn->query("SELECT * FROM users WHERE id = $user_id");
    
    if (!$result) {
        echo "Kullanıcı sorgu hatası: " . $conn->error;
        return false;
    }
    
    $user = $result->fetch_assoc();
    
    if (!$user) {
        echo "Kullanıcı bulunamadı!";
        return false;
    }
    
    // Yatırım durumunu güncelle
    $update1 = $conn->query("UPDATE deposits SET status = 'confirmed', updated_at = NOW() WHERE id = $deposit_id");
    
    if (!$update1) {
        echo "Yatırım güncelleme hatası: " . $conn->error;
        return false;
    }
    
    // Bakiyeyi güncelle
    $current_balance = $user['balance'];
    $new_balance = $current_balance + $deposit['amount'];
    
    $update2 = $conn->query("UPDATE users SET balance = $new_balance, updated_at = NOW() WHERE id = $user_id");
    
    if (!$update2) {
        echo "Bakiye güncelleme hatası: " . $conn->error;
        return false;
    }
    
    // İşlem kaydını basit bir sorgu ile ekleyelim
    $insert = $conn->query("INSERT INTO transactions (user_id, type, amount, balance_before, balance_after, status, reference_id, description, created_at, updated_at) 
                           VALUES ($user_id, 'deposit', {$deposit['amount']}, $current_balance, $new_balance, 'completed', $deposit_id, 'Para yatırma işlemi onaylandı', NOW(), NOW())");
    
    if (!$insert) {
        echo "İşlem kaydı hatası: " . $conn->error;
        return false;
    }
    
    // İşlem logu ekleyelim
    $admin_id = $_SESSION['admin_id'];
    $username = $user['username'];
    $amount = $deposit['amount'];
    
    $log = $conn->query("INSERT INTO admin_logs (admin_id, action, description, created_at) 
                        VALUES ($admin_id, 'deposit_confirm', 'Para yatırma onaylandı ID: $deposit_id, Kullanıcı: $username, Tutar: $amount USDT', NOW())");
    
    if (!$log) {
        echo "Log kaydı hatası: " . $conn->error;
        // Log kaydı önemli değil, işlem gerçekleşti sayılabilir
    }
    
    echo "İşlem başarıyla tamamlandı!";
    return true;
}

/**
 * Para yatırma işlemini iptal et (basit versiyon)
 */
function cancelDeposit($deposit_id) {
    global $conn;
    
    // Yatırım bilgilerini getir
    $deposit = getDepositById($deposit_id);
    
    if (!$deposit) {
        echo "Yatırım bulunamadı!";
        return false;
    }
    
    // Durumu kontrol et
    if ($deposit['status'] !== 'pending') {
        echo "Bu işlem zaten onaylanmış veya iptal edilmiş!";
        return false;
    }
    
    // Kullanıcı bilgisini direkt sorgula
    $user_id = $deposit['user_id'];
    $result = $conn->query("SELECT * FROM users WHERE id = $user_id");
    
    if (!$result) {
        echo "Kullanıcı sorgu hatası: " . $conn->error;
        return false;
    }
    
    $user = $result->fetch_assoc();
    
    if (!$user) {
        echo "Kullanıcı bulunamadı!";
        return false;
    }
    
    // Yatırım durumunu güncelle
    $update = $conn->query("UPDATE deposits SET status = 'failed', updated_at = NOW() WHERE id = $deposit_id");
    
    if (!$update) {
        echo "Yatırım güncelleme hatası: " . $conn->error;
        return false;
    }
    
    // İşlem kaydını basit bir sorgu ile ekleyelim (iptal işleminde bakiye değişmez)
    $current_balance = $user['balance'];
    $insert = $conn->query("INSERT INTO transactions (user_id, type, amount, balance_before, balance_after, status, reference_id, description, created_at, updated_at) 
                           VALUES ($user_id, 'deposit', 0, $current_balance, $current_balance, 'failed', $deposit_id, 'Para yatırma işlemi iptal edildi', NOW(), NOW())");
    
    if (!$insert) {
        echo "İşlem kaydı hatası: " . $conn->error;
        return false;
    }
    
    // İşlem logu ekleyelim
    $admin_id = $_SESSION['admin_id'];
    $username = $user['username'];
    $amount = $deposit['amount'];
    
    $log = $conn->query("INSERT INTO admin_logs (admin_id, action, description, created_at) 
                        VALUES ($admin_id, 'deposit_cancel', 'Para yatırma iptal edildi ID: $deposit_id, Kullanıcı: $username, Tutar: $amount USDT', NOW())");
    
    if (!$log) {
        echo "Log kaydı hatası: " . $conn->error;
        // Log kaydı önemli değil, işlem gerçekleşti sayılabilir
    }
    
    echo "İptal işlemi başarıyla tamamlandı!";
    return true;
}
/**
 * Flash mesajı ayarla
 * 
 * @param string $type Mesaj tipi (success, error, warning, info)
 * @param string $message Mesaj içeriği
 * @return void
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Para yatırma işlemini ID'ye göre getir
 * 
 * @param int $deposit_id Yatırım ID
 * @return array|false Yatırım bilgileri veya false
 */
function getDepositById($deposit_id) {
    global $conn;
    
    debug("getDepositById fonksiyonu çağrıldı", ['deposit_id' => $deposit_id]);
    
    if (!$conn) {
        debug("Veritabanı bağlantısı kurulamadı");
        return false;
    }
    
    debug("Veritabanı bağlantısı kuruldu", ['conn_type' => get_class($conn)]);
    
    $query = "
        SELECT d.*, u.username 
        FROM deposits d
        LEFT JOIN users u ON d.user_id = u.id
        WHERE d.id = ?
    ";
    
    debug("SQL sorgusu hazırlandı", ['query' => $query]);
    
    $stmt = $conn->prepare($query);
    
    if ($stmt === false) {
        debug("SQL sorgusu hazırlanamadı", ['error' => $conn->error]);
        return false;
    }
    
    debug("Sorgu hazırlama başarılı, parametre bağlanıyor");
    $stmt->bind_param("i", $deposit_id);
    
    debug("Sorgu çalıştırılıyor");
    $result = $stmt->execute();
    
    if ($result === false) {
        debug("Sorgu çalıştırma hatası", ['error' => $stmt->error]);
        return false;
    }
    
    debug("Sorgu başarıyla çalıştırıldı, sonuçlar alınıyor");
    $result = $stmt->get_result();
    
    if ($result === false) {
        debug("Sorgu sonucu alınamadı", ['error' => $stmt->error]);
        return false;
    }
    
    $deposit = $result->fetch_assoc();
    debug("Veri alındı", ['deposit' => $deposit ? 'Yatırım bulundu' : 'Yatırım bulunamadı']);
    
    $stmt->close();
    
    return $deposit;
}

