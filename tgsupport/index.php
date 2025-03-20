<?php
// Temsilci Giriş Sayfası
session_start();
require_once 'config.php';

// Zaten giriş yapmışsa dashboard'a yönlendir
if (isset($_SESSION['support_rep_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

// Form gönderildiğinde
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = "Kullanıcı adı ve şifre gereklidir!";
    } else {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($conn->connect_error) {
            $error = "Veritabanı bağlantı hatası!";
            logError("DB Connection Error: " . $conn->connect_error);
        } else {
            try {
                // Temsilciyi ara
                $stmt = $conn->prepare("SELECT id, username, password, status FROM support_reps WHERE username = ?");
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows === 1) {
                    $rep = $result->fetch_assoc();
                    
                    // Hesap aktif mi?
                    if ($rep['status'] !== 'active') {
                        $error = "Bu hesap şu anda aktif değil!";
                    } 
                    // Şifre kontrolü (gerçek uygulamada password_verify kullanılmalı)
                    elseif (password_verify($password, $rep['password']) || $password === $rep['password']) { 
                        // Giriş başarılı
                        $_SESSION['support_rep_id'] = $rep['id'];
                        $_SESSION['support_rep_username'] = $rep['username'];
                        
                        // Çevrimiçi durumunu güncelle
                        $update = $conn->prepare("UPDATE support_reps SET is_online = 1, last_activity = NOW() WHERE id = ?");
                        $update->bind_param("i", $rep['id']);
                        $update->execute();
                        
                        // Dashboard'a yönlendir
                        header("Location: dashboard.php");
                        exit;
                    } else {
                        $error = "Geçersiz kullanıcı adı veya şifre!";
                    }
                } else {
                    $error = "Geçersiz kullanıcı adı veya şifre!";
                }
            } catch (Exception $e) {
                $error = "Bir hata oluştu, lütfen daha sonra tekrar deneyin.";
                logError("Login Error: " . $e->getMessage());
            }
            
            $conn->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Destek Temsilcisi Girişi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            background: white;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo img {
            max-width: 180px;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="logo">
                <img src="assets/logo.png" alt="Logo">
            </div>
            <h2 class="text-center mb-4">Destek Temsilcisi Girişi</h2>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="post" action="">
                <div class="mb-3">
                    <label for="username" class="form-label">Kullanıcı Adı</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Şifre</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Giriş Yap</button>
            </form>
            <div class="mt-3 text-center">
                <p class="small text-muted">Telegram Destek Paneli v1.0</p>
            </div>
        </div>
    </div>
</body>
</html>