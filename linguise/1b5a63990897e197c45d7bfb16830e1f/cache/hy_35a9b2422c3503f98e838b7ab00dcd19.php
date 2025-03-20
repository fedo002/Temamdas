<?php die(); ?><!DOCTYPE html><html lang="hy" dir="ltr"><head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#7367f0">
    <title>&#x533;&#x580;&#x561;&#x576;&#x581;&#x57E;&#x565;&#x56C; | Digiminex Mobile</title>
    
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
            <a class="header-logo" href="/hy/index.php">
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
            <h2 class="auth-title" data-i18n="register.title">&#x54D;&#x57F;&#x565;&#x572;&#x56E;&#x565;&#x584; &#x570;&#x561;&#x577;&#x56B;&#x57E;</h2>
            <p class="auth-subtitle" data-i18n="register.subtitle">&#x533;&#x580;&#x561;&#x576;&#x581;&#x57E;&#x565;&#x584; &#x565;&#x582; &#x57D;&#x56F;&#x57D;&#x565;&#x584; &#x57E;&#x561;&#x57D;&#x57F;&#x561;&#x56F;&#x565;&#x56C;:</p>
            
            <form method="POST" action class="register-form">
                <div class="form-group">
                    <label for="username" data-i18n="register.username">&#x555;&#x563;&#x57F;&#x57E;&#x565;&#x56C;</label>
                    <div class="input-wrapper">
                        <i class="fas fa-user"></i>
                        <input type="text" id="username" name="username" class="form-control" placeholder="&#x538;&#x576;&#x57F;&#x580;&#x565;&#x584; &#x585;&#x563;&#x57F;&#x57E;&#x578;&#x572;&#x56B; &#x561;&#x576;&#x578;&#x582;&#x576;" value required>
                    </div>
                    <small class="form-text" data-i18n="register.username_note">&#x54A;&#x565;&#x57F;&#x584; &#x567; &#x56C;&#x56B;&#x576;&#x56B; 3-20 &#x576;&#x56B;&#x577;</small>
                </div>
                
                <div class="form-group">
                    <label for="email" data-i18n="register.email">&#x537;&#x56C;.&#x583;&#x578;&#x57D;&#x57F;&#x56B; &#x570;&#x561;&#x57D;&#x581;&#x565;</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email" class="form-control" placeholder="&#x544;&#x578;&#x582;&#x57F;&#x584;&#x561;&#x563;&#x580;&#x565;&#x584; &#x571;&#x565;&#x580; &#x567;&#x56C;" value required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password" data-i18n="register.password">&#x533;&#x561;&#x572;&#x57F;&#x576;&#x561;&#x562;&#x561;&#x57C;</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" class="form-control" placeholder="&#x54D;&#x57F;&#x565;&#x572;&#x56E;&#x565;&#x584; &#x563;&#x561;&#x572;&#x57F;&#x576;&#x561;&#x562;&#x561;&#x57C;" required>
                        <button type="button" class="toggle-password" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <small class="form-text" data-i18n="register.password_note">&#x54A;&#x565;&#x57F;&#x584; &#x567; &#x56C;&#x56B;&#x576;&#x56B; &#x561;&#x57C;&#x576;&#x57E;&#x561;&#x566;&#x576; 6 &#x576;&#x56B;&#x577;</small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password" data-i18n="register.confirm_password">&#x540;&#x561;&#x57D;&#x57F;&#x561;&#x57F;&#x565;&#x584; &#x563;&#x561;&#x572;&#x57F;&#x576;&#x561;&#x562;&#x561;&#x57C;&#x568;</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="&#x540;&#x561;&#x57D;&#x57F;&#x561;&#x57F;&#x565;&#x584; &#x571;&#x565;&#x580; &#x563;&#x561;&#x572;&#x57F;&#x576;&#x561;&#x562;&#x561;&#x57C;&#x568;" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="referral_code" data-i18n="register.referral_code">&#x548;&#x582;&#x572;&#x572;&#x578;&#x580;&#x564;&#x574;&#x561;&#x576; &#x56F;&#x578;&#x564;</label>
                    <div class="input-wrapper">
                        <i class="fas fa-users"></i>
                        <input type="text" id="referral_code" name="referral_code" class="form-control" placeholder="&#x544;&#x578;&#x582;&#x57F;&#x584;&#x561;&#x563;&#x580;&#x565;&#x584; &#x570;&#x572;&#x574;&#x561;&#x576; &#x56F;&#x578;&#x564;, &#x565;&#x569;&#x565; &#x578;&#x582;&#x576;&#x565;&#x584; &#x574;&#x565;&#x56F;&#x568;" value required>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="custom-checkbox">
                        <input type="checkbox" id="agree_terms" name="agree_terms" required>
                        <label for="agree_terms">
                            <span data-i18n="register.agree_terms">&#x535;&#x57D; &#x570;&#x561;&#x574;&#x561;&#x571;&#x561;&#x575;&#x576; &#x565;&#x574;</span>  <a href="/hy/terms.php" data-i18n="register.terms_of_service">&#x56E;&#x561;&#x57C;&#x561;&#x575;&#x578;&#x582;&#x569;&#x575;&#x561;&#x576; &#x57A;&#x561;&#x575;&#x574;&#x561;&#x576;&#x576;&#x565;&#x580;&#x56B;&#x576;</a>
                        </label>
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-user-plus me-2"></i>
                        <span data-i18n="register.create_account">&#x54D;&#x57F;&#x565;&#x572;&#x56E;&#x565;&#x584; &#x570;&#x561;&#x577;&#x56B;&#x57E;</span>
                    </button>
                </div>
            </form>
        </div>
        
        <div class="auth-footer">
            <p data-i18n="register.already_have_account">&#x531;&#x580;&#x564;&#x565;&#x576; &#x570;&#x561;&#x577;&#x56B;&#x57E; &#x578;&#x582;&#x576;&#x565;&#x584;:</p>
            <a href="/hy/login.php" class="btn btn-outline-primary" data-i18n="register.login">&#x544;&#x578;&#x582;&#x57F;&#x584;</a>
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
        <a href="/hy/index.php" class="nav-item ">
            <i class="fas fa-home"></i>
            <span data-i18n="buttons.home">&#x54F;&#x576;&#x561;&#x575;&#x56B;&#x576;</span></a>
        
        
         <a href="/hy/packages.php" class="nav-item ">
            <i class="fas fa-cubes"></i>
            <span data-i18n="buttons.packages">&#x583;&#x561;&#x569;&#x565;&#x569;&#x576;&#x565;&#x580;</span></a>
        
                    
         <a href="/hy/login.php" class="nav-item ">
                <i class="fas fa-sign-in-alt"></i>

                    <span data-i18n="buttons.login">&#x544;&#x578;&#x582;&#x57F;&#x584;</span></a>
            
            
             <a href="/hy/register.php" class="nav-item active">
                <i class="fas fa-user-plus"></i>
                <span data-i18n="buttons.register">&#x533;&#x580;&#x561;&#x576;&#x581;&#x57E;&#x565;&#x56C;</span></a>
            
            </nav>

    
    <!-- Backdrop Overlay -->
    <div class="backdrop-overlay" id="backdropOverlay"></div>
    
<script defer src="https://static.cloudflareinsights.com/beacon.min.js/vcd15cbe7772f49c399c6a5babf22c1241717689176015" integrity="sha512-ZpsOmlRQV6y907TI0dKBHq9Md29nnaEIPlkf84rnaERnq6zvWvPUqr2ft8M1aS28oN72PdrCzSjY4U6VaAw1EQ==" data-cf-beacon="{&quot;rayId&quot;:&quot;923157a258046604&quot;,&quot;version&quot;:&quot;2025.1.0&quot;,&quot;r&quot;:1,&quot;serverTiming&quot;:{&quot;name&quot;:{&quot;cfExtPri&quot;:true,&quot;cfL4&quot;:true,&quot;cfSpeedBrain&quot;:true,&quot;cfCacheStatus&quot;:true}},&quot;token&quot;:&quot;af7256c2312a464d847f1edbf0c05061&quot;,&quot;b&quot;:1}" crossorigin="anonymous"></script>

</body></html>