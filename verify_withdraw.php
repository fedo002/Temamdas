<?php
// verify_withdraw.php - Withdrawal password verification
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'includes/config.php';
require_once 'includes/functions.php';

// Required classes for PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=withdraw.php');
    exit;
}

// Get user and security information
$user_id = $_SESSION['user_id'];
$user = getUserDetails($user_id);

// Store form data in session
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['amount'], $_POST['trc20_address'])) {
    $_SESSION['withdraw_data'] = [
        'amount' => $_POST['amount'],
        'trc20_address' => $_POST['trc20_address']
    ];
}

// Get payment settings
$settings = getPaymentSettings();
$minWithdraw = $settings['min_withdraw_amount'];
$networkFee = isset($settings['network_fee']) ? $settings['network_fee'] : 1;

// Get security settings
$conn = $GLOBALS['db']->getConnection();
$stmt = $conn->prepare("SELECT withdraw_verify, withdraw_password, email FROM users WHERE id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$security = $result->fetch_assoc();

$error_message = '';
$success = false;
$email_verify_required = false;
$email_verification_sent = false;

// Function to generate verification code
function generateVerificationCode() {
    return sprintf("%04d", mt_rand(0, 9999));
}

// Function to send verification email
function sendVerificationEmail($email, $code) {
    global $conn;
    
    // Get SMTP settings from database
    $smtp_settings = [];
    $sql = "SELECT setting_key, setting_value FROM site_settings WHERE setting_key IN 
            ('smtp_host', 'smtp_port', 'smtp_username', 'smtp_password', 'smtp_encryption', 'mail_from', 'mail_from_name')";
    
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $smtp_settings[$row['setting_key']] = $row['setting_value'];
        }
    } else {
        error_log("SMTP settings not found");
        return false;
    }
    
    require 'vendor/autoload.php';
    
    $mail = new PHPMailer(true);
    try {
        // SMTP settings
        $mail->isSMTP();
        $mail->Host = $smtp_settings['smtp_host'];
        $mail->SMTPAuth = true;
        $mail->Username = $smtp_settings['smtp_username'];
        $mail->Password = $smtp_settings['smtp_password'];
        $mail->SMTPSecure = $smtp_settings['smtp_encryption'];
        $mail->Port = $smtp_settings['smtp_port'];
        
        // Recipient and content settings
        $mail->setFrom($smtp_settings['mail_from'], $smtp_settings['mail_from_name']);
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = "Withdrawal Verification Code";
        
        // HTML email content
        $mail->Body = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Withdrawal Verification</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                    color: #333;
                    max-width: 600px;
                    margin: 0 auto;
                }
                .container {
                    background-color: #f9f9f9;
                    border-radius: 10px;
                    padding: 20px;
                    margin: 20px auto;
                    border: 1px solid #e0e0e0;
                }
                .header {
                    text-align: center;
                    padding-bottom: 15px;
                    border-bottom: 1px solid #e0e0e0;
                }
                .code-container {
                    background-color: #ffffff;
                    border-radius: 8px;
                    padding: 15px;
                    margin: 20px 0;
                    text-align: center;
                    border: 1px solid #e0e0e0;
                    font-size: 32px;
                    letter-spacing: 5px;
                    font-weight: bold;
                    color: #3F88F6;
                }
                .footer {
                    text-align: center;
                    font-size: 12px;
                    color: #999;
                    margin-top: 20px;
                }
                .note {
                    background-color: #fff8e1;
                    padding: 10px;
                    border-radius: 5px;
                    font-size: 13px;
                    border-left: 4px solid #ffc107;
                    margin: 15px 0;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h2>Withdrawal Verification</h2>
                </div>
                
                <p>Hello,</p>
                
                <p>Use the following code to verify your withdrawal request:</p>
                
                <div class="code-container">
                    ' . $code . '
                </div>
                
                <div class="note">
                    <strong>Note:</strong> This code is valid for only 2 minutes.
                </div>
                
                <p>If you did not request this action, please ignore this email or contact our support team.</p>
                
                <div class="footer">
                    &copy; ' . date('Y') . ' DigiMineX. All rights reserved.
                </div>
            </div>
        </body>
        </html>';
        
        // Send email
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mail sending failed: {$mail->ErrorInfo}");
        return false;
    }
}

