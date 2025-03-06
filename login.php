<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Kullanıcı zaten giriş yapmış ise dashboard'a yönlendir
if(isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

// Form gönderildi ise
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $remember_me = isset($_POST['remember_me']) ? true : false;
    
    // Validasyon
    $errors = [];
    
    if (empty($username)) {
        $errors[] = 'Kullanıcı adı veya e-posta gereklidir.';
    }
    
    if (empty($password)) {
        $errors[] = 'Şifre gereklidir.';
    }
    
    // Hata yoksa giriş işlemini yap
    if (empty($errors)) {
        $result = loginUser($username, $password, $remember_me);
        
        if ($result['success']) {
            $_SESSION['user_id'] = $result['user_id'];
            $_SESSION['username'] = $result['username'];
            
            // Dashboard'a yönlendir
            header('Location: dashboard.php');
            exit;
        } else {
            $errors[] = $result['message'];
        }
    }
}

$page_title = 'Giriş Yap';
include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-5">
            <div class="card">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold">Giriş Yap</h2>
                        <p class="text-muted">Hesabınıza giriş yapın</p>
                    </div>
                    
                    <?php if(isset($errors) && !empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach($errors as $error): ?>
                                <li><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    
                    <form method="POST" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label class="form-label">Kullanıcı Adı / E-posta</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" name="username" placeholder="Kullanıcı adı veya e-posta" value="<?= isset($username) ? htmlspecialchars($username) : '' ?>" required autofocus>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <label class="form-label">Şifre</label>
                                <a href="forgot-password.php" class="text-primary small">Şifremi Unuttum</a>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" name="password" placeholder="******" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember_me" id="remember_me" <?= isset($remember_me) && $remember_me ? 'checked' : '' ?>>
                                <label class="form-check-label" for="remember_me">
                                    Beni Hatırla
                                </label>
                            </div>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i> Giriş Yap
                            </button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-4">
                        <p class="mb-0">Hesabınız yok mu? <a href="register.php" class="fw-bold">Hesap Oluştur</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Şifre göster/gizle
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.querySelector('input[name="password"]');
    
    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.querySelector('i').classList.toggle('fa-eye');
        this.querySelector('i').classList.toggle('fa-eye-slash');
    });
});
</script>

<?php include 'includes/footer.php'; ?>