<?php
// Sayfa başına ekleyin
require_once 'includes/config.php';
require_once 'includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=profile.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user = getUserDetails($user_id);

// TEMEL BİR TEST SAYFASI
echo '<!DOCTYPE html>
<html>
<head>
    <title>Profil Testi</title>
</head>
<body>
    <h1>Profil Bilgileri Testi</h1>
    
    <form>
        <div>
            <label>Username:</label>
            <input type="text" value="' . htmlspecialchars($user['username']) . '" readonly>
        </div>
        <div>
            <label>Email:</label>
            <input type="email" value="' . htmlspecialchars($user['email']) . '" readonly>
        </div>
        <div>
            <label>Phone:</label>
            <input type="tel" value="' . htmlspecialchars($user['phone']) . '">
        </div>
    </form>
    
    <pre>' . print_r($user, true) . '</pre>
</body>
</html>';
exit; // Ana sayfanın geri kalanının işlenmesini durdur
?>