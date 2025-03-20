<?php die(); ?><!DOCTYPE html><html lang="hi" dir="ltr"><head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#7367f0">
    <title>&#x932;&#x949;&#x917;&#x93F;&#x928; | &#x921;&#x93F;&#x91C;&#x940;&#x92E;&#x93F;&#x928;&#x947;&#x915;&#x94D;&#x938; &#x92E;&#x94B;&#x92C;&#x93E;&#x907;&#x932;</title>
    
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
            <a class="header-logo" href="/hi/index.php">
                <img src="assets/images/logo.png" alt="&#x921;&#x93F;&#x91C;&#x940;&#x92E;&#x93F;&#x928;&#x947;&#x915;&#x94D;&#x938;" height="60">
            </a>
        </div>

    </header>

    <!-- Main Content Container -->
    <main class="mobile-content">
        <!-- Page content will be here -->
<div class="auth-page login-page">
    <div class="auth-container">
        <div class="auth-logo">
            <img src="assets/images/logo.png" alt="&#x921;&#x93F;&#x91C;&#x940;&#x92E;&#x93F;&#x928;&#x947;&#x915;&#x94D;&#x938;" height="60">
            <h2>&#x921;&#x93F;&#x91C;&#x940;&#x92E;&#x93F;&#x928;&#x947;&#x915;&#x94D;&#x938;</h2>
        </div>
        
                
        <div class="auth-form">
            <h2 class="auth-title" data-i18n="login.title">&#x935;&#x93E;&#x92A;&#x938;&#x940; &#x92A;&#x930; &#x938;&#x94D;&#x935;&#x93E;&#x917;&#x924; &#x939;&#x948;</h2>
            <p class="auth-subtitle" data-i18n="login.subtitle">&#x905;&#x92A;&#x928;&#x947; &#x905;&#x915;&#x93E;&#x909;&#x902;&#x91F; &#x92E;&#x947;&#x902; &#x932;&#x949;&#x917; &#x907;&#x928; &#x915;&#x930;&#x947;&#x902;</p>
            
            <form method="POST" action class="login-form">
                <div class="form-group">
                    <label for="username" data-i18n="login.username_email">&#x909;&#x92A;&#x92F;&#x94B;&#x917;&#x915;&#x930;&#x94D;&#x924;&#x93E; &#x928;&#x93E;&#x92E; / &#x908;&#x92E;&#x947;&#x932;</label>
                    <div class="input-wrapper">
                        <i class="fas fa-user"></i>
                        <input type="text" id="username" name="username" class="form-control" placeholder="&#x905;&#x92A;&#x928;&#x93E; &#x909;&#x92A;&#x92F;&#x94B;&#x917;&#x915;&#x930;&#x94D;&#x924;&#x93E; &#x928;&#x93E;&#x92E; &#x92F;&#x93E; &#x908;&#x92E;&#x947;&#x932; &#x926;&#x930;&#x94D;&#x91C; &#x915;&#x930;&#x947;&#x902;" value required>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="password-label">
                        <label for="password" data-i18n="login.password">&#x92A;&#x93E;&#x938;&#x935;&#x930;&#x94D;&#x921;</label>
                         <a href="/hi/forgot-password.php" class="forgot-password" data-i18n="login.forgot_password">&#x92D;&#x942;&#x932; &#x917;&#x92F;&#x93E; &#x92A;&#x93E;&#x938;&#x935;&#x930;&#x94D;&#x921;?</a>
                    </div>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" class="form-control" placeholder="&#x905;&#x92A;&#x928;&#x93E; &#x915;&#x942;&#x91F;&#x936;&#x92C;&#x94D;&#x926; &#x92D;&#x930;&#x947;&#x902;" required>
                        <button type="button" class="toggle-password" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                
                <div class="form-group remember-me">
                    <div class="custom-checkbox">
                        <input type="checkbox" id="remember_me" name="remember_me">
                        <label for="remember_me" data-i18n="login.remember_me">&#x92E;&#x941;&#x91D;&#x947; &#x92F;&#x93E;&#x926; &#x915;&#x930;&#x94B;</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        <span data-i18n="login.login">&#x932;&#x949;&#x917; &#x907;&#x928; &#x915;&#x930;&#x947;&#x902;</span>
                    </button>
                </div>
            </form>
        </div>
        
        <div class="auth-footer">
            <p data-i18n="login.no_account">&#x915;&#x94B;&#x908; &#x916;&#x93E;&#x924;&#x93E; &#x928;&#x939;&#x940;&#x902; &#x939;&#x948;?</p>
            <a href="/hi/register.php" class="btn btn-outline-primary" data-i18n="login.create_account">&#x916;&#x93E;&#x924;&#x93E; &#x92C;&#x928;&#x93E;&#x90F;&#x902;</a>
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
        <a href="/hi/index.php" class="nav-item ">
            <i class="fas fa-home"></i>
            <span data-i18n="buttons.home">&#x939;&#x94B;&#x92E;</span></a>
        
        
         <a href="/hi/packages.php" class="nav-item ">
            <i class="fas fa-cubes"></i>
            <span data-i18n="buttons.packages">&#x92A;&#x948;&#x915;&#x947;&#x91C;</span></a>
        
                    
         <a href="/hi/login.php" class="nav-item active">
                <i class="fas fa-sign-in-alt"></i>

                    <span data-i18n="buttons.login">&#x932;&#x949;&#x917;&#x93F;&#x928;</span></a>
            
            
             <a href="/hi/register.php" class="nav-item ">
                <i class="fas fa-user-plus"></i>
                <span data-i18n="buttons.register">&#x930;&#x91C;&#x93F;&#x938;&#x94D;&#x91F;&#x930;</span></a>
            
            </nav>

    
    <!-- Backdrop Overlay -->
    <div class="backdrop-overlay" id="backdropOverlay"></div>
    

</body></html>