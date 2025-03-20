<?php die(); ?><!DOCTYPE html><html lang="tr" dir="ltr"><head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#7367f0">
    <title>Giri&#x15F; | Digiminex Mobile</title>
    
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

	<!--<script async src="https://static.linguise.com/script-js/switcher.bundle.js?d=pk_HTDA4a15TTJcZ8Cb0xIgJorDLHtgAOig"></script>-->
	<script type="text/javascript" src="https://cdn.weglot.com/weglot.min.js"></script>
<script>
    Weglot.initialize({
        api_key: 'wg_79f01ccd102ac475b58cd40c45b213ca8'
    });
</script>
	
	
    <!-- Page-specific CSS -->
        <link rel="stylesheet" href="assets/css/mobile-auth.css">
    <style>
	.linguise_switcher .linguise_switcher_popup {
		background: #121212;
		color: #3a86ff;
}
	</style><script type="application/json" id="linguise-extra-metadata">{"domain":"aHR0cHM6Ly9kaWdpbWluZXguY29t","url":"aHR0cHM6Ly9kaWdpbWluZXguY29tL2xvZ2luLnBocA==","language":"en","translate_urls":true,"dynamic_translations":{"enabled":true},"language_settings":{"display":"popup","position":"top_right","flag_shape":"round","flag_width":"20","enabled_flag":true,"flag_de_type":"de","flag_en_type":"en-us","flag_es_type":"es","flag_pt_type":"pt","flag_tw_type":"zh-tw","flag_shadow_h":2,"flag_shadow_v":2,"flag_shadow_blur":12,"enabled_lang_name":true,"flag_shadow_color":"#000000","lang_name_display":"native","flag_border_radius":0,"flag_shadow_spread":0,"flag_hover_shadow_h":3,"flag_hover_shadow_v":3,"language_name_color":"#e0e0e0","flag_hover_shadow_blur":6,"enabled_lang_short_name":false,"flag_hover_shadow_color":"#000000","flag_hover_shadow_spread":0,"language_name_hover_color":"#454545","popup_language_name_color":"#000000","popup_language_name_hover_color":"#000000"},"languages":[{"code":"zh-cn","name":"Chinese","original_name":"中文"},{"code":"es","name":"Spanish","original_name":"Español"},{"code":"fr","name":"French","original_name":"Français"},{"code":"de","name":"German","original_name":"Deutsch"},{"code":"ru","name":"Russian","original_name":"Русский"},{"code":"tr","name":"Turkish","original_name":"Türkçe"},{"code":"en","name":"English","original_name":"English"}],"structure":"subfolders","platform":"other_php","debug":false,"public_key":"pk_HTDA4a15TTJcZ8Cb0xIgJorDLHtgAOig","rules":[],"cached_selectors":[]}</script></head>
	
		
<body>
    <!-- Mobile Header -->
    <header class="mobile-header">
        <div class="header-container">
            <a class="header-logo" href="/tr/index.php">
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
            <h2 class="auth-title" data-i18n="login.title">Tekrar ho&#x15F;geldiniz</h2>
            <p class="auth-subtitle" data-i18n="login.subtitle">Hesab&#x131;n&#x131;za giri&#x15F; yap&#x131;n</p>
            
            <form method="POST" action class="login-form">
                <div class="form-group">
                    <label for="username" data-i18n="login.username_email">Kullan&#x131;c&#x131; Ad&#x131; / E -posta</label>
                    <div class="input-wrapper">
                        <i class="fas fa-user"></i>
                        <input type="text" id="username" name="username" class="form-control" placeholder="Kullan&#x131;c&#x131; ad&#x131;n&#x131;z&#x131; veya e -postan&#x131;z&#x131; girin" value required>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="password-label">
                        <label for="password" data-i18n="login.password">&#x15E;ifre</label>
                         <a href="/tr/forgot-password.php" class="forgot-password" data-i18n="login.forgot_password">&#x15F;ifreyi mi unuttunuz?</a>
                    </div>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" class="form-control" placeholder="&#x15E;ifrenizi girin" required>
                        <button type="button" class="toggle-password" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                
                <div class="form-group remember-me">
                    <div class="custom-checkbox">
                        <input type="checkbox" id="remember_me" name="remember_me">
                        <label for="remember_me" data-i18n="login.remember_me">Beni Hat&#x131;rla</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        <span data-i18n="login.login">Giri&#x15F; yapmak</span>
                    </button>
                </div>
            </form>
        </div>
        
        <div class="auth-footer">
            <p data-i18n="login.no_account">Hesab&#x131;n&#x131;z yok mu?</p>
            <a href="/tr/register.php" class="btn btn-outline-primary" data-i18n="login.create_account">Hesap olu&#x15F;turmak</a>
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
        <a href="/tr/index.php" class="nav-item ">
            <i class="fas fa-home"></i>
            <span data-i18n="buttons.home">Ana Sayfa</span></a>
        
        
         <a href="/tr/packages.php" class="nav-item ">
            <i class="fas fa-cubes"></i>
            <span data-i18n="buttons.packages">Paketleri</span></a>
        
                    
         <a href="/tr/login.php" class="nav-item active">
                <i class="fas fa-sign-in-alt"></i>

                    <span data-i18n="buttons.login">Giri&#x15F;</span></a>
            
            
             <a href="/tr/register.php" class="nav-item ">
                <i class="fas fa-user-plus"></i>
                <span data-i18n="buttons.register">Kay&#x131;t</span></a>
            
            </nav>

    
    <!-- Backdrop Overlay -->
    <div class="backdrop-overlay" id="backdropOverlay"></div>

    
    <!-- Initialize Language System and Modal -->
    <script defer src="assets/js/bg.js"></script>
    

</body></html>