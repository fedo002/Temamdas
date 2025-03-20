<?php
// mobile/index.php - Main landing page for mobile app
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);

// Define page-specific meta information
$page_title = APP_NAME . " - " . ($isLoggedIn ? "Dashboard" : "Welcome");

// Include mobile header
include 'includes/mobile-header.php';
?>

<!-- Hero Section for Non-Logged Users -->
<?php if (!$isLoggedIn): ?>
<div class="mobile-hero">
    <div class="hero-content">
        <img src="assets/images/hero-icon.png" alt="Welcome" class="hero-icon">
        <h1 data-i18n="index.welcome_title">Welcome to</h1><h1> <?= APP_NAME ?></h1>
        <p data-i18n="index.welcome_subtitle">Earn passive income with our innovative mining and VIP solutions</p>
        
        <div class="hero-buttons">
            <a href="login.php" class="auth-button login-button">
                <i class="fas fa-sign-in-alt me-2"></i> <span data-i18n="buttons.login">Login</span>
            </a>
            <a href="register.php" class="auth-button register-button">
                <i class="fas fa-user-plus me-2"></i> <span data-i18n="buttons.register">Register</span>
            </a>
        </div>
    </div>
    
    <div class="wave-divider">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
            <path fill="#ffffff" fill-opacity="1" d="M0,96L60,112C120,128,240,160,360,186.7C480,213,600,235,720,224C840,213,960,171,1080,149.3C1200,128,1320,128,1380,128L1440,128L1440,320L1380,320C1320,320,1200,320,1080,320C960,320,840,320,720,320C600,320,480,320,360,320C240,320,120,320,60,320L0,320Z"></path>
        </svg>
    </div>
</div>

<!-- Features Section -->
<div class="features-section">
    <h2 class="section-title" data-i18n="index.features_title">Why Choose Us</h2>
    
    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-shield-alt"></i>
            </div>
            <h3 data-i18n="index.feature1_title">Secure Platform</h3>
            <p data-i18n="index.feature1_desc">Your investments are protected with top-tier security measures</p>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <h3 data-i18n="index.feature2_title">Daily Profits</h3>
            <p data-i18n="index.feature2_desc">Earn regular income with our optimized mining solutions</p>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-users"></i>
            </div>
            <h3 data-i18n="index.feature3_title">Referral System</h3>
            <p data-i18n="index.feature3_desc">Invite friends and earn additional commissions</p>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-headset"></i>
            </div>
            <h3 data-i18n="index.feature4_title">24/7 Support</h3>
            <p data-i18n="index.feature4_desc">Our team is always available to help you</p>
        </div>
    </div>
</div>

<!-- How It Works Section -->
<div class="how-it-works-section">
    <h2 class="section-title" data-i18n="index.how_it_works_title">How It Works</h2>
    
    <div class="steps-container">
        <div class="step">
            <div class="step-number">1</div>
            <div class="step-content">
                <h3 data-i18n="index.step1_title">Create Account</h3>
                <p data-i18n="index.step1_desc">Sign up for free and verify your account</p>
            </div>
        </div>
        
        <div class="step">
            <div class="step-number">2</div>
            <div class="step-content">
                <h3 data-i18n="index.step2_title">Choose Package</h3>
                <p data-i18n="index.step2_desc">Select from our VIP or mining packages</p>
            </div>
        </div>
        
        <div class="step">
            <div class="step-number">3</div>
            <div class="step-content">
                <h3 data-i18n="index.step3_title">Deposit Funds</h3>
                <p data-i18n="index.step3_desc">Add funds to your account using USDT</p>
            </div>
        </div>
        
        <div class="step">
            <div class="step-number">4</div>
            <div class="step-content">
                <h3 data-i18n="index.step4_title">Earn Profits</h3>
                <p data-i18n="index.step4_desc">Start earning daily income from your investments</p>
            </div>
        </div>
    </div>
    
    <div class="text-center mt-4">
        <a href="register.php" class="auth-button" style="width: 95%;">
            <i class="fas fa-rocket me-2"></i> <span data-i18n="index.get_started">Get Started Now</span>
        </a>
    </div>
</div>


