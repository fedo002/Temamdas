<?php

require_once 'includes/config.php';
require_once 'includes/functions.php';

// URL'den dil algılama (session/çerez olmadan)
$request_uri = $_SERVER['REQUEST_URI'];

// Desteklenen dilleri kontrol et
$supported_languages = ['en', 'ru', 'tr', 'ka'];
$detected_language = '';
$current_language = 'en'; // Varsayılan dil

// URL'den dil kodunu çıkarmaya çalış
if (preg_match('|/([a-z]{2})(/daily-game\.php)|', $request_uri, $matches)) {
    $detected_language = $matches[1];
    
    // Eğer algılanan dil desteklenen dillerden biriyse, kullan
    if (in_array($detected_language, $supported_languages)) {
        $current_language = $detected_language;
    }
}

// Debug output
error_log("Language forced based on URL: " . $current_language);

// Oturum kontrolü - oturumda dil kaydedilmişse kullan
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['user_language'])) {
    $current_language = $_SESSION['user_language'];
}
error_log("SESSION language: " . ($_SESSION['user_language'] ?? 'not set'));
// Tespit edilen dili oturuma kaydet
$_SESSION['user_language'] = $current_language;

// Debug için
error_log("LANGUAGE DETECTION: URL: $request_uri, Current Language: $current_language");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=daily-game.php&lang=' . $current_language);
    exit;
}

// Çeviri dosyasını yükle, ancak asıl çeviriler JavaScript tarafında asenkron olarak yüklenecek
$translations_file = 'lang/game_' . $current_language . '.json';
if (file_exists($translations_file)) {
    $translations_json = file_get_contents($translations_file);
    $translations = json_decode($translations_json, true);
} else {
    // Varsayılan temel çeviriler (minimum düzeyde)
    $translations = [
        'daily_game' => [
            'modals' => [
                'timeout' => [
                    'title' => 'Time\'s Up!',
                    'message1' => 'You didn\'t select a card within the time limit.',
                    'message2' => 'The lowest reward has been added to your account.',
                    'btn_ok' => 'OK'
                ],
                'result' => [
                    'title' => 'Congratulations!',
                    'message' => 'Your reward has been added to your account.',
                    'try_again_message' => 'You can try again for a better reward!',
                    'remaining_retries' => 'Your remaining retry attempts',
                    'btn_ok' => 'OK',
                    'btn_try_again' => 'Try Again'
                ],
                'error' => [
                    'title' => 'Error',
                    'generic_error' => 'An error occurred!',
                    'try_again' => 'Please try again later.',
                    'connection_error' => 'Connection Error',
                    'check_connection' => 'Please check your internet connection and try again.',
                    'no_attempts' => 'Your daily limit is reached!',
                    'try_tomorrow' => 'Come back tomorrow.',
                    'btn_ok' => 'OK'
                ],
                'confirmation' => [
                    'title' => 'Confirmation Request',
                    'try_luck_warning' => 'If you choose to try your luck, you will receive the lowest reward if you exit the game and your attempt will be counted. Do you want to continue?',
                    'btn_confirm' => 'OK',
                    'btn_cancel' => 'Cancel'
                ]
            ],
            'loading' => 'Processing your reward...'
        ]
    ];
}

// Translation helper function
function t($key, $replacements = []) {
    global $translations;
    $value = $translations;
    
    foreach (explode('.', $key) as $segment) {
        if (isset($value[$segment])) {
            $value = $value[$segment];
        } else {
            return $key; // Key not found
        }
    }
    
    if (is_string($value) && !empty($replacements)) {
        foreach ($replacements as $search => $replace) {
            $value = str_replace('{'.$search.'}', $replace, $value);
        }
    }
    
    return $value;
}

// Default values
$remaining_attempts = 0;
$win_chance = 0;
$base_reward = 5.0; // Default
$vip_details = ['name' => 'None', 'daily_game_limit' => 0, 'game_max_win_chance' => 0];
$card_rewards = [
    'low' => 3.0,
    'medium' => 7.0,
    'high' => 10.0
];

// Get user and VIP details
$user_id = $_SESSION['user_id'] ?? 0;
$user = getUserDetails($user_id);

// Database connection
$conn = dbConnect();

// Debug function for logging
function debugLog($message) {
    error_log("[MOBILE_DAILY_GAME] " . $message);
}

// Get game settings from database
$game_settings = [];
$settings_query = "SELECT setting_key, setting_value, vip_level FROM game_settings ORDER BY vip_level ASC";
$settings_result = $conn->query($settings_query);

if ($settings_result) {
    while ($row = $settings_result->fetch_assoc()) {
        $key = $row['setting_key'];
        $value = $row['setting_value'];
        $level = $row['vip_level'];
        
        // General settings
        if ($level == 0) {
            $game_settings[$key] = $value;
        }
        
        // VIP level specific settings
        if ($level > 0) {
            $game_settings[$key . '_vip' . $level] = $value;
        }
    }
}

