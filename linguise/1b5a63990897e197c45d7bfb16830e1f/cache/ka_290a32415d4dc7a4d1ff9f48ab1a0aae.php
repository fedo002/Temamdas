<?php die(); ?><!DOCTYPE html><html lang="ka" dir="ltr"><head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#7367f0">
    <title>&#x10E8;&#x10D4;&#x10E1;&#x10D5;&#x10DA;&#x10D0; | Digiminex &#x10DB;&#x10DD;&#x10D1;&#x10D8;&#x10DA;&#x10E3;&#x10E0;&#x10D8;</title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="assets/images/apple-touch-icon.png">
    
    
    <!-- Prevent phone number detection -->
    <meta name="format-detection" content="telephone=no">
    
    <script defer src="assets/js/all.js"></script>
    <link href="assets/css/fontawesome.css" rel="stylesheet">
    <link href="assets/css/brands.css" rel="stylesheet">
    <link href="assets/css/solid.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet">
    
    <!-- Mobile CSS -->
    <link rel="stylesheet" href="assets/css/mobile.css">
    <link rel="stylesheet" href="assets/css/translation-system.css">

	
	<script async src="https://static.linguise.com/script-js/switcher.bundle.js?d=pk_YNgK025ZNYdLQVprpwbLmTm0DljVi7ht"></script>
	
    <!-- Page-specific CSS -->
        <link rel="stylesheet" href="assets/css/mobile-auth.css">
    <style>
	.linguise_switcher .linguise_switcher_popup {
		background: #121212;
		color: #3a86ff;
}
	</style></head>
	
		
<body>
    <!-- Mobile Header -->
    <header class="mobile-header">
        <div class="header-container">
            <a class="header-logo" href="/ka/index.php">
                <img src="assets/images/logo.png" alt="Digiminex" height="60">
            </a>
        </div>

    </header>

    <!-- Main Content Container -->
    <main class="mobile-content">
        <!-- Page content will be here -->