<!-- User Testimonials -->
<div class="testimonials-section">
    <h2 class="section-title" data-i18n="index.testimonials_title">What Our Users Say</h2>
    
    <div class="testimonial-slider">
        <div class="testimonial-slide">
            <div class="testimonial-card">
                <div class="testimonial-content">
                    <div class="testimonial-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="testimonial-text">"I've been using this platform for 3 months and my investments have grown steadily. The daily earnings are consistent and withdrawals are processed quickly."</p>
                    <div class="testimonial-author">John D.</div>
                </div>
            </div>
        </div>
        
        <div class="testimonial-slide">
            <div class="testimonial-card">
                <div class="testimonial-content">
                    <div class="testimonial-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="testimonial-text">"The referral program is amazing! I've invited several friends and the commissions are adding up. Customer support is also very responsive."</p>
                    <div class="testimonial-author">Sarah M.</div>
                </div>
            </div>
        </div>
        
        <div class="testimonial-slide">
            <div class="testimonial-card">
                <div class="testimonial-content">
                    <div class="testimonial-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                    <p class="testimonial-text">"As a beginner in crypto mining, I found this platform very user-friendly. The VIP packages offer good value and the daily game is a fun bonus."</p>
                    <div class="testimonial-author">Alex T.</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="testimonial-indicators">
        <button class="indicator active" data-slide="0"></button>
        <button class="indicator" data-slide="1"></button>
        <button class="indicator" data-slide="2"></button>
    </div>
</div>

<!-- CTA Section -->
<div class="cta-section">
    <div class="cta-content">
        <h2 data-i18n="index.cta_title">Ready to Start Earning?</h2>
        <p data-i18n="index.cta_subtitle">Join thousands of users already growing their passive income</p>
        
        <div class="cta-buttons">
            <a href="register.php" class="auth-button" style="font-size: 13px;">
                <i class="fas fa-user-plus me-2"></i> <span data-i18n="buttons.register">Register Now</span>
            </a>
            <a href="about.php" class="auth-button" style="font-size: 13px;">
                <i class="fas fa-info-circle me-2"></i> <span data-i18n="buttons.learn_more">Learn More</span>
            </a>
        </div>
    </div>
</div>

<?php else: ?>
<!-- Dashboard for logged in users -->
<div class="user-welcome">
    <div class="welcome-header">
        <div class="user-avatar">
            <i class="fas fa-user"></i>
        </div>
        <div class="welcome-text">
            <h2 data-i18n="dashboard.welcome">Welcome back,</h2><h2> <?= htmlspecialchars($_SESSION['username']) ?>!</h2>
            <p data-i18n="dashboard.account_overview">Here's your account overview</p>
        </div>
    </div>
</div>

<!-- Balance Cards -->
<div class="balance-cards">
    <?php
    // Get user details
    $user = getUserDetails($_SESSION['user_id']);
    
    // Get mining earnings
    $mining_earnings = dailEearn($_SESSION['user_id']);
    $reff_earnings = dailyreff($_SESSION['user_id']);
    ?>
    
    <div class="balance-card main-balance">
        <div class="balance-info">
            <div class="balance-label" data-i18n="dashboard.main_balance">Main Balance</div>
            <div class="balance-value"><?= number_format($user['balance'], 2) ?> USDT</div>
        </div>
        <div class="balance-icon">
            <i class="fas fa-wallet"></i>
        </div>
    </div>
    
    <div class="balance-card mining-balance">
        <div class="balance-info">
            <div class="balance-label" data-i18n="dashboard.daily_earnings">Daily Earnings</div>
            <div class="balance-value"><?= number_format($mining_earnings['today'], 2) ?> USDT</div>
        </div>
        <div class="balance-icon">
            <i class="fas fa-chart-line"></i>
        </div>
    </div>
    <div class="balance-card yesday-balance">
        <div class="balance-info">
            <div class="balance-label" data-i18n="dashboard.daily_earnings">Yesterday Earnings</div>
            <div class="balance-value"><?= number_format($mining_earnings['yesterday'], 2) ?> USDT</div>
        </div>
        <div class="balance-icon">
            <i class="fas fa-chart-line"></i>
        </div>
    </div>
    <div class="balance-card ref-balance">
        <div class="balance-info">
            <div class="balance-label" data-i18n="dashboard.main_balance">Daily Refferal Earnings</div>
            <div class="balance-value"><?= number_format($reff_earnings['today'], 2) ?> USDT</div>
        </div>
        <div class="balance-icon">
            <i class="fas fa-wallet"></i>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="quick-actions">
    <h3 data-i18n="dashboard.quick_actions">Quick Actions</h3>
    
    <div class="action-buttons">
        <a href="deposit.php" class="action-button">
            <div class="action-icon">
                <i class="fas fa-plus"></i>
            </div>
            <span data-i18n="dashboard.deposit">Deposit</span>
        </a>
        
        <a href="withdraw.php" class="action-button">
            <div class="action-icon">
                <i class="fas fa-minus"></i>
            </div>
            <span data-i18n="dashboard.withdraw">Withdraw</span>
        </a>
        
        <a href="daily-game.php" class="action-button">
            <div class="action-icon">
                <i class="fas fa-gamepad"></i>
            </div>
            <span data-i18n="dashboard.play_game">Play Game</span>
        </a>
        
        <a href="referrals.php" class="action-button">
            <div class="action-icon">
                <i class="fas fa-users"></i>
            </div>
            <span data-i18n="dashboard.referrals">Referrals</span>
        </a>

        <a href="mining.php" class="action-button">
            <div class="action-icon">
            <i class="miningicon"></i>
            </div>
            <span data-i18n="dashboard.referrals">Mining</span>
        </a>
    </div>
