<?php die(); ?><!DOCTYPE html><html lang="ru" dir="ltr"><head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#7367f0">
    <title>Digiminex - Dashboard | Digiminex Mobile</title>
    
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

	<script async src="https://static.linguise.com/script-js/switcher.bundle.js?d=pk_HTDA4a15TTJcZ8Cb0xIgJorDLHtgAOig"></script>
	<!--<script type="text/javascript" src="https://cdn.weglot.com/weglot.min.js"></script>
<script>
    Weglot.initialize({
        api_key: 'wg_79f01ccd102ac475b58cd40c45b213ca8'
    });
</script>-->
	
	
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
            <a class="header-logo" href="/ru/index.php">
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
            <h2 data-i18n="dashboard.welcome">&#x414;&#x43E;&#x431;&#x440;&#x43E; &#x43F;&#x43E;&#x436;&#x430;&#x43B;&#x43E;&#x432;&#x430;&#x442;&#x44C;,</h2><h2> &#x41F;&#x430;&#x440;&#x430;&#x433;&#x430;&#x440;&#x430;!</h2>
            <p data-i18n="dashboard.account_overview">&#x412;&#x43E;&#x442; &#x43E;&#x431;&#x437;&#x43E;&#x440; &#x432;&#x430;&#x448;&#x435;&#x439; &#x443;&#x447;&#x435;&#x442;&#x43D;&#x43E;&#x439; &#x437;&#x430;&#x43F;&#x438;&#x441;&#x438;</p>
        </div>
    </div>
</div>

<!-- Balance Cards -->
<div class="balance-cards">
        
    <div class="balance-card main-balance">
        <div class="balance-info">
            <div class="balance-label" data-i18n="dashboard.main_balance">&#x41E;&#x441;&#x43D;&#x43E;&#x432;&#x43D;&#x43E;&#x439; &#x431;&#x430;&#x43B;&#x430;&#x43D;&#x441;</div>
            <div class="balance-value">1116,00 USDT</div>
        </div>
        <div class="balance-icon">
            <i class="fas fa-wallet"></i>
        </div>
    </div>
    
    <div class="balance-card mining-balance">
        <div class="balance-info">
            <div class="balance-label" data-i18n="dashboard.daily_earnings">&#x415;&#x436;&#x435;&#x434;&#x43D;&#x435;&#x432;&#x43D;&#x44B;&#x435; &#x434;&#x43E;&#x445;&#x43E;&#x434;&#x44B;</div>
            <div class="balance-value">1 047,000000 USDT</div>
        </div>
        <div class="balance-icon">
            <i class="fas fa-chart-line"></i>
        </div>
    </div>
    <div class="balance-card yesday-balance">
        <div class="balance-info">
            <div class="balance-label" data-i18n="dashboard.daily_earnings">&#x412;&#x447;&#x435;&#x440;&#x430; &#x434;&#x43E;&#x445;&#x43E;&#x434;&#x44B;</div>
            <div class="balance-value">69.000000 USDT</div>
        </div>
        <div class="balance-icon">
            <i class="fas fa-chart-line"></i>
        </div>
    </div>
    <div class="balance-card ref-balance">
        <div class="balance-info">
            <div class="balance-label" data-i18n="dashboard.main_balance">&#x415;&#x436;&#x435;&#x434;&#x43D;&#x435;&#x432;&#x43D;&#x44B;&#x435; &#x440;&#x435;&#x444;&#x435;&#x440;&#x430;&#x43B;&#x44C;&#x43D;&#x44B;&#x435; &#x434;&#x43E;&#x445;&#x43E;&#x434;&#x44B;</div>
            <div class="balance-value">0,000000 USDT</div>
        </div>
        <div class="balance-icon">
            <i class="fas fa-wallet"></i>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="quick-actions">
    <h3 data-i18n="dashboard.quick_actions">&#x411;&#x44B;&#x441;&#x442;&#x440;&#x44B;&#x435; &#x434;&#x435;&#x439;&#x441;&#x442;&#x432;&#x438;&#x44F;</h3>
    
    <div class="action-buttons">
        <a href="/ru/deposit.php" class="action-button">
            <div class="action-icon">
                <i class="fas fa-plus"></i>
            </div>
            <span data-i18n="dashboard.deposit">&#x414;&#x435;&#x43F;&#x43E;&#x437;&#x438;&#x442;&#x43D;&#x44B;&#x439;</span>
        </a>
        
        <a href="/ru/withdraw.php" class="action-button">
            <div class="action-icon">
                <i class="fas fa-minus"></i>
            </div>
            <span data-i18n="dashboard.withdraw">&#x41E;&#x442;&#x437;&#x44B;&#x432;&#x430;&#x442;&#x44C;</span>
        </a>
        
        <a href="/ru/daily-game.php" class="action-button">
            <div class="action-icon">
                <i class="fas fa-gamepad"></i>
            </div>
            <span data-i18n="dashboard.play_game">&#x418;&#x433;&#x440;&#x430;&#x442;&#x44C; &#x432; &#x438;&#x433;&#x440;&#x443;</span>
        </a>
        
        <a href="/ru/referrals.php" class="action-button">
            <div class="action-icon">
                <i class="fas fa-users"></i>
            </div>
            <span data-i18n="dashboard.referrals">&#x420;&#x435;&#x444;&#x435;&#x440;&#x430;&#x43B;&#x44B;</span>
        </a>

        <a href="/ru/mining.php" class="action-button">
            <div class="action-icon">
            <i class="miningicon"></i>
            </div>
            <span data-i18n="dashboard.referrals">&#x414;&#x43E;&#x431;&#x44B;&#x447;&#x430;</span>
        </a>
    </div>
