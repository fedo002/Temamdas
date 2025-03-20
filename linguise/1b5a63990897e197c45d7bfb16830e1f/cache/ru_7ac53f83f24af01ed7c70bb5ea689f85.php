<?php die(); ?><!DOCTYPE html><html lang="ru" dir="ltr"><head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#7367f0">
    <title>&#x412;&#x445;&#x43E;&#x434; &#x432; &#x441;&#x438;&#x441;&#x442;&#x435;&#x43C;&#x443; | Digiminex Mobile</title>
    
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
            <a class="header-logo" href="/ru/index.php">
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
            <h2 class="auth-title" data-i18n="login.title">&#x414;&#x43E;&#x431;&#x440;&#x43E; &#x43F;&#x43E;&#x436;&#x430;&#x43B;&#x43E;&#x432;&#x430;&#x442;&#x44C;</h2>
            <p class="auth-subtitle" data-i18n="login.subtitle">&#x412;&#x43E;&#x439;&#x434;&#x438;&#x442;&#x435; &#x432; &#x441;&#x432;&#x43E;&#x44E; &#x443;&#x447;&#x435;&#x442;&#x43D;&#x443;&#x44E; &#x437;&#x430;&#x43F;&#x438;&#x441;&#x44C;</p>
            
            <form method="POST" action class="login-form">
                <div class="form-group">
                    <label for="username" data-i18n="login.username_email">&#x418;&#x43C;&#x44F; &#x43F;&#x43E;&#x43B;&#x44C;&#x437;&#x43E;&#x432;&#x430;&#x442;&#x435;&#x43B;&#x44F; / &#x44D;&#x43B;&#x435;&#x43A;&#x442;&#x440;&#x43E;&#x43D;&#x43D;&#x430;&#x44F; &#x43F;&#x43E;&#x447;&#x442;&#x430;</label>
                    <div class="input-wrapper">
                        <i class="fas fa-user"></i>
                        <input type="text" id="username" name="username" class="form-control" placeholder="&#x412;&#x432;&#x435;&#x434;&#x438;&#x442;&#x435; &#x441;&#x432;&#x43E;&#x435; &#x438;&#x43C;&#x44F; &#x43F;&#x43E;&#x43B;&#x44C;&#x437;&#x43E;&#x432;&#x430;&#x442;&#x435;&#x43B;&#x44F; &#x438;&#x43B;&#x438; &#x44D;&#x43B;&#x435;&#x43A;&#x442;&#x440;&#x43E;&#x43D;&#x43D;&#x443;&#x44E; &#x43F;&#x43E;&#x447;&#x442;&#x443;" value required>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="password-label">
                        <label for="password" data-i18n="login.password">&#x41F;&#x430;&#x440;&#x43E;&#x43B;&#x44C;</label>
                         <a href="/ru/forgot-password.php" class="forgot-password" data-i18n="login.forgot_password">&#x437;&#x430;&#x431;&#x44B;&#x43B; &#x43F;&#x430;&#x440;&#x43E;&#x43B;&#x44C;?</a>
                    </div>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" class="form-control" placeholder="&#x412;&#x432;&#x435;&#x434;&#x438;&#x442;&#x435; &#x441;&#x432;&#x43E;&#x439; &#x43F;&#x430;&#x440;&#x43E;&#x43B;&#x44C;" required>
                        <button type="button" class="toggle-password" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                
                <div class="form-group remember-me">
                    <div class="custom-checkbox">
                        <input type="checkbox" id="remember_me" name="remember_me">
                        <label for="remember_me" data-i18n="login.remember_me">&#x417;&#x430;&#x43F;&#x43E;&#x43C;&#x43D;&#x438;&#x442;&#x44C; &#x43C;&#x435;&#x43D;&#x44F;</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        <span data-i18n="login.login">&#x410;&#x432;&#x442;&#x43E;&#x440;&#x438;&#x437;&#x43E;&#x432;&#x430;&#x442;&#x44C;&#x441;&#x44F;</span>
                    </button>
                </div>
            </form>
        </div>
        
        <div class="auth-footer">
            <p data-i18n="login.no_account">&#x423; &#x432;&#x430;&#x441; &#x43D;&#x435;&#x442; &#x443;&#x447;&#x435;&#x442;&#x43D;&#x43E;&#x439; &#x437;&#x430;&#x43F;&#x438;&#x441;&#x438;?</p>
            <a href="/ru/register.php" class="btn btn-outline-primary" data-i18n="login.create_account">&#x417;&#x430;&#x440;&#x435;&#x433;&#x438;&#x441;&#x442;&#x440;&#x438;&#x440;&#x43E;&#x432;&#x430;&#x442;&#x44C;&#x441;&#x44F;</a>
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
        <a href="/ru/index.php" class="nav-item ">
            <i class="fas fa-home"></i>
            <span data-i18n="buttons.home">&#x414;&#x43E;&#x43C;&#x430;&#x448;&#x43D;&#x438;&#x435;</span></a>
        
        
         <a href="/ru/packages.php" class="nav-item ">
            <i class="fas fa-cubes"></i>
            <span data-i18n="buttons.packages">&#x43F;&#x430;&#x43A;&#x435;&#x442;&#x44B;</span></a>
        
                    
         <a href="/ru/login.php" class="nav-item active">
                <i class="fas fa-sign-in-alt"></i>

                    <span data-i18n="buttons.login">&#x410;&#x432;&#x442;&#x43E;&#x440;</span></a>
            
            
            <a href="/ru/register.php" class="nav-item ">
                <i class="fas fa-user-plus"></i>
                <span data-i18n="buttons.register">&#x200B;&#x440;&#x435;&#x433;&#x438;&#x441;&#x442;&#x440;</span></a>
            
            </nav>

    
    <!-- Backdrop Overlay -->
    <div class="backdrop-overlay" id="backdropOverlay"></div>
    
<script defer src="https://static.cloudflareinsights.com/beacon.min.js/vcd15cbe7772f49c399c6a5babf22c1241717689176015" integrity="sha512-ZpsOmlRQV6y907TI0dKBHq9Md29nnaEIPlkf84rnaERnq6zvWvPUqr2ft8M1aS28oN72PdrCzSjY4U6VaAw1EQ==" data-cf-beacon="{&quot;rayId&quot;:&quot;923251cbcc7a9fc9&quot;,&quot;version&quot;:&quot;2025.1.0&quot;,&quot;r&quot;:1,&quot;serverTiming&quot;:{&quot;name&quot;:{&quot;cfExtPri&quot;:true,&quot;cfL4&quot;:true,&quot;cfSpeedBrain&quot;:true,&quot;cfCacheStatus&quot;:true}},&quot;token&quot;:&quot;af7256c2312a464d847f1edbf0c05061&quot;,&quot;b&quot;:1}" crossorigin="anonymous"></script>

</body></html>