</div>

<!-- Recent Transactions -->
<div class="recent-transactions">
    <div class="section-header">
        <h3 data-i18n="dashboard.recent_transactions">Recent Transactions</h3>
        <a href="transactions.php" class="view-all" data-i18n="dashboard.view_all">View All</a>
    </div>
    
    <div class="transactions-list">
        <?php
        // Get recent transactions
        $transactions = getUserTransactions($_SESSION['user_id'], 5);
        
        if (!empty($transactions)):
            foreach($transactions as $tx):
        ?>
        <div class="transaction-item">
            <div class="transaction-icon 
                <?php
                if ($tx['type'] == 'deposit') echo 'tx-deposit';
                elseif ($tx['type'] == 'withdraw') echo 'tx-withdraw';
                elseif ($tx['type'] == 'referral') echo 'tx-referral';
                elseif ($tx['type'] == 'mining') echo 'tx-mining';
                elseif ($tx['type'] == 'game') echo 'tx-game';
                elseif ($tx['type'] == 'vip') echo 'tx-vip';
                else echo 'tx-other';
                ?>">
                <i class="fas 
                <?php
                if ($tx['type'] == 'deposit') echo 'fa-arrow-down';
                elseif ($tx['type'] == 'withdraw') echo 'fa-arrow-up';
                elseif ($tx['type'] == 'referral') echo 'fa-user-friends';
                elseif ($tx['type'] == 'mining') echo 'fa-microchip';
                elseif ($tx['type'] == 'game') echo 'fa-gamepad';
                elseif ($tx['type'] == 'vip') echo 'fa-crown';
                else echo 'fa-exchange-alt';
                ?>"></i>
            </div>
            <div class="transaction-details">
                <div class="transaction-title">
                    <?php
                    if ($tx['type'] == 'deposit') echo '<span data-i18n="dashboard.deposit">Deposit</span>';
                    elseif ($tx['type'] == 'withdraw') echo '<span data-i18n="dashboard.withdraw">Withdrawal</span>';
                    elseif ($tx['type'] == 'referral') echo '<span data-i18n="dashboard.referral">Referral Commission</span>';
                    elseif ($tx['type'] == 'mining') echo '<span data-i18n="dashboard.mining">Mining Package</span>';
                    elseif ($tx['type'] == 'game') echo '<span data-i18n="dashboard.game">Game Reward</span>';
                    elseif ($tx['type'] == 'vip') echo '<span data-i18n="dashboard.vip_package">VIP Package</span>';
                    else echo ucfirst($tx['type']);
                    ?>
                </div>
                <div class="transaction-date"><?= date('d M Y, H:i', strtotime($tx['created_at'])) ?></div>
            </div>
            <div class="transaction-amount <?= $tx['amount'] >= 0 ? 'positive' : 'negative' ?>">
                <?= ($tx['amount'] >= 0 ? '+' : '') . number_format($tx['amount'], 2) ?> USDT
            </div>
        </div>
        <?php
            endforeach;
        else:
        ?>
        <div class="no-transactions">
            <i class="fas fa-receipt"></i>
            <p data-i18n="dashboard.no_transactions">No transactions yet</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php endif; ?>

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

<?php
// Include mobile footer
include 'includes/mobile-footer.php';
?>