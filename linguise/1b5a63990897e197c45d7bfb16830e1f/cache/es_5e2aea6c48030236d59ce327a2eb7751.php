<?php die(); ?><!DOCTYPE html><html lang="es" dir="ltr"><head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#7367f0">
    <title>Digiminex - Panel de control | M&#xF3;vil Digiminex</title>
    
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
	</style></head>
	
		
<body>
    <!-- Mobile Header -->
    <header class="mobile-header">
        <div class="header-container">
            <a class="header-logo" href="/es/index.php">
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
            <h2 data-i18n="dashboard.welcome">Bienvenido de nuevo,</h2><h2> &#xA1;Paragara!</h2>
            <p data-i18n="dashboard.account_overview">Aqu&#xED; est&#xE1; su descripci&#xF3;n de su cuenta</p>
        </div>
    </div>
</div>

<!-- Balance Cards -->
<div class="balance-cards">
        
    <div class="balance-card main-balance">
        <div class="balance-info">
            <div class="balance-label" data-i18n="dashboard.main_balance">Saldo principal</div>
            <div class="balance-value">528.00 USDT</div>
        </div>
        <div class="balance-icon">
            <i class="fas fa-wallet"></i>
        </div>
    </div>
    
    <div class="balance-card mining-balance">
        <div class="balance-info">
            <div class="balance-label" data-i18n="dashboard.daily_earnings">Ganancias diarias</div>
            <div class="balance-value">60.00 USDT</div>
        </div>
        <div class="balance-icon">
            <i class="fas fa-chart-line"></i>
        </div>
    </div>
    <div class="balance-card yesday-balance">
        <div class="balance-info">
            <div class="balance-label" data-i18n="dashboard.daily_earnings">Ganancias de ayer</div>
            <div class="balance-value">468.00 USDT</div>
        </div>
        <div class="balance-icon">
            <i class="fas fa-chart-line"></i>
        </div>
    </div>
    <div class="balance-card ref-balance">
        <div class="balance-info">
            <div class="balance-label" data-i18n="dashboard.main_balance">Ganancias refferales diarias</div>
            <div class="balance-value">0.00 USDT</div>
        </div>
        <div class="balance-icon">
            <i class="fas fa-wallet"></i>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="quick-actions">
    <h3 data-i18n="dashboard.quick_actions">Acciones r&#xE1;pidas</h3>
    
    <div class="action-buttons">
        <a href="/es/deposit.php" class="action-button">
            <div class="action-icon">
                <i class="fas fa-plus"></i>
            </div>
            <span data-i18n="dashboard.deposit">Dep&#xF3;sito</span>
        </a>
        
        <a href="/es/withdraw.php" class="action-button">
            <div class="action-icon">
                <i class="fas fa-minus"></i>
            </div>
            <span data-i18n="dashboard.withdraw">Retirar</span>
        </a>
        
        <a href="/es/daily-game.php" class="action-button">
            <div class="action-icon">
                <i class="fas fa-gamepad"></i>
            </div>
            <span data-i18n="dashboard.play_game">Juego</span>
        </a>
        
        <a href="/es/referrals.php" class="action-button">
            <div class="action-icon">
                <i class="fas fa-users"></i>
            </div>
            <span data-i18n="dashboard.referrals">Referencias</span>
        </a>

        <a href="/es/mining.php" class="action-button">
            <div class="action-icon">
            <i class="miningicon"></i>
            </div>
            <span data-i18n="dashboard.referrals">Miner&#xED;a</span>
        </a>
    </div>
</div>

<!-- Recent Transactions -->
<div class="recent-transactions">
    <div class="section-header">
        <h3 data-i18n="dashboard.recent_transactions">Transacciones recientes</h3>
        <a href="/es/transactions.php" class="view-all" data-i18n="dashboard.view_all">Ver todo</a>
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
                    <span data-i18n="dashboard.game">Recompensa del juego</span>                </div>
                <div class="transaction-date">19 de marzo de 2025, 08:54</div>
            </div>
            <div class="transaction-amount positive">
                +30.00 USDT
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
                    <span data-i18n="dashboard.game">Recompensa del juego</span>                </div>
                <div class="transaction-date">19 de marzo de 2025, 01:06</div>
            </div>
            <div class="transaction-amount positive">
                +30.00 USDT
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
                    <span data-i18n="dashboard.game">Recompensa del juego</span>                </div>
                <div class="transaction-date">18 de marzo de 2025, 22:54</div>
            </div>
            <div class="transaction-amount positive">
                +29.00 USDT
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
                    <span data-i18n="dashboard.game">Recompensa del juego</span>                </div>
                <div class="transaction-date">18 de marzo de 2025, 22:54</div>
            </div>
            <div class="transaction-amount positive">
                +29.00 USDT
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
                    <span data-i18n="dashboard.game">Recompensa del juego</span>                </div>
                <div class="transaction-date">18 de marzo de 2025, 22:53</div>
            </div>
            <div class="transaction-amount positive">
                +40.00 USDT
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
        <a href="/es/index.php" class="nav-item active">
            <i class="fas fa-home"></i>
            <span data-i18n="buttons.home">Inicio</span>
        </a>
        
        <a href="/es/packages.php" class="nav-item ">
            <i class="fas fa-cubes"></i>
            <span data-i18n="buttons.packages">Paquetes</span>
        </a>
        
                    <a href="/es/daily-game.php" class="nav-item nav-item-main ">
                <div class="main-btn">
                    <i class="fas fa-trophy"></i>
                </div>
                <span data-i18n="dashboard.daily_game">Juego</span>
            </a>
            
            <a href="/es/notifications.php" class="nav-item ">
                <i class="fas fa-bell"></i>
                                <span data-i18n="buttons.notifications">Notificaciones</span>
            </a>
            
            <a href="/es/profile.php" class="nav-item ">
                <i class="fas fa-user"></i>
                <span data-i18n="buttons.myAccount">Cuenta</span>
            </a>
            </nav>

    
    <!-- Backdrop Overlay -->
    <div class="backdrop-overlay" id="backdropOverlay"></div>
    

</body></html>