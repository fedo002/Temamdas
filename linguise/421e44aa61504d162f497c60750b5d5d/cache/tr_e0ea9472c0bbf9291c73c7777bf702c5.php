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

	<script async src="https://static.linguise.com/script-js/switcher.bundle.js?d=pk_HTDA4a15TTJcZ8Cb0xIgJorDLHtgAOig"></script>
	
	
	
    <!-- Page-specific CSS -->
    <style>
	.linguise_switcher .linguise_switcher_popup {
		background: #121212;
		color: #3a86ff;
}
	</style><script type="application/json" id="linguise-extra-metadata">{"domain":"aHR0cHM6Ly9kaWdpbWluZXguY29t","url":"aHR0cHM6Ly9kaWdpbWluZXguY29tL2RhaWx5LWdhbWUucGhw","language":"en","translate_urls":true,"dynamic_translations":{"enabled":true},"language_settings":{"display":"popup","position":"top_right","flag_shape":"round","flag_width":"20","enabled_flag":true,"flag_de_type":"de","flag_en_type":"en-us","flag_es_type":"es","flag_pt_type":"pt","flag_tw_type":"zh-tw","flag_shadow_h":2,"flag_shadow_v":2,"flag_shadow_blur":12,"enabled_lang_name":true,"flag_shadow_color":"#000000","lang_name_display":"native","flag_border_radius":0,"flag_shadow_spread":0,"flag_hover_shadow_h":3,"flag_hover_shadow_v":3,"language_name_color":"#e0e0e0","flag_hover_shadow_blur":6,"enabled_lang_short_name":false,"flag_hover_shadow_color":"#000000","flag_hover_shadow_spread":0,"language_name_hover_color":"#454545","popup_language_name_color":"#000000","popup_language_name_hover_color":"#000000"},"languages":[{"code":"zh-cn","name":"Chinese","original_name":"中文"},{"code":"es","name":"Spanish","original_name":"Español"},{"code":"fr","name":"French","original_name":"Français"},{"code":"de","name":"German","original_name":"Deutsch"},{"code":"ru","name":"Russian","original_name":"Русский"},{"code":"tr","name":"Turkish","original_name":"Türkçe"},{"code":"en","name":"English","original_name":"English"}],"structure":"subfolders","platform":"other_php","debug":false,"public_key":"pk_HTDA4a15TTJcZ8Cb0xIgJorDLHtgAOig","rules":[],"cached_selectors":[]}</script></head>
	
		
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
                <span>Kalan denemeler: <strong>967</strong></span>
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
    <div class="game-area">
                <!-- Main game options (Direct reward) -->
        <div class="game-stage" id="direct-reward">
            <h2>G&#xFC;nl&#xFC;k &#xD6;d&#xFC;l&#xFC;n&#xFC;z</h2>
            <p>29 USDT Haz&#x131;r! &#x15E;imdi al ya da &#x15F;ans&#x131;n&#x131;z&#x131; daha fazla i&#xE7;in deneyin mi?</p>
            
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
                                <span>Kart 1</span>
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
                                <span>Kart 2</span>
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
                                <span>Kart 3</span>
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
    <div class="modal" id="resultModal">
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
    <div class="modal" id="countdownModal">
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
    <div id="loadingOverlay" class="loading-overlay">
        <div class="spinner"></div>
        <p>&#xD6;d&#xFC;l&#xFC;n&#xFC;z&#xFC; i&#x15F;lemek ...</p>
    </div>
    
    <!-- Backdrop -->
    <div class="backdrop" id="backdrop"></div>
</div>

<style>
/* Daily Game Page Styles */
.daily-game-page {
    padding: 15px;
    color: #333;
    position: relative;
}

/* Loading Overlay */
.loading-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7);
    z-index: 2000;
    justify-content: center;
    align-items: center;
    flex-direction: column;
}

.loading-overlay.show {
    display: flex;
}

