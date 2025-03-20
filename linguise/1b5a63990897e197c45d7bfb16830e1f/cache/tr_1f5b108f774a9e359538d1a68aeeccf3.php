<?php die(); ?><!DOCTYPE html><html lang="tr" dir="ltr"><head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#7367f0">
    <title>Digiminex - G&#xF6;sterge Tablosu | Digiminex Mobile</title>
    
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
            <a class="header-logo" href="/tr/index.php">
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
            <h2 data-i18n="dashboard.welcome">Tekrar ho&#x15F;geldiniz,</h2><h2> Paragara!</h2>
            <p data-i18n="dashboard.account_overview">&#x130;&#x15F;te hesab&#x131;n&#x131;za genel bak&#x131;&#x15F;</p>
        </div>
    </div>
</div>

<!-- Balance Cards -->
<div class="balance-cards">
        
    <div class="balance-card main-balance">
        <div class="balance-info">
            <div class="balance-label" data-i18n="dashboard.main_balance">Ana denge</div>
            <div class="balance-value">558.00 USDT</div>
        </div>
        <div class="balance-icon">
            <i class="fas fa-wallet"></i>
        </div>
    </div>
    
    <div class="balance-card mining-balance">
        <div class="balance-info">
            <div class="balance-label" data-i18n="dashboard.daily_earnings">G&#xFC;nl&#xFC;k Kazan&#xE7;</div>
            <div class="balance-value">30.00 USDT</div>
        </div>
        <div class="balance-icon">
            <i class="fas fa-chart-line"></i>
        </div>
    </div>
    <div class="balance-card yesday-balance">
        <div class="balance-info">
            <div class="balance-label" data-i18n="dashboard.daily_earnings">D&#xFC;n Kazan&#xE7;</div>
            <div class="balance-value">60.00 USDT</div>
        </div>
        <div class="balance-icon">
            <i class="fas fa-chart-line"></i>
        </div>
    </div>
    <div class="balance-card ref-balance">
        <div class="balance-info">
            <div class="balance-label" data-i18n="dashboard.main_balance">G&#xFC;nl&#xFC;k Referal Kazan&#xE7;lar</div>
            <div class="balance-value">0.00 USDT</div>
        </div>
        <div class="balance-icon">
            <i class="fas fa-wallet"></i>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="quick-actions">
    <h3 data-i18n="dashboard.quick_actions">H&#x131;zl&#x131; eylemler</h3>
    
    <div class="action-buttons">
        <a href="/tr/deposit.php" class="action-button">
            <div class="action-icon">
                <i class="fas fa-plus"></i>
            </div>
            <span data-i18n="dashboard.deposit">Yat&#x131;rmak</span>
        </a>
        
        <a href="/tr/withdraw.php" class="action-button">
            <div class="action-icon">
                <i class="fas fa-minus"></i>
            </div>
            <span data-i18n="dashboard.withdraw">Geri &#xE7;ekilmek</span>
        </a>
        
        <a href="/tr/daily-game.php" class="action-button">
            <div class="action-icon">
                <i class="fas fa-gamepad"></i>
            </div>
            <span data-i18n="dashboard.play_game">Oyun Oyunu</span>
        </a>
        
        <a href="/tr/referrals.php" class="action-button">
            <div class="action-icon">
                <i class="fas fa-users"></i>
            </div>
            <span data-i18n="dashboard.referrals">Y&#xF6;nlendirmeler</span>
        </a>

        <a href="/tr/mining.php" class="action-button">
            <div class="action-icon">
            <i class="miningicon"></i>
            </div>
            <span data-i18n="dashboard.referrals">Madencilik</span>
        </a>
    </div>
</div>

<!-- Recent Transactions -->
<div class="recent-transactions">
    <div class="section-header">
        <h3 data-i18n="dashboard.recent_transactions">Son i&#x15F;lemler</h3>
        <a href="/tr/transactions.php" class="view-all" data-i18n="dashboard.view_all">Hepsini g&#xF6;r&#xFC;nt&#xFC;le</a>
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
                    <span data-i18n="dashboard.game">Oyun &#xF6;d&#xFC;l&#xFC;</span>                </div>
                <div class="transaction-date">20 Mar 2025, 01:32</div>
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
                    <span data-i18n="dashboard.game">Oyun &#xF6;d&#xFC;l&#xFC;</span>                </div>
                <div class="transaction-date">19 Mar 2025, 08:54</div>
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
                    <span data-i18n="dashboard.game">Oyun &#xF6;d&#xFC;l&#xFC;</span>                </div>
                <div class="transaction-date">19 Mar 2025, 01:06</div>
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
                    <span data-i18n="dashboard.game">Oyun &#xF6;d&#xFC;l&#xFC;</span>                </div>
                <div class="transaction-date">18 Mar 2025, 22:54</div>
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
                    <span data-i18n="dashboard.game">Oyun &#xF6;d&#xFC;l&#xFC;</span>                </div>
                <div class="transaction-date">18 Mar 2025, 22:54</div>
            </div>
            <div class="transaction-amount positive">
                +29.00 USDT
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
        <a href="/tr/index.php" class="nav-item active">
            <i class="fas fa-home"></i>
            <span data-i18n="buttons.home">Ana sayfa</span>
        </a>
        
        <a href="/tr/packages.php" class="nav-item ">
            <i class="fas fa-cubes"></i>
            <span data-i18n="buttons.packages">Paketler</span>
        </a>
        
                    <a href="/tr/daily-game.php" class="nav-item nav-item-main ">
                <div class="main-btn">
                    <i class="fas fa-trophy"></i>
                </div>
                <span data-i18n="dashboard.daily_game">Oyun</span>
            </a>
            
            <a href="/tr/notifications.php" class="nav-item ">
                <i class="fas fa-bell"></i>
                                <span data-i18n="buttons.notifications">Bildirimler</span>
            </a>
            
            <a href="/tr/profile.php" class="nav-item ">
                <i class="fas fa-user"></i>
                <span data-i18n="buttons.myAccount">Hesap</span>
            </a>
            </nav>

    
    <!-- Backdrop Overlay -->
    <div class="backdrop-overlay" id="backdropOverlay"></div>
    
<script defer src="https://static.cloudflareinsights.com/beacon.min.js/vcd15cbe7772f49c399c6a5babf22c1241717689176015" integrity="sha512-ZpsOmlRQV6y907TI0dKBHq9Md29nnaEIPlkf84rnaERnq6zvWvPUqr2ft8M1aS28oN72PdrCzSjY4U6VaAw1EQ==" data-cf-beacon="{&quot;rayId&quot;:&quot;923655c1ddfeb8fd&quot;,&quot;version&quot;:&quot;2025.1.0&quot;,&quot;r&quot;:1,&quot;serverTiming&quot;:{&quot;name&quot;:{&quot;cfExtPri&quot;:true,&quot;cfL4&quot;:true,&quot;cfSpeedBrain&quot;:true,&quot;cfCacheStatus&quot;:true}},&quot;token&quot;:&quot;af7256c2312a464d847f1edbf0c05061&quot;,&quot;b&quot;:1}" crossorigin="anonymous"></script>

</body></html>