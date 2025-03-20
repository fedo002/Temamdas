<?php die(); ?><!DOCTYPE html><html lang="fr" dir="ltr"><head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#7367f0">
    <title>DigiMinex - Tableau de bord | Digiminex Mobile</title>
    
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
        <link rel="stylesheet" href="assets/css/mobile-index.css">
    <style>
	.linguise_switcher .linguise_switcher_popup {
		background: #121212;
		color: #3a86ff;
}
	</style><script type="application/json" id="linguise-extra-metadata">{"domain":"aHR0cHM6Ly9kaWdpbWluZXguY29t","url":"aHR0cHM6Ly9kaWdpbWluZXguY29tL2luZGV4LnBocA==","language":"en","translate_urls":true,"dynamic_translations":{"enabled":true},"language_settings":{"display":"popup","position":"top_right","flag_shape":"round","flag_width":"018","enabled_flag":true,"flag_de_type":"de","flag_en_type":"en-us","flag_es_type":"es","flag_pt_type":"pt","flag_tw_type":"zh-tw","flag_shadow_h":2,"flag_shadow_v":2,"flag_shadow_blur":12,"enabled_lang_name":false,"flag_shadow_color":"#d9d9d9","lang_name_display":"native","flag_border_radius":0,"flag_shadow_spread":0,"flag_hover_shadow_h":3,"flag_hover_shadow_v":3,"language_flag_order":["ar","hy","az","be","fr","ka","de","hi","ru","es","tr","uk","zh-cn"],"language_name_color":"#dedede","flag_hover_shadow_blur":6,"enabled_lang_short_name":true,"flag_hover_shadow_color":"#000000","flag_hover_shadow_spread":0,"language_name_hover_color":"#d6d6d6","popup_language_name_color":"#000000","popup_language_name_hover_color":"#000000"},"languages":[{"code":"zh-cn","name":"Chinese","original_name":"中文"},{"code":"es","name":"Spanish","original_name":"Español"},{"code":"fr","name":"French","original_name":"Français"},{"code":"de","name":"German","original_name":"Deutsch"},{"code":"ru","name":"Russian","original_name":"Русский"},{"code":"ka","name":"Georgian","original_name":"ქართული"},{"code":"tr","name":"Turkish","original_name":"Türkçe"},{"code":"ar","name":"Arabic","original_name":"العربية"},{"code":"az","name":"Azerbaijani","original_name":"Azərbaycanca"},{"code":"hy","name":"Armenian","original_name":"Հայերեն"},{"code":"be","name":"Belarusian","original_name":"Беларуская"},{"code":"uk","name":"Ukrainian","original_name":"Українська"},{"code":"hi","name":"Hindi","original_name":"हिन्दी"},{"code":"en","name":"English","original_name":"English"}],"structure":"subfolders","platform":"other_php","debug":false,"public_key":"pk_YNgK025ZNYdLQVprpwbLmTm0DljVi7ht","rules":[],"cached_selectors":[]}</script></head>
	
		
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
<!-- Hero Section for Non-Logged Users -->
<!-- Dashboard for logged in users -->
<div class="user-welcome">
    <div class="welcome-header">
        <div class="user-avatar">
            <i class="fas fa-user"></i>
        </div>
        <div class="welcome-text">
            <h2 data-i18n="dashboard.welcome">Content de te revoir,</h2><h2> Pranga!</h2>
            <p data-i18n="dashboard.account_overview">Voici votre aper&#xE7;u de votre compte</p>
        </div>
    </div>
</div>

