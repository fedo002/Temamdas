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
                <span>Kalan denemeler: <strong>85</strong></span>
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
                <h3 id="resultModalTitle">&#x2018;!</h3>
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
                <h3>&#x10D3;&#x10E0;&#x10DD; &#x10D0;&#x10DB;&#x10DD;&#x10D8;&#x10EC;&#x10E3;&#x10E0;&#x10D0;!</h3>
            </div>
            <div class="modal-body">
                <i class="fas fa-clock" style="font-size: 3rem; color: #ff9f43; margin-bottom: 15px;"></i>
                <p>&#x10D7;&#x10E5;&#x10D5;&#x10D4;&#x10DC; &#x10D0;&#x10E0; &#x10D0;&#x10D2;&#x10D8;&#x10E0;&#x10E9;&#x10D4;&#x10D5;&#x10D8;&#x10D0;&#x10D7; &#x10D1;&#x10D0;&#x10E0;&#x10D0;&#x10D7;&#x10D8; &#x10DA;&#x10D8;&#x10DB;&#x10D8;&#x10E2;&#x10E8;&#x10D8;.</p>
                <p>&#x10E3;&#x10DB;&#x10EA;&#x10D8;&#x10E0;&#x10D4;&#x10E1;&#x10D8; &#x10EF;&#x10D8;&#x10DA;&#x10D3;&#x10DD; &#x10D3;&#x10D0;&#x10D4;&#x10DB;&#x10D0;&#x10E2;&#x10D0; &#x10D7;&#x10E5;&#x10D5;&#x10D4;&#x10DC;&#x10E1; &#x10D0;&#x10DC;&#x10D2;&#x10D0;&#x10E0;&#x10D8;&#x10E8;&#x10E1;.</p>
            </div>
            <div class="modal-footer">
                <button onclick="window.location.reload()" class="btn btn-primary">&#x10D9;&#x10D0;&#x10E0;&#x10D2;&#x10D8;</button>
            </div>
        </div>
    </div>
    
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay no-translate" data-no-translate="true">
        <div class="spinner"></div>
        <p>&#x10D7;&#x10E5;&#x10D5;&#x10D4;&#x10DC;&#x10D8; &#x10EF;&#x10D8;&#x10DA;&#x10D3;&#x10DD; &#x10DB;&#x10E3;&#x10E8;&#x10D0;&#x10D5;&#x10D3;&#x10D4;&#x10D1;&#x10D0; ...</p>
    </div>
    
    <!-- Custom confirmation modal -->
    <div class="custom-message-box no-translate" id="custom-message-box" data-no-translate="true" style="display: none;">
        <div class="message-box-content">
            <div class="message-box-header">
                <h3 id="message-box-title">&#x10D3;&#x10D0;&#x10D3;&#x10D0;&#x10E1;&#x10E2;&#x10E3;&#x10E0;&#x10D4;&#x10D1;&#x10D8;&#x10E1; &#x10DB;&#x10DD;&#x10D7;&#x10EE;&#x10DD;&#x10D5;&#x10DC;&#x10D0;</h3>
            </div>
            <div class="message-box-body">
                <p id="message-box-message">&#x10D7;&#x10E3; &#x10D2;&#x10D0;&#x10D3;&#x10D0;&#x10EC;&#x10E7;&#x10D5;&#x10D4;&#x10E2;&#x10D7;, &#x10E0;&#x10DD;&#x10DB; &#x10E1;&#x10EA;&#x10D0;&#x10D3;&#x10DD;&#x10D7; &#x10D8;&#x10E6;&#x10D1;&#x10D0;&#x10DA;&#x10D8;, &#x10D7;&#x10D0;&#x10DB;&#x10D0;&#x10E8;&#x10D8;&#x10D3;&#x10D0;&#x10DC; &#x10D2;&#x10D0;&#x10E1;&#x10D5;&#x10DA;&#x10D8;&#x10E1; &#x10E8;&#x10D4;&#x10DB;&#x10D7;&#x10EE;&#x10D5;&#x10D4;&#x10D5;&#x10D0;&#x10E8;&#x10D8; &#x10DB;&#x10D8;&#x10DC;&#x10D8;&#x10DB;&#x10D0;&#x10DA;&#x10E3;&#x10E0; &#x10EF;&#x10D8;&#x10DA;&#x10D3;&#x10DD;&#x10E1; &#x10D3;&#x10D0; &#x10D7;&#x10E5;&#x10D5;&#x10D4;&#x10DC;&#x10D8; &#x10EA;&#x10D3;&#x10D0; &#x10E9;&#x10D0;&#x10D8;&#x10D7;&#x10D5;&#x10DA;&#x10D4;&#x10D1;&#x10D0;. &#x10D2;&#x10E1;&#x10E3;&#x10E0;&#x10D7; &#x10D2;&#x10D0;&#x10D2;&#x10E0;&#x10EB;&#x10D4;&#x10DA;&#x10D4;&#x10D1;&#x10D0;?</p>
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
        reward: 4.2,
        cardRewards: {
            low: 3,
            medium: 4,
            high: 6        },
        vipLevel: 1,
        currentLang: "ka",
        translations: {"stage2":{"title":"\u10d0\u10d8\u10e0\u10e9\u10d8\u10d4\u10d7 \u10d7\u10e5\u10d5\u10d4\u10dc\u10d8 \u10d1\u10d0\u10e0\u10d0\u10d7\u10d8","description":"\u10d0\u10d8\u10e0\u10e9\u10d8\u10d4\u10d7 \u10d1\u10d0\u10e0\u10d0\u10d7\u10d8 \u10d7\u10e5\u10d5\u10d4\u10dc\u10d8 \u10ef\u10d8\u10da\u10d3\u10dd\u10e1 \u10e1\u10d0\u10dc\u10d0\u10ee\u10d0\u10d5\u10d0\u10d3!","possible_rewards":"\u10e8\u10d4\u10e1\u10d0\u10eb\u10da\u10dd \u10ef\u10d8\u10da\u10d3\u10dd\u10d4\u10d1\u10d8:","timer_text":"\u10d1\u10d0\u10e0\u10d0\u10d7\u10d8\u10e1 \u10d0\u10e0\u10e9\u10d4\u10d5\u10d8\u10e1 \u10d3\u10e0\u10dd: {seconds} \u10ec\u10d0\u10db\u10d8","card_prefix":"\u10d1\u10d0\u10e0\u10d0\u10d7\u10d8"},"no_attempts":{"title":"\u10d7\u10d0\u10db\u10d0\u10e8\u10d8\u10e1 \u10ea\u10d3\u10d4\u10d1\u10d8 \u10d0\u10db\u10dd\u10d8\u10ec\u10e3\u10e0\u10d0","description":"\u10d7\u10e5\u10d5\u10d4\u10dc \u10d2\u10d0\u10db\u10dd\u10d8\u10e7\u10d4\u10dc\u10d4\u10d7 \u10d3\u10e6\u10d4\u10d5\u10d0\u10dc\u10d3\u10d4\u10da\u10d8 \u10e7\u10d5\u10d4\u10da\u10d0 \u10d7\u10d0\u10db\u10d0\u10e8\u10d8\u10e1 \u10ea\u10d3\u10d0. \u10d3\u10d0\u10d1\u10e0\u10e3\u10dc\u10d3\u10d8\u10d7 \u10ee\u10d5\u10d0\u10da \u10d0\u10dc \u10d0\u10d0\u10db\u10d0\u10e6\u10da\u10d4\u10d7 \u10d7\u10e5\u10d5\u10d4\u10dc\u10d8 VIP \u10d3\u10dd\u10dc\u10d4 \u10db\u10d4\u10e2\u10d8 \u10ea\u10d3\u10d4\u10d1\u10d8\u10e1\u10d7\u10d5\u10d8\u10e1!","btn_explore_vip":"\u10d2\u10d0\u10d4\u10ea\u10d0\u10dc\u10d8\u10d7 VIP \u10de\u10d0\u10d9\u10d4\u10e2\u10d4\u10d1\u10e1"},"modals":{"timeout":{"title":"\u10d3\u10e0\u10dd \u10d0\u10db\u10dd\u10d8\u10ec\u10e3\u10e0\u10d0!","message1":"\u10d7\u10e5\u10d5\u10d4\u10dc \u10d0\u10e0 \u10d0\u10d2\u10d8\u10e0\u10e9\u10d4\u10d5\u10d8\u10d0\u10d7 \u10d1\u10d0\u10e0\u10d0\u10d7\u10d8 \u10d3\u10e0\u10dd\u10d8\u10e1 \u10da\u10d8\u10db\u10d8\u10e2\u10e8\u10d8.","message2":"\u10e3\u10db\u10ea\u10d8\u10e0\u10d4\u10e1\u10d8 \u10ef\u10d8\u10da\u10d3\u10dd \u10d3\u10d0\u10d4\u10db\u10d0\u10e2\u10d0 \u10d7\u10e5\u10d5\u10d4\u10dc\u10e1 \u10d0\u10dc\u10d2\u10d0\u10e0\u10d8\u10e8\u10e1.","btn_ok":"\u10d9\u10d0\u10e0\u10d2\u10d8"},"result":{"title":"\u10d2\u10d8\u10da\u10dd\u10ea\u10d0\u10d5\u10d7!","message":"\u10d7\u10e5\u10d5\u10d4\u10dc\u10d8 \u10ef\u10d8\u10da\u10d3\u10dd \u10d3\u10d0\u10d4\u10db\u10d0\u10e2\u10d0 \u10d7\u10e5\u10d5\u10d4\u10dc\u10e1 \u10d0\u10dc\u10d2\u10d0\u10e0\u10d8\u10e8\u10e1.","try_again_message":"\u10e8\u10d4\u10d2\u10d8\u10eb\u10da\u10d8\u10d0\u10d7 \u10d9\u10d8\u10d3\u10d4\u10d5 \u10e1\u10ea\u10d0\u10d3\u10dd\u10d7 \u10e3\u10d9\u10d4\u10d7\u10d4\u10e1\u10d8 \u10ef\u10d8\u10da\u10d3\u10dd\u10e1\u10d7\u10d5\u10d8\u10e1!","remaining_retries":"\u10d7\u10e5\u10d5\u10d4\u10dc\u10d8 \u10d3\u10d0\u10e0\u10e9\u10d4\u10dc\u10d8\u10da\u10d8 \u10d2\u10d0\u10dc\u10db\u10d4\u10dd\u10e0\u10d4\u10d1\u10d8\u10d7\u10d8 \u10ea\u10d3\u10d4\u10d1\u10d8","btn_ok":"\u10d9\u10d0\u10e0\u10d2\u10d8","btn_try_again":"\u10d9\u10d8\u10d3\u10d4\u10d5 \u10e1\u10ea\u10d0\u10d3\u10d4\u10d7"},"error":{"title":"\u10e8\u10d4\u10ea\u10d3\u10dd\u10db\u10d0","generic_error":"\u10d3\u10d0\u10e4\u10d8\u10e5\u10e1\u10d8\u10e0\u10d3\u10d0 \u10e8\u10d4\u10ea\u10d3\u10dd\u10db\u10d0!","try_again":"\u10d2\u10d7\u10ee\u10dd\u10d5\u10d7, \u10e1\u10ea\u10d0\u10d3\u10dd\u10d7 \u10db\u10dd\u10d2\u10d5\u10d8\u10d0\u10dc\u10d4\u10d1\u10d8\u10d7.","connection_error":"\u10d9\u10d0\u10d5\u10e8\u10d8\u10e0\u10d8\u10e1 \u10e8\u10d4\u10ea\u10d3\u10dd\u10db\u10d0","check_connection":"\u10d2\u10d7\u10ee\u10dd\u10d5\u10d7, \u10e8\u10d4\u10d0\u10db\u10dd\u10ec\u10db\u10dd\u10d7 \u10d7\u10e5\u10d5\u10d4\u10dc\u10d8 \u10d8\u10dc\u10e2\u10d4\u10e0\u10dc\u10d4\u10e2 \u10d9\u10d0\u10d5\u10e8\u10d8\u10e0\u10d8 \u10d3\u10d0 \u10e1\u10ea\u10d0\u10d3\u10dd\u10d7 \u10d9\u10d5\u10da\u10d0\u10d5.","no_attempts":"\u10d7\u10e5\u10d5\u10d4\u10dc\u10d8 \u10d3\u10e6\u10d8\u10e3\u10e0\u10d8 \u10da\u10d8\u10db\u10d8\u10e2\u10d8 \u10d0\u10db\u10dd\u10d8\u10ec\u10e3\u10e0\u10d0!","try_tomorrow":"\u10d3\u10d0\u10d1\u10e0\u10e3\u10dc\u10d3\u10d8\u10d7 \u10ee\u10d5\u10d0\u10da.","btn_ok":"\u10d9\u10d0\u10e0\u10d2\u10d8"},"confirmation":{"title":"\u10d3\u10d0\u10d3\u10d0\u10e1\u10e2\u10e3\u10e0\u10d4\u10d1\u10d8\u10e1 \u10db\u10dd\u10d7\u10ee\u10dd\u10d5\u10dc\u10d0","try_luck_warning":"\u10d7\u10e3 \u10d2\u10d0\u10d3\u10d0\u10ec\u10e7\u10d5\u10d4\u10e2\u10d7, \u10e0\u10dd\u10db \u10e1\u10ea\u10d0\u10d3\u10dd\u10d7 \u10d8\u10e6\u10d1\u10d0\u10da\u10d8, \u10d7\u10d0\u10db\u10d0\u10e8\u10d8\u10d3\u10d0\u10dc \u10d2\u10d0\u10e1\u10d5\u10da\u10d8\u10e1 \u10e8\u10d4\u10db\u10d7\u10ee\u10d5\u10d4\u10d5\u10d0\u10e8\u10d8 \u10db\u10d8\u10d8\u10e6\u10d4\u10d1\u10d7 \u10db\u10d8\u10dc\u10d8\u10db\u10d0\u10da\u10e3\u10e0 \u10ef\u10d8\u10da\u10d3\u10dd\u10e1 \u10d3\u10d0 \u10d7\u10e5\u10d5\u10d4\u10dc\u10d8 \u10ea\u10d3\u10d0 \u10e9\u10d0\u10d8\u10d7\u10d5\u10da\u10d4\u10d1\u10d0. \u10d2\u10e1\u10e3\u10e0\u10d7 \u10d2\u10d0\u10d2\u10e0\u10eb\u10d4\u10da\u10d4\u10d1\u10d0?","btn_confirm":"\u10d9\u10d0\u10e0\u10d2\u10d8","btn_cancel":"\u10d2\u10d0\u10e3\u10e5\u10db\u10d4\u10d1\u10d0"}},"loading":"\u10d7\u10e5\u10d5\u10d4\u10dc\u10d8 \u10ef\u10d8\u10da\u10d3\u10dd \u10db\u10e3\u10e8\u10d0\u10d5\u10d3\u10d4\u10d1\u10d0..."}    };
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