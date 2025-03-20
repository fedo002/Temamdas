<?php die(); ?><!DOCTYPE html><html lang="zh-cn" dir="ltr"><head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#7367f0">
    <title>&#x6CE8;&#x518C;| Digiminex&#x79FB;&#x52A8;&#x8BBE;&#x5907;</title>
    
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
            <a class="header-logo" href="/zh-cn/index.php">
                <img src="assets/images/logo.png" alt="Digiminex" height="60">
            </a>
        </div>

    </header>

    <!-- Main Content Container -->
    <main class="mobile-content">
        <!-- Page content will be here -->
<div class="auth-page register-page">
    <div class="auth-container">
        <div class="auth-logo">
            <img src="assets/images/logo.png" alt="Digiminex" height="60">
            <h2>Digiminex</h2>
        </div>
        
                
        <div class="auth-form">
            <h2 class="auth-title" data-i18n="register.title">&#x521B;&#x5EFA;&#x8D26;&#x6237;</h2>
            <p class="auth-subtitle" data-i18n="register.subtitle">&#x6CE8;&#x518C;&#x5E76;&#x5F00;&#x59CB;&#x8D5A;&#x94B1;&#xFF01;</p>
            
            <form method="POST" action class="register-form">
                <div class="form-group">
                    <label for="username" data-i18n="register.username">&#x7528;&#x6237;&#x540D;</label>
                    <div class="input-wrapper">
                        <i class="fas fa-user"></i>
                        <input type="text" id="username" name="username" class="form-control" placeholder="&#x9009;&#x62E9;&#x4E00;&#x4E2A;&#x7528;&#x6237;&#x540D;" value required>
                    </div>
                    <small class="form-text" data-i18n="register.username_note">&#x5FC5;&#x987B;&#x662F;3-20&#x4E2A;&#x5B57;&#x7B26;</small>
                </div>
                
                <div class="form-group">
                    <label for="email" data-i18n="register.email">&#x7535;&#x5B50;&#x90AE;&#x4EF6;</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email" class="form-control" placeholder="&#x8F93;&#x5165;&#x60A8;&#x7684;&#x7535;&#x5B50;&#x90AE;&#x4EF6;" value required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password" data-i18n="register.password">&#x5BC6;&#x7801;</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" class="form-control" placeholder="&#x521B;&#x5EFA;&#x5BC6;&#x7801;" required>
                        <button type="button" class="toggle-password" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <small class="form-text" data-i18n="register.password_note">&#x5FC5;&#x987B;&#x81F3;&#x5C11;6&#x4E2A;&#x5B57;&#x7B26;</small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password" data-i18n="register.confirm_password">&#x786E;&#x8BA4;&#x5BC6;&#x7801;</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="&#x786E;&#x8BA4;&#x60A8;&#x7684;&#x5BC6;&#x7801;" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="referral_code" data-i18n="register.referral_code">&#x63A8;&#x8350;&#x4EE3;&#x7801;</label>
                    <div class="input-wrapper">
                        <i class="fas fa-users"></i>
                        <input type="text" id="referral_code" name="referral_code" class="form-control" placeholder="&#x5982;&#x679C;&#x6709;&#x4E00;&#x4E2A;&#xFF0C;&#x8BF7;&#x8F93;&#x5165;&#x63A8;&#x8350;&#x4EE3;&#x7801;" value required>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="custom-checkbox">
                        <input type="checkbox" id="agree_terms" name="agree_terms" required>
                        <label for="agree_terms">
                            <span data-i18n="register.agree_terms">&#x6211;&#x540C;&#x610F;</span> <a href="/zh-cn/terms.php" data-i18n="register.terms_of_service">&#x670D;&#x52A1;&#x6761;&#x6B3E;</a>
                        </label>
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-user-plus me-2"></i>
                        <span data-i18n="register.create_account">&#x521B;&#x5EFA;&#x8D26;&#x6237;</span>
                    </button>
                </div>
            </form>
        </div>
        
        <div class="auth-footer">
            <p data-i18n="register.already_have_account">&#x5DF2;&#x7ECF;&#x6709;&#x4E00;&#x4E2A;&#x5E10;&#x6237;&#xFF1F;</p>
            <a href="/zh-cn/login.php" class="btn btn-outline-primary" data-i18n="register.login">&#x767B;&#x5F55;</a>
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
        <a href="/zh-cn/index.php" class="nav-item ">
            <i class="fas fa-home"></i>
            <span data-i18n="buttons.home">&#x623F;&#x5C4B;</span></a>
        
        
        <a href="/zh-cn/packages.php" class="nav-item ">
            <i class="fas fa-cubes"></i>
            <span data-i18n="buttons.packages">&#x5305;</span></a>
        
                    
        <a href="/zh-cn/login.php" class="nav-item ">
                <i class="fas fa-sign-in-alt"></i>

                    <span data-i18n="buttons.login">&#x767B;&#x5F55;</span></a>
            
            
            <a href="/zh-cn/register.php" class="nav-item active">
                <i class="fas fa-user-plus"></i>
                <span data-i18n="buttons.register">&#x767B;&#x8BB0;&#x518C;</span></a>
            
            </nav>

    
    <!-- Backdrop Overlay -->
    <div class="backdrop-overlay" id="backdropOverlay"></div>
    
<script defer src="https://static.cloudflareinsights.com/beacon.min.js/vcd15cbe7772f49c399c6a5babf22c1241717689176015" integrity="sha512-ZpsOmlRQV6y907TI0dKBHq9Md29nnaEIPlkf84rnaERnq6zvWvPUqr2ft8M1aS28oN72PdrCzSjY4U6VaAw1EQ==" data-cf-beacon="{&quot;rayId&quot;:&quot;92336f41fe06b915&quot;,&quot;version&quot;:&quot;2025.1.0&quot;,&quot;r&quot;:1,&quot;serverTiming&quot;:{&quot;name&quot;:{&quot;cfExtPri&quot;:true,&quot;cfL4&quot;:true,&quot;cfSpeedBrain&quot;:true,&quot;cfCacheStatus&quot;:true}},&quot;token&quot;:&quot;af7256c2312a464d847f1edbf0c05061&quot;,&quot;b&quot;:1}" crossorigin="anonymous"></script>

</body></html>