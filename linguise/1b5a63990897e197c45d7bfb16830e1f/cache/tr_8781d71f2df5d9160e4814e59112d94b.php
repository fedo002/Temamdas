<?php die(); ?><!DOCTYPE html><html lang="tr" dir="ltr"><head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#7367f0">
    <title>G&#xFC;nl&#xFC;k &#xD6;d&#xFC;l Oyunu | Digiminex Mobile</title>
    
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
<div class="daily-game-page">

    <!-- Sound effects -->
    <audio id="cardFlipSound" preload="auto">
        <source src="assets/sounds/card-flip.mp3" type="audio/mpeg">
    </audio>
    <audio id="winSound" preload="auto">
        <source src="assets/sounds/win.mp3" type="audio/mpeg">
    </audio>
    <audio id="tensionSound" preload="auto" loop>
        <source src="assets/sounds/tension.mp3" type="audio/mpeg">
    </audio>
    <audio id="dealerSound" preload="auto">
        <source src="assets/sounds/card-shuffling.mp3" type="audio/mpeg">
    </audio>

    <!-- Game Header -->
    <div class="game-header">
        <h1>G&#xFC;nl&#xFC;k &#xF6;d&#xFC;l oyunu</h1>
        <p>&#x15E;ans&#x131;n&#x131;z&#x131; deneyin ve USDT &#xD6;d&#xFC;lleri Kazan&#x131;n!</p>
        
        <div class="game-stats">
            <div class="stat-badge">
                <i class="fas fa-gamepad"></i>
                <span>Kalan denemeler: <strong>999</strong></span>
            </div>
            <div class="stat-badge">
                <i class="fas fa-percentage"></i>
                <span>&#x15E;ans kazan: <strong>80.0%</strong></span>
            </div>
        </div>
        
        <div class="vip-badge">
            <i class="fas fa-crown"></i> VIP Seviyesi: Platinum        </div>
    </div>

    
    <!-- Game Area -->
    <div class="game-area no-translate" data-no-translate="true">
                <!-- Main game options (Direct reward) -->
        <div class="game-stage" id="direct-reward">
            <h2>G&#xFC;nl&#xFC;k &#xD6;d&#xFC;l&#xFC;n&#xFC;z</h2>
            <p>  29 USDT Haz&#x131;r! &#x15E;imdi al ya da &#x15F;ans&#x131;n&#x131;z&#x131; daha fazla i&#xE7;in deneyin mi?</p>
            
            <div class="game-explanation">
                <div class="explanation-option">
                    <i class="fas fa-check-circle"></i>
                    <h3>&#xD6;d&#xFC;l almak</h3>
                    <p>&#x15E;imdi garantili 29 USDT al&#x131;n</p>
                </div>
                <div class="explanation-option">
                    <i class="fas fa-dice"></i>
                    <h3>&#x15E;ans&#x131;n&#x131; dene</h3>
                    <p>40 USDT&apos;ye kadar kazanma &#x15F;ans&#x131; i&#xE7;in garantili &#xF6;d&#xFC;l&#xFC;n&#xFC;z&#xFC; riske at&#x131;n!</p>
                </div>
            </div>
            
            <div class="decision-buttons"><i class="fas fa-check-circle"></i>
                    
                <button class="btn btn-success" onclick="takePrize()">29 USDT al</button>
                 <i class="fas fa-dice"></i>
                    <button class="btn btn-warning" onclick="doubleOrNothing()">&#x15F;ans&#x131;n&#x131;z&#x131; deneyin</button>
            </div>
        </div>
        
        <!-- Card Selection (hidden initially) -->
        <div class="game-stage" id="card-selection" style="display: none;">
            <h2>Kart&#x131;n&#x131;z&#x131; Se&#xE7;in</h2>
            <p>&#xD6;d&#xFC;l&#xFC;n&#xFC;z&#xFC; g&#xF6;rmek i&#xE7;in bir kart se&#xE7;in!</p>
            
            <div class="possible-rewards">
                <span>Olas&#x131; &#xF6;d&#xFC;ller:</span>
                <div class="reward-badges">
                    <span class="reward-badge low">20.00 USDT</span>
                     <span class="reward-badge medium">30.00 USDT</span>
                     <span class="reward-badge high">40.00 USDT</span>
                </div>
            </div>
            
            <div id="timer-container" class="timer-container">
                <div class="timer-bar">
                    <div class="timer-progress" id="timer-progress"></div>
                </div>
                <div class="timer-text">Kart se&#xE7;me zaman&#x131;: <span id="timer-seconds">30</span> saniye</div>
            </div>
            
            <div class="cards-container three-cards">
                <div class="game-card" id="card1" onclick="selectCard(&apos;card1&apos;)" style="display: none;">
                    <div class="card-inner">
                        <div class="card-front card-color-1">
                            <div class="card-content">
                                <i class="fas fa-question"></i>
                            </div>
                        </div>
                        <div class="card-back">
                            <div class="card-result" id="card1-result">
                                <!-- Result will be shown here -->
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="game-card" id="card2" onclick="selectCard(&apos;card2&apos;)" style="display: none;">
                    <div class="card-inner">
                        <div class="card-front card-color-2">
                            <div class="card-content">
                                <i class="fas fa-question"></i>
                            </div>
                        </div>
                        <div class="card-back">
                            <div class="card-result" id="card2-result">
                                <!-- Result will be shown here -->
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="game-card" id="card3" onclick="selectCard(&apos;card3&apos;)" style="display: none;">
                    <div class="card-inner">
                        <div class="card-front card-color-3">
                            <div class="card-content">
                                <i class="fas fa-question"></i>
                            </div>
                        </div>
                        <div class="card-back">
                            <div class="card-result" id="card3-result">
                                <!-- Result will be shown here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Dealer Animation - At bottom of card section -->
            <div class="dealer-animation" id="dealerAnimation">
                <div class="dealer-table"></div>
                <div class="dealing-cards">
                    <div class="dealing-card card1"></div>
                    <div class="dealing-card card2"></div>
                    <div class="dealing-card card3"></div>
                </div>
                <div class="dealer">
                    <div class="dealer-avatar">
                        <i class="fas fa-user-tie"></i>
                    </div>
                </div>
            </div>
        </div>
            </div>
    
    <!-- Results Modal -->
    <div class="modal no-translate" id="resultModal" data-no-translate="true">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="resultModalTitle">Tebrikler!</h3>
                <span class="close-modal" onclick="closeModal()">&#xD7;</span>
            </div>
            <div class="modal-body" id="resultContent">
                <!-- Result content will be here -->
            </div>
            <div class="modal-footer" id="resultModalFooter">
                <button onclick="closeModal()" class="btn btn-primary">TAMAM</button>
            </div>
        </div>
    </div>
    
    <!-- Countdown Modal -->
    <div class="modal no-translate" id="countdownModal" data-no-translate="true">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Zaman kalkt&#x131;!</h3>
            </div>
            <div class="modal-body">
                <i class="fas fa-clock" style="font-size: 3rem; color: #ff9f43; margin-bottom: 15px;"></i>
                <p>Zaman s&#x131;n&#x131;r&#x131; i&#xE7;inde bir kart se&#xE7;mediniz.</p>
                <p>Hesab&#x131;n&#x131;za en d&#xFC;&#x15F;&#xFC;k &#xF6;d&#xFC;l eklendi.</p>
            </div>
            <div class="modal-footer">
                <button onclick="window.location.reload()" class="btn btn-primary">TAMAM</button>
            </div>
        </div>
    </div>
    
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay no-translate" data-no-translate="true">
        <div class="spinner"></div>
        <p>&#xD6;d&#xFC;l&#xFC;n&#xFC;z&#xFC; i&#x15F;lemek ...</p>
    </div>
    
    <!-- Custom confirmation modal -->
    <div class="custom-message-box no-translate" id="custom-message-box" data-no-translate="true" style="display: none;">
        <div class="message-box-content">
            <div class="message-box-header">
                <h3 id="message-box-title">Onay iste&#x11F;i</h3>
            </div>
            <div class="message-box-body">
                <p id="message-box-message">&#x15E;ans&#x131;n&#x131;z&#x131; denemeyi se&#xE7;erseniz, oyundan &#xE7;&#x131;k&#x131;yorsan&#x131;z en d&#xFC;&#x15F;&#xFC;k &#xF6;d&#xFC;l&#xFC; alacaks&#x131;n&#x131;z ve giri&#x15F;iminiz say&#x131;lacakt&#x131;r. Devam etmek ister misin?</p>
            </div>
            <div class="message-box-footer">
                <button id="message-box-confirm" class="btn btn-primary">Tamam</button>
                 <button id="message-box-cancel" class="btn btn-outline">iptal et</button>
            </div>
        </div>
    </div>
    
    <!-- Backdrop -->
    <div class="backdrop" id="backdrop"></div>