// Helper function for getting VIP-specific settings
function getGameSetting($key, $vip_level, $game_settings, $default = null) {
    // First try VIP-specific setting
    $vip_key = $key . '_vip' . $vip_level;
    if (isset($game_settings[$vip_key])) {
        debugLog("Using VIP-specific setting: " . $vip_key . " = " . $game_settings[$vip_key]);
        return floatval($game_settings[$vip_key]);
    }
    
    // Then try general setting
    if (isset($game_settings[$key])) {
        debugLog("Using general setting: " . $key . " = " . $game_settings[$key]);
        return floatval($game_settings[$key]);
    }
    
    // Return default value if nothing found
    debugLog("Using default value for: " . $key . " = " . $default);
    return $default;
}

if ($user && isset($user['vip_level'])) {
    $vip_level = $user['vip_level'];
    debugLog("User VIP Level: " . $vip_level);

    // If VIP level is 0 or NULL, set limits to 0
    if (empty($vip_level) || $vip_level == 0) {
        $remaining_attempts = 1;
        $win_chance = 0;
    } else {
        $vip_details_result = getVipDetails($vip_level);
        
        if ($vip_details_result && !isset($vip_details_result['error'])) {
            $vip_details = $vip_details_result;
        }

        // Check if game is active
        if (isset($game_settings['daily_game_active']) && $game_settings['daily_game_active'] != '1') {
            header('Location: dashboard.php?error=game_disabled');
            exit;
        }

        // Check daily attempts - ONLY COUNT COMPLETED GAMES
        $today = date('Y-m-d');
        $attempts_query = "SELECT COUNT(*) as attempt_count FROM game_attempts 
                 WHERE user_id = ? AND DATE(created_at) = ? 
                 AND (stage = 1 OR 
                     (stage = 2 AND attempt_result IN ('win', 'timeout', 'exit')))";
        $stmt = $conn->prepare($attempts_query);
        $stmt->bind_param('is', $user_id, $today);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $daily_attempts = intval($row['attempt_count']);
        
        $max_attempts = $vip_details['daily_game_limit'] ?? 1;
        $remaining_attempts = $max_attempts - $daily_attempts;
        
        if($remaining_attempts <= 0) {
            $error_message = t('daily_limit_reached');
              $try_tomorrow_message = t('try_tomorrow');

        }

        // Win chance based on VIP level
        $win_chance = isset($vip_details['game_max_win_chance']) ? floatval($vip_details['game_max_win_chance']) : 0.15;
        
        // Get base reward from database for this VIP level
        $base_reward = getGameSetting('stage1_base_reward', $vip_level, $game_settings, 5.0);
        debugLog("Base reward for VIP " . $vip_level . ": " . $base_reward);
        
        // Get card rewards from database for this VIP level
        $card_rewards = [
            'low' => getGameSetting('stage2_low_reward', $vip_level, $game_settings, 3.0),
            'medium' => getGameSetting('stage2_medium_reward', $vip_level, $game_settings, 7.0),
            'high' => getGameSetting('stage2_high_reward', $vip_level, $game_settings, 10.0)
        ];
        debugLog("Card rewards for VIP " . $vip_level . ": " . json_encode($card_rewards));
    }
}