<div class="auth-page login-page">
    <div class="auth-container">
        <div class="auth-logo">
            <img src="assets/images/logo.png" alt="Digiminex" height="60">
            <h2>Digiminex</h2>
        </div>
        
                
        <div class="auth-form">
            <h2 class="auth-title" data-i18n="login.title">&#x10DB;&#x10DD;&#x10D2;&#x10D4;&#x10E1;&#x10D0;&#x10DA;&#x10DB;&#x10D4;&#x10D1;&#x10D8;&#x10D7;</h2>
            <p class="auth-subtitle" data-i18n="login.subtitle">&#x10E8;&#x10D4;&#x10D3;&#x10D8;&#x10D7; &#x10D7;&#x10E5;&#x10D5;&#x10D4;&#x10DC;&#x10E1; &#x10D0;&#x10DC;&#x10D2;&#x10D0;&#x10E0;&#x10D8;&#x10E8;&#x10D6;&#x10D4;</p>
            
            <form method="POST" action class="login-form">
                <div class="form-group">
                    <label for="username" data-i18n="login.username_email">&#x10DB;&#x10DD;&#x10DB;&#x10EE;&#x10DB;&#x10D0;&#x10E0;&#x10D4;&#x10D1;&#x10DA;&#x10D8;&#x10E1; &#x10E1;&#x10D0;&#x10EE;&#x10D4;&#x10DA;&#x10D8; / &#x10D4;&#x10DA;.&#x10E4;&#x10DD;&#x10E1;&#x10E2;&#x10D0;</label>
                    <div class="input-wrapper">
                        <i class="fas fa-user"></i>
                        <input type="text" id="username" name="username" class="form-control" placeholder="&#x10E8;&#x10D4;&#x10D8;&#x10E7;&#x10D5;&#x10D0;&#x10DC;&#x10D4;&#x10D7; &#x10D7;&#x10E5;&#x10D5;&#x10D4;&#x10DC;&#x10D8; &#x10DB;&#x10DD;&#x10DB;&#x10EE;&#x10DB;&#x10D0;&#x10E0;&#x10D4;&#x10D1;&#x10DA;&#x10D8;&#x10E1; &#x10E1;&#x10D0;&#x10EE;&#x10D4;&#x10DA;&#x10D8; &#x10D0;&#x10DC; &#x10D4;&#x10DA;.&#x10E4;&#x10DD;&#x10E1;&#x10E2;&#x10D0;" value required>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="password-label">
                        <label for="password" data-i18n="login.password">&#x10DE;&#x10D0;&#x10E0;&#x10DD;&#x10DA;&#x10D8;</label>
                         <a href="/ka/forgot-password.php" class="forgot-password" data-i18n="login.forgot_password">&#x10D3;&#x10D0;&#x10D2;&#x10D0;&#x10D5;&#x10D8;&#x10EC;&#x10E7;&#x10D3;&#x10D0;&#x10D7; &#x10DE;&#x10D0;&#x10E0;&#x10DD;&#x10DA;&#x10D8;?</a>
                    </div>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" class="form-control" placeholder="&#x10E8;&#x10D4;&#x10D8;&#x10E7;&#x10D5;&#x10D0;&#x10DC;&#x10D4;&#x10D7; &#x10D7;&#x10E5;&#x10D5;&#x10D4;&#x10DC;&#x10D8; &#x10DE;&#x10D0;&#x10E0;&#x10DD;&#x10DA;&#x10D8;" required>
                        <button type="button" class="toggle-password" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                
                <div class="form-group remember-me">
                    <div class="custom-checkbox">
                        <input type="checkbox" id="remember_me" name="remember_me">
                        <label for="remember_me" data-i18n="login.remember_me">&#x10D3;&#x10D0;&#x10DB;&#x10D8;&#x10DB;&#x10D0;&#x10EE;&#x10E1;&#x10DD;&#x10D5;&#x10E0;&#x10D4;</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        <span data-i18n="login.login">&#x10E8;&#x10D4;&#x10E1;&#x10D5;&#x10DA;&#x10D0;</span>
                    </button>
                </div>
            </form>
        </div>
        
        <div class="auth-footer">
            <p data-i18n="login.no_account">&#x10D0;&#x10DC;&#x10D2;&#x10D0;&#x10E0;&#x10D8;&#x10E8;&#x10D8; &#x10D0;&#x10E0; &#x10D2;&#x10D0;&#x10E5;&#x10D5;&#x10D7;?</p>
            <a href="/ka/register.php" class="btn btn-outline-primary" data-i18n="login.create_account">&#x10D0;&#x10DC;&#x10D2;&#x10D0;&#x10E0;&#x10D8;&#x10E8;&#x10D8;&#x10E1; &#x10E8;&#x10D4;&#x10E5;&#x10DB;&#x10DC;&#x10D0;</a>
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

</main>

    <!-- Mobile Bottom Navigation -->
    <nav class="mobile-bottom-nav">
        <a href="/ka/index.php" class="nav-item ">
            <i class="fas fa-home"></i>
            <span data-i18n="buttons.home">&#x10E1;&#x10D0;&#x10EC;&#x10E7;&#x10D8;&#x10E1;&#x10D8;</span></a>
        
        
         <a href="/ka/packages.php" class="nav-item ">
            <i class="fas fa-cubes"></i>
            <span data-i18n="buttons.packages">&#x10DE;&#x10D0;&#x10D9;&#x10D4;&#x10E2;&#x10D4;&#x10D1;&#x10D8;&#x10E1;</span></a>
        
                    
         <a href="/ka/login.php" class="nav-item active">
                <i class="fas fa-sign-in-alt"></i>

                    <span data-i18n="buttons.login">&#x10E8;&#x10D4;&#x10E1;&#x10D5;&#x10DA;&#x10D0;</span></a>
            
            
             <a href="/ka/register.php" class="nav-item ">
                <i class="fas fa-user-plus"></i>
                <span data-i18n="buttons.register">&#x10E0;&#x10D4;&#x10D2;&#x10D8;&#x10E1;&#x10E2;&#x10E0;&#x10D0;&#x10EA;&#x10D8;&#x10D0;</span></a>
            
            </nav>

    
    <!-- Backdrop Overlay -->
    <div class="backdrop-overlay" id="backdropOverlay"></div>
    

</body></html>