<!-- Balance Cards -->
<div class="balance-cards">
        
    <div class="balance-card main-balance">
        <div class="balance-info">
            <div class="balance-label" data-i18n="dashboard.main_balance">&#xC9;quilibre principal</div>
            <div class="balance-value">38,319.47 USDT</div>
        </div>
        <div class="balance-icon">
            <i class="fas fa-wallet"></i>
        </div>
    </div>
    
    <div class="balance-card mining-balance">
        <div class="balance-info">
            <div class="balance-label" data-i18n="dashboard.daily_earnings">Gains quotidiens</div>
            <div class="balance-value">139.332000 USDT</div>
        </div>
        <div class="balance-icon">
            <i class="fas fa-chart-line"></i>
        </div>
    </div>
    <div class="balance-card yesday-balance">
        <div class="balance-info">
            <div class="balance-label" data-i18n="dashboard.daily_earnings">Hier Gainwing</div>
            <div class="balance-value">76.072000 USDT</div>
        </div>
        <div class="balance-icon">
            <i class="fas fa-chart-line"></i>
        </div>
    </div>
    <div class="balance-card ref-balance">
        <div class="balance-info">
            <div class="balance-label" data-i18n="dashboard.main_balance">Gains de r&#xE9;plication quotidiens</div>
            <div class="balance-value">9.132000 USDT</div>
        </div>
        <div class="balance-icon">
            <i class="fas fa-wallet"></i>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="quick-actions">
    <h3 data-i18n="dashboard.quick_actions">Actions rapides</h3>
    
    <div class="action-buttons">
        <a href="/fr/deposit.php" class="action-button">
            <div class="action-icon">
                <i class="fas fa-plus"></i>
            </div>
            <span data-i18n="dashboard.deposit">D&#xE9;p&#xF4;t</span>
        </a>
        
        <a href="/fr/withdraw.php" class="action-button">
            <div class="action-icon">
                <i class="fas fa-minus"></i>
            </div>
            <span data-i18n="dashboard.withdraw">Retirer</span>
        </a>
        
        <a href="/fr/daily-game.php" class="action-button">
            <div class="action-icon">
                <i class="fas fa-gamepad"></i>
            </div>
            <span data-i18n="dashboard.play_game">Jouer au jeu</span>
        </a>
        
        <a href="/fr/referrals.php" class="action-button">
            <div class="action-icon">
                <i class="fas fa-users"></i>
            </div>
            <span data-i18n="dashboard.referrals">R&#xE9;f&#xE9;rences</span>
        </a>

        <a href="/fr/mining.php" class="action-button">
            <div class="action-icon">
            <i class="miningicon"></i>
            </div>
            <span data-i18n="dashboard.referrals">Exploitation mini&#xE8;re</span>
        </a>
    </div>
</div>

<!-- Recent Transactions -->
<div class="recent-transactions">
    <div class="section-header">
        <h3 data-i18n="dashboard.recent_transactions">Transactions r&#xE9;centes</h3>
        <a href="/fr/transactions.php" class="view-all" data-i18n="dashboard.view_all">Afficher tous</a>
    </div>
    
    <div class="transactions-list">
                <div class="transaction-item">
            <div class="transaction-icon 
                tx-game">
                <i class="fas 
                fa-gamepad"></i>
            </div>
            <div class="transaction-details">
                <div class="transaction-title">
                    <span data-i18n="dashboard.game">R&#xE9;compense du jeu</span>                </div>
                <div class="transaction-date">17 mars 2025, 20:05</div>
            </div>
            <div class="transaction-amount positive">
                +4,00 USDT
            </div>
        </div>
                <div class="transaction-item">
            <div class="transaction-icon 
                tx-game">
                <i class="fas 
                fa-gamepad"></i>
            </div>
            <div class="transaction-details">
                <div class="transaction-title">
                    <span data-i18n="dashboard.game">R&#xE9;compense du jeu</span>                </div>
                <div class="transaction-date">17 mars 2025, 19:30</div>
            </div>
            <div class="transaction-amount positive">
                +6,00 USDT
            </div>
        </div>
                <div class="transaction-item">
            <div class="transaction-icon 
                tx-game">
                <i class="fas 
                fa-gamepad"></i>
            </div>
            <div class="transaction-details">
                <div class="transaction-title">
                    <span data-i18n="dashboard.game">R&#xE9;compense du jeu</span>                </div>
                <div class="transaction-date">17 mars 2025, 19:22</div>
            </div>
            <div class="transaction-amount positive">
                +4,20 USDT
            </div>
        </div>
                <div class="transaction-item">
            <div class="transaction-icon 
                tx-game">
                <i class="fas 
                fa-gamepad"></i>
            </div>
            <div class="transaction-details">
                <div class="transaction-title">
                    <span data-i18n="dashboard.game">R&#xE9;compense du jeu</span>                </div>
                <div class="transaction-date">17 mars 2025, 19:16</div>
            </div>
            <div class="transaction-amount positive">
                +4,20 USDT
            </div>
        </div>
                <div class="transaction-item">
            <div class="transaction-icon 
                tx-game">
                <i class="fas 
                fa-gamepad"></i>
            </div>
            <div class="transaction-details">
                <div class="transaction-title">
                    <span data-i18n="dashboard.game">R&#xE9;compense du jeu</span>                </div>
                <div class="transaction-date">17 mars 2025, 19:16</div>
            </div>
            <div class="transaction-amount positive">
                +4,20 USDT
            </div>
        </div>
            </div>
