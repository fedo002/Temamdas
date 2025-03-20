<?php
/**
 * NOWPayments IPN Handler
 * 
 * Bu dosya, NOWPayments'ten gelen IPN (Instant Payment Notification) bildirimlerini işler
 */

// Gerekli dosyaları dahil et
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/class/NowPaymentsAPI.php';

// NOWPayments ayarlarını al
$settings = getPaymentSettings();
$api = new NowPaymentsAPI($settings['nowpayments_api_key'], $settings['nowpayments_ipn_secret'], $settings['nowpayments_test_mode']);

// IPN verilerini al
$ipnData = file_get_contents('php://input');
$nowpaymentsSignature = isset($_SERVER['HTTP_X_NOWPAYMENTS_SIG']) ? $_SERVER['HTTP_X_NOWPAYMENTS_SIG'] : '';

// Log all requests for debugging
$logFile = '../logs/nowpayments_ipn_' . date('Y-m-d') . '.log';
$logData = date('Y-m-d H:i:s') . " - Received IPN: " . $ipnData . PHP_EOL;
$logData .= "Signature: " . $nowpaymentsSignature . PHP_EOL;
file_put_contents($logFile, $logData, FILE_APPEND);

// IPN verisini işle
$processedData = $api->processIpn($ipnData, $nowpaymentsSignature);

if ($processedData) {
    // Ödeme başarılı olarak işaretlendi
    if ($processedData['payment_status'] === 'confirmed' || $processedData['payment_status'] === 'finished') {
        // Veritabanı bağlantısını aç
        $db = new DB();
        $conn = $db->getConnection();
        
        // Ödeme ID'sine göre ödemeyi bul
        $paymentId = $processedData['payment_id'];
        $stmt = $conn->prepare("SELECT * FROM deposits WHERE payment_id = ?");
        $stmt->bind_param("s", $paymentId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $deposit = $result->fetch_assoc();
            
            // Ödeme zaten işlendiyse tekrar işleme
            if ($deposit['status'] === 'confirmed') {
                file_put_contents($logFile, "Payment already processed: " . $paymentId . PHP_EOL, FILE_APPEND);
                echo 'OK';
                exit;
            }
            
            // Ödeme miktarını ve kullanıcı ID'sini al
            $userId = $deposit['user_id'];
            $amount = $processedData['actually_paid'];
            $payCurrency = $processedData['pay_currency'];
            
            // Ödeme USDT TRC20 olarak mı yapıldı?
            if (strtolower($payCurrency) === 'usdttrc20') {
                // Kullanıcının bakiyesini güncelle
                $stmt = $conn->prepare("UPDATE users SET balance = balance + ?, total_deposit = total_deposit + ? WHERE id = ?");
                $stmt->bind_param("ddi", $amount, $amount, $userId);
                $result = $stmt->execute();
                
                if ($result) {
                    // Deposit durumunu güncelle
                    $status = 'confirmed';
                    $txHash = $processedData['payment_id'];
                    
                    $stmt = $conn->prepare("UPDATE deposits SET status = ?, amount = ?, transaction_hash = ? WHERE id = ?");
                    $stmt->bind_param("sdsi", $status, $amount, $txHash, $deposit['id']);
                    $stmt->execute();
                    
                    // İşlem kaydı ekle
                    $stmt = $conn->prepare("INSERT INTO transactions (user_id, type, amount, status, description) VALUES (?, 'deposit', ?, 'completed', ?)");
                    $description = "NOWPayments deposit: " . $txHash;
                    $stmt->bind_param("ids", $userId, $amount, $description);
                    $stmt->execute();
                    
                    // Referans kontrolü yap - Kullanıcının referansı var mı?
                    processReferralCommission($userId, $amount);
                    
                    file_put_contents($logFile, "Payment successfully processed: " . $paymentId . " - Amount: " . $amount . " USDT" . PHP_EOL, FILE_APPEND);
                } else {
                    file_put_contents($logFile, "Error updating user balance: " . $conn->error . PHP_EOL, FILE_APPEND);
                }
            } else {
                file_put_contents($logFile, "Payment currency is not USDT TRC20: " . $payCurrency . PHP_EOL, FILE_APPEND);
            }
        } else {
            file_put_contents($logFile, "Payment not found in database: " . $paymentId . PHP_EOL, FILE_APPEND);
        }
    } else {
        file_put_contents($logFile, "Payment status is not confirmed: " . $processedData['payment_status'] . PHP_EOL, FILE_APPEND);
    }
    
    echo 'OK';
} else {
    // IPN doğrulanamadı
    file_put_contents($logFile, "IPN verification failed!" . PHP_EOL, FILE_APPEND);
    header('HTTP/1.1 400 Bad Request');
    echo 'IPN Verification Failed';
}