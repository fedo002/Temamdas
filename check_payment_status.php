<?php
// check_payment_status.php - NowPayments ödeme durumunu kontrol eden dosya
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/class/NowPaymentsAPI.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Payment ID parametresi kontrolü
if (!isset($_GET['payment_id']) || empty($_GET['payment_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Missing payment ID']);
    exit;
}

$payment_id = $_GET['payment_id'];

// Get payment settings
$settings = getPaymentSettings();

// Initialize API
$api = new NowPaymentsAPI($settings['nowpayments_api_key'], $settings['nowpayments_ipn_secret'], $settings['nowpayments_test_mode']);

try {
    // Ödeme durumunu API'den kontrol et
    $paymentStatus = $api->getPaymentStatus($payment_id);
    
    if ($paymentStatus) {
        // Eğer durum değiştiyse veritabanını güncelle
        updateDepositStatus($payment_id, $paymentStatus['payment_status']);
        
        // Eğer ödeme tamamlandıysa, kullanıcı bakiyesini güncelle
        if ($paymentStatus['payment_status'] === 'confirmed' || $paymentStatus['payment_status'] === 'finished') {
            // Deposit kaydını al
            $deposit = getDepositByPaymentId($payment_id);
            if ($deposit && $deposit['status'] !== 'confirmed') {
                // Kullanıcı bakiyesini güncelle
                addUserBalance($user_id, $deposit['amount']);
                // İşlem kaydı oluştur
                createTransaction($user_id, 'deposit', $deposit['amount'], 'Crypto deposit');
                // Referans kazançlarını dağıt
                distributeReferralEarnings($user_id, $deposit['amount'], 'deposit');
            }
        }
        
        header('Content-Type: application/json');
        echo json_encode([
            'status' => $paymentStatus['payment_status'],
            'amount' => $paymentStatus['price_amount'],
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        exit;
    } else {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Payment not found']);
        exit;
    }
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}