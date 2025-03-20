<?php die(); ?><!DOCTYPE html><html lang="fr" dir="ltr"><head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#7367f0">
    <title>Registre | Digiminex Mobile</title>
    
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
            <a class="header-logo" href="/fr/index.php">
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
            <h2 class="auth-title" data-i18n="register.title">Cr&#xE9;er un compte</h2>
            <p class="auth-subtitle" data-i18n="register.subtitle">Inscrivez-vous et commencez &#xE0; gagner!</p>
            
            <form method="POST" action class="register-form">
                <div class="form-group">
                    <label for="username" data-i18n="register.username">Nom d&apos;utilisateur</label>
                    <div class="input-wrapper">
                        <i class="fas fa-user"></i>
                        <input type="text" id="username" name="username" class="form-control" placeholder="Choisissez un nom d&amp;#39;utilisateur" value required>
                    </div>
                    <small class="form-text" data-i18n="register.username_note">Doit &#xEA;tre 3-20 caract&#xE8;res</small>
                </div>
                
                <div class="form-group">
                    <label for="email" data-i18n="register.email">Adresse email</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email" class="form-control" placeholder="Entrez votre e-mail" value required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password" data-i18n="register.password">Mot de passe</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Cr&#xE9;er un mot de passe" required>
                        <button type="button" class="toggle-password" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <small class="form-text" data-i18n="register.password_note">Doit &#xEA;tre au moins 6 caract&#xE8;res</small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password" data-i18n="register.confirm_password">Confirmez le mot de passe</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirmez votre mot de passe" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="referral_code" data-i18n="register.referral_code">Code de r&#xE9;f&#xE9;rence</label>
                    <div class="input-wrapper">
                        <i class="fas fa-users"></i>
                        <input type="text" id="referral_code" name="referral_code" class="form-control" placeholder="Entrez le code de r&#xE9;f&#xE9;rence si vous en avez un" value required>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="custom-checkbox">
                        <input type="checkbox" id="agree_terms" name="agree_terms" required>
                        <label for="agree_terms">
                            <span data-i18n="register.agree_terms">J&apos;accepte les</span>  <a href="/fr/terms.php" data-i18n="register.terms_of_service">conditions d&apos;utilisation</a>
                        </label>
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-user-plus me-2"></i>
                        <span data-i18n="register.create_account">Cr&#xE9;er un compte</span>
                    </button>
                </div>
            </form>
        </div>
        
        <div class="auth-footer">
            <p data-i18n="register.already_have_account">Vous avez d&#xE9;j&#xE0; un compte?</p>
            <a href="/fr/login.php" class="btn btn-outline-primary" data-i18n="register.login">Se connecter</a>
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
            
            <a href="/fr/register.php" class="nav-item active">
                <i class="fas fa-user-plus"></i>
                <span data-i18n="buttons.register">Registre</span></a>
            
             
        
                    <a href="/fr/login.php" class="nav-item ">
                <i class="fas fa-sign-in-alt"></i>

                    <span data-i18n="buttons.login">de connexion</span></a>
             
        
        <a href="/fr/packages.php" class="nav-item ">
            <i class="fas fa-cubes"></i>
            <span data-i18n="buttons.packages">&#xE0;</span></a>
         
        <a href="/fr/index.php" class="nav-item ">
            <i class="fas fa-home"></i>
            <span data-i18n="buttons.home">domicile</span></a>
        </nav>

    
    <!-- Backdrop Overlay -->
    <div class="backdrop-overlay" id="backdropOverlay"></div>
    
<script defer src="https://static.cloudflareinsights.com/beacon.min.js/vcd15cbe7772f49c399c6a5babf22c1241717689176015" integrity="sha512-ZpsOmlRQV6y907TI0dKBHq9Md29nnaEIPlkf84rnaERnq6zvWvPUqr2ft8M1aS28oN72PdrCzSjY4U6VaAw1EQ==" data-cf-beacon="{&quot;rayId&quot;:&quot;923500cbbbd53c6d&quot;,&quot;version&quot;:&quot;2025.1.0&quot;,&quot;r&quot;:1,&quot;serverTiming&quot;:{&quot;name&quot;:{&quot;cfExtPri&quot;:true,&quot;cfL4&quot;:true,&quot;cfSpeedBrain&quot;:true,&quot;cfCacheStatus&quot;:true}},&quot;token&quot;:&quot;af7256c2312a464d847f1edbf0c05061&quot;,&quot;b&quot;:1}" crossorigin="anonymous"></script>

</body></html>