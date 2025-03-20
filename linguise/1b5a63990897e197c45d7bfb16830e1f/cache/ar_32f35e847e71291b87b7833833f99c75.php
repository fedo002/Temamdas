<?php die(); ?><!DOCTYPE html><html lang="ar" dir="rtl"><head>
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
            <a class="header-logo" href="/ar/index.php">
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
            <h2 data-i18n="dashboard.welcome">&#x645;&#x631;&#x62D;&#x628;&#x64B;&#x627; &#x628;&#x639;&#x648;&#x62F;&#x62A;&#x643;&#x60C;</h2><h2> &#x628;&#x631;&#x627;&#x646;&#x62C;&#x627;!</h2>
            <p data-i18n="dashboard.account_overview">&#x647;&#x627; &#x647;&#x64A; &#x646;&#x638;&#x631;&#x629; &#x639;&#x627;&#x645;&#x629; &#x639;&#x644;&#x649; &#x62D;&#x633;&#x627;&#x628;&#x643;</p>
        </div>
    </div>
</div>

<!-- Balance Cards -->
<div class="balance-cards">
        
    <div class="balance-card main-balance">
        <div class="balance-info">
            <div class="balance-label" data-i18n="dashboard.main_balance">&#x627;&#x644;&#x62A;&#x648;&#x627;&#x632;&#x646; &#x627;&#x644;&#x631;&#x626;&#x64A;&#x633;&#x64A;</div>
            <div class="balance-value">38&#x60C;355.87 USDT</div>
        </div>
        <div class="balance-icon">
            <i class="fas fa-wallet"></i>
        </div>
    </div>
    
    <div class="balance-card mining-balance">
        <div class="balance-info">
            <div class="balance-label" data-i18n="dashboard.daily_earnings">&#x627;&#x644;&#x623;&#x631;&#x628;&#x627;&#x62D; &#x627;&#x644;&#x64A;&#x648;&#x645;&#x64A;&#x629;</div>
            <div class="balance-value">7.000000 USDT</div>
        </div>
        <div class="balance-icon">
            <i class="fas fa-chart-line"></i>
        </div>
    </div>
    <div class="balance-card yesday-balance">
        <div class="balance-info">
            <div class="balance-label" data-i18n="dashboard.daily_earnings">&#x627;&#x644;&#x623;&#x631;&#x628;&#x627;&#x62D; &#x628;&#x627;&#x644;&#x623;&#x645;&#x633;</div>
            <div class="balance-value">168.732000 USDT</div>
        </div>
        <div class="balance-icon">
            <i class="fas fa-chart-line"></i>
        </div>
    </div>
    <div class="balance-card ref-balance">
        <div class="balance-info">
            <div class="balance-label" data-i18n="dashboard.main_balance">&#x627;&#x644;&#x623;&#x631;&#x628;&#x627;&#x62D; &#x627;&#x644;&#x64A;&#x648;&#x645;&#x64A;&#x629; &#x627;&#x644;&#x639;&#x643;&#x633;&#x64A;&#x629;</div>
            <div class="balance-value">0.000000 USDT</div>
        </div>
        <div class="balance-icon">
            <i class="fas fa-wallet"></i>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="quick-actions">
    <h3 data-i18n="dashboard.quick_actions">&#x625;&#x62C;&#x631;&#x627;&#x621;&#x627;&#x62A; &#x633;&#x631;&#x64A;&#x639;&#x629;</h3>
    
    <div class="action-buttons">
        <a href="/ar/deposit.php" class="action-button">
            <div class="action-icon">
                <i class="fas fa-plus"></i>
            </div>
            <span data-i18n="dashboard.deposit">&#x625;&#x64A;&#x62F;&#x627;&#x639;</span>
        </a>
        
        <a href="/ar/withdraw.php" class="action-button">
            <div class="action-icon">
                <i class="fas fa-minus"></i>
            </div>
            <span data-i18n="dashboard.withdraw">&#x64A;&#x646;&#x633;&#x62D;&#x628;</span>
        </a>
        
        <a href="/ar/daily-game.php" class="action-button">
            <div class="action-icon">
                <i class="fas fa-gamepad"></i>
            </div>
            <span data-i18n="dashboard.play_game">&#x644;&#x639;&#x628;&#x629; &#x627;&#x644;&#x644;&#x639;&#x628;</span>
        </a>
        
        <a href="/ar/referrals.php" class="action-button">
            <div class="action-icon">
                <i class="fas fa-users"></i>
            </div>
            <span data-i18n="dashboard.referrals">&#x627;&#x644;&#x625;&#x62D;&#x627;&#x644;&#x627;&#x62A;</span>
        </a>

        <a href="/ar/mining.php" class="action-button">
            <div class="action-icon">
            <i class="miningicon"></i>
            </div>
            <span data-i18n="dashboard.referrals">&#x627;&#x644;&#x62A;&#x639;&#x62F;&#x64A;&#x646;</span>
        </a>
    </div>
