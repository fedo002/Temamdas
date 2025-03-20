<?php
session_start();
require_once '../includes/config.php';

if (isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

$conn = $GLOBALS['db']->getConnection();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Hata ayıklama için şifre bilgilerini görelim (geliştirme ortamında geçici olarak kullanın)
    // echo "Girilen şifre: " . $password . "<br>";
    
    if (empty($username) || empty($password)) {
        $error = 'Lütfen kullanıcı adı ve şifre girin';
    } else {
        // Kullanıcıyı veritabanından çekelim
        $stmt = $conn->prepare("SELECT id, username, password FROM admins WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Hata ayıklama: Veritabanındaki hash'i görelim (geliştirme ortamında)
            // echo "Veritabanındaki hash: " . $user['password'] . "<br>";
            
            // Şifre doğrulama
            $passwordCheck = password_verify($password, $user['password']);
            // echo "Şifre doğrulama sonucu: " . ($passwordCheck ? "Doğru" : "Yanlış") . "<br>";
            
            if ($passwordCheck) {
                // Giriş başarılı
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_username'] = $user['username'];
                
                // Başarılı giriş logunu kaydet
                $logStmt = $conn->prepare("INSERT INTO admin_login_logs (admin_id, ip_address, user_agent, status) VALUES (?, ?, ?, 'success')");
                $ip = $_SERVER['REMOTE_ADDR'];
                $userAgent = $_SERVER['HTTP_USER_AGENT'];
                $logStmt->bind_param("iss", $user['id'], $ip, $userAgent);
                $logStmt->execute();
                
                header('Location: index.php');
                exit;
            } else {
                $error = 'Geçersiz şifre';
                
                // Başarısız girişi logla
                $logStmt = $conn->prepare("INSERT INTO admin_login_logs (username, ip_address, user_agent, status) VALUES (?, ?, ?, 'failed')");
                $ip = $_SERVER['REMOTE_ADDR'];
                $userAgent = $_SERVER['HTTP_USER_AGENT'];
                $logStmt->bind_param("sss", $username, $ip, $userAgent);
                $logStmt->execute();
            }
        } else {
            $error = 'Kullanıcı bulunamadı';
            
            // Geçersiz kullanıcı logla
            $logStmt = $conn->prepare("INSERT INTO admin_login_logs (username, ip_address, user_agent, status) VALUES (?, ?, ?, 'invalid_user')");
            $ip = $_SERVER['REMOTE_ADDR'];
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
            $logStmt->bind_param("sss", $username, $ip, $userAgent);
            $logStmt->execute();
        }
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="admin-login-page">
    <div class="admin-login-container">
        <div class="admin-login-box">
            <div class="admin-login-logo">
                <img src="../assets/images/logo.png" alt="Logo">
            </div>
            <h2>Admin Panel</h2>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="" class="admin-login-form">
                <div class="form-group">
                    <label for="username">Kullanıcı Adı</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Şifre</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">Giriş Yap</button>
            </form>
        </div>
    </div>
    
    <script src="../assets/js/admin.js"></script>
</body>
</html>