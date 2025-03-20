<?php die(); ?><!DOCTYPE html><html lang="tr" dir="ltr"><head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#7367f0">
    <title>Destek | Digiminex Mobile</title>
    
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
<div class="support-page">
    <h1 class="page-title">Destek</h1>
    
    <!-- VIP Support Buttons (conditional) -->
    <div class="vip-support-buttons">
                                
                            <a href="https://t.me/yourtelegramname" class="vip-support-btn telegram-btn">
                    <i class="fab fa-telegram"></i> Telgraf deste&#x11F;i
                </a>
                        </div>
    
    <!-- Tabs Navigation -->
    <div class="tabs-container">
        <div class="tab-buttons"><i class="fas fa-ticket-alt"></i>
                
            <button class="tab-button active" data-tab="tickets">Biletlerim</button>
             <i class="fas fa-plus-circle"></i>
                <button class="tab-button" data-tab="new-ticket">Yeni Bilet</button>
        </div>
        
        <!-- Tickets Tab -->
        <div class="tab-content">
            <div class="tab-pane active" id="tickets">
                                
                                    <!-- Tickets List -->
                    <div class="tickets-list">
                                                    <div class="no-tickets">
                                <i class="fas fa-ticket-alt"></i>
                                <p>Hen&#xFC;z destek bileti olu&#x15F;turmad&#x131;n&#x131;z.</p>
                                <button class="btn btn-primary tab-trigger" data-tab="new-ticket">
                                    <i class="fas fa-plus-circle"></i> Yeni Bilet Olu&#x15F;tur
                                </button>
                            </div>
                                            </div>
                            </div>
            
            <!-- New Ticket Tab -->
            <div class="tab-pane" id="new-ticket">
                <div class="new-ticket-form">
                    <div class="contact-info">
                        <h3><i class="fas fa-headset"></i> &#x130;leti&#x15F;im deste&#x11F;i</h3>
                        <p>Bir sorunuz mu yoksa yard&#x131;ma m&#x131; ihtiyac&#x131;n&#x131;z var? Formu doldurun ve destek ekibimiz m&#xFC;mk&#xFC;n olan en k&#x131;sa s&#xFC;rede size geri d&#xF6;necektir.</p>
                        
                        <div class="contact-method">
                            <h4><i class="fas fa-comment-alt"></i> Sohbet deste&#x11F;i</h4>
                            <p>&#xC7;al&#x131;&#x15F;ma Saatleri: 09:00 - 18:00, hafta i&#xE7;i</p>
                        </div>
                    </div>
                    
                    <form method="POST" action="/tr/support.php?new_ticket=1" class="support-form">
                        <h3><i class="fas fa-plus-circle"></i> Yeni Bilet Olu&#x15F;tur</h3>
                        
                        <div class="form-group">
                            <label for="process">Sorun T&#xFC;r&#xFC;</label>
                            <select id="process" name="process" required>
                                <option value>Sorun T&#xFC;r&#xFC;n&#xFC; Se&#xE7;in</option>
                                <option value="withdrawal_issues">Para &#xE7;ekme sorunlar&#x131;</option>
                                <option value="deposit_issues">Depozito Sorunlar&#x131;</option>
                                <option value="password_issues">&#x15E;ifre Sorunlar&#x131;</option>
                                <option value="event_issues">Etkinlik Sorunlar&#x131;</option>
                                <option value="daily_game_issues">G&#xFC;nl&#xFC;k Oyun Sorunlar&#x131;</option>
                                <option value="other">Di&#x11F;er</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="category">Bilet &#xF6;nceli&#x11F;i</label>
                            <select id="category" name="category" required>
                                <option value>&#xD6;nceli&#x11F;i se&#xE7;in</option>
                                <option value="low">D&#xFC;&#x15F;&#xFC;k</option>
                                <option value="medium">Orta</option>
                                <option value="high">Y&#xFC;ksek</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="subject">Ders</label>
                            <input type="text" id="subject" name="subject" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="message">Mesaj</label>
                            <textarea id="message" name="message" rows="5" required></textarea>
                        </div>
                        
                        <button type="submit" name="submit_ticket" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Bilet Olu&#x15F;tur
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Support Page Styles */
.support-page {
    padding: 15px;
}

