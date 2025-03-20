<?php die(); ?><!DOCTYPE html><html lang="ka" dir="ltr"><head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#7367f0">
    <title>&#x10E7;&#x10DD;&#x10D5;&#x10D4;&#x10DA;&#x10D3;&#x10E6;&#x10D8;&#x10E3;&#x10E0;&#x10D8; &#x10EF;&#x10D8;&#x10DA;&#x10D3;&#x10DD;&#x10E1; &#x10D7;&#x10D0;&#x10DB;&#x10D0;&#x10E8;&#x10D8; | Digiminex &#x10DB;&#x10DD;&#x10D1;&#x10D8;&#x10DA;&#x10E3;&#x10E0;&#x10D8;</title>
    
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
            <a class="header-logo" href="/ka/index.php">
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
        <h1>&#x10E7;&#x10DD;&#x10D5;&#x10D4;&#x10DA;&#x10D3;&#x10E6;&#x10D8;&#x10E3;&#x10E0;&#x10D8; &#x10EF;&#x10D8;&#x10DA;&#x10D3;&#x10DD;&#x10E1; &#x10D7;&#x10D0;&#x10DB;&#x10D0;&#x10E8;&#x10D8;</h1>
        <p>&#x10E1;&#x10EA;&#x10D0;&#x10D3;&#x10D4;&#x10D7; &#x10D7;&#x10E5;&#x10D5;&#x10D4;&#x10DC;&#x10D8; &#x10D8;&#x10E6;&#x10D1;&#x10D0;&#x10DA;&#x10D8; &#x10D3;&#x10D0; &#x10DB;&#x10DD;&#x10D8;&#x10D2;&#x10D4;&#x10D7; USDT &#x10EF;&#x10D8;&#x10DA;&#x10D3;&#x10DD;&#x10D4;&#x10D1;&#x10D8;!</p>
        
        <div class="game-stats">
            <div class="stat-badge">
                <i class="fas fa-gamepad"></i>
                <span>&#x10D3;&#x10D0;&#x10E0;&#x10E9;&#x10D4;&#x10DC;&#x10D8;&#x10DA;&#x10D8; &#x10DB;&#x10EA;&#x10D3;&#x10D4;&#x10DA;&#x10DD;&#x10D1;&#x10D4;&#x10D1;&#x10D8;: <strong>991</strong></span>
            </div>
            <div class="stat-badge">
                <i class="fas fa-percentage"></i>
                <span>&#x10DB;&#x10DD;&#x10D8;&#x10D2;&#x10D4;&#x10D7; &#x10E8;&#x10D0;&#x10DC;&#x10E1;&#x10D8;: <strong>80.0%</strong></span>
            </div>
        </div>
        
        <div class="vip-badge">
            <i class="fas fa-crown"></i> VIP &#x10D3;&#x10DD;&#x10DC;&#x10D4;: &#x10DE;&#x10DA;&#x10D0;&#x10E2;&#x10D8;&#x10DC;&#x10D8;        </div>
    </div>

    
    <!-- Game Area -->
    <div class="game-area no-translate" data-no-translate="true">
                <!-- Main game options (Direct reward) -->
        <div class="game-stage" id="direct-reward">
            <h2>&#x10D7;&#x10E5;&#x10D5;&#x10D4;&#x10DC;&#x10D8; &#x10E7;&#x10DD;&#x10D5;&#x10D4;&#x10DA;&#x10D3;&#x10E6;&#x10D8;&#x10E3;&#x10E0;&#x10D8; &#x10EF;&#x10D8;&#x10DA;&#x10D3;&#x10DD;</h2>
            <p>  29 USDT &#x10DB;&#x10D6;&#x10D0;&#x10D3;! &#x10DB;&#x10D8;&#x10D8;&#x10E6;&#x10D4;&#x10D7; &#x10D0;&#x10EE;&#x10DA;&#x10D0; &#x10D0;&#x10DC; &#x10E1;&#x10EA;&#x10D0;&#x10D3;&#x10D4;&#x10D7; &#x10D7;&#x10E5;&#x10D5;&#x10D4;&#x10DC;&#x10D8; &#x10D8;&#x10E6;&#x10D1;&#x10D0;&#x10DA;&#x10D8; &#x10DB;&#x10D4;&#x10E2;&#x10D8;?</p>
            
            <div class="game-explanation">
                <div class="explanation-option">
                    <i class="fas fa-check-circle"></i>
                    <h3>&#x10DB;&#x10D8;&#x10D8;&#x10E6;&#x10D4;&#x10D7; &#x10EF;&#x10D8;&#x10DA;&#x10D3;&#x10DD;</h3>
                    <p>&#x10DB;&#x10D8;&#x10D8;&#x10E6;&#x10D4;&#x10D7; &#x10D2;&#x10D0;&#x10E0;&#x10D0;&#x10DC;&#x10E2;&#x10D8;&#x10E0;&#x10D4;&#x10D1;&#x10E3;&#x10DA;&#x10D8; 29 &#x10D0;&#x10E8;&#x10E8; &#x10D3;&#x10DD;&#x10DA;&#x10D0;&#x10E0;&#x10D8;</p>
                </div>
                <div class="explanation-option">
                    <i class="fas fa-dice"></i>
                    <h3>&#x10E1;&#x10EA;&#x10D0;&#x10D3;&#x10D4;&#x10D7; &#x10D7;&#x10E5;&#x10D5;&#x10D4;&#x10DC;&#x10D8; &#x10D8;&#x10E6;&#x10D1;&#x10D0;&#x10DA;&#x10D8;</h3>
                    <p>&#x10E0;&#x10D8;&#x10E1;&#x10D9;&#x10D8;&#x10E1; &#x10D2;&#x10D0;&#x10E0;&#x10D0;&#x10DC;&#x10E2;&#x10D8;&#x10E0;&#x10D4;&#x10D1;&#x10E3;&#x10DA;&#x10D8; &#x10EF;&#x10D8;&#x10DA;&#x10D3;&#x10DD; 40 &#x10D0;&#x10E8;&#x10E8; &#x10D3;&#x10DD;&#x10DA;&#x10D0;&#x10E0;&#x10D0;&#x10DB;&#x10D3;&#x10D4; &#x10DB;&#x10DD;&#x10D2;&#x10D4;&#x10D1;&#x10D8;&#x10E1; &#x10E8;&#x10D0;&#x10DC;&#x10E1;&#x10D8;!</p>
                </div>
            </div>
            
            <div class="decision-buttons"><i class="fas fa-check-circle"></i>
                    
                <button class="btn btn-success" onclick="takePrize()">&#x10DB;&#x10D8;&#x10D8;&#x10E6;&#x10D4;&#x10D7; 29 &#x10D0;&#x10E8;&#x10E8;</button>
                 <i class="fas fa-dice"></i>
                    <button class="btn btn-warning" onclick="doubleOrNothing()">&#x10D3;&#x10DD;&#x10DA;&#x10D0;&#x10E0;&#x10D8;</button>
            </div>
        </div>
        
        <!-- Card Selection (hidden initially) -->
        <div class="game-stage" id="card-selection" style="display: none;">
            <h2>&#x10D0;&#x10D8;&#x10E0;&#x10E9;&#x10D8;&#x10D4; &#x10E8;&#x10D4;&#x10DC;&#x10D8; &#x10D1;&#x10D0;&#x10E0;&#x10D0;&#x10D7;&#x10D8;</h2>
            <p>&#x10E8;&#x10D4;&#x10D0;&#x10E0;&#x10E9;&#x10D8;&#x10D4;&#x10D7; &#x10D1;&#x10D0;&#x10E0;&#x10D0;&#x10D7;&#x10D8;, &#x10E0;&#x10DD;&#x10DB; &#x10DC;&#x10D0;&#x10EE;&#x10DD;&#x10D7; &#x10D7;&#x10E5;&#x10D5;&#x10D4;&#x10DC;&#x10D8; &#x10EF;&#x10D8;&#x10DA;&#x10D3;&#x10DD;!</p>
            
            <div class="possible-rewards">
                <span>&#x10E8;&#x10D4;&#x10E1;&#x10D0;&#x10EB;&#x10DA;&#x10DD; &#x10EF;&#x10D8;&#x10DA;&#x10D3;&#x10DD;&#x10D4;&#x10D1;&#x10D8;:</span>
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
                <div class="timer-text">&#x10D1;&#x10D0;&#x10E0;&#x10D0;&#x10D7;&#x10D8;&#x10E1; &#x10D0;&#x10E0;&#x10E9;&#x10D4;&#x10D5;&#x10D8;&#x10E1; &#x10D3;&#x10E0;&#x10DD;: <span id="timer-seconds">30</span> &#x10EC;&#x10D0;&#x10DB;&#x10D8;</div>
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
                <h3 id="resultModalTitle">&#x10D2;&#x10D8;&#x10DA;&#x10DD;&#x10EA;&#x10D0;&#x10D5;&#x10D7;!</h3>
                <span class="close-modal" onclick="closeModal()">&#xD7;</span>
            </div>
            <div class="modal-body" id="resultContent">
                <!-- Result content will be here -->
            </div>
            <div class="modal-footer" id="resultModalFooter">
                <button onclick="closeModal()" class="btn btn-primary">&#x10D9;&#x10D0;&#x10E0;&#x10D2;&#x10D8;</button>
            </div>
        </div>
    </div>
    
    <!-- Countdown Modal -->
    <div class="modal no-translate" id="countdownModal" data-no-translate="true">
        <div class="modal-content">
            <div class="modal-header">
                <h3>&#x10D3;&#x10E0;&#x10DD;&#x10D0;!</h3>
            </div>
            <div class="modal-body">
                <i class="fas fa-clock" style="font-size: 3rem; color: #ff9f43; margin-bottom: 15px;"></i>
                <p>&#x10D7;&#x10E5;&#x10D5;&#x10D4;&#x10DC; &#x10D0;&#x10E0; &#x10E8;&#x10D4;&#x10D0;&#x10E0;&#x10E9;&#x10D8;&#x10D4;&#x10D7; &#x10D1;&#x10D0;&#x10E0;&#x10D0;&#x10D7;&#x10D8; &#x10D5;&#x10D0;&#x10D3;&#x10D0;&#x10E8;&#x10D8;.</p>
                <p>&#x10D7;&#x10E5;&#x10D5;&#x10D4;&#x10DC;&#x10E1; &#x10D0;&#x10DC;&#x10D2;&#x10D0;&#x10E0;&#x10D8;&#x10E8;&#x10E1; &#x10D3;&#x10D0;&#x10D4;&#x10DB;&#x10D0;&#x10E2;&#x10D0; &#x10E7;&#x10D5;&#x10D4;&#x10DA;&#x10D0;&#x10D6;&#x10D4; &#x10D3;&#x10D0;&#x10D1;&#x10D0;&#x10DA;&#x10D8; &#x10EF;&#x10D8;&#x10DA;&#x10D3;&#x10DD;.</p>
            </div>
            <div class="modal-footer">
                <button onclick="window.location.reload()" class="btn btn-primary">&#x10D9;&#x10D0;&#x10E0;&#x10D2;&#x10D8;</button>
            </div>
        </div>
    </div>
    
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay no-translate" data-no-translate="true">
        <div class="spinner"></div>
        <p>&#x10D7;&#x10E5;&#x10D5;&#x10D4;&#x10DC;&#x10D8; &#x10EF;&#x10D8;&#x10DA;&#x10D3;&#x10DD;&#x10E1; &#x10D3;&#x10D0;&#x10DB;&#x10E3;&#x10E8;&#x10D0;&#x10D5;&#x10D4;&#x10D1;&#x10D0; ...</p>
    </div>
    
    <!-- Custom confirmation modal -->
    <div class="custom-message-box no-translate" id="custom-message-box" data-no-translate="true" style="display: none;">
        <div class="message-box-content">
            <div class="message-box-header">
                <h3 id="message-box-title">&#x10D3;&#x10D0;&#x10D3;&#x10D0;&#x10E1;&#x10E2;&#x10E3;&#x10E0;&#x10D4;&#x10D1;&#x10D8;&#x10E1; &#x10DB;&#x10DD;&#x10D7;&#x10EE;&#x10DD;&#x10D5;&#x10DC;&#x10D0;</h3>
            </div>
            <div class="message-box-body">
                <p id="message-box-message">&#x10D7;&#x10E3; &#x10D2;&#x10D0;&#x10D3;&#x10D0;&#x10EC;&#x10E7;&#x10D5;&#x10D4;&#x10E2;&#x10D7; &#x10D7;&#x10E5;&#x10D5;&#x10D4;&#x10DC;&#x10D8; &#x10D8;&#x10E6;&#x10D1;&#x10DA;&#x10D8;&#x10E1; &#x10DB;&#x10DD;&#x10E1;&#x10D8;&#x10DC;&#x10EF;&#x10D5;&#x10D0;&#x10E1;, &#x10D7;&#x10E5;&#x10D5;&#x10D4;&#x10DC; &#x10DB;&#x10D8;&#x10D8;&#x10E6;&#x10D4;&#x10D1;&#x10D7; &#x10E7;&#x10D5;&#x10D4;&#x10DA;&#x10D0;&#x10D6;&#x10D4; &#x10D3;&#x10D0;&#x10D1;&#x10D0;&#x10DA; &#x10EF;&#x10D8;&#x10DA;&#x10D3;&#x10DD;&#x10E1;, &#x10D7;&#x10E3; &#x10D7;&#x10D0;&#x10DB;&#x10D0;&#x10E8;&#x10E1; &#x10D2;&#x10D0;&#x10E1;&#x10D5;&#x10DA;&#x10D0;&#x10E1; &#x10D0;&#x10DE;&#x10D8;&#x10E0;&#x10D4;&#x10D1;&#x10D7; &#x10D3;&#x10D0; &#x10D7;&#x10E5;&#x10D5;&#x10D4;&#x10DC;&#x10D8; &#x10DB;&#x10EA;&#x10D3;&#x10D4;&#x10DA;&#x10DD;&#x10D1;&#x10D0; &#x10E9;&#x10D0;&#x10D8;&#x10D7;&#x10D5;&#x10DA;&#x10D4;&#x10D1;&#x10D0;. &#x10D2;&#x10E1;&#x10E3;&#x10E0;&#x10D7; &#x10D2;&#x10D0;&#x10D0;&#x10D2;&#x10E0;&#x10EB;&#x10D4;&#x10DA;&#x10DD;&#x10D7;?</p>
            </div>
            <div class="message-box-footer">
                <button id="message-box-confirm" class="btn btn-primary">&#x10D9;&#x10D0;&#x10E0;&#x10D2;&#x10D8;</button>
                 <button id="message-box-cancel" class="btn btn-outline">&#x10D2;&#x10D0;&#x10E3;&#x10E5;&#x10DB;&#x10D4;&#x10D1;&#x10D0;</button>
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
        currentLang: "",
        translations: {} // Başlangıçta boş, en son yüklenecek
    };