</div>

<!-- Recent Transactions -->
<div class="recent-transactions">
    <div class="section-header">
        <h3 data-i18n="dashboard.recent_transactions">&#x41D;&#x435;&#x434;&#x430;&#x432;&#x43D;&#x438;&#x435; &#x442;&#x440;&#x430;&#x43D;&#x437;&#x430;&#x43A;&#x446;&#x438;&#x438;</h3>
        <a href="/ru/transactions.php" class="view-all" data-i18n="dashboard.view_all">&#x41F;&#x440;&#x43E;&#x441;&#x43C;&#x43E;&#x442;&#x440;&#x435;&#x442;&#x44C; &#x432;&#x441;&#x435;</a>
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
                    <span data-i18n="dashboard.game">&#x41D;&#x430;&#x433;&#x440;&#x430;&#x434;&#x430; &#x438;&#x433;&#x440;&#x44B;</span>                </div>
                <div class="transaction-date">17 &#x43C;&#x430;&#x440;&#x442;&#x430; 2025 &#x433;., 16:02</div>
            </div>
            <div class="transaction-amount positive">
                +29,00 USDT
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
                    <span data-i18n="dashboard.game">&#x41D;&#x430;&#x433;&#x440;&#x430;&#x434;&#x430; &#x438;&#x433;&#x440;&#x44B;</span>                </div>
                <div class="transaction-date">17 &#x43C;&#x430;&#x440;&#x442;&#x430; 2025 &#x433;., 16:01</div>
            </div>
            <div class="transaction-amount positive">
                +30,00 USDT
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
                    <span data-i18n="dashboard.game">&#x41D;&#x430;&#x433;&#x440;&#x430;&#x434;&#x430; &#x438;&#x433;&#x440;&#x44B;</span>                </div>
                <div class="transaction-date">17 &#x43C;&#x430;&#x440;&#x442;&#x430; 2025 &#x433;., 16:00</div>
            </div>
            <div class="transaction-amount positive">
                +30,00 USDT
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
                    <span data-i18n="dashboard.game">&#x41D;&#x430;&#x433;&#x440;&#x430;&#x434;&#x430; &#x438;&#x433;&#x440;&#x44B;</span>                </div>
                <div class="transaction-date">17 &#x43C;&#x430;&#x440;&#x442;&#x430; 2025 &#x433;., 15:59</div>
            </div>
            <div class="transaction-amount positive">
                +30,00 USDT
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
                    <span data-i18n="dashboard.game">&#x41D;&#x430;&#x433;&#x440;&#x430;&#x434;&#x430; &#x438;&#x433;&#x440;&#x44B;</span>                </div>
                <div class="transaction-date">17 &#x43C;&#x430;&#x440;&#x442;&#x430; 2025 &#x433;., 15:58</div>
            </div>
            <div class="transaction-amount positive">
                +20,00 USDT
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
        <a href="/ru/index.php" class="nav-item active">
            <i class="fas fa-home"></i>
            <span data-i18n="buttons.home">&#x414;&#x43E;&#x43C;&#x430;&#x448;&#x43D;&#x44F;&#x44F; &#x441;&#x442;&#x440;&#x430;&#x43D;&#x438;&#x446;&#x430;</span>
        </a>
        
        <a href="/ru/packages.php" class="nav-item ">
            <i class="fas fa-cubes"></i>
            <span data-i18n="buttons.packages">&#x41F;&#x430;&#x43A;&#x435;&#x442;&#x44B;</span>
        </a>
        
                    <a href="/ru/daily-game.php" class="nav-item nav-item-main ">
                <div class="main-btn">
                    <i class="fas fa-trophy"></i>
                </div>
                <span data-i18n="dashboard.daily_game">&#x418;&#x433;&#x440;&#x430;</span>
            </a>
            
            <a href="/ru/notifications.php" class="nav-item ">
                <i class="fas fa-bell"></i>
                                <span data-i18n="buttons.notifications">&#x423;&#x432;&#x435;&#x434;&#x43E;&#x43C;&#x43B;&#x435;&#x43D;&#x438;&#x44F;</span>
            </a>
            
            <a href="/ru/profile.php" class="nav-item ">
                <i class="fas fa-user"></i>
                <span data-i18n="buttons.myAccount">&#x421;&#x447;&#x435;&#x442;</span>
            </a>
            </nav>

    
    <!-- Backdrop Overlay -->
    <div class="backdrop-overlay" id="backdropOverlay"></div>

    
    <!-- Initialize Language System and Modal -->
    <script defer src="assets/js/bg.js"></script>
    

</body></html>