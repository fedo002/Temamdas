<?php die(); ?><!DOCTYPE html><html lang="tr" dir="ltr"><head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#7367f0">
    <title>Bildirimler | Digiminex Mobile</title>
    
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
<div class="notifications-page">
    <div class="page-header">
        <h1>Bildirimler</h1>
        
                <a href="/tr/notifications.php?mark_all=1" class="mark-all-btn">
            <i class="fas fa-check-double"></i> Mark T&#xFC;m&#xFC; Oku
        </a>
            </div>
    
        
        
    <div class="notifications-container">
                                    <div class="notification-card unread">
                    <div class="notification-badge">
                                                    <i class="fas fa-bell"></i>
                                            </div>
                    
                    <div class="notification-content">
                        <div class="notification-header">
                            <h3>Madencilik sistemi aktif</h3>
                            <span class="notification-time">
                                19.03.2025 18:22                            </span>
                        </div>
                        
                        <p class="notification-text">Ho&#x15F; geldiniz, madencilik sistemimiz aktif, Telegram Grubu&apos;nun ayr&#x131;nt&#x131;lar&#x131;n&#x131; &#xF6;&#x11F;renebilirsiniz.</p>
                        
                                                    <div class="notification-actions">
                                <a href="/tr/notifications.php?mark_read=9" class="mark-read-btn">
                                    <i class="fas fa-check"></i> Okuma olarak i&#x15F;aretleyin
                                </a>
                            </div>
                                            </div>
                </div>
                        </div>
</div>

<style>
/* Notifications Page Styles */
.notifications-page {
    padding: 15px;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.page-header h1 {
    font-size: 1.8rem;
    font-weight: 700;
    margin: 0;
    color: #000;
}

.mark-all-btn {
    display: flex;
    align-items: center;
    color: #000;
    font-size: 0.9rem;
    font-weight: 500;
    text-decoration: none;
}

.mark-all-btn i {
    margin-right: 5px;
}

.notifications-container {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.notification-card {
    background-color: white;
    border-radius: 12px;
    padding: 15px;
    display: flex;
    align-items: flex-start;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    position: relative;
    transition: all 0.3s ease;
}

.notification-card.unread {
    border-left: 4px solid var(--primary-color);
}

.notification-card.read {
    opacity: 0.7;
}

.notification-badge {
    width: 40px;
    height: 40px;
    min-width: 40px;
    border-radius: 50%;
    background-color: rgba(115, 103, 240, 0.1);
    color: var(--primary-color);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    margin-right: 15px;
}

.notification-card[data-type="vip"] .notification-badge {
    background-color: rgba(255, 159, 67, 0.1);
    color: #ff9f43;
}

.notification-card[data-type="mining"] .notification-badge {
    background-color: rgba(0, 207, 232, 0.1);
    color: #00cfe8;
}

.notification-content {
    flex: 1;
}

.notification-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 10px;
}

.notification-header h3 {
    font-size: 1rem;
    font-weight: 600;
    margin: 0;
    padding-right: 10px;
}

.notification-time {
    font-size: 0.8rem;
    color: var(--text-muted);
    white-space: nowrap;
}

.notification-text {
    font-size: 0.9rem;
    color: #666;
    margin: 0 0 10px;
    line-height: 1.5;
}

.notification-actions {
    display: flex;
    justify-content: flex-end;
}

.mark-read-btn {
    font-size: 0.8rem;
    color: var(--primary-color);
    background: none;
    border: none;
    display: flex;
    align-items: center;
    padding: 5px 0;
    cursor: pointer;
    text-decoration: none;
}

.mark-read-btn i {
    margin-right: 5px;
}

/* Empty State */
.empty-notifications {
    text-align: center;
    padding: 40px 20px;
    background-color: white;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.empty-notifications i {
    font-size: 3rem;
    color: #ddd;
    margin-bottom: 15px;
}

.empty-notifications h3 {
    font-size: 1.2rem;
    font-weight: 600;
    margin: 0 0 10px;
}

.empty-notifications p {
    color: var(--text-muted);
    margin: 0;
}
</style>

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
            
            <a href="/tr/notifications.php" class="nav-item active">
                <i class="fas fa-bell"></i>
                                    <span class="notification-badge">1</span>
                                <span data-i18n="buttons.notifications">Bildirimler</span>
            </a>
            
            <a href="/tr/profile.php" class="nav-item ">
                <i class="fas fa-user"></i>
                <span data-i18n="buttons.myAccount">Hesap</span>
            </a>
            </nav>

    
    <!-- Backdrop Overlay -->
    <div class="backdrop-overlay" id="backdropOverlay"></div>
    

</body></html>