// Process password verification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['withdraw_password'])) {
    if (password_verify($_POST['withdraw_password'], $security['withdraw_password'])) {
        // Security password is correct, check verification level
        if ($security['withdraw_verify'] == 2) {
            // Email verification required
            $email_verify_required = true;
            
            // Generate new verification code
            $verification_code = generateVerificationCode();
            $expiry_time = date('Y-m-d H:i:s', strtotime('+2 minutes'));
            
            // Save verification code to database
            $stmt = $conn->prepare("UPDATE users SET withdraw_verify_code = ?, withdraw_verify_expires = ? WHERE id = ?");
            $stmt->bind_param('ssi', $verification_code, $expiry_time, $user_id);
            
            if ($stmt->execute()) {
                // Send verification code via email
                if (sendVerificationEmail($security['email'], $verification_code)) {
                    $_SESSION['withdraw_password_verified'] = true;
                    $email_verification_sent = true;
                } else {
                    $error_message = "An error occurred while sending the verification code. Please try again.";
                }
            } else {
                $error_message = "An error occurred while generating verification code. Please try again.";
            }
        } else {
            // Password verification is sufficient, process withdrawal
            processWithdrawal();
        }
    } else {
        $error_message = "Incorrect security password. Please try again.";
    }
}

// Process email verification code
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verification_code']) && isset($_SESSION['withdraw_password_verified'])) {
    $entered_code = $_POST['verification_code'];
    
    // Get verification code and expiry time from database
    $stmt = $conn->prepare("SELECT withdraw_verify_code, withdraw_verify_expires FROM users WHERE id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $code_data = $result->fetch_assoc();
    
    $current_time = date('Y-m-d H:i:s');
    
    // Check verification code
    if (empty($code_data['withdraw_verify_code']) || $code_data['withdraw_verify_code'] != $entered_code) {
        $error_message = "Invalid verification code. Please try again.";
    } 
    // Check expiry time
    else if ($current_time > $code_data['withdraw_verify_expires']) {
        $error_message = "Verification code has expired. Please request a new code.";
    } 
    else {
        // Verification successful, process withdrawal
        processWithdrawal();
    }
}

// Function to process withdrawal
function processWithdrawal() {
    global $user_id, $minWithdraw, $networkFee, $user, $error_message, $success, $conn;
    
    if (isset($_SESSION['withdraw_data'])) {
        $amount = (float)$_SESSION['withdraw_data']['amount'];
        $address = trim($_SESSION['withdraw_data']['trc20_address']);
        
        // Basic validation
        if ($amount < $minWithdraw) {
            $error_message = "Minimum withdrawal amount must be $minWithdraw USDT.";
        } else if ($amount + $networkFee > $user['balance']) {
            $error_message = "Insufficient balance. Total withdrawal amount: " . ($amount + $networkFee) . " USDT";
        } else if (empty($address)) {
            $error_message = "TRC20 wallet address is required.";
        } else {
            try {
                // Process the withdrawal
                $result = processWithdrawalWithFees($user_id, $amount, $address, 0, $networkFee);
                
                if ($result['success']) {
                    // Clear session data
                    unset($_SESSION['withdraw_data']);
                    unset($_SESSION['withdraw_password_verified']);
                    
                    // Clear verification code in database
                    $stmt = $conn->prepare("UPDATE users SET withdraw_verify_code = NULL, withdraw_verify_expires = NULL WHERE id = ?");
                    $stmt->bind_param('i', $user_id);
                    $stmt->execute();
                    
                    // Set success flag
                    $success = true;
                } else {
                    $error_message = $result['message'];
                }
            } catch (Exception $e) {
                $error_message = "An error occurred during processing: " . $e->getMessage();
            }
        }
    } else {
        $error_message = "Withdrawal information not found. Please try again.";
    }
}

// Load translations
$lang = isset($_SESSION['language']) ? $_SESSION['language'] : 'en';
$translations = [];
$translation_file = "lang/{$lang}/withdraw.json";

if (file_exists($translation_file)) {
    $translations = json_decode(file_get_contents($translation_file), true);
} else {
    // Fallback to English if translation file doesn't exist
    $translation_file = "lang/en/withdraw.json";
    if (file_exists($translation_file)) {
        $translations = json_decode(file_get_contents($translation_file), true);
    }
}

// Translation helper function
function trans($key, $default = '') {
    global $translations;
    return isset($translations[$key]) ? $translations[$key] : ($default ?: $key);
}