$page_title = "Daily Reward Game";
include 'includes/mobile-header.php';
?>

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
        <h1>Daily Reward Game</h1>
        <p>Try your luck and win USDT rewards!</p>
        
        <div class="game-stats">
            <div class="stat-badge">
                <i class="fas fa-gamepad"></i>
                <span>Remaining Tries: <strong><?= $remaining_attempts ?></strong></span>
            </div>
            <div class="stat-badge">
                <i class="fas fa-percentage"></i>
                <span>Win Chance: <strong><?= number_format($win_chance * 100, 1) ?>%</strong></span>
            </div>
        </div>
        
        <div class="vip-badge">
            <i class="fas fa-crown"></i> VIP Level : <?= isset($vip_details['name']) ? htmlspecialchars($vip_details['name']) : 'None' ?>
        </div>
    </div>

    
    <!-- Game Area -->
    <div class="game-area no-translate" data-no-translate="true">
        <?php if($remaining_attempts > 0): ?>
        <!-- Main game options (Direct reward) -->
        <div class="game-stage" id="direct-reward">
            <h2>Your Daily Reward</h2>
            <p>  <?= $base_reward ?> USDT ready! Take it now or try your luck for more?</p>
            
            <div class="game-explanation">
                <div class="explanation-option">
                    <i class="fas fa-check-circle"></i>
                    <h3>Take Reward</h3>
                    <p>Get guaranteed <?= $base_reward ?> USDT now</p>
                </div>
                <div class="explanation-option">
                    <i class="fas fa-dice"></i>
                    <h3>Try Your Luck</h3>
                    <p>Risk your guaranteed reward for a chance to win up to <?= $card_rewards['high'] ?> USDT!</p>
                </div>
            </div>
            
            <div class="decision-buttons">
                <button class="btn btn-success" onclick="takePrize()">
                    <i class="fas fa-check-circle"></i> Take <?= $base_reward ?> USDT
                </button>
                <button class="btn btn-warning" onclick="doubleOrNothing()">
                    <i class="fas fa-dice"></i> Try Your Luck
                </button>
            </div>
        </div>
        
        <!-- Card Selection (hidden initially) -->
        <div class="game-stage" id="card-selection" style="display: none;">
            <h2>Choose Your Card</h2>
            <p>Select a card to see your reward!</p>
            
            <div class="possible-rewards">
                <span>Possible Rewards:</span>
                <div class="reward-badges">
                    <span class="reward-badge low"><?= number_format($card_rewards['low'], 2) ?> USDT</span>
                    <span class="reward-badge medium"><?= number_format($card_rewards['medium'], 2) ?> USDT</span>
                    <span class="reward-badge high"><?= number_format($card_rewards['high'], 2) ?> USDT</span>
                </div>
            </div>
            
            <div id="timer-container" class="timer-container">
                <div class="timer-bar">
                    <div class="timer-progress" id="timer-progress"></div>
                </div>
                <div class="timer-text">Time to select a card: <span id="timer-seconds">30</span> seconds</div>
            </div>
            
            <div class="cards-container three-cards">
                <div class="game-card" id="card1" onclick="selectCard('card1')" style="display: none;">
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
                
                <div class="game-card" id="card2" onclick="selectCard('card2')" style="display: none;">
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
                
                <div class="game-card" id="card3" onclick="selectCard('card3')" style="display: none;">
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
        <?php else: ?>
        <!-- No Attempts Left -->
        <div class="no-attempts">
            <i class="fas fa-hourglass-end"></i>
            <h2>No Game Attempts Left</h2>
            <p>You played today. Come back tomorrow.</p>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Results Modal -->
    <div class="modal no-translate" id="resultModal" data-no-translate="true">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="resultModalTitle"><?= t('daily_game.modals.result.title') ?></h3>
                <span class="close-modal" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body" id="resultContent">
                <!-- Result content will be here -->
            </div>
            <div class="modal-footer" id="resultModalFooter">
                <button onclick="closeModal()" class="btn btn-primary"><?= t('daily_game.modals.result.btn_ok') ?></button>
            </div>
        </div>
    </div>
    
    <!-- Countdown Modal -->
    <div class="modal no-translate" id="countdownModal" data-no-translate="true">
        <div class="modal-content">
            <div class="modal-header">
                <h3><?= t('daily_game.modals.timeout.title') ?></h3>
            </div>
            <div class="modal-body">
                <i class="fas fa-clock" style="font-size: 3rem; color: #ff9f43; margin-bottom: 15px;"></i>
                <p><?= t('daily_game.modals.timeout.message1') ?></p>
                <p><?= t('daily_game.modals.timeout.message2') ?></p>
            </div>
            <div class="modal-footer">
                <button onclick="window.location.reload()" class="btn btn-primary"><?= t('daily_game.modals.timeout.btn_ok') ?></button>
            </div>
        </div>
    </div>
    
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay no-translate" data-no-translate="true">
        <div class="spinner"></div>
        <p><?= t('daily_game.loading') ?></p>
    </div>
    
    <!-- Custom confirmation modal -->
    <div class="custom-message-box no-translate" id="custom-message-box" data-no-translate="true" style="display: none;">
        <div class="message-box-content">
            <div class="message-box-header">
                <h3 id="message-box-title"><?= t('daily_game.modals.confirmation.title') ?></h3>
            </div>
            <div class="message-box-body">
                <p id="message-box-message"><?= t('daily_game.modals.confirmation.try_luck_warning') ?></p>
            </div>
            <div class="message-box-footer">
                <button id="message-box-confirm" class="btn btn-primary"><?= t('daily_game.modals.confirmation.btn_confirm') ?></button>
                <button id="message-box-cancel" class="btn btn-outline"><?= t('daily_game.modals.confirmation.btn_cancel') ?></button>
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
        reward: <?= floatval($base_reward) ?>,
        cardRewards: {
            low: <?= floatval($card_rewards['low']) ?>,
            medium: <?= floatval($card_rewards['medium']) ?>,
            high: <?= floatval($card_rewards['high']) ?>
        },
        vipLevel: <?= intval($vip_level ?? 0) ?>,
        currentLang: "<?= htmlspecialchars($current_language) ?>",
        translations: {} // Başlangıçta boş, en son yüklenecek
    };
</script>
<script src="assets/js/daily-game.js"></script>

<?php include "includes/mobile-footer.php"; ?>