.spinner {
    border: 5px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    border-top: 5px solid white;
    width: 50px;
    height: 50px;
    animation: spin 1s linear infinite;
    margin-bottom: 20px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.loading-overlay p {
    color: white;
    font-size: 1.2rem;
}

/* Game Explanation */
.game-explanation {
    display: flex;
    justify-content: space-around;
    margin: 20px 0;
    flex-wrap: wrap;
}

.explanation-option {
    text-align: center;
    background-color: rgba(0,0,0,0.03);
    border-radius: 10px;
    padding: 15px;
    width: 45%;
    margin-bottom: 10px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    transition: transform 0.3s;
}

.explanation-option:hover {
    transform: translateY(-5px);
}

.explanation-option i {
    font-size: 2rem;
    margin-bottom: 10px;
    color: var(--primary-color, #7367f0);
}

.explanation-option h3 {
    font-size: 1rem;
    margin: 0 0 8px;
    font-weight: 600;
}

.explanation-option p {
    font-size: 0.8rem;
    margin: 0;
    color: #666;
}

/* Timer */
.timer-container {
    margin: 20px auto;
    width: 90%;
    max-width: 400px;
}

.timer-bar {
    height: 10px;
    background-color: #e9ecef;
    border-radius: 5px;
    overflow: hidden;
    margin-bottom: 5px;
}

.timer-progress {
    height: 100%;
    background-color: #ff9f43;
    width: 100%;
    transition: width 1s linear;
}

.timer-text {
    text-align: center;
    font-size: 0.9rem;
    color: #666;
}

/* Game Header */
.game-header {
    background-color: var(--primary-color, #7367f0);
    color: white;
    border-radius: 15px;
    padding: 20px;
    text-align: center;
    margin-bottom: 20px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(115, 103, 240, 0.3);
}

.game-header:before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(45deg, rgba(255,255,255,0.1) 25%, transparent 25%, transparent 50%, rgba(255,255,255,0.1) 50%, rgba(255,255,255,0.1) 75%, transparent 75%, transparent);
    background-size: 10px 10px;
    opacity: 0.2;
}

.game-header h1 {
    font-size: 1.8rem;
    font-weight: 700;
    margin: 0 0 10px;
}

.game-header p {
    opacity: 0.9;
    margin: 0 0 15px;
}

.game-stats {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-bottom: 15px;
}

.stat-badge {
    background-color: rgba(255, 255, 255, 0.2);
    border-radius: 20px;
    padding: 8px 15px;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
}

.stat-badge i {
    margin-right: 8px;
}

.vip-badge {
    background-color: #ff9f43;
    color: white;
    display: inline-block;
    padding: 8px 15px;
    border-radius: 20px;
    font-weight: 600;
}

.vip-badge i {
    margin-right: 5px;
}

/* Game Area */
.game-area {
    background-color: white;
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.game-stage {
    text-align: center;
}

.game-stage h2 {
    font-size: 1.3rem;
    font-weight: 600;
    margin: 0 0 10px;
    color: var(--primary-color, #7367f0);
}

.game-stage p {
    color: #666;
    margin-bottom: 20px;
}

/* Dealer Animation - Positioned at the bottom */
.dealer-animation {
    position: relative;
    height: 180px;
    margin-top: 20px;
    margin-bottom: 0;
    display: none;
}

.dealer {
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    z-index: 10;
}

.dealer-avatar {
    width: 60px;
    height: 60px;
    background-color: #333;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.8rem;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.dealer-table {
    position: absolute;
    bottom: 70px;
    left: 50%;
    transform: translateX(-50%);
    width: 250px;
    height: 120px;
    background-color: #24862f;
    border-radius: 0 0 120px 120px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    border: 8px solid #743c16;
    border-top: none;
}

.dealing-cards {
    position: absolute;
    bottom: 100px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 5;
}

.dealing-card {
    position: absolute;
    width: 50px;
    height: 70px;
    background-color: white;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    transform-origin: center bottom;
    opacity: 0;
}

.dealing-card.card1 {
    transform: rotate(-25deg) translateX(-80px);
}

.dealing-card.card2 {
    transform: translateX(-25px);
}

.dealing-card.card3 {
    transform: rotate(25deg) translateX(30px);
}

/* Card dealing animation - Adjusted for bottom dealing */
@keyframes dealCard1 {
    0% { opacity: 1; transform: rotate(0) translateX(0); }
    100% { opacity: 1; transform: rotate(-25deg) translateX(-80px) translateY(-60px); }
}

@keyframes dealCard2 {
    0% { opacity: 1; transform: rotate(0) translateX(0); }
    100% { opacity: 1; transform: translateX(-25px) translateY(-60px); }
}

@keyframes dealCard3 {
    0% { opacity: 1; transform: rotate(0) translateX(0); }
    100% { opacity: 1; transform: rotate(25deg) translateX(30px) translateY(-60px); }
}

/* Cards */
.cards-container {
    display: flex;
    justify-content: center;
    gap: 20px;
    perspective: 1000px;
}

.three-cards {
    flex-wrap: wrap;
}

.three-cards .game-card {
    flex: 0 0 calc(33% - 15px);
    margin-bottom: 15px;
}

.game-card {
    width: 100px; /* Smaller initial size */
    height: 140px;
    cursor: pointer;
    position: relative;
    transform-style: preserve-3d;
    transition: transform 0.6s, filter 0.3s;
}

.card-inner {
    position: relative;
    width: 100%;
    height: 100%;
    text-align: center;
    transition: transform 0.6s;
    transform-style: preserve-3d;
}

.game-card.flipped .card-inner {
    transform: rotateY(180deg);
}

.card-front, .card-back {
    position: absolute;
    width: 100%;
    height: 100%;
    -webkit-backface-visibility: hidden;
    backface-visibility: hidden;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.card-front {
    color: white;
    border: 3px solid white;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

/* Card shaking animation */
@keyframes cardShake {
    0% { transform: translate(0, 0) rotate(0); }
    20% { transform: translate(-2px, 0) rotate(-2deg); }
    40% { transform: translate(2px, 0) rotate(2deg); }
    60% { transform: translate(-2px, 0) rotate(-1deg); }
    80% { transform: translate(2px, 0) rotate(1deg); }
    100% { transform: translate(0, 0) rotate(0); }
}

.shake {
    animation: cardShake 0.5s ease-in-out infinite;
    filter: brightness(1.1);
}

/* Random card colors */
.card-color-1 {
    background: linear-gradient(135deg, #7367F0, #4839EB);
}

.card-color-2 {
    background: linear-gradient(135deg, #FF9F43, #FF8412);
}

.card-color-3 {
    background: linear-gradient(135deg, #28C76F, #1F9D57);
}

.card-back {
    background-color: white;
    color: #333;
    border: 3px solid var(--primary-color, #7367f0);
    transform: rotateY(180deg);
}

.card-content {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.card-content i {
    font-size: 2.5rem;
    margin-bottom: 10px;
}

.card-result {
    padding: 10px;
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

/* Card reveal animation */
@keyframes reveal {
    0% { transform: scale(0.8); opacity: 0; }
    100% { transform: scale(1); opacity: 1; }
}

.card-result i, .card-result h3 {
    animation: reveal 0.5s ease-out forwards;
}

/* Decision Buttons */
.decision-buttons {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-top: 30px;
}

/* Reward Badges */
.possible-rewards {
    margin-bottom: 20px;
    font-size: 0.9rem;
}

.reward-badges {
    display: flex;
    gap: 10px;
    justify-content: center;
    margin-top: 10px;
}

.reward-badge {
    padding: 5px 10px;
    border-radius: 15px;
    font-weight: 600;
}

.reward-badge.low {
    background-color: #e9ecef;
    color: #495057;
}

.reward-badge.medium {
    background-color: #4a00e0;
    color: white;
}

.reward-badge.high {
    background-color: #ff9f43;
    color: white;
}

/* No Attempts */
.no-attempts {
    text-align: center;
    padding: 30px 20px;
}

.no-attempts i {
    font-size: 3rem;
    color: #ff9f43;
    margin-bottom: 20px;
}

.no-attempts h2 {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 15px;
}

.no-attempts p {
    color: #666;
    margin-bottom: 25px;
}

/* Buttons */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 10px 20px;
    border-radius: 10px;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none;
    transition: background-color 0.3s, transform 0.2s;
    border: none;
}

.btn:active {
    transform: scale(0.98);
}

.btn i {
    margin-right: 8px;
}

.btn-primary {
    background-color: var(--primary-color, #7367f0);
    color: white;
}

.btn-success {
    background-color: #28c76f;
    color: white;
}

.btn-warning {
    background-color: #ff9f43;
    color: white;
}

/* Spacing for multiple buttons */
.mr-2 {
    margin-right: 0.5rem;
}

/* Modal */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
}

.modal-content {
    background-color: white;
    margin: 20% auto;
    padding: 0;
    width: 90%;
    max-width: 400px;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    position: relative;
    z-index: 1001;
    transform: translateY(20px);
    opacity: 0;
    transition: transform 0.3s, opacity 0.3s;
}

.modal.show .modal-content {
    transform: translateY(0);
    opacity: 1;
}

.modal-header {
    padding: 15px 20px;
    border-bottom: 1px solid #eee;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.modal-header h3 {
    margin: 0;
    font-size: 1.2rem;
    font-weight: 600;
}

.close-modal {
    font-size: 1.5rem;
    font-weight: 700;
    color: #aaa;
    cursor: pointer;
}

.modal-body {
    padding: 20px;
    text-align: center;
}

.modal-footer {
    padding: 15px 20px;
    border-top: 1px solid #eee;
    text-align: right;
}

.backdrop {
    display: none;
    position: fixed;
    z-index: 999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    opacity: 0;
    transition: opacity 0.3s;
}

.backdrop.show {
    opacity: 1;
}

/* Confetti animation for big wins */
.confetti {
    position: fixed;
    width: 10px;
    height: 10px;
    background-color: #f00;
    top: -10px;
    z-index: 1002;
    animation: confetti-fall 4s linear forwards;
}

@keyframes confetti-fall {
    0% {
        transform: translateY(0) rotate(0deg);
        opacity: 1;
    }
    100% {
        transform: translateY(100vh) rotate(720deg);
        opacity: 0;
    }
}

/* Custom message box styles */
.custom-message-box {
    display: none;
    position: fixed;
    z-index: 2001;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7);
    justify-content: center;
    align-items: center;
}

.message-box-content {
    background-color: white;
    border-radius: 15px;
    width: 90%;
    max-width: 400px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    animation: message-box-in 0.3s ease-out;
}

@keyframes message-box-in {
    from { transform: translateY(-50px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

.message-box-header {
    padding: 15px 20px;
    border-bottom: 1px solid #eee;
}

.message-box-header h3 {
    margin: 0;
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--primary-color, #7367f0);
}

.message-box-body {
    padding: 20px;
    text-align: center;
}

.message-box-body p {
    margin: 0;
    color: #666;
}

.message-box-footer {
    padding: 15px 20px;
    border-top: 1px solid #eee;
    text-align: right;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

.btn-outline {
    background-color: transparent;
    border: 1px solid #ddd;
    color: #666;
}
</style>

<script>
// Game state
let gameState = {
    reward: 29,
    cardRewards: {
        low: 20,
        medium: 30,
        high: 40    },
    inProgress: false,
    timerInterval: null,
    shakeInterval: null,  // Added for random card shaking
    selectedCard: null,
    timeLeft: 30,
    gameStarted: false,  // Track if game has started for auto-reward on leave
    exitAttempt: false,  // Track if user attempted to exit
    currentPrize: 0,     // For storing current prize when retrys offered
    gameAttemptId: 0,    // Store the game attempt ID for updating
    vipLevel: 3,
    remainingRetries: 0  // Remaining retry attempts
};

// Load translations
const translations = {"daily_game":{"page_title":"Daily Reward Game","header_title":"Daily Reward Game","header_subtitle":"Try your luck and win USDT rewards!","stats":{"attempts":"Remaining Tries","win_chance":"Win Chance","vip_level":"VIP Level"},"stage1":{"title":"Your Daily Reward","description":"{amount} USDT ready! Take it now or try your luck for more?","option1":{"title":"Take Reward","description":"Get guaranteed {amount} USDT now"},"option2":{"title":"Try Your Luck","description":"Risk your guaranteed reward for a chance to win up to {max_amount} USDT!"},"btn_take":"Take {amount} USDT","btn_try_luck":"Try Your Luck"},"stage2":{"title":"Choose Your Card","description":"Select a card to see your reward!","possible_rewards":"Possible Rewards:","timer_text":"Time to select a card: {seconds} seconds","card_prefix":"Card"},"no_attempts":{"title":"No Game Attempts Left","description":"You have used all your game attempts for today. Come back tomorrow or upgrade your VIP level for more attempts!","btn_explore_vip":"Explore VIP Packages"},"modals":{"timeout":{"title":"Time's Up!","message1":"You didn't select a card within the time limit.","message2":"The lowest reward has been added to your account.","btn_ok":"OK"},"result":{"title":"Congratulations!","message":"Your reward has been added to your account.","try_again_message":"You can try again for a better reward!","remaining_retries":"Your remaining retry attempts","btn_ok":"OK","btn_try_again":"Try Again"},"error":{"title":"Error","generic_error":"An error occurred!","try_again":"Please try again later.","connection_error":"Connection Error","check_connection":"Please check your internet connection and try again.","no_attempts":"Your daily limit is reached!","try_tomorrow":"Come back tomorrow.","btn_ok":"OK"},"confirmation":{"title":"Confirmation Request","try_luck_warning":"If you choose to try your luck, you will receive the lowest reward if you exit the game and your attempt will be counted. Do you want to continue?","btn_confirm":"OK","btn_cancel":"Cancel"}},"loading":"Processing your reward..."}};

// Show error if translations not loaded properly
if (!translations || !translations.daily_game) {
    console.error("Translations not loaded correctly!");
}

// Custom message box
function showMessageBox(title, message, onConfirm, onCancel = null) {
    // Create message box container if it doesn't exist
    let messageBox = document.getElementById('custom-message-box');
    if (!messageBox) {
        messageBox = document.createElement('div');
        messageBox.id = 'custom-message-box';
        messageBox.className = 'custom-message-box';
        
        // Add HTML structure
        messageBox.innerHTML = `
            <div class="message-box-content">
                <div class="message-box-header">
                    <h3 id="message-box-title">${title}</h3>
                </div>
                <div class="message-box-body">
                    <p id="message-box-message">${message}</p>
                </div>
                <div class="message-box-footer">
                    <button id="message-box-confirm" class="btn btn-primary">${translations.daily_game?.modals?.confirmation?.btn_confirm || 'OK'}</button>
                    <button id="message-box-cancel" class="btn btn-outline" style="display:none;">${translations.daily_game?.modals?.confirmation?.btn_cancel || 'Cancel'}</button>
                </div>
            </div>
        `;
        
        document.body.appendChild(messageBox);
    } else {
        // Update existing message box
        document.getElementById('message-box-title').textContent = title;
        document.getElementById('message-box-message').textContent = message;
    }
    
    // Configure buttons
    const confirmBtn = document.getElementById('message-box-confirm');
    const cancelBtn = document.getElementById('message-box-cancel');
    
    // Remove existing event listeners
    const newConfirmBtn = confirmBtn.cloneNode(true);
    const newCancelBtn = cancelBtn.cloneNode(true);
    confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
    cancelBtn.parentNode.replaceChild(newCancelBtn, cancelBtn);
    
    // Add event listeners
    newConfirmBtn.addEventListener('click', function() {
        messageBox.style.display = 'none';
        if (typeof onConfirm === 'function') onConfirm();
    });
    
    if (onCancel) {
        newCancelBtn.style.display = 'inline-flex';
        newCancelBtn.addEventListener('click', function() {
            messageBox.style.display = 'none';
            onCancel();
        });
    } else {
        newCancelBtn.style.display = 'none';
    }
    
    // Show message box
    messageBox.style.display = 'flex';
}

// Prevent page leaving when game is in progress
window.addEventListener('beforeunload', function(e) {
    if (gameState.inProgress && gameState.gameStarted && !gameState.selectedCard) {
        // We already have a record created at game start, so just warn
        e.preventDefault();
        e.returnValue = translations.daily_game?.modals?.confirmation?.try_luck_warning || 
                       'Game in progress! If you exit, the lowest reward will be automatically given and your attempt will be counted.';
        return e.returnValue;
    }
    
    // If game completed and reload needed
    if (gameState.selectedCard && !gameState.inProgress) {
        // Clear user game state
        sessionStorage.removeItem('gameInProgress');
    } else if (gameState.inProgress && gameState.gameStarted) {
        // If game is in progress, save state
        sessionStorage.setItem('gameInProgress', 'true');
    }
});

// Force refresh the page (not from cache)
function forceRefresh() {
    window.location.href = window.location.href.split('?')[0] + '?refresh=' + new Date().getTime();
}

// When page loads, assign random gradient colors to cards
document.addEventListener('DOMContentLoaded', function() {
    // Check previous game state
    const gameWasInProgress = sessionStorage.getItem('gameInProgress');
    
    if (gameWasInProgress) {
        // Clear previous game state
        sessionStorage.removeItem('gameInProgress');
        
        // Force page refresh - not from cache
        if (performance.navigation.type !== 1) { // 1 = Refresh
            forceRefresh();
        }
    }
    
    // Log the current game state values for debugging
    console.log("Initial gameState:", gameState);
    
    // Define a set of different gradient combinations
    const gradients = [
        'linear-gradient(135deg, #7367F0, #4839EB)', // Purple
        'linear-gradient(135deg, #FF9F43, #FF8412)', // Orange
        'linear-gradient(135deg, #28C76F, #1F9D57)', // Green
        'linear-gradient(135deg, #EA5455, #D43A3A)', // Red
        'linear-gradient(135deg, #00CFE8, #00A1B5)', // Blue
        'linear-gradient(135deg, #FCCE54, #E8B10D)'  // Yellow
    ];
    
    // Shuffle the gradients
    const shuffled = [...gradients].sort(() => 0.5 - Math.random());
    
    // Assign to cards
    const cardFronts = document.querySelectorAll('.card-front');
    cardFronts.forEach((front, index) => {
        front.style.background = shuffled[index % shuffled.length];
    });
    
    // Preload all sounds
    const sounds = [
        document.getElementById('cardFlipSound'),
        document.getElementById('winSound'),
        document.getElementById('tensionSound'),
        document.getElementById('dealerSound')
    ];
    
    sounds.forEach(sound => {
        if (sound) {
            sound.load();
        }
    });
});

// Take the prize directly
function takePrize() {
    if (gameState.inProgress) return;
    
    // Show loading overlay
    showLoading();
    
    console.log("Taking prize: " + gameState.reward);
    
    fetch('ajax/play_game.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'Cache-Control': 'no-cache, no-store'
        },
        body: `stage=1&action=take&amount=${gameState.reward}`
    })
    .then(response => response.json())
    .then(data => {
        console.log('Take prize response:', data);
        
        // Hide loading overlay
        hideLoading();
        
        if (data.status === 'success') {
            // Play win sound
            playSound('winSound');
            
            // Show success modal
            showResultModal(translations.daily_game?.modals?.result?.title || 'Congratulations!', `
                <i class="fas fa-trophy" style="font-size: 3rem; color: gold; margin-bottom: 15px;"></i>
                <h3>${gameState.reward} USDT</h3>
                <p>${translations.daily_game?.modals?.result?.message || 'Your reward has been added to your account.'}</p>
            `);
            
            // Reload page after modal is closed
            document.getElementById('resultModal').addEventListener('hidden', function() {
                forceRefresh();
            }, { once: true });
        } else {
            // Show error modal
            showResultModal(translations.daily_game?.modals?.error?.title || 'Error', `
                <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: #ea5455; margin-bottom: 15px;"></i>
                <h3>${translations.daily_game?.modals?.error?.generic_error || 'An error occurred!'}</h3>
                <p>${data.message || translations.daily_game?.modals?.error?.try_again || 'Please try again later.'}</p>
            `);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        hideLoading();
        
        showResultModal(translations.daily_game?.modals?.error?.title || 'Error', `
            <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: #ea5455; margin-bottom: 15px;"></i>
            <h3>${translations.daily_game?.modals?.error?.connection_error || 'Connection Error'}</h3>
            <p>${translations.daily_game?.modals?.error?.check_connection || 'Please check your internet connection and try again.'}</p>
        `);
        
        // Reload page
        setTimeout(() => {
            forceRefresh();
        }, 2000);
    });
}

// Go to cards selection
function doubleOrNothing() {
    if (gameState.inProgress) return;
    
    // Check remaining attempts
    if (parseInt(967) <= 0) {
        showResultModal(translations.daily_game?.modals?.error?.title || 'Error', `
            <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: #ea5455; margin-bottom: 15px;"></i>
            <h3>${translations.daily_game?.modals?.error?.no_attempts || 'Your daily limit is reached!'}</h3>
            <p>${translations.daily_game?.modals?.error?.try_tomorrow || 'Come back tomorrow.'}</p>
        `);
        return;
    }
    
    // Show custom confirmation dialog
    showMessageBox(
        translations.daily_game?.modals?.confirmation?.title || 'Confirmation Request', 
        translations.daily_game?.modals?.confirmation?.try_luck_warning || 
            'If you choose to try your luck, you will receive the lowest reward if you exit the game and your attempt will be counted. Do you want to continue?',
        function() {
            // User confirmed, first create automatic record
            createInitialRecord().then(result => {
                // Store the attempt ID for possible updates
                if (result && result.attempt_id) {
                    gameState.gameAttemptId = result.attempt_id;
                }
                // Start the game
                startDoubleOrNothingGame();
            }).catch(error => {
                console.error("Error creating initial record:", error);
                // Check if error is because of no attempts left
                if (error.message && error.message.includes("Daily attempt limit")) {
                    showResultModal(translations.daily_game?.modals?.error?.title || 'Error', `
                        <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: #ea5455; margin-bottom: 15px;"></i>
                        <h3>${translations.daily_game?.modals?.error?.no_attempts || 'Your daily limit is reached!'}</h3>
                        <p>${translations.daily_game?.modals?.error?.try_tomorrow || 'Come back tomorrow.'}</p>
                    `);
                    return;
                }
                // Still start the game for better user experience
                startDoubleOrNothingGame();
            });
        },
        function() {
            // User canceled
            console.log("User canceled double or nothing");
        }
    );
}
// Create initial record with lowest prize
function createInitialRecord() {
    return new Promise((resolve, reject) => {
        // Show loading temporarily
        showLoading();
        
        // Create the initial record with exit=true
        fetch('ajax/play_game.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'Cache-Control': 'no-cache, no-store'
            },
            body: `stage=2&card=initial&record_only=1`
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            console.log('Initial record created:', data);
            
            if (data.status === 'success') {
                // Don't update balance, just create record
                resolve(data);
            } else {
                // Custom error message rejection
                reject(new Error(data.message || "Failed to create initial record"));
                
                // If error is daily limit
                if (data.message && data.message.includes("Daily attempt limit")) {
                    // Show error to user
                    showResultModal(translations.daily_game?.modals?.error?.title || 'Error', `
                        <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: #ea5455; margin-bottom: 15px;"></i>
                        <h3>${translations.daily_game?.modals?.error?.no_attempts || 'Your daily limit is reached!'}</h3>
                        <p>${translations.daily_game?.modals?.error?.try_tomorrow || 'Come back tomorrow.'}</p>
                    `);
                    
                    // Reload page
                    setTimeout(() => {
                        forceRefresh();
                    }, 2000);
                }
            }
        })
        .catch(error => {
            hideLoading();
            console.error('Error creating initial record:', error);
            reject(error);
        });
    });
}

// Start the double or nothing game
function startDoubleOrNothingGame() {
    // Set game in progress to prevent leaving
    gameState.inProgress = true;
    gameState.gameStarted = true;
    
    // Hide direct reward, show card selection
    document.getElementById('direct-reward').style.display = 'none';
    document.getElementById('card-selection').style.display = 'block';
    
    // Show dealer animation first
    const dealerAnimation = document.getElementById('dealerAnimation');
    dealerAnimation.style.display = 'block';
    
    // Play dealer shuffling sound
    playSound('dealerSound');
    
    // Start dealer animation
    setTimeout(() => {
        // Animate each card being dealt
        const card1 = document.querySelector('.dealing-card.card1');
        const card2 = document.querySelector('.dealing-card.card2');
        const card3 = document.querySelector('.dealing-card.card3');
        
        card1.style.animation = 'dealCard1 0.6s ease-out forwards';
        
        setTimeout(() => {
            card2.style.animation = 'dealCard2 0.6s ease-out forwards';
        }, 200);
        
        setTimeout(() => {
            card3.style.animation = 'dealCard3 0.6s ease-out forwards';
        }, 400);
        
        // After dealing, show the actual cards
        setTimeout(() => {
            // Show the actual game cards
            const gameCards = document.querySelectorAll('.game-card');
            gameCards.forEach(card => {
                card.style.display = 'block';
            });
            
            // Fade out the dealer animation
            dealerAnimation.style.opacity = '0';
            dealerAnimation.style.transition = 'opacity 0.5s';
            
            // Start the tension music
            playSound('tensionSound');
            
            // Start random card shaking
            startRandomCardShaking();
            
            // Hide the dealer animation after fade out
            setTimeout(() => {
                dealerAnimation.style.display = 'none';
                
                // Start the timer
                startTimer();
            }, 500);
        }, 1500);
    }, 1000);
}

// Start random card shaking animation with color change
function startRandomCardShaking() {
    const gameCards = document.querySelectorAll('.game-card');
    let currentCard = null;
    let originalBackground = '';
    const highlightColor = 'linear-gradient(135deg, #FF9F43, #FF8412)'; // Orange highlight for shaking card
    
    // Remove any existing shake classes
    gameCards.forEach(card => {
        card.classList.remove('shake');
    });
    
    // Function to shake a random card
    const shakeRandomCard = () => {
        // Remove shake from current card and restore color if it exists
        if (currentCard) {
            currentCard.classList.remove('shake');
            const front = currentCard.querySelector('.card-front');
            if (front && originalBackground) {
                front.style.background = originalBackground;
            }
        }
        
        // Select a random card
        const randomIndex = Math.floor(Math.random() * gameCards.length);
        currentCard = gameCards[randomIndex];
        
        // Store original background
        const front = currentCard.querySelector('.card-front');
        if (front) {
            originalBackground = front.style.background;
            front.style.background = highlightColor;
        }
        
        // Add shake to selected card
        currentCard.classList.add('shake');
    };
    
    // Initial shake
    shakeRandomCard();
    
    // Initialize the shaking interval - faster now (1 second)
    gameState.shakeInterval = setInterval(shakeRandomCard, 1000);
}

// Stop card shaking
function stopCardShaking() {
    if (gameState.shakeInterval) {
        clearInterval(gameState.shakeInterval);
        
        // Remove shake from all cards and restore colors
        const gameCards = document.querySelectorAll('.game-card');
        gameCards.forEach(card => {
            card.classList.remove('shake');
            // Optionally restore original colors here
        });
    }
}

// Start countdown timer
function startTimer() {
    // Show timer container
    const timerContainer = document.getElementById('timer-container');
    timerContainer.style.display = 'block';
    
    // Initialize time
    gameState.timeLeft = 30;
    document.getElementById('timer-seconds').textContent = gameState.timeLeft;
    
    // Start the timer interval
    gameState.timerInterval = setInterval(() => {
        gameState.timeLeft--;
        
        // Update timer display
        document.getElementById('timer-seconds').textContent = gameState.timeLeft;
        
        // Update progress bar
        const progress = document.getElementById('timer-progress');
        progress.style.width = (gameState.timeLeft / 30 * 100) + '%';
        
        // Change colors based on time left
        if (gameState.timeLeft <= 10) {
            progress.style.backgroundColor = '#EA5455';
        } else if (gameState.timeLeft <= 20) {
            progress.style.backgroundColor = '#FF9F43';
        }
        
        // Time's up
        if (gameState.timeLeft <= 0) {
            clearInterval(gameState.timerInterval);
            timeUp();
        }
    }, 1000);
}

// Handle time up (no card selected)
function timeUp() {
    if (gameState.selectedCard) return; // Card already selected, do nothing
    
    // Stop the timer and music
    clearInterval(gameState.timerInterval);
    stopCardShaking();
    stopSound('tensionSound');
    
    // Update timeout record
    makeAutomaticSelection();
}

// Make automatic selection (timeout)
function makeAutomaticSelection() {
    console.log("Making automatic selection for timeout");
    
    showLoading();
    
    fetch('ajax/play_game.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'Cache-Control': 'no-cache, no-store'
        },
        body: `stage=2&card=timeout&attempt_id=${gameState.gameAttemptId}`
    })
    .then(response => response.json())
    .then(data => {
        console.log("Timeout selection response:", data);
        
        hideLoading();
        
        // Show the countdown modal
        const countdownModal = document.getElementById('countdownModal');
        const backdrop = document.getElementById('backdrop');
        
        // Update the modal content
        if (data.status === 'success') {
            const prize = parseFloat(data.prize || gameState.cardRewards.low);
            
            // Update modal content with the actual prize
            const modalBody = countdownModal.querySelector('.modal-body');
            modalBody.innerHTML = `
                <i class="fas fa-clock" style="font-size: 3rem; color: #ff9f43; margin-bottom: 15px;"></i>
                <p>${translations.daily_game?.modals?.timeout?.message1 || 'You didn\'t select a card within the time limit.'}</p>
                <p>${translations.daily_game?.modals?.timeout?.message2?.replace('{amount}', prize.toFixed(2)) || `${prize.toFixed(2)} USDT has been added to your account.`}</p>
            `;
        }
        
        // Show the modal
        countdownModal.style.display = 'block';
        backdrop.style.display = 'block';
        
        setTimeout(() => {
            countdownModal.classList.add('show');
            backdrop.classList.add('show');
        }, 10);
        
        // Game completed, allow page refresh
        gameState.inProgress = false;
    })
    .catch(error => {
        console.error("Error in timeout selection:", error);
        
        hideLoading();
        
        // Show error modal and reload
        showResultModal(translations.daily_game?.modals?.error?.title || 'Error', `
            <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: #ea5455; margin-bottom: 15px;"></i>
            <h3>${translations.daily_game?.modals?.error?.connection_error || 'Connection Error'}</h3>
            <p>${translations.daily_game?.modals?.error?.check_connection || 'Please check your internet connection and try again.'}</p>
        `);
        
        // Reload page
        setTimeout(() => {
            forceRefresh();
        }, 2000);
        
        // Game completed, allow page refresh
        gameState.inProgress = false;
    });
}

// Select card with other cards reveal functionality
function selectCard(cardId) {
    // If a card is already selected or game is not in progress, return
    if (gameState.selectedCard || !gameState.inProgress) return;
    
    console.log("Card selected: " + cardId);
    gameState.selectedCard = cardId;
    
    // Clear the timer
    clearInterval(gameState.timerInterval);
    document.getElementById('timer-container').style.display = 'none';
    
    // Stop the shaking and tension music
    stopCardShaking();
    stopSound('tensionSound');
    
    // Disable all card selection
    const gameCards = document.querySelectorAll('.game-card');
    gameCards.forEach(card => {
        card.style.pointerEvents = 'none';
    });
    
    // Flip the selected card
    const selectedCard = document.getElementById(cardId);
    if (selectedCard) {
        selectedCard.classList.add('flipped');
        
        // Play card flip sound
        playSound('cardFlipSound');
        
        console.log("Card flipped: " + cardId);
    } else {
        console.error("Card not found: " + cardId);
    }
    
    // Show loading overlay
    showLoading();
    
    // Send the card selection with the attempt ID for update
    fetch('ajax/play_game.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'Cache-Control': 'no-cache, no-store'
        },
        body: `stage=2&card=${cardId}&attempt_id=${gameState.gameAttemptId}`
    })
    .then(response => response.text())
    .then(text => {
        console.log("Raw response: " + text);
        try {
            return JSON.parse(text);
        } catch (e) {
            console.error("JSON parsing error: " + e);
            throw new Error("Invalid server response: " + text);
        }
    })
    .then(data => {
        console.log("Card selection response:", data);
        
        // Hide loading overlay
        hideLoading();
        
        // Get retry info if available
        if (data.status === 'success') {
            if (data.hasOwnProperty('can_try_again')) {
                gameState.canTryAgain = data.can_try_again;
            }
            
            // If remaining attempts info is available
            if (data.hasOwnProperty('remaining_retries')) {
                gameState.remainingRetries = data.remaining_retries;
            }
        }
        
        // Show result on selected card
        setTimeout(() => {
            const resultElement = document.getElementById(cardId + '-result');
            
            if (data.status === 'success') {
                const prize = parseFloat(data.prize || 0);
                const prizeType = data.prize_type || 'low';
                
                // Create content based on card result
                let resultHTML;
                if (prizeType === 'low') {
                    resultHTML = `
                        <i class="fas fa-coins" style="font-size: 3rem; color: #6c757d; margin-bottom: 10px;"></i>
                        <h3>${prize.toFixed(2)} USDT </h3>
                    `;
                } else if (prizeType === 'medium') {
                    resultHTML = `
                        <i class="fas fa-coins" style="font-size: 3rem; color: #4a00e0; margin-bottom: 10px;"></i>
                        <h3>${prize.toFixed(2)} USDT</h3>
                    `;
                } else {
                    resultHTML = `
                        <i class="fas fa-crown" style="font-size: 3rem; color: gold; margin-bottom: 10px;"></i>
                        <h3>${prize.toFixed(2)} USDT</h3>
                    `;
                    
                    // For high prizes, create confetti effect
                    createConfetti();
                }
                
                if (resultElement) {
                    resultElement.innerHTML = resultHTML;
                } else {
                    console.error("Result element not found: " + cardId + "-result");
                }
                
                // Play win sound
                playSound('winSound');
                
                // Reveal other cards
                setTimeout(() => {
                    // Get all card IDs except the selected one
                    const otherCardIds = ['card1', 'card2', 'card3'].filter(id => id !== cardId);
                    
                    // Reveal each other card with sequential animation
                    otherCardIds.forEach((otherId, index) => {
                        setTimeout(() => {
                            revealOtherCard(otherId, data.all_cards[otherId]);
                        }, index * 500); // 500ms delay between card reveals
                    });
                    
                    // Check if we should offer retry option for low rewards
                    if (data.can_try_again && prizeType === 'low') {
                        // Show retry option after all cards revealed
                        setTimeout(() => {
                            // Store the current prize for retry handling
                            gameState.currentPrize = prize;
                            
                            // Show result modal with retry option
                            showResultModalWithRetry(
                                translations.daily_game?.modals?.result?.title || 'Congratulations!', 
                                `<i class="fas fa-coins" style="font-size: 3rem; color: #6c757d; margin-bottom: 15px;"></i>
                                <h3>${prize.toFixed(2)} USDT</h3>
                                <p>Add your reward to your account or</p>
                                <p class="mt-3">${translations.daily_game?.modals?.result?.try_again_message || 'You can try again for a better reward!'}</p>`,
                                prize
                            );
                        }, otherCardIds.length * 500 + 1000);
                    } else {
                        // Regular result modal after all cards revealed
                        setTimeout(() => {
                            let modalIcon, modalColor;
                            
                            if (prizeType === 'low') {
                                modalIcon = 'fa-coins';
                                modalColor = '#6c757d';
                            } else if (prizeType === 'medium') {
                                modalIcon = 'fa-coins';
                                modalColor = '#4a00e0';
                            } else {
                                modalIcon = 'fa-crown';
                                modalColor = 'gold';
                            }
                            
                            showResultModal(translations.daily_game?.modals?.result?.title || 'Congratulations!', `
                                <i class="fas ${modalIcon}" style="font-size: 3rem; color: ${modalColor}; margin-bottom: 15px;"></i>
                                <h3>${prize.toFixed(2)} USDT</h3>
                                <p>${translations.daily_game?.modals?.result?.message || 'Your reward has been added to your account.'}</p>
                            `);
                            
                            // Reload page after closing the modal
                            document.getElementById('resultModal').addEventListener('hidden', function() {
                                forceRefresh();
                            }, { once: true });
                        }, otherCardIds.length * 500 + 1000);
                    }
                    
                    // Game completed, allow page refresh
                    gameState.inProgress = false;
                }, 1000);
            } else {
                // Error handling
                if (resultElement) {
                    resultElement.innerHTML = `
                        <i class="fas fa-exclamation-circle" style="font-size: 3rem; color: #ea5455; margin-bottom: 10px;"></i>
                        <h3>${translations.daily_game?.modals?.error?.generic_error || 'Error!'}</h3>
                        <p>${data.message || translations.daily_game?.modals?.error?.try_again || 'An error occurred!'}</p>
                    `;
                }
                
                // Show error modal
                showResultModal(translations.daily_game?.modals?.error?.title || 'Error', `
                    <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: #ea5455; margin-bottom: 15px;"></i>
                    <h3>${translations.daily_game?.modals?.error?.generic_error || 'An error occurred!'}</h3>
                    <p>${data.message || translations.daily_game?.modals?.error?.try_again || 'Please try again later.'}</p>
                `);
                
                // Reload page
                setTimeout(() => {
                    forceRefresh();
                }, 3000);
                
                // Game completed, allow page refresh
                gameState.inProgress = false;
            }
        }, 500);
    })
    .catch(error => {
        console.error("Error caught:", error);
        hideLoading();
        
        // Show error in card
        const resultElement = document.getElementById(cardId + '-result');
        if (resultElement) {
            resultElement.innerHTML = `
                <i class="fas fa-exclamation-circle" style="font-size: 3rem; color: #ea5455; margin-bottom: 10px;"></i>
                <h3>${translations.daily_game?.modals?.error?.generic_error || 'Error!'}</h3>
                <p>${error.message}</p>
            `;
        }
        
        // Show error modal
        showResultModal(translations.daily_game?.modals?.error?.title || 'Error', `
            <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: #ea5455; margin-bottom: 15px;"></i>
            <h3>${translations.daily_game?.modals?.error?.connection_error || 'Connection Error'}</h3>
            <p>${translations.daily_game?.modals?.error?.check_connection || 'Please check your internet connection and try again.'}</p>
        `);
        
        // Reload page
        setTimeout(() => {
            forceRefresh();
        }, 3000);
        
        // Game completed, allow page refresh
        gameState.inProgress = false;
    });
}

// Show result modal with retry option
function showResultModalWithRetry(title, content, currentPrize) {
    const modal = document.getElementById('resultModal');
    const backdrop = document.getElementById('backdrop');
    const titleElement = document.getElementById('resultModalTitle');
    const contentElement = document.getElementById('resultContent');
    const footerElement = document.getElementById('resultModalFooter');
    
    if (titleElement) titleElement.textContent = title;
    if (contentElement) contentElement.innerHTML = content;
    
    // Add remaining retries info
    if (contentElement) {
        // Show remaining retries if available
        if (gameState.hasOwnProperty('remainingRetries')) {
            const retriesInfo = document.createElement('p');
            retriesInfo.className = 'mt-2 text-muted';
            retriesInfo.innerHTML = `<small>${translations.daily_game?.modals?.result?.remaining_retries || 'Your remaining retry attempts'}: <strong>${gameState.remainingRetries}</strong></small>`;
            contentElement.appendChild(retriesInfo);
        }
    }
    
    // Add retry button to footer
    if (footerElement) {
        footerElement.innerHTML = `
            <button onclick="tryAgain()" class="btn btn-warning mr-2">
                <i class="fas fa-dice"></i> ${translations.daily_game?.modals?.result?.btn_try_again || 'Try Again'}
            </button>
            <button onclick="closeModalR()" class="btn btn-primary">
                Add
            </button>
        `;
    }
    
    // Store current prize in game state for retry
    gameState.currentPrize = currentPrize;
    
    // Show modal and backdrop with animation
    modal.style.display = 'block';
    backdrop.style.display = 'block';
    
    setTimeout(() => {
        modal.classList.add('show');
        backdrop.classList.add('show');
    }, 10);
    
    // Add hidden event for later use
    modal.addEventListener('hidden', function() {
        // Custom event that can be listened for
    }, { once: true });
}

// Try again function
function tryAgain() {
    // Close current result modal
    closeModal();
    
    // Show loading screen
    showLoading();
    
    console.log("Trying again - Current game state:", 
        {attemptId: gameState.gameAttemptId, prize: gameState.currentPrize, vipLevel: gameState.vipLevel});
    
    // Send retry request
    fetch('ajax/play_game.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'Cache-Control': 'no-cache, no-store'
        },
        body: `stage=2&action=retry&current_prize=${gameState.currentPrize}&attempt_id=${gameState.gameAttemptId}`
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        console.log('Retry response:', data);
        
        if (data.status === 'success') {
            // Store remaining retry attempts (if in response)
            if (data.hasOwnProperty('remaining_retries')) {
                gameState.remainingRetries = data.remaining_retries;
                console.log("Remaining retry attempts:", gameState.remainingRetries);
            }
            
            // Reset game state
            gameState.selectedCard = null;
            gameState.inProgress = false;
            gameState.gameStarted = false;
            
            // Get new attempt ID if available
            if (data.attempt_id) {
                gameState.gameAttemptId = data.attempt_id;
                console.log("New attempt ID:", gameState.gameAttemptId);
            }
            
            // Reset flipped cards and clear
            const gameCards = document.querySelectorAll('.game-card');
            gameCards.forEach(card => {
                card.classList.remove('flipped');
                card.style.display = 'none'; // Hide all cards
                card.style.pointerEvents = 'auto'; // Re-enable card selection
                
                // Reset card results
                const resultElement = card.querySelector('.card-result');
                if (resultElement) {
                    resultElement.innerHTML = '';
                }
            });
            
            // Reset card selection area
            document.getElementById('direct-reward').style.display = 'none';
            document.getElementById('card-selection').style.display = 'block';
            
            // Reset dealer animation
            const dealerAnimation = document.getElementById('dealerAnimation');
            if (dealerAnimation) {
                dealerAnimation.style.display = 'none';
                dealerAnimation.style.opacity = '1';
            }
            
            // Clear UI
            const timerContainer = document.getElementById('timer-container');
            if (timerContainer) {
                timerContainer.style.display = 'none';
            }
            
            // Reset all animation states
            const dealingCards = document.querySelectorAll('.dealing-card');
            dealingCards.forEach(card => {
                card.style.animation = 'none';
                card.offsetHeight; // Force reflow
                card.style.opacity = '0';
            });
            
            // Restart the game
            setTimeout(() => {
                startDoubleOrNothingGame();
            }, 100);
        } else {
            // Show error
            showResultModal(translations.daily_game?.modals?.error?.title || 'Error', `
                <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: #ea5455; margin-bottom: 15px;"></i>
                <h3>${translations.daily_game?.modals?.error?.generic_error || 'An error occurred!'}</h3>
                <p>${data.message || translations.daily_game?.modals?.error?.try_again || 'Please try again later.'}</p>
            `);
            
            // Reload after error
            setTimeout(() => {
                forceRefresh();
            }, 2000);
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error:', error);
        
        showResultModal(translations.daily_game?.modals?.error?.title || 'Error', `
            <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: #ea5455; margin-bottom: 15px;"></i>
            <h3>${translations.daily_game?.modals?.error?.connection_error || 'Connection Error'}</h3>
            <p>${translations.daily_game?.modals?.error?.check_connection || 'Please check your internet connection and try again.'}</p>
        `);
        
        // Reload after error
        setTimeout(() => {
            forceRefresh();
        }, 2000);
    });
}
// Reveal other cards function
function revealOtherCard(cardId, cardInfo) {
    const otherCard = document.getElementById(cardId);
    const resultElement = document.getElementById(cardId + '-result');
    
    if (!otherCard || !resultElement) {
        console.error("Card or result element not found: " + cardId);
        return;
    }
    
    // Play card flip sound
    playSound('cardFlipSound');
    
    // Flip the card
    otherCard.classList.add('flipped');
    
    // Get prize info
    const prize = parseFloat(cardInfo.prize || 0);
    const prizeType = cardInfo.type || 'low';
    
    // Create content based on card result
    let resultHTML;
    if (prizeType === 'low') {
        resultHTML = `
            <i class="fas fa-coins" style="font-size: 3rem; color: #6c757d; margin-bottom: 10px;"></i>
            <h3>${prize.toFixed(2)} USDT</h3>
        `;
    } else if (prizeType === 'medium') {
        resultHTML = `
            <i class="fas fa-coins" style="font-size: 3rem; color: #4a00e0; margin-bottom: 10px;"></i>
            <h3>${prize.toFixed(2)} USDT</h3>
        `;
    } else {
        resultHTML = `
            <i class="fas fa-crown" style="font-size: 3rem; color: gold; margin-bottom: 10px;"></i>
            <h3>${prize.toFixed(2)} USDT</h3>
        `;
    }
    
    // Show result with a slight delay for animation
    setTimeout(() => {
        resultElement.innerHTML = resultHTML;
    }, 300);
}

// Create confetti effect for big wins
function createConfetti() {
    const colors = ['#ff0000', '#00ff00', '#0000ff', '#ffff00', '#00ffff', '#ff00ff'];
    const confettiCount = 100;
    const container = document.querySelector('body');
    
    for (let i = 0; i < confettiCount; i++) {
        const confetti = document.createElement('div');
        confetti.className = 'confetti';
        confetti.style.left = Math.random() * 100 + 'vw';
        confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
        confetti.style.animationDuration = (Math.random() * 3 + 2) + 's';
        confetti.style.animationDelay = Math.random() * 2 + 's';
        
        container.appendChild(confetti);
        
        // Remove confetti after animation
        setTimeout(() => {
            confetti.remove();
        }, 5000);
    }
}

// Show loading overlay
function showLoading() {
    const loadingOverlay = document.getElementById('loadingOverlay');
    loadingOverlay.classList.add('show');
}

// Hide loading overlay
function hideLoading() {
    const loadingOverlay = document.getElementById('loadingOverlay');
    loadingOverlay.classList.remove('show');
}

// Play a sound
function playSound(soundId) {
    const sound = document.getElementById(soundId);
    if (sound) {
        sound.currentTime = 0;
        sound.play().catch(error => {
            console.log("Sound play error:", error);
        });
    }
}

// Stop a sound
function stopSound(soundId) {
    const sound = document.getElementById(soundId);
    if (sound) {
        sound.pause();
        sound.currentTime = 0;
    }
}

// Show result modal
function showResultModal(title, content) {
    const modal = document.getElementById('resultModal');
    const backdrop = document.getElementById('backdrop');
    const titleElement = document.getElementById('resultModalTitle');
    const contentElement = document.getElementById('resultContent');
    const footerElement = document.getElementById('resultModalFooter');
    
    if (titleElement) titleElement.textContent = title;
    if (contentElement) contentElement.innerHTML = content;
    
    // Reset footer to default
    if (footerElement) {
        footerElement.innerHTML = `
            <button onclick="closeModal()" class="btn btn-primary">
                ${translations.daily_game?.modals?.result?.btn_ok || 'OK'}
            </button>
        `;
    }
    
    // Show modal and backdrop with animation
    modal.style.display = 'block';
    backdrop.style.display = 'block';
    
    setTimeout(() => {
        modal.classList.add('show');
        backdrop.classList.add('show');
    }, 10);
    
    // Add hidden event for later use
    modal.addEventListener('hidden', function() {
        // Custom event that can be listened for
    }, { once: true });
}

// Close modal
function closeModal() {
    const modal = document.getElementById('resultModal');
    const backdrop = document.getElementById('backdrop');
    
    if (!modal || !backdrop) return;
    
    modal.classList.remove('show');
    backdrop.classList.remove('show');
    
    setTimeout(() => {
        modal.style.display = 'none';
        backdrop.style.display = 'none';
        
        // Dispatch custom event
        modal.dispatchEvent(new Event('hidden'));
    }, 300);
}

// Close modal
function closeModalR() {
    const modal = document.getElementById('resultModal');
    const backdrop = document.getElementById('backdrop');
    
    if (!modal || !backdrop) return;
    
    modal.classList.remove('show');
    backdrop.classList.remove('show');
    
    setTimeout(() => {
        modal.style.display = 'none';
        backdrop.style.display = 'none';
        
        // Dispatch custom event
        modal.dispatchEvent(new Event('hidden'));
        forceRefresh();
    }, 1500);
}


// Cookie functions
function setCookie(name, value, days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "") + expires + "; path=/";
}

function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}

function eraseCookie(name) {
    document.cookie = name + '=; Max-Age=-99999999; path=/;';
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

    
    <!-- Initialize Language System and Modal -->
    <script defer src="assets/js/bg.js"></script>
    

</body></html>