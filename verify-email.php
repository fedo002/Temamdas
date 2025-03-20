<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get user and connection details
$user_id = $_SESSION['user_id'];
$conn = $GLOBALS['db']->getConnection();

$error_message = '';
$success_message = '';
$time_remaining = '';
$code_expired = false;

// Get user info - Hata düzeltildi: SQL sorgusu kontrolü eklendi
$stmt = $conn->prepare("SELECT email, email_verify, email_verify_code, email_verify_codetime, username FROM users WHERE id = ?");
if ($stmt === false) {
    die("SQL Hazırlama Hatası: " . $conn->error);
}

$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header('Location: login.php');
    exit;
}

$user = $result->fetch_assoc();

// Check if email is already verified
if ($user['email_verify'] == 1) {
    header('Location: profile.php');
    exit;
}

// Function to generate 8-digit code
function generateVerificationCode() {
    return sprintf("%04d", mt_rand(0, 9999));
}

function sendVerificationEmail($email, $username, $code) {
    global $conn;
    
    //Load Composer's autoloader
    require 'vendor/autoload.php';
    // SMTP ayarlarını veritabanından çek
    $smtp_settings = [];
    $sql = "SELECT setting_key, setting_value FROM site_settings WHERE setting_key IN 
            ('smtp_host', 'smtp_port', 'smtp_username', 'smtp_password', 'smtp_encryption', 'mail_from', 'mail_from_name')";
    
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $smtp_settings[$row['setting_key']] = $row['setting_value'];
        }
    } else {
        error_log("SMTP ayarları bulunamadı");
        return false;
    }
    
    
    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);
    try {
       
        // SMTP ayarları
        $mail->isSMTP();
        $mail->Host = $smtp_settings['smtp_host'];
        $mail->SMTPAuth = true;
        $mail->Username = $smtp_settings['smtp_username'];
        $mail->Password = $smtp_settings['smtp_password'];
        $mail->SMTPSecure = $smtp_settings['smtp_encryption'];
        $mail->Port = $smtp_settings['smtp_port'];
        
        // Alıcı ve içerik ayarları
        $mail->setFrom($smtp_settings['mail_from'], $smtp_settings['mail_from_name']);
        $mail->addAddress($email, $username);
        $mail->isHTML(true);
        $mail->Subject = "Email Verification Code";
        
        // HTML email içeriği
        $mail->Body = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Email Verification</title>
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
                    font-size: 24px;
                    letter-spacing: 2px;
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
                    <h2>Email Verification</h2>
                </div>
                
                <p>Hello ' . htmlspecialchars($username) . ',</p>
                
                <p>Thank you for registering with us. To complete your registration, please use the verification code below:</p>
                
                <div class="code-container">
                    ' . $code . '
                </div>
                
                <div class="note">
                    <strong>Note:</strong> This code is valid for 30 minutes only.
                </div>
                
                <p>If you didn\'t request this verification, please ignore this email or contact our support team.</p>
                
                <div class="footer">
                    &copy; ' . date('Y') . ' Kazanç Ağacı. All rights reserved.
                </div>
            </div>
        </body>
        </html>';
        
        // E-postayı gönder
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mail sending failed: {$mail->ErrorInfo}");
        return false;
    }
}

// Process new code generation
if (isset($_POST['resend_code'])) {
    $new_code = generateVerificationCode();
    $current_time = date('Y-m-d H:i:s');
    
    $stmt = $conn->prepare("UPDATE users SET email_verify_code = ?, email_verify_codetime = ? WHERE id = ?");
    if ($stmt === false) {
        die("SQL Hazırlama Hatası: " . $conn->error);
    }
    
    $stmt->bind_param('ssi', $new_code, $current_time, $user_id);
    
    if ($stmt->execute()) {
        if (sendVerificationEmail($user['email'], $user['username'], $new_code)) {
            $success_message = "A new verification code has been sent to your email address.";
            // Update user data
            $user['email_verify_code'] = $new_code;
            $user['email_verify_codetime'] = $current_time;
        } else {
            $error_message = "Failed to send verification email. Please try again.";
        }
    } else {
        $error_message = "Failed to generate a new code. Please try again.";
    }
}