</div>

<!-- CSS dosyasını ekle -->
<link rel="stylesheet" href="assets/css/daily-game.css">

<!-- JavaScript -->
<script>
    // Dil ve çeviri bilgilerini JS'ye aktar
    const gameConfig = {
        reward: 29,
        cardRewards: {
            low: 20,
            medium: 30,
            high: 40        },
        vipLevel: 3,
        currentLang: "en",
        translations: {"stage2":{"title":"Choose Your Card","description":"Select a card to see your reward!","possible_rewards":"Possible Rewards:","timer_text":"Time to select a card: {seconds} seconds","card_prefix":"Card"},"modals":{"timeout":{"title":"Time's Up!","message1":"You didn't select a card within the time limit.","message2":"The lowest reward has been added to your account.","btn_ok":"OK"},"result":{"title":"Congratulations!","message":"Your reward has been added to your account.","try_again_message":"You can try again for a better reward!","remaining_retries":"Your remaining retry attempts","btn_ok":"OK","btn_try_again":"Try Again"},"error":{"title":"Error","generic_error":"An error occurred!","try_again":"Please try again later.","connection_error":"Connection Error","check_connection":"Please check your internet connection and try again.","no_attempts":"Your daily limit is reached!","try_tomorrow":"Come back tomorrow.","btn_ok":"OK"},"confirmation":{"title":"Confirmation Request","try_luck_warning":"If you choose to try your luck, you will receive the lowest reward if you exit the game and your attempt will be counted. Do you want to continue?","btn_confirm":"OK","btn_cancel":"Cancel"}},"loading":"Processing your reward..."}    };
</script>
<script src="assets/js/daily-game.js"></script>

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
        
                    <a href="/tr/daily-game.php" class="nav-item nav-item-main active">
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