// English translations
$translations = [
    'withdraw' => [
        'page_title' => 'Withdraw Funds',
        'verification_title' => 'Withdraw Verification',
        'email_verification' => 'Email Verification',
        'security_check' => 'Security Check',
        'security_message' => 'We have sent a 4-digit verification code to your email. Please enter the code to continue.',
        'code_sent' => 'Verification code was sent to',
        'verification_code' => '4-Digit Verification Code',
        'time_remaining' => 'Time remaining:',
        'verify_and_withdraw' => 'Verify Code & Withdraw',
        'back' => 'Go Back',
        'withdrawal_verification' => 'Withdrawal Verification',
        'security' => 'Security',
        'security_message_password' => 'Enter your security password to continue with your withdrawal.',
        'security_password' => 'Withdrawal Security Password',
        'security_password_placeholder' => 'Enter your security password',
        'verify_password' => 'Verify Password',
        'withdrawal_summary' => 'Withdrawal Summary',
        'amount' => 'Amount',
        'network_fee' => 'Network Fee',
        'total_amount' => 'Total Amount',
        'trc20_address' => 'TRC20 Address',
        'success_title' => 'Withdrawal Request Received!',
        'success_message' => 'Your request will be processed after review. You can track the status in "My Transactions".',
        'my_transactions' => 'My Transactions',
        'new_request' => 'New Request'
    ]
];

// Helper function for translations
function t($key, $default = '') {
    global $translations;
    $parts = explode('.', $key);
    if (count($parts) === 2 && isset($translations[$parts[0]][$parts[1]])) {
        return $translations[$parts[0]][$parts[1]];
    }
    return $default ?: $key;
}

// Page title
$page_title = t('withdraw.verification_title', 'Withdrawal Verification');
include 'includes/mobile-header.php';
?>

<div class="withdraw-page">
    <h1 class="page-title"><?= t('withdraw.verification_title', 'Withdrawal Verification') ?></h1>
    
    <?php if ($success): ?>
    <!-- Success Message -->
    <div class="success-container">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <h2><?= t('withdraw.success_title', 'Withdrawal Request Received!') ?></h2>
        <p><?= t('withdraw.success_message', 'Your request will be processed after review. You can track the status in "My Transactions".') ?></p>
        
        <div class="action-buttons">
            <a href="transactions.php?filter=withdraw" class="btn btn-primary">
                <i class="fas fa-list"></i> <?= t('withdraw.my_transactions', 'My Transactions') ?>
            </a>
            <a href="withdraw.php" class="btn btn-outline">
                <i class="fas fa-plus"></i> <?= t('withdraw.new_request', 'New Request') ?>
            </a>
        </div>
    </div>
    <?php elseif ($email_verify_required && $email_verification_sent): ?>
    <!-- Email Verification Form -->
    <div class="card withdraw-form-card">
        <h2><i class="fas fa-envelope"></i> <?= t('withdraw.email_verification', 'Email Verification') ?></h2>
        
        <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            <?= $error_message ?>
        </div>
        <?php endif; ?>
        
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <strong><?= t('withdraw.security_check', 'Security Check') ?>:</strong> <?= t('withdraw.security_message', 'We have sent a 4-digit verification code to your email. Please enter the code to continue.') ?>
        </div>
        
        <p class="verification-email-sent">
            <?= t('withdraw.code_sent', 'Verification code was sent to') ?> <strong><?= maskEmail($security['email']) ?></strong>
        </p>
        
        <form method="POST" action="verify_withdraw.php" id="emailVerifyForm">
            <div class="form-group">
                <label for="verification_code"><?= t('withdraw.verification_code', '4-Digit Verification Code') ?></label>
                <div class="otp-input-container">
                    <input type="text" id="verification_code" name="verification_code" maxlength="4" pattern="[0-9]{4}" placeholder="0000" required autofocus>
                </div>
                <div class="code-expiry" id="codeExpiryTimer">
                    <?= t('withdraw.time_remaining', 'Time remaining') ?>: <span id="timer">02:00</span>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">
                <i class="fas fa-check-circle"></i> <?= t('withdraw.verify_and_withdraw', 'Verify Code & Withdraw') ?>
            </button>
            
            <a href="withdraw.php" class="btn btn-outline btn-block mt-3">
                <i class="fas fa-arrow-left"></i> <?= t('withdraw.back', 'Go Back') ?>
            </a>
        </form>
    </div>
    <?php else: ?>
    <!-- Password Verification Form -->
    <div class="card withdraw-form-card">
        <h2><i class="fas fa-lock"></i> <?= t('withdraw.withdrawal_verification', 'Withdrawal Verification') ?></h2>
        
        <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            <?= $error_message ?>
        </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['withdraw_data'])): ?>
        <div class="withdrawal-summary">
            <div class="summary-item">
                <span class="summary-label"><?= t('withdraw.amount', 'Amount') ?>:</span>
                <span class="summary-value"><?= number_format($_SESSION['withdraw_data']['amount'], 2) ?> USDT</span>
            </div>
            <div class="summary-item">
                <span class="summary-label"><?= t('withdraw.network_fee', 'Network Fee') ?>:</span>
                <span class="summary-value"><?= number_format($networkFee, 2) ?> USDT</span>
            </div>
            <div class="summary-item total">
                <span class="summary-label"><?= t('withdraw.total_amount', 'Total Amount') ?>:</span>
                <span class="summary-value"><?= number_format($_SESSION['withdraw_data']['amount'] + $networkFee, 2) ?> USDT</span>
            </div>
            <div class="summary-item">
                <span class="summary-label"><?= t('withdraw.trc20_address', 'TRC20 Address') ?>:</span>
                <span class="summary-value address"><?= htmlspecialchars($_SESSION['withdraw_data']['trc20_address']) ?></span>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <strong><?= t('withdraw.security', 'Security') ?>:</strong> <?= t('withdraw.security_message_password', 'Enter your security password to continue with your withdrawal.') ?>
        </div>
        
        <form method="POST" action="verify_withdraw.php" id="passwordVerifyForm">
            <div class="form-group">
                <label for="withdraw_password"><?= t('withdraw.security_password', 'Withdrawal Security Password') ?></label>
                <input type="password" id="withdraw_password" name="withdraw_password" placeholder="<?= t('withdraw.security_password_placeholder', 'Enter your security password') ?>" required autofocus>
            </div>
            
            <button type="submit" name="verify_submit" class="btn btn-primary btn-block">
                <i class="fas fa-check-circle"></i> <?= t('withdraw.verify_password', 'Verify Password') ?>
            </button>
            
            <a href="withdraw.php" class="btn btn-outline btn-block mt-3">
                <i class="fas fa-arrow-left"></i> <?= t('withdraw.back', 'Go Back') ?>
            </a>
        </form>
    </div>
    <?php endif; ?>
