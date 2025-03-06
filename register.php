<?php
// Temel yapılandırma dosyalarını dahil et
require_once 'includes/config.php'; 
session_start();
require_once 'includes/functions.php';

// Kullanıcı zaten giriş yapmış ise dashboard'a yönlendir
if(isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

// Referans kodu
$ref_code = isset($_GET['ref']) ? $_GET['ref'] : null;

// Form gönderildi ise
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $referral_code = trim($_POST['referral_code']);
    $agree_terms = isset($_POST['agree_terms']) ? true : false;
    
    // Validasyon
    $errors = [];
    
    if (empty($username)) {
        $errors[] = 'Kullanıcı adı gereklidir.';
    } elseif (strlen($username) < 3 || strlen($username) > 20) {
        $errors[] = 'Kullanıcı adı 3-20 karakter arasında olmalıdır.';
    } elseif (!isUsernameAvailable($username)) {
        $errors[] = 'Bu kullanıcı adı zaten kullanılmaktadır.';
    }
    
    if (empty($email)) {
        $errors[] = 'E-posta adresi gereklidir.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Geçerli bir e-posta adresi giriniz.';
    } elseif (!isEmailAvailable($email)) {
        $errors[] = 'Bu e-posta adresi zaten kullanılmaktadır.';
    }
    
    if (empty($password)) {
        $errors[] = 'Şifre gereklidir.';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Şifre en az 6 karakter olmalıdır.';
    }
    
    if ($password !== $confirm_password) {
        $errors[] = 'Şifreler eşleşmiyor.';
    }
    
    if (!empty($referral_code) && !isValidReferralCode($referral_code)) {
        $errors[] = 'Geçersiz referans kodu.';
    }
    
    if (!$agree_terms) {
        $errors[] = 'Kullanım şartlarını kabul etmelisiniz.';
    }
    
    // Hata yoksa kayıt işlemini yap
    if (empty($errors)) {
        $result = registerUser($username, $email, $password, $referral_code);
        
        if ($result['success']) {
            $_SESSION['user_id'] = $result['user_id'];
            $_SESSION['username'] = $username;
            
            // Başarılı kayıt mesajı
            $_SESSION['register_success'] = true;
            
            // Dashboard'a yönlendir
            header('Location: dashboard.php');
            exit;
        } else {
            $errors[] = $result['message'];
        }
    }
}

$page_title = 'Hesap Oluştur';
include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold">Hesap Oluştur</h2>
                        <p class="text-muted">Hemen kaydol ve kazanmaya başla!</p>
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
                            <label class="form-label">Kullanıcı Adı</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" name="username" placeholder="Kullanıcı adınız" value="<?= isset($username) ? htmlspecialchars($username) : '' ?>" required>
                            </div>
                            <div class="form-text">3-20 karakter arasında olmalıdır.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">E-posta Adresi</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" name="email" placeholder="ornek@email.com" value="<?= isset($email) ? htmlspecialchars($email) : '' ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Şifre</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" name="password" placeholder="******" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="form-text">En az 6 karakter olmalıdır.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Şifre Tekrar</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" name="confirm_password" placeholder="******" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Referans Kodu</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-users"></i></span>
                                <input type="text" class="form-control" name="referral_code" placeholder="Referans kodu" required value="<?= isset($ref_code) ? htmlspecialchars($ref_code) : (isset($referral_code) ? htmlspecialchars($referral_code) : '') ?>">
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="agree_terms" id="agree_terms" required <?= isset($agree_terms) && $agree_terms ? 'checked' : '' ?>>
                                <label class="form-check-label" for="agree_terms">
                                    <a href="terms.php" target="_blank">Kullanım Şartları</a>'nı kabul ediyorum
                                </label>
                            </div>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-user-plus me-2"></i> Hesap Oluştur
                            </button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-4">
                        <p class="mb-0">Zaten hesabınız var mı? <a href="login.php" class="fw-bold">Giriş Yap</a></p>
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