.page-title {
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 20px;
    color: var(--dark-color);
}

/* VIP Support Buttons */
.vip-support-buttons {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
}

.vip-support-btn {
    flex: 1;
    padding: 12px 16px;
    text-align: center;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.95rem;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
}

.vip-support-btn i {
    margin-right: 8px;
    font-size: 1.2rem;
}

.whatsapp-btn {
    background-color: #25D366;
    color: white;
}

.telegram-btn {
    background-color: #0088cc;
    color: white;
}

/* Tab navigation */
.tabs-container {
    background-color: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
}

.tab-buttons {
    display: flex;
    border-bottom: 1px solid #eee;
}

.tab-button {
    flex: 1;
    padding: 15px;
    text-align: center;
    background: none;
    border: none;
    font-size: 0.9rem;
    font-weight: 500;
    color: #6e6b7b;
    cursor: pointer;
    position: relative;
}

.tab-button i {
    margin-right: 5px;
}

.tab-button.active {
    color: var(--primary-color);
}

.tab-button.active::after {
    content: '';
    position: absolute;
    bottom: -1px;
    left: 0;
    width: 100%;
    height: 3px;
    background-color: var(--primary-color);
}

.tab-content {
    padding: 20px;
}

.tab-pane {
    display: none;
}

.tab-pane.active {
    display: block;
}

/* Tickets List */
.tickets-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 15px;
}

.ticket-card {
    background-color: #f8f9fa;
    border-radius: 10px;
    overflow: hidden;
    text-decoration: none;
    color: inherit;
    display: block;
    transition: transform 0.3s, box-shadow 0.3s;
}

.ticket-card:active {
    transform: scale(0.98);
}

.ticket-card-header {
    padding: 12px 15px;
    background-color: #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.ticket-id {
    font-weight: 500;
    font-size: 0.9rem;
}

.ticket-status {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 500;
}

.status-open {
    background-color: #7367f0;
    color: white;
}

.status-closed {
    background-color: #82868b;
    color: white;
}

.status-pending {
    background-color: #ff9f43;
    color: white;
}

.status-awaiting_response {
    background-color: #28c76f;
    color: white;
}

.ticket-card-body {
    padding: 15px;
    position: relative;
}

.ticket-subject {
    font-size: 1rem;
    font-weight: 600;
    margin: 0 0 10px;
    padding-right: 40px;
}

.unread-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    background-color: #ea5455;
    color: white;
    padding: 3px 8px;
    border-radius: 10px;
    font-size: 0.7rem;
    font-weight: 500;
}

.ticket-meta {
    display: flex;
    justify-content: space-between;
    font-size: 0.8rem;
    color: #6e6b7b;
}

.ticket-priority i, 
.ticket-date i {
    margin-right: 5px;
}

/* No tickets message */
.no-tickets {
    text-align: center;
    padding: 30px 20px;
}

.no-tickets i {
    font-size: 3rem;
    color: #ddd;
    margin-bottom: 15px;
}

.no-tickets p {
    margin-bottom: 20px;
    color: #6e6b7b;
}

/* Ticket Detail View */
.ticket-detail {
    background-color: #f8f9fa;
    border-radius: 10px;
    overflow: hidden;
}

.ticket-header {
    padding: 15px;
    background-color: white;
    border-bottom: 1px solid #eee;
}

.ticket-title h2 {
    font-size: 1.2rem;
    font-weight: 600;
    margin: 0 0 10px;
}

.ticket-meta {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
}

.ticket-info {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    font-size: 0.8rem;
    color: #6e6b7b;
    margin-bottom: 15px;
}

.back-btn {
    display: inline-block;
    padding: 8px 12px;
    background-color: #f0f0f0;
    color: #333;
    text-decoration: none;
    border-radius: 6px;
    font-size: 0.9rem;
}

.ticket-conversation {
    padding: 15px;
}

.message {
    margin-bottom: 20px;
    border-radius: 10px;
    overflow: hidden;
}

