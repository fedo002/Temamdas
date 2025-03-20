<?php
// mobile/mining-details.php - Mining package details mobile page
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If user is already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Process login form
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember_me = isset($_POST['remember_me']);
    
    if (empty($username) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        // User login
        $result = loginUser($username, $password, $remember_me);
        
        if ($result['success']) {
            // Set session variables
            $_SESSION['user_id'] = $result['user_id'];
            $_SESSION['username'] = $result['username'];
            $_SESSION['logged_in'] = true;
            
            // Redirect
            $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';
            header("Location: $redirect");
            exit;
        } else {
            $error = $result['message'];
        }
    }
}

// Page title
$page_title = "Login";

// Include mobile header
include 'includes/mobile-header.php';
?>

<div class="auth-page login-page">
    <div class="auth-container">
        <div class="auth-logo">
            <img src="assets/images/logo.png" alt="<?= APP_NAME ?>" height="60">
            <h2><?= APP_NAME ?></h2>
        </div>
        
        <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle me-2"></i> <?= $error ?>
        </div>
        <?php endif; ?>
        
        <div class="auth-form">
            <h2 class="auth-title" data-i18n="login.title">Welcome Back</h2>
            <p class="auth-subtitle" data-i18n="login.subtitle">Login to your account</p>
            
            <form method="POST" action="" class="login-form">
                <div class="form-group">
                    <label for="username" data-i18n="login.username_email">Username / Email</label>
                    <div class="input-wrapper">
                        <i class="fas fa-user"></i>
                        <input type="text" id="username" name="username" class="form-control" placeholder="Enter your username or email" value="<?= isset($username) ? htmlspecialchars($username) : '' ?>" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="password-label">
                        <label for="password" data-i18n="login.password">Password</label>
                        <a href="forgot-password.php" class="forgot-password" data-i18n="login.forgot_password">Forgot Password?</a>
                    </div>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
                        <button type="button" class="toggle-password" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                
                <div class="form-group remember-me">
                    <div class="custom-checkbox">
                        <input type="checkbox" id="remember_me" name="remember_me" <?= isset($remember_me) && $remember_me ? 'checked' : '' ?>>
                        <label for="remember_me" data-i18n="login.remember_me">Remember Me</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        <span data-i18n="login.login">Login</span>
                    </button>
                </div>
            </form>
        </div>
        
        <div class="auth-footer">
            <p data-i18n="login.no_account">Don't have an account?</p>
            <a href="register.php" class="btn btn-outline-primary" data-i18n="login.create_account">Create Account</a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    
    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Toggle icon
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });
    }
    
    // Custom checkbox styling
    const customCheckbox = document.querySelector('.custom-checkbox input');
    if (customCheckbox) {
        customCheckbox.addEventListener('change', function() {
            this.parentElement.classList.toggle('checked', this.checked);
        });
        
        // Initialize state
        if (customCheckbox.checked) {
            customCheckbox.parentElement.classList.add('checked');
        }
    }
});
</script>

<?php
// Include mobile footer
include 'includes/mobile-footer.php';
?>