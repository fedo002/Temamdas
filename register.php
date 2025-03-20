<?php
// mobile/register.php - Mobile-optimized registration page
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

// Get referral code from URL
$ref_code = isset($_GET['ref']) ? $_GET['ref'] : '';

// Process registration form
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $referral_code = trim($_POST['referral_code'] ?? '');
    $agree_terms = isset($_POST['agree_terms']) ? true : false;
    
    // Validation
    if (empty($username)) {
        $errors[] = 'Username is required.';
    } elseif (strlen($username) < 3 || strlen($username) > 20) {
        $errors[] = 'Username must be 3-20 characters.';
    } elseif (!isUsernameAvailable($username)) {
        $errors[] = 'This username is already taken.';
    }
    
    if (empty($email)) {
        $errors[] = 'Email address is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    } elseif (!isEmailAvailable($email)) {
        $errors[] = 'This email address is already registered.';
    }
    
    if (empty($password)) {
        $errors[] = 'Password is required.';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }
    
    if ($password !== $confirm_password) {
        $errors[] = 'Passwords do not match.';
    }
    
    if (!empty($referral_code) && !isValidReferralCode($referral_code)) {
        $errors[] = 'Invalid referral code.';
    }
    
    if (!$agree_terms) {
        $errors[] = 'You must agree to the Terms of Service.';
    }
    
    // If no errors, register the user
    if (empty($errors)) {
        $result = registerUser($username, $email, $password, $referral_code);
        
        if ($result['success']) {
            // Set session variables
            $_SESSION['user_id'] = $result['user_id'];
            $_SESSION['username'] = $username;
            $_SESSION['register_success'] = true;
            
            // Redirect to dashboard
            header('Location: index.php');
            exit;
        } else {
            $errors[] = $result['message'];
        }
    }
}

// Page title
$page_title = 'Register';

// Include mobile header
include 'includes/mobile-header.php';
?>

<div class="auth-page register-page">
    <div class="auth-container">
        <div class="auth-logo">
            <img src="assets/images/logo.png" alt="<?= APP_NAME ?>" height="60">
            <h2><?= APP_NAME ?></h2>
        </div>
        
        <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach($errors as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
        
        <div class="auth-form">
            <h2 class="auth-title" data-i18n="register.title">Create Account</h2>
            <p class="auth-subtitle" data-i18n="register.subtitle">Sign up and start earning!</p>
            
            <form method="POST" action="" class="register-form">
                <div class="form-group">
                    <label for="username" data-i18n="register.username">Username</label>
                    <div class="input-wrapper">
                        <i class="fas fa-user"></i>
                        <input type="text" id="username" name="username" class="form-control" placeholder="Choose a username" value="<?= isset($username) ? htmlspecialchars($username) : '' ?>" required>
                    </div>
                    <small class="form-text" data-i18n="register.username_note">Must be 3-20 characters</small>
                </div>
                
                <div class="form-group">
                    <label for="email" data-i18n="register.email">Email Address</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" value="<?= isset($email) ? htmlspecialchars($email) : '' ?>" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password" data-i18n="register.password">Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Create a password" required>
                        <button type="button" class="toggle-password" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <small class="form-text" data-i18n="register.password_note">Must be at least 6 characters</small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password" data-i18n="register.confirm_password">Confirm Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirm your password" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="referral_code" data-i18n="register.referral_code">Referral Code</label>
                    <div class="input-wrapper">
                        <i class="fas fa-users"></i>
                        <input type="text" id="referral_code" name="referral_code" class="form-control" placeholder="Enter referral code if you have one" value="<?= isset($ref_code) ? htmlspecialchars($ref_code) : (isset($referral_code) ? htmlspecialchars($referral_code) : '') ?>" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="custom-checkbox">
                        <input type="checkbox" id="agree_terms" name="agree_terms" required <?= isset($agree_terms) && $agree_terms ? 'checked' : '' ?>>
                        <label for="agree_terms">
                            <span data-i18n="register.agree_terms">I agree to the</span> <a href="terms.php" data-i18n="register.terms_of_service">Terms of Service</a>
                        </label>
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-user-plus me-2"></i>
                        <span data-i18n="register.create_account">Create Account</span>
                    </button>
                </div>
            </form>
        </div>
        
        <div class="auth-footer">
            <p data-i18n="register.already_have_account">Already have an account?</p>
            <a href="login.php" class="btn btn-outline-primary" data-i18n="register.login">Login</a>
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