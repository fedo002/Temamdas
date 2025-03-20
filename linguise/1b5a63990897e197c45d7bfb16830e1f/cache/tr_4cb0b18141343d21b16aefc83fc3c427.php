<?php die(); ?><!DOCTYPE html><html lang="tr" dir="ltr"><head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#7367f0">
    <title>Profilim | Digiminex Mobile</title>
    
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
        <link rel="stylesheet" href="assets/css/mobile-profile.css">
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
<div class="profile-page">
    <!-- Profile Header -->
    <div class="profile-header">
        <div class="profile-avatar">
            <span class="avatar-text"></span>
        </div>
        <h2 class="profile-username"></h2>
        <div class="profile-meta">
            <div class="meta-item">
                <i class="fas fa-trophy"></i>
                <span>VIP Seviyesi: Standart</span>
            </div>
            <div class="meta-item">
                <i class="fas fa-calendar-alt"></i>
                <span>&#xDC;ye: 01.01.1970</span>
            </div>
        </div>
        
        <a href="/tr/packages.php?type=vip" class="btn btn-sm btn-primary mt-2">
            <i class="fas fa-crown me-1"></i> <span data-i18n="profile.view_vip_packages">VIP Paketlerini G&#xF6;r&#xFC;nt&#xFC;le</span>
        </a>
    </div>
    
    <!-- Profile Navigation -->
    <div class="profile-navigation">
        <button class="profile-nav-item active" data-tab="profile-info">
            <i class="fas fa-user"></i>
            <span data-i18n="profile.personal_info">Ki&#x15F;isel Bilgi</span></button>
        
          
        <button class="profile-nav-item" data-tab="referral-info">
            <i class="fas fa-users"></i>
            <span data-i18n="profile.referral_info">Tavsiyeleri</span></button>
    
         <button class="profile-nav-item" data-tab="password-change">
            <i class="fas fa-key"></i>
            <span data-i18n="profile.change_password">De&#x11F;i&#x15F;tir</span></button>
        </div>
    
        
        
    <!-- Profile Content -->
    <div class="profile-content">
        <!-- Personal Info Tab -->
        <div class="profile-tab active" id="profile-info">
            <div class="content-card">
                <div class="card-header">
                    <h3 data-i18n="profile.profile_information">Profil Bilgileri</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action>
                        <div class="form-group">
                            <label for="username" data-i18n="profile.username">Kullan&#x131;c&#x131; ad&#x131;</label>
                            <input type="text" id="username" class="form-control" value readonly>
                             <small class="form-text" data-i18n="profile.username_note">kullan&#x131;c&#x131; ad&#x131; de&#x11F;i&#x15F;tirilemez</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="full_name" data-i18n="profile.full_name">Ad Soyad</label>
                            <input type="text" id="full_name" name="full_name" class="form-control" value>
                        </div>
                        
                        <div class="form-group">
                            <label for="email" data-i18n="profile.email">E -posta adresi</label>
                            <input type="email" id="email" name="email" class="form-control" value required>
                        </div>
                        
                        <div class="form-group">
                            <input type="text" id="trc20_address" name="trc20_address" class="form-control" value>
                            <small class="form-text" data-i18n="profile.trc20_address_note">Para &#xE7;ekme i&#xE7;in kullan&#x131;lan</small>
                         
                            <label for="trc20_address" data-i18n="profile.trc20_address">TRC20 c&#xFC;zdan adresi</label></div>
                        
                        <div class="form-group">
                            <label for="membership_date" data-i18n="profile.membership_date">&#xDC;yelik tarihi</label>
                            <input type="text" id="membership_date" class="form-control" value="01.01.1970 02:00" readonly>
                        </div>
                        
                        <button type="submit" name="update_profile" class="btn btn-primary btn-block">
                            <i class="fas fa-save me-2"></i> <span data-i18n="profile.save_changes">De&#x11F;i&#x15F;iklikleri Kaydet</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Change Password Tab -->
        <div class="profile-tab" id="password-change">
            <div class="content-card">
                <div class="card-header">
                    <h3 data-i18n="profile.change_password">&#x15E;ifre de&#x11F;i&#x15F;tir</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action>
                        <div class="form-group">
                            <label for="current_password" data-i18n="profile.current_password">Mevcut &#x15E;ifre</label>
                            <input type="password" id="current_password" name="current_password" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="new_password" data-i18n="profile.new_password">Yeni &#x15F;ifre</label>
                            <input type="password" id="new_password" name="new_password" class="form-control" required>
                             <small class="form-text" data-i18n="profile.password_requirements">en az 6 karakter olmal&#x131;</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password" data-i18n="profile.confirm_password">Yeni &#x15F;ifreyi onaylay&#x131;n</label>
                            <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                        </div>
                        
                        <button type="submit" name="change_password" class="btn btn-primary btn-block">
                            <i class="fas fa-key me-2"></i> <span data-i18n="profile.update_password">&#x15E;ifreyi g&#xFC;ncelle</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Referral Info Tab -->
        <div class="profile-tab" id="referral-info">
            <div class="content-card">
                <div class="card-header">
                    <h3 data-i18n="profile.referral_code">Tavsiye Kodunuz</h3>
                </div>
                <div class="card-body">
                    <div class="referral-code-container">
                        <label data-i18n="profile.referral_code">Tavsiye Kodunuz</label>
                        <div class="referral-code-wrapper">
                            <input type="text" id="referralCode" class="form-control" value readonly>
                            <button type="button" id="copyReferralBtn" class="copy-btn">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="referral-link-container mt-3">
                        <label data-i18n="profile.referral_link">Tavsiye ba&#x11F;lant&#x131;n&#x131;z</label>
                        <div class="referral-code-wrapper">
                            <input type="text" id="referralLink" class="form-control" value="http://localhost/register.php?ref=" readonly>
                            <button type="button" id="copyLinkBtn" class="copy-btn">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="referral-stats mt-4">
                        <div class="stats-grid">
                            <div class="stat-item">
                                <div class="stat-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="stat-value">1</div>
                                <div class="stat-label" data-i18n="profile.total_referrals">Toplam Tavsiyeler</div>
                            </div>
                            
                            <div class="stat-item">
                                <div class="stat-icon">
                                    <i class="fas fa-wallet"></i>
                                </div>
                                <div class="stat-value">0.00</div>
                                <div class="stat-label" data-i18n="profile.referral_balance">Sevk dengesi</div>
                            </div>
                            
                            <div class="stat-item">
                                <div class="stat-icon">
                                    <i class="fas fa-percentage"></i>
                                </div>
                                <div class="stat-value">0.0%</div>
                                <div class="stat-label" data-i18n="profile.commission_rate">Komisyon oran&#x131;</div>
                            </div>
                            
                            <div class="stat-item">
                                <div class="stat-icon">
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                                <div class="stat-value">113.89</div>
                                <div class="stat-label" data-i18n="profile.total_earnings">Toplam Kazan&#xE7;</div>
                            </div>
                        </div>
                    </div>
                    
                                        
                    <div class="social-share mt-4">
                        <h4 data-i18n="profile.share_referral">Tavsiye ba&#x11F;lant&#x131;n&#x131;z&#x131; payla&#x15F;&#x131;n</h4>
                        <div class="social-buttons">
                            <a href="https://www.facebook.com/sharer/sharer.php?u=http%3A%2F%2Flocalhost%2Fregister.php%3Fref%3D" target="_blank" class="social-btn facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url=http%3A%2F%2Flocalhost%2Fregister.php%3Fref%3D&amp;text=Join+me+on+this+amazing+platform%21" target="_blank" class="social-btn twitter">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="https://api.whatsapp.com/send?text=Join+me+on+this+platform+and+start+earning%21+http%3A%2F%2Flocalhost%2Fregister.php%3Fref%3D" target="_blank" class="social-btn whatsapp">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                            <a href="mailto:?subject=Join+me+on+this+platform&amp;body=Hi%2C%0A%0AI%27m+inviting+you+to+join+this+platform+using+my+referral+link%3A%0A%0Ahttp%3A%2F%2Flocalhost%2Fregister.php%3Fref%3D" class="social-btn email">
                                <i class="fas fa-envelope"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Referral Information -->
                        <div class="referral-data mt-4">
                <div class="content-card">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#referred-users" type="button" role="tab" aria-selected="true" data-i18n="profile.referred_users">Y&#xF6;nlendirilen kullan&#x131;c&#x131;lar</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#earnings-history" type="button" role="tab" aria-selected="false" data-i18n="profile.earnings_history">Kazan&#xE7; Ge&#xE7;mi&#x15F;i</button>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="referred-users" role="tabpanel">
                                                            <div class="user-list">
                                                                            <div class="user-item">
                                            <div class="user-info">
                                                <div class="user-name">Pranga01</div>
                                                <div class="user-date">06.03.2025</div>
                                            </div>
                                            <div class="user-status">
                                                <span class="status-badge status-active">
                                                    Aktif                                                </span>
                                            </div>
                                        </div>
                                                                    </div>
                                                                                    </div>
                        <div class="tab-pane" id="earnings-history" role="tabpanel">
                                                            <div class="earnings-list">
                                                                            <div class="earning-item">
                                            <div class="earning-info">
                                                <div class="earning-amount">1.20 USDT</div>
                                                <div class="earning-desc">Seviye-3 Tavsiye Komisyonu: Paragara Oyun Kazan&#xE7;lar&#x131;</div>
                                            </div>
                                            <div class="earning-date">19.03.2025</div>
                                        </div>
                                                                            <div class="earning-item">
                                            <div class="earning-info">
                                                <div class="earning-amount">1.20 USDT</div>
                                                <div class="earning-desc">Seviye-3 Tavsiye Komisyonu: Paragara Oyun Kazan&#xE7;lar&#x131;</div>
                                            </div>
                                            <div class="earning-date">19.03.2025</div>
                                        </div>
                                                                            <div class="earning-item">
                                            <div class="earning-info">
                                                <div class="earning-amount">1.16 USDT</div>
                                                <div class="earning-desc">Seviye-3 Tavsiye Komisyonu: Paragara Oyun Kazan&#xE7;lar&#x131;</div>
                                            </div>
                                            <div class="earning-date">18.03.2025</div>
                                        </div>
                                                                            <div class="earning-item">
                                            <div class="earning-info">
                                                <div class="earning-amount">1.16 USDT</div>
                                                <div class="earning-desc">Seviye-3 Tavsiye Komisyonu: Paragara Oyun Kazan&#xE7;lar&#x131;</div>
                                            </div>
                                            <div class="earning-date">18.03.2025</div>
                                        </div>
                                                                            <div class="earning-item">
                                            <div class="earning-info">
                                                <div class="earning-amount">1.60 USDT</div>
                                                <div class="earning-desc">Seviye-3 Tavsiye Komisyonu: Paragara Oyun Kazan&#xE7;lar&#x131;</div>
                                            </div>
                                            <div class="earning-date">18.03.2025</div>
                                        </div>
                                                                    </div>
                                                                    <div class="view-all-link">
                                        <a href="/tr/referral_earnings.php" data-i18n="profile.view_all_earnings">T&#xFC;m Kazan&#xE7;lar&#x131; G&#xF6;r&#xFC;nt&#xFC;le</a>
                                    </div>
                                                                                    </div>
                    </div>
                </div>
            </div>
                        
            <!-- How Referrals Work -->
            <div class="content-card mt-4">
                <div class="card-header">
                    <h3 data-i18n="profile.how_referrals_work">Tavsiyeler nas&#x131;l &#xE7;al&#x131;&#x15F;&#x131;r?</h3>
                </div>
                <div class="card-body">
                    <div class="steps-container">
                        <div class="step">
                            <div class="step-number">1</div>
                            <div class="step-content">
                                <h4 data-i18n="profile.step1_title">Ba&#x11F;lant&#x131;n&#x131;z&#x131; Payla&#x15F;&#x131;n</h4>
                                <p data-i18n="profile.step1_desc">E&#x15F;siz y&#xF6;nlendirme ba&#x11F;lant&#x131;n&#x131;z&#x131; arkada&#x15F;lar&#x131;n&#x131;zla, ailenizle veya sosyal medyada payla&#x15F;&#x131;n</p>
                            </div>
                        </div>
                        <div class="step">
                            <div class="step-number">2</div>
                            <div class="step-content">
                                <h4 data-i18n="profile.step2_title">Kaydolurlar</h4>
                                <p data-i18n="profile.step2_desc">Birisi ba&#x11F;lant&#x131;n&#x131;z&#x131; t&#x131;klat&#x131;p bir hesap olu&#x15F;turdu&#x11F;unda, sevkiniz olur</p>
                            </div>
                        </div>
                        <div class="step">
                            <div class="step-number">3</div>
                            <div class="step-content">
                                <h4 data-i18n="profile.step3_title">Sat&#x131;n Al&#x131;yorlar</h4>
                                <p data-i18n="profile.step3_desc">Tavsiyeleriniz bir paket veya hizmet sat&#x131;n ald&#x131;&#x11F;&#x131;nda, komisyon kazan&#x131;rs&#x131;n&#x131;z</p>
                            </div>
                        </div>
                        <div class="step">
                            <div class="step-number">4</div>
                            <div class="step-content">
                                <h4 data-i18n="profile.step4_title">&#xD6;d&#xFC;l kazan&#x131;yorsun</h4>
                                <p data-i18n="profile.step4_desc">Sat&#x131;n alma i&#x15F;lemlerinde% 0.0 komisyon kazan&#x131;yorsunuz</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Account Actions -->
    <div class="account-actions">
        <a href="/tr/transactions.php" class="action-link">
            <i class="fas fa-exchange-alt"></i>
            <span data-i18n="profile.my_transactions">&#x130;&#x15F;lemlerim</span></a>
        
         
        <a href="/tr/logout.php" class="action-link text-danger">
            <i class="fas fa-sign-out-alt"></i>
            <span data-i18n="profile.logout">oturum a&#xE7;may&#x131;</span></a>
    
         <a href="/tr/support.php" class="action-link">
            <i class="fas fa-headset"></i>
            <span data-i18n="profile.support">destekliyor</span></a>
        </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab navigation
    const navItems = document.querySelectorAll('.profile-nav-item');
    const tabPanes = document.querySelectorAll('.profile-tab');
    
    navItems.forEach(item => {
        item.addEventListener('click', function() {
            // Remove active class from all items
            navItems.forEach(navItem => navItem.classList.remove('active'));
            tabPanes.forEach(pane => pane.classList.remove('active'));
            
            // Add active class to clicked item
            this.classList.add('active');
            
            // Show corresponding tab
            const tabId = this.getAttribute('data-tab');
            document.getElementById(tabId).classList.add('active');
        });
    });
    
    // Copy referral code functionality
    const copyReferralBtn = document.getElementById('copyReferralBtn');
    const referralCodeInput = document.getElementById('referralCode');
    
    if (copyReferralBtn && referralCodeInput) {
        copyReferralBtn.addEventListener('click', function() {
            referralCodeInput.select();
            document.execCommand('copy');
            
            // Show success message
            showToast('Referral code copied!', 'success');
            
            // Change button icon temporarily
            const icon = this.querySelector('i');
            icon.classList.remove('fa-copy');
            icon.classList.add('fa-check');
            
            setTimeout(() => {
                icon.classList.remove('fa-check');
                icon.classList.add('fa-copy');
            }, 2000);
        });
    }
    
    // Copy referral link functionality
    const copyLinkBtn = document.getElementById('copyLinkBtn');
    const referralLinkInput = document.getElementById('referralLink');
    
    if (copyLinkBtn && referralLinkInput) {
        copyLinkBtn.addEventListener('click', function() {
            referralLinkInput.select();
            document.execCommand('copy');
            
            // Show success message
            showToast('Referral link copied!', 'success');
            
            // Change button icon temporarily
            const icon = this.querySelector('i');
            icon.classList.remove('fa-copy');
            icon.classList.add('fa-check');
            
            setTimeout(() => {
                icon.classList.remove('fa-check');
                icon.classList.add('fa-copy');
            }, 2000);
        });
    }
    
    // Bootstrap tabs initialization
    const tabElms = document.querySelectorAll('button[data-bs-toggle="tab"]');
    tabElms.forEach(tabEl => {
        tabEl.addEventListener('click', function(event) {
            event.preventDefault();
            
            // Remove active class from all tab buttons and panes
            document.querySelectorAll('.nav-link').forEach(nav => nav.classList.remove('active'));
            document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('active'));
            
            // Add active class to clicked tab button
            this.classList.add('active');
            
            // Show corresponding tab pane
            const target = document.querySelector(this.getAttribute('data-bs-target'));
            if (target) {
                target.classList.add('active');
            }
        });
    });
});

// Toast notification function
function showToast(message, type = 'info') {
    // Create toast element if it doesn't exist
    let toast = document.querySelector('.toast-notification');
    if (!toast) {
        toast = document.createElement('div');
        toast.className = 'toast-notification';
        document.body.appendChild(toast);
    }
    
    // Set type and message
    toast.className = `toast-notification toast-${type}`;
    toast.innerHTML = message;
    
    // Show toast
    toast.classList.add('show');
    
    // Hide after 3 seconds
    setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);
}
</script>

</main>

    <!-- Mobile Bottom Navigation -->
    <nav class="mobile-bottom-nav">
        <a href="/tr/index.php" class="nav-item ">
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
            
            <a href="/tr/profile.php" class="nav-item active">
                <i class="fas fa-user"></i>
                <span data-i18n="buttons.myAccount">Hesap</span>
            </a>
            </nav>

    
    <!-- Backdrop Overlay -->
    <div class="backdrop-overlay" id="backdropOverlay"></div>
    

</body></html>