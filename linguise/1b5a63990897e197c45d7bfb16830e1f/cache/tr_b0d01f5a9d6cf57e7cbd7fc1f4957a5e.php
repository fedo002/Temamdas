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
                <span>Kalan denemeler: <strong>96</strong></span>
            </div>
            <div class="stat-badge">
                <i class="fas fa-percentage"></i>
                <span>&#x15E;ans kazan: <strong>80.0%</strong></span>
            </div>
        </div>
        
        <div class="vip-badge">
            <i class="fas fa-crown"></i> VIP Seviyesi: G&#xFC;m&#xFC;&#x15F;        </div>
    </div>

    
    <!-- Game Area -->
    <div class="game-area no-translate" data-no-translate="true">
                <!-- Main game options (Direct reward) -->
        <div class="game-stage" id="direct-reward">
            <h2>G&#xFC;nl&#xFC;k &#xD6;d&#xFC;l&#xFC;n&#xFC;z</h2>
            <p>  4.2 USDT Haz&#x131;r! &#x15E;imdi al ya da &#x15F;ans&#x131;n&#x131;z&#x131; daha fazla i&#xE7;in deneyin mi?</p>
            
            <div class="game-explanation">
                <div class="explanation-option">
                    <i class="fas fa-check-circle"></i>
                    <h3>&#xD6;d&#xFC;l almak</h3>
                    <p>&#x15E;imdi garantili 4.2 USDT al&#x131;n</p>
                </div>
                <div class="explanation-option">
                    <i class="fas fa-dice"></i>
                    <h3>&#x15E;ans&#x131;n&#x131; dene</h3>
                    <p>6 USDT&apos;ye kadar kazanma &#x15F;ans&#x131; i&#xE7;in garantili &#xF6;d&#xFC;l&#xFC;n&#xFC;z&#xFC; riske at&#x131;n!</p>
                </div>
            </div>
            
            <div class="decision-buttons"><i class="fas fa-check-circle"></i>
                    
                <button class="btn btn-success" onclick="takePrize()">4.2 USDT al</button>
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
                    <span class="reward-badge low">3.00 USDT</span>
                     <span class="reward-badge medium">4.00 USDT</span>
                     <span class="reward-badge high">6.00 USDT</span>
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
                <h3 id="resultModalTitle">&#x41F;&#x437;&#x440;&#x430;&#x432;&#x43B;&#x44F;&#x435;&#x43C;!</h3>
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
                <h3>&#x412;&#x440;&#x435;&#x43C;&#x44F; &#x412;&#x44B;&#x448;&#x43B;&#x43E;!</h3>
            </div>
            <div class="modal-body">
                <i class="fas fa-clock" style="font-size: 3rem; color: #ff9f43; margin-bottom: 15px;"></i>
                <p>&#x412;&#x44B; &#x43D;&#x435; &#x412;&#x44B;&#x440;&#x430;&#x43B;&#x438; &#x43A;&#x430;&#x440;&#x442;ron &#x412; &#x43E;&#x442;&#x432;&#x435;&#x434;&#x435;&#x434;&#x43D;&#x43E;&#x435; &#x412;&#x440;&#x435;&#x43C;&#x44F;.</p>
                <p>&#x41C;&#x43D;&#x438;&#x43C;&#x430;&#x43B;&#x44C;&#x43D;&#x430;&#x44F; &#x43D;&#x430;&#x433;&#x440;&#x430;&#x434;&#x430; &#x434;&#x43E;&#x431;&#x430;&#x432;&#x43B;&#x435;&#x43D;&#x430; &#x200B;&#x200B;&#x43D;&#x430;&#x448; &#x447;&#x435;&#x442;.</p>
            </div>
            <div class="modal-footer">
                <button onclick="window.location.reload()" class="btn btn-primary">TAMAM</button>
            </div>
        </div>
    </div>
    
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay no-translate" data-no-translate="true">
        <div class="spinner"></div>
        <p>&#x41E;&#x431;&#x435;&#x439; &#x43D;&#x430;&#x433;&#x440;&#x430;&#x434;&#x44B; ...</p>
    </div>
    
    <!-- Custom confirmation modal -->
    <div class="custom-message-box no-translate" id="custom-message-box" data-no-translate="true" style="display: none;">
        <div class="message-box-content">
            <div class="message-box-header">
                <h3 id="message-box-title">&#x417;&#x430;&#x43F;&#x440;&#x43E;&#x441; &#x43F;&#x43E;&#x434;&#x442;&#x432;&#x435;&#x440;&#x436;&#x434;&#x435;&#x43D;&#x438;&#x44F;</h3>
            </div>
            <div class="message-box-body">
                <p id="message-box-message">&#x415;&#x441;&#x43B;&#x438; &#x412;&#x44B; &#x440;&#x435;&#x448;&#x435; &#x438;&#x441;&#x43F;&#x44B;&#x442;&#x430;&#x442;&#x44C; &#xB5;&#x434;&#x430;&#x447;tin, &#x412;&#x44B; &#x43F;&#x43E;&#x44F;&#x44B;&#x439; &#x43D;&#x430;&#x433;&#x440;&#x430;eder u, &#x435;&#x441;&#x43B;&#x438; &#x412;&#x44B;&#x439;&#x434;&#x435;&#x442;&#x435; &#x43D;&#x430;&#x438;&#x43C;&#x435;&#x43D;&#x448;&#x448;ou&#x44E; &#x43D;&#x430;&#x433;&#x430;&#x441; de&#x11F;erlendir. &#x425;&#x43E;&#x442;&#x438;&#x442;&#x435; &#x43F;&#x43E;&#x43B;&#x436;&#x438;&#x442;&#x44C;?</p>
            </div>
            <div class="message-box-footer">
                <button id="message-box-confirm" class="btn btn-primary">Tamam</button>
                 <button id="message-box-cancel" class="btn btn-outline">&#x43E;&#x442;&#x43C;&#x435;&#x43D;&#x430;</button>
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
        reward: 4.2,
        cardRewards: {
            low: 3,
            medium: 4,
            high: 6        },
        vipLevel: 1,
        currentLang: "ru",
        translations: {"stage2":{"title":"\u0412\u044b\u0431\u0435\u0440\u0438\u0442\u0435 \u041a\u0430\u0440\u0442\u0443","description":"\u0412\u044b\u0431\u0435\u0440\u0438\u0442\u0435 \u043a\u0430\u0440\u0442\u0443, \u0447\u0442\u043e\u0431\u044b \u0443\u0432\u0438\u0434\u0435\u0442\u044c \u0432\u0430\u0448\u0443 \u043d\u0430\u0433\u0440\u0430\u0434\u0443!","possible_rewards":"\u0412\u043e\u0437\u043c\u043e\u0436\u043d\u044b\u0435 \u041d\u0430\u0433\u0440\u0430\u0434\u044b:","timer_text":"\u0412\u0440\u0435\u043c\u044f \u043d\u0430 \u0432\u044b\u0431\u043e\u0440 \u043a\u0430\u0440\u0442\u044b: {seconds} \u0441\u0435\u043a\u0443\u043d\u0434","card_prefix":"\u041a\u0430\u0440\u0442\u0430"},"no_attempts":{"title":"\u041d\u0435 \u041e\u0441\u0442\u0430\u043b\u043e\u0441\u044c \u041f\u043e\u043f\u044b\u0442\u043e\u043a","description":"\u0412\u044b \u0438\u0441\u043f\u043e\u043b\u044c\u0437\u043e\u0432\u0430\u043b\u0438 \u0432\u0441\u0435 \u043f\u043e\u043f\u044b\u0442\u043a\u0438 \u043d\u0430 \u0441\u0435\u0433\u043e\u0434\u043d\u044f. \u0412\u0435\u0440\u043d\u0438\u0442\u0435\u0441\u044c \u0437\u0430\u0432\u0442\u0440\u0430 \u0438\u043b\u0438 \u043f\u043e\u0432\u044b\u0441\u044c\u0442\u0435 \u0441\u0432\u043e\u0439 VIP \u0443\u0440\u043e\u0432\u0435\u043d\u044c \u0434\u043b\u044f \u043f\u043e\u043b\u0443\u0447\u0435\u043d\u0438\u044f \u0431\u043e\u043b\u044c\u0448\u0435\u0433\u043e \u043a\u043e\u043b\u0438\u0447\u0435\u0441\u0442\u0432\u0430 \u043f\u043e\u043f\u044b\u0442\u043e\u043a!","btn_explore_vip":"\u0418\u0437\u0443\u0447\u0438\u0442\u044c VIP \u041f\u0430\u043a\u0435\u0442\u044b"},"modals":{"timeout":{"title":"\u0412\u0440\u0435\u043c\u044f \u0412\u044b\u0448\u043b\u043e!","message1":"\u0412\u044b \u043d\u0435 \u0432\u044b\u0431\u0440\u0430\u043b\u0438 \u043a\u0430\u0440\u0442\u0443 \u0432 \u043e\u0442\u0432\u0435\u0434\u0435\u043d\u043d\u043e\u0435 \u0432\u0440\u0435\u043c\u044f.","message2":"\u041c\u0438\u043d\u0438\u043c\u0430\u043b\u044c\u043d\u0430\u044f \u043d\u0430\u0433\u0440\u0430\u0434\u0430 \u0434\u043e\u0431\u0430\u0432\u043b\u0435\u043d\u0430 \u043d\u0430 \u0432\u0430\u0448 \u0441\u0447\u0435\u0442.","btn_ok":"OK"},"result":{"title":"\u041f\u043e\u0437\u0434\u0440\u0430\u0432\u043b\u044f\u0435\u043c!","message":"\u0412\u0430\u0448\u0430 \u043d\u0430\u0433\u0440\u0430\u0434\u0430 \u0434\u043e\u0431\u0430\u0432\u043b\u0435\u043d\u0430 \u043d\u0430 \u0432\u0430\u0448 \u0441\u0447\u0435\u0442.","try_again_message":"\u0412\u044b \u043c\u043e\u0436\u0435\u0442\u0435 \u043f\u043e\u043f\u0440\u043e\u0431\u043e\u0432\u0430\u0442\u044c \u0441\u043d\u043e\u0432\u0430 \u0434\u043b\u044f \u043b\u0443\u0447\u0448\u0435\u0439 \u043d\u0430\u0433\u0440\u0430\u0434\u044b!","remaining_retries":"\u0412\u0430\u0448\u0438 \u043e\u0441\u0442\u0430\u0432\u0448\u0438\u0435\u0441\u044f \u043f\u043e\u043f\u044b\u0442\u043a\u0438 \u043f\u0435\u0440\u0435\u0438\u0433\u0440\u044b\u0432\u0430\u043d\u0438\u044f","btn_ok":"OK","btn_try_again":"\u041f\u043e\u043f\u0440\u043e\u0431\u043e\u0432\u0430\u0442\u044c \u0421\u043d\u043e\u0432\u0430"},"error":{"title":"\u041e\u0448\u0438\u0431\u043a\u0430","generic_error":"\u041f\u0440\u043e\u0438\u0437\u043e\u0448\u043b\u0430 \u043e\u0448\u0438\u0431\u043a\u0430!","try_again":"\u041f\u043e\u0436\u0430\u043b\u0443\u0439\u0441\u0442\u0430, \u043f\u043e\u0432\u0442\u043e\u0440\u0438\u0442\u0435 \u043f\u043e\u043f\u044b\u0442\u043a\u0443 \u043f\u043e\u0437\u0436\u0435.","connection_error":"\u041e\u0448\u0438\u0431\u043a\u0430 \u0421\u043e\u0435\u0434\u0438\u043d\u0435\u043d\u0438\u044f","check_connection":"\u041f\u043e\u0436\u0430\u043b\u0443\u0439\u0441\u0442\u0430, \u043f\u0440\u043e\u0432\u0435\u0440\u044c\u0442\u0435 \u0432\u0430\u0448\u0435 \u0438\u043d\u0442\u0435\u0440\u043d\u0435\u0442-\u0441\u043e\u0435\u0434\u0438\u043d\u0435\u043d\u0438\u0435 \u0438 \u043f\u043e\u043f\u0440\u043e\u0431\u0443\u0439\u0442\u0435 \u0441\u043d\u043e\u0432\u0430.","no_attempts":"\u0414\u043e\u0441\u0442\u0438\u0433\u043d\u0443\u0442 \u0434\u043d\u0435\u0432\u043d\u043e\u0439 \u043b\u0438\u043c\u0438\u0442!","try_tomorrow":"\u0412\u043e\u0437\u0432\u0440\u0430\u0449\u0430\u0439\u0442\u0435\u0441\u044c \u0437\u0430\u0432\u0442\u0440\u0430.","btn_ok":"OK"},"confirmation":{"title":"\u0417\u0430\u043f\u0440\u043e\u0441 \u041f\u043e\u0434\u0442\u0432\u0435\u0440\u0436\u0434\u0435\u043d\u0438\u044f","try_luck_warning":"\u0415\u0441\u043b\u0438 \u0432\u044b \u0440\u0435\u0448\u0438\u0442\u0435 \u0438\u0441\u043f\u044b\u0442\u0430\u0442\u044c \u0443\u0434\u0430\u0447\u0443, \u0432\u044b \u043f\u043e\u043b\u0443\u0447\u0438\u0442\u0435 \u043d\u0430\u0438\u043c\u0435\u043d\u044c\u0448\u0443\u044e \u043d\u0430\u0433\u0440\u0430\u0434\u0443, \u0435\u0441\u043b\u0438 \u0432\u044b\u0439\u0434\u0435\u0442\u0435 \u0438\u0437 \u0438\u0433\u0440\u044b, \u0438 \u0432\u0430\u0448\u0430 \u043f\u043e\u043f\u044b\u0442\u043a\u0430 \u0431\u0443\u0434\u0435\u0442 \u0437\u0430\u0441\u0447\u0438\u0442\u0430\u043d\u0430. \u0425\u043e\u0442\u0438\u0442\u0435 \u043f\u0440\u043e\u0434\u043e\u043b\u0436\u0438\u0442\u044c?","btn_confirm":"OK","btn_cancel":"\u041e\u0442\u043c\u0435\u043d\u0430"}},"loading":"\u041e\u0431\u0440\u0430\u0431\u043e\u0442\u043a\u0430 \u0432\u0430\u0448\u0435\u0439 \u043d\u0430\u0433\u0440\u0430\u0434\u044b..."}    };
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