.user-message {
    background-color: rgba(115, 103, 240, 0.1);
}

.admin-message {
    background-color: white;
}

.message-header {
    padding: 10px 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.9rem;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.message-header span {
    font-size: 0.8rem;
    color: #6e6b7b;
}

.message-content {
    padding: 15px;
    font-size: 0.95rem;
    line-height: 1.5;
}

.no-messages {
    text-align: center;
    padding: 20px;
    color: #6e6b7b;
}

/* Reply Form */
.reply-form {
    padding: 15px;
    background-color: white;
    border-top: 1px solid #eee;
}

.reply-form h3 {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 15px;
}

.form-group {
    margin-bottom: 15px;
}

.form-control {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 0.95rem;
}

textarea.form-control {
    resize: vertical;
}

.btn {
    padding: 12px 20px;
    border-radius: 8px;
    font-weight: 500;
    font-size: 0.95rem;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: background-color 0.3s;
}

.btn i {
    margin-right: 8px;
}

.btn-primary {
    background-color: var(--primary-color);
    color: white;
}

.closed-ticket-message {
    padding: 15px;
    background-color: #fff3cd;
    color: #856404;
    text-align: center;
    border-top: 1px solid #ffeeba;
}

/* New Ticket Form */
.new-ticket-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.contact-info {
    background-color: #f8f9fa;
    border-radius: 10px;
    padding: 15px;
}

.contact-info h3 {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 10px;
}

.contact-info p {
    color: #6e6b7b;
    margin-bottom: 15px;
}

.contact-method {
    background-color: white;
    border-radius: 8px;
    padding: 15px;
}

.contact-method h4 {
    font-size: 1rem;
    font-weight: 600;
    margin: 0 0 10px;
}

.contact-method p {
    margin: 0;
    font-size: 0.9rem;
}

.support-form {
    background-color: white;
    border-radius: 10px;
    padding: 20px;
}

.support-form h3 {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 20px;
}

.support-form label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    font-size: 0.9rem;
}

.support-form input,
.support-form select,
.support-form textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 0.95rem;
}

.support-form button {
    width: 100%;
    margin-top: 10px;
}

/* Alert Boxes */
.alert {
    padding: 12px 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 0.95rem;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab functionality
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabPanes = document.querySelectorAll('.tab-pane');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            
            // Remove active class from all buttons and panes
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabPanes.forEach(pane => pane.classList.remove('active'));
            
            // Add active class to clicked button and corresponding pane
            this.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        });
    });
    
    // Tab trigger buttons (e.g. from "no tickets" message)
    const tabTriggers = document.querySelectorAll('.tab-trigger');
    tabTriggers.forEach(trigger => {
        trigger.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            const tabButton = document.querySelector(`.tab-button[data-tab="${tabId}"]`);
            if (tabButton) {
                tabButton.click();
            }
        });
    });
    
    // Check URL parameter to set active tab
    const urlParams = new URLSearchParams(window.location.search);
    const newTicketParam = urlParams.get('new_ticket');
    
    if (newTicketParam) {
        const newTicketTab = document.querySelector('.tab-button[data-tab="new-ticket"]');
        if (newTicketTab) {
            newTicketTab.click();
        }
    }
    
    // Display issue type in user-friendly format
    const processValues = {
        'withdrawal_issues': 'Withdrawal Issues',
        'deposit_issues': 'Deposit Issues',
        'password_issues': 'Password Issues',
        'event_issues': 'Event Issues',
        'daily_game_issues': 'Daily Game Issues',
        'other': 'Other'
    };
    
    // Update displayed process names if present on the page
    const processElements = document.querySelectorAll('.process-name');
    processElements.forEach(element => {
        const processValue = element.textContent.trim();
        if (processValues[processValue]) {
            element.textContent = processValues[processValue];
        }
    });
});
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
            
            <a href="/tr/profile.php" class="nav-item ">
                <i class="fas fa-user"></i>
                <span data-i18n="buttons.myAccount">Hesap</span>
            </a>
            </nav>

    
    <!-- Backdrop Overlay -->
    <div class="backdrop-overlay" id="backdropOverlay"></div>
    

</body></html>