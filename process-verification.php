<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Hata düzeltildi: Başlık açıklaması değiştirildi
// Email verification processing script

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'User not logged in'
    ]);
    exit;
}

// Get user and connection details
$user_id = $_SESSION['user_id'];
$conn = $GLOBALS['db']->getConnection();

// Function to generate 8-digit code
function generateVerificationCode() {
    return sprintf("%08d", mt_rand(0, 99999999));
}

// Function to send verification email
function sendVerificationEmail($email, $username, $code) {
    $subject = "Email Verification Code";
    
    // HTML email body
    $htmlMessage = '
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
                &copy; ' . date('Y') . ' Your Company Name. All rights reserved.
            </div>
        </div>
    </body>
    </html>';
    
    // Email headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: Your Company <noreply@yourcompany.com>' . "\r\n";
    
    // Send email
    return mail($email, $subject, $htmlMessage, $headers);
}

// Get user info - Hata düzeltildi: SQL sorgusu kontrolü eklendi
$stmt = $conn->prepare("SELECT email, email_verify, username FROM users WHERE id = ?");
if ($stmt === false) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $conn->error
    ]);
    exit;
}

$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'User not found'
    ]);
    exit;
}

$user = $result->fetch_assoc();

// Check if email is already verified
if ($user['email_verify'] == 1) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Email already verified'
    ]);
    exit;
}

// Generate new verification code
$verification_code = generateVerificationCode();
$current_time = date('Y-m-d H:i:s');

// Save verification code and time to database - Hata düzeltildi: SQL sorgusu kontrolü eklendi
$stmt = $conn->prepare("UPDATE users SET email_verify_code = ?, email_verify_codetime = ? WHERE id = ?");
if ($stmt === false) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $conn->error
    ]);
    exit;
}

$stmt->bind_param('ssi', $verification_code, $current_time, $user_id);

if ($stmt->execute()) {
    // Send verification email
    if (sendVerificationEmail($user['email'], $user['username'], $verification_code)) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Verification code sent successfully'
        ]);
        
        // Redirect to verification page if not AJAX request
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
            header('Location: verify-email.php');
            exit;
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to send verification email'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to generate verification code: ' . $stmt->error
    ]);
}