</div>

<style>
:root {
    --primary-color: #3F88F6;
    --primary-dark: #2d6ed6;
    --primary-color-rgb: 63, 136, 246;
    --danger-color: #dc3545;
    --warning-color: #ffc107;
    --success-color: #28c76f;
    --info-color: #17a2b8;
    --gray-light: #f8f9fa;
    --gray: #6e6b7b;
    --border-color: #ddd;
}

.withdraw-page {
    padding: 15px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #333;
    background-color: #f8f9fa;
}

.page-title {
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 20px;
    color: var(--primary-color);
}

/* Cards */
.card {
    background-color: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    max-width: 600px;
    margin: 0 auto;
}

.card h2 {
    font-size: 1.3rem;
    font-weight: 600;
    margin: 0 0 20px;
    display: flex;
    align-items: center;
}

.card h2 i {
    margin-right: 10px;
    color: var(--primary-color);
}

/* Form Styles */
.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    font-size: 0.9rem;
}

.form-group input {
    padding: 12px 15px;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    font-size: 1rem;
    width: 100%;
    outline: none;
    transition: border-color 0.3s;
}

.form-group input:focus {
    border-color: var(--primary-color);
}

/* OTP Input Styles */
.otp-input-container {
    display: flex;
    justify-content: center;
    margin: 15px 0;
}

#verification_code {
    width: 180px;
    height: 60px;
    font-size: 24px;
    letter-spacing: 10px;
    padding-left: 15px;
    text-align: center;
    font-weight: bold;
}

.code-expiry {
    text-align: center;
    color: var(--gray);
    font-size: 0.9rem;
    margin-top: 10px;
}

#timer {
    font-weight: bold;
    color: var(--primary-color);
}

.verification-email-sent {
    text-align: center;
    margin: 20px 0;
    color: var(--gray);
}

/* Alerts */
.alert {
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 0.9rem;
    display: flex;
    align-items: flex-start;
}

.alert i {
    margin-right: 10px;
    font-size: 1.1rem;
    margin-top: 2px;
}

.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
}

.alert-info {
    background-color: #d1ecf1;
    color: #0c5460;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
}

/* Buttons */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 12px 20px;
    border-radius: 8px;
    font-weight: 500;
    text-decoration: none;
    cursor: pointer;
    transition: background-color 0.3s, transform 0.1s;
    border: none;
    font-size: 1rem;
}