</div>


<!-- Include mobile-specific scripts for the index page -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Package tabs functionality
    const packageTabs = document.querySelectorAll('.package-tab');
    packageTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            // Remove active class from all tabs
            packageTabs.forEach(t => t.classList.remove('active'));
            
            // Add active class to clicked tab
            this.classList.add('active');
            
            // Hide all tab panes
            document.querySelectorAll('.tab-pane').forEach(pane => {
                pane.classList.remove('active');
            });
            
            // Show selected tab pane
            const tabId = this.getAttribute('data-tab');
            document.getElementById(tabId + '-packages').classList.add('active');
        });
    });
    
    // Testimonial slider functionality
    let currentSlide = 0;
    const slides = document.querySelectorAll('.testimonial-slide');
    const indicators = document.querySelectorAll('.testimonial-indicators .indicator');
    
    function showSlide(index) {
        // Hide all slides
        slides.forEach(slide => {
            slide.style.display = 'none';
        });
        
        // Remove active class from all indicators
        indicators.forEach(indicator => {
            indicator.classList.remove('active');
        });
        
        // Show current slide and activate indicator
        slides[index].style.display = 'block';
        indicators[index].classList.add('active');
    }
    
    // Show first slide initially
    showSlide(currentSlide);
    
    // Auto-rotate slides every 5 seconds
    setInterval(() => {
        currentSlide = (currentSlide + 1) % slides.length;
        showSlide(currentSlide);
    }, 5000);
    
    // Handle indicator clicks
    indicators.forEach((indicator, index) => {
        indicator.addEventListener('click', () => {
            currentSlide = index;
            showSlide(currentSlide);
        });
    });
    
    // Swipe functionality for testimonials
    let touchStartX = 0;
    let touchEndX = 0;
    
    const testimonialSlider = document.querySelector('.testimonial-slider');
    
    if (testimonialSlider) {
        testimonialSlider.addEventListener('touchstart', e => {
            touchStartX = e.changedTouches[0].screenX;
        });
        
        testimonialSlider.addEventListener('touchend', e => {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        });
    }
    
    function handleSwipe() {
        // Detect left or right swipe
        if (touchEndX < touchStartX - 50) {
            // Swipe left - next slide
            currentSlide = (currentSlide + 1) % slides.length;
            showSlide(currentSlide);
        } else if (touchEndX > touchStartX + 50) {
            // Swipe right - previous slide
            currentSlide = (currentSlide - 1 + slides.length) % slides.length;
            showSlide(currentSlide);
        }
    }
});
</script>

</main>

    <!-- Mobile Bottom Navigation -->
    <nav class="mobile-bottom-nav">
        <a href="/fr/index.php" class="nav-item active">
            <i class="fas fa-home"></i>
            <span data-i18n="buttons.home">Accueil</span>
        </a>
        
        <a href="/fr/packages.php" class="nav-item ">
            <i class="fas fa-cubes"></i>
            <span data-i18n="buttons.packages">Packages</span>
        </a>
        
                    <a href="/fr/daily-game.php" class="nav-item nav-item-main ">
                <div class="main-btn">
                    <i class="fas fa-trophy"></i>
                </div>
                <span data-i18n="dashboard.daily_game">Jeu</span>
            </a>
            
            <a href="/fr/notifications.php" class="nav-item ">
                <i class="fas fa-bell"></i>
                                <span data-i18n="buttons.notifications">Notifications</span>
            </a>
            
            <a href="/fr/profile.php" class="nav-item ">
                <i class="fas fa-user"></i>
                <span data-i18n="buttons.myAccount">Compte</span>
            </a>
            </nav>

    
    <!-- Backdrop Overlay -->
    <div class="backdrop-overlay" id="backdropOverlay"></div>

    
    <!-- Initialize Language System and Modal -->
    <script defer src="assets/js/bg.js"></script>
    

</body></html>