// Process verification code submission
if (isset($_POST['verify_code'])) {
    $entered_code = $_POST['verification_code'];
    
    // Check if code is empty
    if (empty($entered_code)) {
        $error_message = "Please enter the verification code.";
    } 
    // Check if code is numeric and 8 digits
    elseif (!is_numeric($entered_code) || strlen($entered_code) != 4) {
        $error_message = "Verification code must be 4 digits.";
    } 
    else {
        // Check if code matches and is not expired
        $code_time = new DateTime($user['email_verify_codetime']);
        $current_time = new DateTime();
        $interval = $current_time->diff($code_time);
        $minutes_passed = ($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i;
        
        if ($minutes_passed > 30) {
            $error_message = "Verification code has expired. Please request a new one.";
            $code_expired = true;
        } 
        elseif ($entered_code != $user['email_verify_code']) {
            $error_message = "Invalid verification code. Please try again.";
        } 
        else {
            // Update user as verified
            $stmt = $conn->prepare("UPDATE users SET email_verify = 1 WHERE id = ?");
            if ($stmt === false) {
                die("SQL Hazırlama Hatası: " . $conn->error);
            }
            
            $stmt->bind_param('i', $user_id);
            
            if ($stmt->execute()) {
                // Redirect to profile page
                header('Location: profile.php?verified=1');
                exit;
            } else {
                $error_message = "Failed to verify email. Please try again.";
            }
        }
    }
}

// Calculate time remaining for code validity
if (!empty($user['email_verify_codetime'])) {
    $code_time = new DateTime($user['email_verify_codetime']);
    $current_time = new DateTime();
    $interval = $current_time->diff($code_time);
    $minutes_passed = ($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i;
    
    if ($minutes_passed > 30) {
        $code_expired = true;
        $time_remaining = "Code expired";
    } else {
        $time_remaining = 30 - $minutes_passed . " minutes";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #3F88F6;
            --primary-dark: #2d6ecd;
            --secondary-color: #6c757d;
            --light-color: #f8f9fa;
            --danger-color: #dc3545;
            --success-color: #28a745;
            --warning-color: #ffc107;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            background-color: #f0f2f5;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            width: 100%;
            max-width: 500px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            padding: 25px 20px;
            text-align: center;
            position: relative;
        }
        
        .header h1 {
            margin-bottom: 10px;
            font-size: 24px;
            font-weight: 600;
        }
        
        .header p {
            font-size: 16px;
            opacity: 0.9;
        }
        
        .email-icon {
            width: 80px;
            height: 80px;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 36px;
        }
        
        .content {
            padding: 30px;
        }
        
        .info-box {
            background-color: var(--light-color);
            border-left: 4px solid var(--primary-color);
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 25px;
        }
        
        .info-box.warning {
            border-left-color: var(--warning-color);
        }
        
        .email-display {
            background-color: #e9ecef;
            padding: 10px 15px;
            border-radius: 5px;
            font-weight: 500;
            margin: 15px 0;
            display: flex;
            align-items: center;
        }
        
        .email-display i {
            margin-right: 10px;
            color: var(--secondary-color);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #495057;
        }
        
        .code-input {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        
        .digit-input {
            width: 45px;
            height: 50px;
            font-size: 24px;
            text-align: center;
            border: 2px solid #ced4da;
            border-radius: 5px;
            background-color: white;
            transition: all 0.2s ease;
        }
        
        .digit-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(63, 136, 246, 0.25);
            outline: none;
        }
        
        .verification-code {
            width: 100%;
            height: 50px;
            font-size: 20px;
            letter-spacing: 5px;
            text-align: center;
            border: 2px solid #ced4da;
            border-radius: 5px;
            background-color: white;
            transition: all 0.2s ease;
        }
        
        .verification-code:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(63, 136, 246, 0.25);
            outline: none;
        }
        
        .btn {
            display: inline-block;
            font-weight: 500;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            user-select: none;
            border: 1px solid transparent;
            padding: 12px 20px;
            font-size: 16px;
            line-height: 1.5;
            border-radius: 5px;
            transition: all 0.15s ease-in-out;
            cursor: pointer;
            width: 100%;
        }
        
        .btn-primary {
            color: #fff;
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }
        
        .btn-outline {
            color: var(--secondary-color);
            background-color: transparent;
            border-color: #ced4da;
        }
        
        .btn-outline:hover {
            color: #212529;
            background-color: #e9ecef;
        }
        
        .resend-section {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            text-align: center;
        }
        
        .resend-timer {
            font-size: 14px;
            color: var(--secondary-color);
            margin-bottom: 15px;
        }
        
        .alert {
            padding: 12px 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 5px;
        }
        
        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
        
        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
        
        @media (max-width: 500px) {
            .container {
                border-radius: 0;
            }
            
            .content {
                padding: 20px;
            }
            
            .digit-input {
                width: 40px;
                height: 45px;
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="email-icon">
                <i class="fas fa-envelope"></i>
            </div>
            <h1>Verify Your Email</h1>
            <p>We've sent a verification code to your email</p>
        </div>
        
        <div class="content">
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            
            <div class="info-box <?php echo $code_expired ? 'warning' : ''; ?>">
                <?php if ($code_expired): ?>
                    <i class="fas fa-exclamation-triangle"></i> Your verification code has expired. Please request a new one.
                <?php else: ?>
                    <p>We've sent a verification code to your email address. Enter the code below to verify your account.</p>
                <?php endif; ?>
            </div>
            
            <div class="email-display">
                <i class="fas fa-at"></i> <?php echo htmlspecialchars($user['email']); ?>
            </div>
            
            <form method="post" action="">
                <div class="form-group">
                    <label for="verification_code">Enter 4-digit verification code</label>
                    <input type="text" name="verification_code" id="verification_code" class="verification-code" maxlength="4" placeholder="0000" required <?php echo $code_expired ? 'disabled' : ''; ?>>
                </div>
                
                <button type="submit" name="verify_code" class="btn btn-primary" <?php echo $code_expired ? 'disabled' : ''; ?>>
                    <i class="fas fa-check-circle"></i> Verify Email
                </button>
            </form>
            
            <div class="resend-section">
                <?php if (!empty($time_remaining) && !$code_expired): ?>
                    <div class="resend-timer">
                        <i class="fas fa-clock"></i> Time remaining: <?php echo $time_remaining; ?>
                    </div>
                <?php endif; ?>
                
                <form method="post" action="">
                    <button type="submit" name="resend_code" class="btn btn-outline">
                        <i class="fas fa-redo"></i> <?php echo $code_expired ? 'Request New Code' : 'Resend Code'; ?>
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        // Focus on first input on page load
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('verification_code').focus();
        });
        
        // Allow only numbers in the verification code field
        document.getElementById('verification_code').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>
</body>
</html>