</script>
<script src="assets/js/daily-game.js"></script>

</main>

    <!-- Mobile Bottom Navigation -->
    <nav class="mobile-bottom-nav">
        <a href="/ka/index.php" class="nav-item ">
            <i class="fas fa-home"></i>
            <span data-i18n="buttons.home">&#x10E1;&#x10D0;&#x10EE;&#x10DA;&#x10D8;</span>
        </a>
        
        <a href="/ka/packages.php" class="nav-item ">
            <i class="fas fa-cubes"></i>
            <span data-i18n="buttons.packages">&#x10DE;&#x10D0;&#x10D9;&#x10D4;&#x10E2;&#x10D4;&#x10D1;&#x10D8;</span>
        </a>
        
                    <a href="/ka/daily-game.php" class="nav-item nav-item-main active">
                <div class="main-btn">
                    <i class="fas fa-trophy"></i>
                </div>
                <span data-i18n="dashboard.daily_game">&#x10D7;&#x10D0;&#x10DB;&#x10D0;&#x10E8;&#x10D8;</span>
            </a>
            
            <a href="/ka/notifications.php" class="nav-item ">
                <i class="fas fa-bell"></i>
                                <span data-i18n="buttons.notifications">&#x10E8;&#x10D4;&#x10E2;&#x10E7;&#x10DD;&#x10D1;&#x10D8;&#x10DC;&#x10D4;&#x10D1;&#x10D4;&#x10D1;&#x10D8;</span>
            </a>
            
            <a href="/ka/profile.php" class="nav-item ">
                <i class="fas fa-user"></i>
                <span data-i18n="buttons.myAccount">&#x10D0;&#x10DC;&#x10D2;&#x10D0;&#x10E0;&#x10D8;&#x10E8;&#x10D8;</span>
            </a>
            </nav>

    
    <!-- Backdrop Overlay -->
    <div class="backdrop-overlay" id="backdropOverlay"></div>
    

</body></html>