</div>

<!-- Recent Transactions -->
<div class="recent-transactions">
    <div class="section-header">
        <h3 data-i18n="dashboard.recent_transactions">&#x627;&#x644;&#x645;&#x639;&#x627;&#x645;&#x644;&#x627;&#x62A; &#x627;&#x644;&#x623;&#x62E;&#x64A;&#x631;&#x629;</h3>
        <a href="/ar/transactions.php" class="view-all" data-i18n="dashboard.view_all">&#x639;&#x631;&#x636; &#x627;&#x644;&#x643;&#x644;</a>
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
                    <span data-i18n="dashboard.game">&#x645;&#x643;&#x627;&#x641;&#x623;&#x629; &#x627;&#x644;&#x644;&#x639;&#x628;&#x629;</span>                </div>
                <div class="transaction-date">18 &#x645;&#x627;&#x631;&#x633; 2025 &#x60C; 03:39</div>
            </div>
            <div class="transaction-amount positive">
                +4.00 USDT
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
                    <span data-i18n="dashboard.game">&#x645;&#x643;&#x627;&#x641;&#x623;&#x629; &#x627;&#x644;&#x644;&#x639;&#x628;&#x629;</span>                </div>
                <div class="transaction-date">18 &#x645;&#x627;&#x631;&#x633; 2025 &#x60C; 03:36</div>
            </div>
            <div class="transaction-amount positive">
                +3.00 USDT
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
                    <span data-i18n="dashboard.game">&#x645;&#x643;&#x627;&#x641;&#x623;&#x629; &#x627;&#x644;&#x644;&#x639;&#x628;&#x629;</span>                </div>
                <div class="transaction-date">17 &#x645;&#x627;&#x631;&#x633; 2025 &#x60C; 21:01</div>
            </div>
            <div class="transaction-amount positive">
                +3.00 USDT
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
                    <span data-i18n="dashboard.game">&#x645;&#x643;&#x627;&#x641;&#x623;&#x629; &#x627;&#x644;&#x644;&#x639;&#x628;&#x629;</span>                </div>
                <div class="transaction-date">17 &#x645;&#x627;&#x631;&#x633; 2025 &#x60C; 21:01</div>
            </div>
            <div class="transaction-amount positive">
                +4.20 USDT
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
                    <span data-i18n="dashboard.game">&#x645;&#x643;&#x627;&#x641;&#x623;&#x629; &#x627;&#x644;&#x644;&#x639;&#x628;&#x629;</span>                </div>
                <div class="transaction-date">17 &#x645;&#x627;&#x631;&#x633; 2025 &#x60C; 21:01</div>
            </div>
            <div class="transaction-amount positive">
                +4.20 USDT
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
        <a href="/ar/index.php" class="nav-item active">
            <i class="fas fa-home"></i>
            <span data-i18n="buttons.home">&#x627;&#x644;&#x631;&#x626;&#x64A;&#x633;&#x64A;&#x629;</span>
        </a>
        
        <a href="/ar/packages.php" class="nav-item ">
            <i class="fas fa-cubes"></i>
            <span data-i18n="buttons.packages">&#x62D;&#x632;&#x645;</span>
        </a>
        
                    <a href="/ar/daily-game.php" class="nav-item nav-item-main ">
                <div class="main-btn">
                    <i class="fas fa-trophy"></i>
                </div>
                <span data-i18n="dashboard.daily_game">&#x644;&#x639;&#x628;&#x629;</span>
            </a>
            
            <a href="/ar/notifications.php" class="nav-item ">
                <i class="fas fa-bell"></i>
                                <span data-i18n="buttons.notifications">&#x625;&#x634;&#x639;&#x627;&#x631;&#x627;&#x62A;</span>
            </a>
            
            <a href="/ar/profile.php" class="nav-item ">
                <i class="fas fa-user"></i>
                <span data-i18n="buttons.myAccount">&#x62D;&#x633;&#x627;&#x628;</span>
            </a>
            </nav>

    
    <!-- Backdrop Overlay -->
    <div class="backdrop-overlay" id="backdropOverlay"></div>

    
    <!-- Initialize Language System and Modal -->
    <script defer src="assets/js/bg.js"></script>
    

</body></html>