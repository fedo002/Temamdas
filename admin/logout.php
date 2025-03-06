<?php
// admin/logout.php
session_start();

// Log the logout if admin was logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    require_once '../includes/config.php';
    
    // Log the logout
    $logStmt = $conn->prepare("INSERT INTO admin_login_logs (admin_id, ip_address, user_agent, status) VALUES (?, ?, ?, 'logout')");
    $adminId = $_SESSION['admin_id'];
    $ip = $_SERVER['REMOTE_ADDR'];
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    $logStmt->bind_param("iss", $adminId, $ip, $userAgent);
    $logStmt->execute();
}

// Clear all session variables
$_SESSION = array();

// Destroy the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Redirect to login page
header('Location: login.php');
exit;
?>