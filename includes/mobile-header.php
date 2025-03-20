<?php
// Tarayıcı önbelleğini devre dışı bırak
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Email verification check
if (isset($_SESSION['user_id'])) {
    try {
        // Get database connection - assuming $GLOBALS['db'] is available
        if (isset($GLOBALS['db'])) {
            $conn = $GLOBALS['db']->getConnection();
            
            // Check email verification status
            $user_id = $_SESSION['user_id'];
            $stmt = $conn->prepare("SELECT email_verify FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                
                // Redirect if email is not verified (email_verify = 0)
                if ((int)$user['email_verify'] === 0) {
                    // Skip redirect for the verification page itself and logout page
                    $current_page = basename($_SERVER['PHP_SELF']);
                    $excluded_pages = ['verify-email.php', 'logout.php', 'login.php'];
                    
                    if (!in_array($current_page, $excluded_pages)) {
                        header("Location: verify-email.php");
                        exit;
                    }
                }
            }
        }
    } catch (Exception $e) {
        // Silent fail
        error_log("Email verification check error: " . $e->getMessage());
    }
}

// Notification count for logged in users
$unread_notification_count = 0;
if (isset($_SESSION['user_id']) && function_exists('getUserNotifications')) {
    try {
        $notificationData = getUserNotifications($_SESSION['user_id']);
        $unread_notification_count = $notificationData['unread_count'] ?? 0;
    } catch (Exception $e) {
        // Silent fail
    }
}


// Set page title if not already set
if (!isset($page_title)) {
    $page_title = APP_NAME;
}
?>
<!DOCTYPE html>
<html lang="<?= $current_lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#7367f0">
    <title><?= $page_title ?> | <?= APP_NAME ?> Mobile</title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="assets/images/apple-touch-icon.png">
    
    
    <!-- Prevent phone number detection -->
    <meta name="format-detection" content="telephone=no">
    
    <script defer src="assets/js/all.js"></script>
    <link href="assets/css/fontawesome.css" rel="stylesheet" />
    <link href="assets/css/brands.css" rel="stylesheet" />
    <link href="assets/css/solid.css" rel="stylesheet" />
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Mobile CSS -->
    <link rel="stylesheet" href="assets/css/mobile.css">
    <link rel="stylesheet" href="assets/css/translation-system.css">

	
	<script async src="https://static.linguise.com/script-js/switcher.bundle.js?d=pk_YNgK025ZNYdLQVprpwbLmTm0DljVi7ht"></script>
	
    <!-- Page-specific CSS -->
    <?php if (basename($_SERVER['PHP_SELF']) === 'login.php' || basename($_SERVER['PHP_SELF']) === 'register.php'): ?>
    <link rel="stylesheet" href="assets/css/mobile-auth.css">
    <?php elseif (basename($_SERVER['PHP_SELF']) === 'profile.php'): ?>
    <link rel="stylesheet" href="assets/css/mobile-profile.css">
    <?php elseif (basename($_SERVER['PHP_SELF']) === 'packages.php'): ?>
    <link rel="stylesheet" href="assets/css/mobile-packages.css">
    <?php elseif (basename($_SERVER['PHP_SELF']) === 'transactions.php'): ?>
    <link rel="stylesheet" href="assets/css/mobile-transactions.css">
    <?php elseif (basename($_SERVER['PHP_SELF']) === 'index.php'): ?>
    <link rel="stylesheet" href="assets/css/mobile-index.css">
    <?php endif; ?>
</head>
	
		<style>
	.linguise_switcher .linguise_switcher_popup {
		background: #121212;
		color: #3a86ff;
}
	</style>
<body>
    <!-- Mobile Header -->
    <header class="mobile-header">
        <div class="header-container">
            <a class="header-logo" href="index.php">
                <img src="assets/images/logo.png" alt="<?= APP_NAME ?>" height="60">
            </a>
        </div>

    </header>

    <!-- Main Content Container -->
    <main class="mobile-content">
        <!-- Page content will be here -->