.btn:active {
    transform: translateY(1px);
}

.btn i {
    margin-right: 8px;
}

.btn-block {
    display: flex;
    width: 100%;
}

.btn-primary {
    background-color: var(--primary-color);
    color: white;
}

.btn-primary:hover {
    background-color: var(--primary-dark);
}

.btn-outline {
    background-color: transparent;
    border: 1px solid var(--primary-color);
    color: var(--primary-color);
    margin-top: 10px;
}

.btn-outline:hover {
    background-color: rgba(var(--primary-color-rgb), 0.05);
}

.mt-3 {
    margin-top: 15px;
}

/* Success Container */
.success-container {
    text-align: center;
    background-color: white;
    border-radius: 12px;
    padding: 30px 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    max-width: 600px;
    margin: 0 auto;
}

.success-icon {
    font-size: 4rem;
    color: var(--success-color);
    margin-bottom: 20px;
}

.success-container h2 {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 15px;
}

.success-container p {
    color: var(--gray);
    margin-bottom: 25px;
}

.action-buttons {
    display: flex;
    gap: 10px;
    justify-content: center;
}

/* Withdrawal Summary */
.withdrawal-summary {
    background-color: var(--gray-light);
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 20px;
}

.summary-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
    font-size: 0.9rem;
}

.summary-item.total {
    margin-top: 8px;
    padding-top: 8px;
    border-top: 1px dashed var(--border-color);
    font-weight: 600;
}

.summary-value.address {
    word-break: break-all;
    font-family: monospace;
    font-size: 0.8rem;
}

@media (max-width: 500px) {
    .action-buttons {
        flex-direction: column;
    }
    
    .page-title {
        font-size: 1.5rem;
    }
    
    .card {
        padding: 15px;
    }
    
    .form-group input {
        padding: 10px 12px;
    }
    
    .btn {
        padding: 10px 16px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password verification form - Add loading state
    const passwordVerifyForm = document.getElementById('passwordVerifyForm');
    if (passwordVerifyForm) {
        passwordVerifyForm.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';
            }
        });
    }
    
    // Email verification form - Timer
    const timerElement = document.getElementById('timer');
    if (timerElement) {
        let timeLeft = 120; // 2 minutes in seconds
        
        const countdownTimer = setInterval(function() {
            timeLeft--;
            
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            
            // Format the time as MM:SS
            timerElement.textContent = minutes.toString().padStart(2, '0') + ':' + 
                                       seconds.toString().padStart(2, '0');
            
            if (timeLeft <= 0) {
                clearInterval(countdownTimer);
                timerElement.textContent = "00:00";
                timerElement.style.color = "var(--danger-color)";
                
                // Add a message that the code has expired
                const expiryMsg = document.createElement('div');
                expiryMsg.className = 'alert alert-warning';
                expiryMsg.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Verification code has expired. Please refresh the page and try again.';
                
                const formElement = document.getElementById('emailVerifyForm');
                formElement.prepend(expiryMsg);
                
                // Disable the submit button
                const submitBtn = formElement.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                }
            }
        }, 1000);
        
        // Number input formatting - allow only numbers
        const codeInput = document.getElementById('verification_code');
        if (codeInput) {
            codeInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        }
    }
});

<?php
// Email masking function
function maskEmail($email) {
    if (empty($email)) return '';
    
    $parts = explode('@', $email);
    if (count($parts) != 2) return $email;
    
    $name = $parts[0];
    $domain = $parts[1];
    
    $nameLength = strlen($name);
    if ($nameLength <= 2) {
        $maskedName = $name[0] . str_repeat('*', $nameLength - 1);
    } else {
        $maskedName = $name[0] . str_repeat('*', $nameLength - 2) . $name[$nameLength - 1];
    }
    
    $domainParts = explode('.', $domain);
    $domainName = $domainParts[0];
    $domainExt = implode('.', array_slice($domainParts, 1));
    
    $domainNameLength = strlen($domainName);
    if ($domainNameLength <= 2) {
        $maskedDomainName = $domainName;
    } else {
        $maskedDomainName = $domainName[0] . str_repeat('*', $domainNameLength - 2) . $domainName[$domainNameLength - 1];
    }
    
    return $maskedName . '@' . $maskedDomainName . '.' . $domainExt;
}
?>
</script>

<?php include 'includes/mobile-footer.php'; ?>