<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set JSON response header
header('Content-Type: application/json');

// Dil algılama - URL'den, GET parametresinden veya session'dan
$current_language = 'en'; // Varsayılan dil
$supported_languages = ['en', 'tr', 'ru', 'ka']; // Desteklenen diller

// 1. GET parametresinden dil algılama
if (isset($_POST['lang']) && in_array($_POST['lang'], $supported_languages)) {
    $current_language = $_POST['lang'];
}
// 2. Referrer URL'den dil algılama
else if (isset($_SERVER['HTTP_REFERER'])) {
    $referrer_url = $_SERVER['HTTP_REFERER'];
    
    // /{lang}/ formatında URL'yi kontrol et
    if (preg_match('#://[^/]+/([a-z]{2})/#', $referrer_url, $matches) || 
        preg_match('#://[^/]+/([a-z]{2})$#', $referrer_url, $matches)) {
        $detected_lang = $matches[1];
        
        if (in_array($detected_lang, $supported_languages)) {
            $current_language = $detected_lang;
        }
    }
}
// 3. Session'dan dil algılama
else if (isset($_SESSION['user_language']) && in_array($_SESSION['user_language'], $supported_languages)) {
    $current_language = $_SESSION['user_language'];
}

// Oturum güncelleme
$_SESSION['user_language'] = $current_language;

error_log("[GAME LANGUAGE] Detected language: " . $current_language);

// Çeviriler - dil dosyasından yükle
$translations_file = __DIR__ . '/../lang/game_' . $current_language . '.json';
if (file_exists($translations_file)) {
    $translations_json = file_get_contents($translations_file);
    $translations = json_decode($translations_json, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("Error loading translations from file: " . json_last_error_msg());
        // Fallback to hardcoded translations if JSON parsing fails
        $translations = getHardcodedTranslations($current_language);
    }
} else {
    // Dosya yoksa sabit çevirileri kullan
    error_log("Translation file not found: " . $translations_file);
    $translations = getHardcodedTranslations($current_language);
}

// Hardcoded translations as fallback
function getHardcodedTranslations($lang) {
    $all_translations = [
        'en' => [
            'congratulations' => 'Congratulations!',
            'reward_added' => 'Your reward has been added to your account.',
            'ok' => 'OK',
            'error' => 'Error',
            'daily_limit_reached' => 'Daily attempt limit reached',
            'connection_error' => 'Connection Error',
            'check_connection' => 'Please check your internet connection and try again.',
            'try_tomorrow' => 'Come back tomorrow.',
            'modals' => [
                'confirmation' => [
                    'title' => 'Confirmation Request',
                    'try_luck_warning' => 'If you choose to try your luck, you will receive the lowest reward if you exit the game and your attempt will be counted. Do you want to continue?'
                ]
            ]
        ],
        'tr' => [
            'congratulations' => 'Tebrikler!',
            'reward_added' => 'Ödülünüz hesabınıza eklendi.',
            'ok' => 'Tamam',
            'error' => 'Hata',
            'daily_limit_reached' => 'Günlük limitinize ulaştınız!',
            'connection_error' => 'Bağlantı Hatası',
            'check_connection' => 'Lütfen internet bağlantınızı kontrol edin ve tekrar deneyin.',
            'try_tomorrow' => 'Yarın tekrar gelin.',
            'modals' => [
                'confirmation' => [
                    'title' => 'Onay İsteği',
                    'try_luck_warning' => 'Şansınızı denemek isterseniz, oyundan çıkmanız durumunda en düşük ödülü alacaksınız ve hakkınız kullanılmış sayılacaktır. Devam etmek istiyor musunuz?'
                ]
            ]
        ],
        'ru' => [
            'congratulations' => 'Поздравляем!',
            'reward_added' => 'Ваша награда добавлена на ваш счет.',
            'ok' => 'OK',
            'error' => 'Ошибка',
            'daily_limit_reached' => 'Достигнут дневной лимит!',
            'connection_error' => 'Ошибка Соединения',
            'check_connection' => 'Пожалуйста, проверьте ваше интернет-соединение и попробуйте снова.',
            'try_tomorrow' => 'Возвращайтесь завтра.',
            'modals' => [
                'confirmation' => [
                    'title' => 'Запрос Подтверждения',
                    'try_luck_warning' => 'Если вы решите испытать удачу, вы получите наименьшую награду, если выйдете из игры, и ваша попытка будет засчитана. Хотите продолжить?'
                ]
            ]
        ],
        'ka' => [
            'congratulations' => 'გილოცავთ!',
            'reward_added' => 'თქვენი ჯილდო დაემატა თქვენს ანგარიშს.',
            'ok' => 'კარგი',
            'error' => 'შეცდომა',
            'daily_limit_reached' => 'თქვენი დღიური ლიმიტი ამოიწურა!',
            'connection_error' => 'კავშირის შეცდომა',
            'check_connection' => 'გთხოვთ, შეამოწმოთ თქვენი ინტერნეტ კავშირი და სცადოთ კვლავ.',
            'try_tomorrow' => 'დაბრუნდით ხვალ.',
            'modals' => [
                'confirmation' => [
                    'title' => 'დადასტურების მოთხოვნა',
                    'try_luck_warning' => 'თუ გადაწყვეტთ, რომ სცადოთ იღბალი, თამაშიდან გასვლის შემთხვევაში მიიღებთ მინიმალურ ჯილდოს და თქვენი ცდა ჩაითვლება. გსურთ გაგრძელება?'
                ]
            ]
        ]
    ];
    
    return $all_translations[$lang] ?? $all_translations['en'];
}

// Translation helper function
function t($key, $default = null) {
    global $translations, $current_language;
    
    // Log the translation attempt for debugging
    error_log("Translation request: $key, Language: $current_language");
    
    // Dot notation handling
    $parts = explode('.', $key);
    $value = $translations;
    
    foreach ($parts as $part) {
        if (isset($value[$part])) {
            $value = $value[$part];
        } else {
            error_log("Translation key not found: $key");
            return $default !== null ? $default : $key;
        }
    }
    
    if (is_array($value)) {
        error_log("Translation key resolves to array: $key");
        return $default !== null ? $default : $key;
    }
    
    error_log("Translation found for $key: $value");
    return $value;
}
// Error logging function
function logError($message) {
    error_log("[GAME ERROR] " . $message);
}

// Check for user session
if(!isset($_SESSION['user_id'])) {
    echo json_encode([
        'status' => 'error', 
        'message' => 'Session not found',
        'translated_strings' => [
            'error_title' => t('error'),
            'error_message' => 'Your session has expired. Please login again.',
            'ok_button' => t('ok')
        ]
    ]);
    exit;
}

$user_id = $_SESSION['user_id'];
logError("User ID: " . $user_id . ", POST data: " . json_encode($_POST) . ", Language: " . $current_language);

try {
    // Database connection
    $conn = dbConnect();
    
    // Get user and VIP details
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if (!$user) {
        throw new Exception("User not found");
    }
    
    $vip_level = $user['vip_level'] ?? 0;
    logError("User VIP Level: " . $vip_level);
    
    // Get VIP details
    $stmt = $conn->prepare("SELECT * FROM vip_packages WHERE id = ?");
    $stmt->bind_param('i', $vip_level);
    $stmt->execute();
    $result = $stmt->get_result();
    $vip_details = $result->fetch_assoc();
    
    if (!$vip_details) {
        // Default values
        $vip_details = [
            'daily_game_limit' => 1,
            'game_max_win_chance' => 0.15
        ];
    }
    
    // Check daily remaining attempts - ONLY COUNT COMPLETED GAMES
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
    
    $max_attempts = $vip_details['daily_game_limit'];
    $remaining_attempts = $max_attempts - $daily_attempts;
    
    if($remaining_attempts <= 0) {
        echo json_encode([
            'status' => 'error', 
            'message' => t('daily_limit_reached'),
            'translated_strings' => [
                'error_title' => t('error'),
                'error_message' => t('daily_limit_reached'),
                'try_tomorrow' => t('try_tomorrow'),
                'ok_button' => t('ok')
            ]
        ]);
        exit;
    }

    // Get game settings
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
        
    // Get POST data
    $stage = isset($_POST['stage']) ? intval($_POST['stage']) : 1;
    $card = isset($_POST['card']) ? $_POST['card'] : null;
    $action = isset($_POST['action']) ? $_POST['action'] : null;
    $record_only = isset($_POST['record_only']) ? intval($_POST['record_only']) : 0;
    $attempt_id = isset($_POST['attempt_id']) ? intval($_POST['attempt_id']) : 0;

    // Get site settings
    $site_settings = [];
    $site_settings_query = "SELECT * FROM site_settings";
    $site_settings_result = $conn->query($site_settings_query);
        
    if ($site_settings_result) {
        while ($row = $site_settings_result->fetch_assoc()) {
            $site_settings[$row['setting_key']] = $row['setting_value'];
        }
    }
    
    // Base reward for this VIP level
    $base_reward = getGameSetting('stage1_base_reward', $vip_level, $game_settings, 5.0);
    logError("Base reward for VIP " . $vip_level . ": " . $base_reward);

    // Card rewards for this VIP level
    $card_rewards = [
        'low' => getGameSetting('stage2_low_reward', $vip_level, $game_settings, 3.0),
        'medium' => getGameSetting('stage2_medium_reward', $vip_level, $game_settings, 7.0),
        'high' => getGameSetting('stage2_high_reward', $vip_level, $game_settings, 10.0)
    ];
    logError("Card rewards for VIP " . $vip_level . ": " . json_encode($card_rewards));
        
    // Card chances (without VIP adjustment)
    $card_chances = [
        'low' => floatval($game_settings['stage2_low_chance'] ?? 0.75),
        'medium' => floatval($game_settings['stage2_medium_chance'] ?? 0.20),
        'high' => floatval($game_settings['stage2_high_chance'] ?? 0.05)
    ];
    
    // Ensure chances sum to 1
    $total_chance = $card_chances['low'] + $card_chances['medium'] + $card_chances['high'];
    if (abs($total_chance - 1.0) > 0.01) {
        $card_chances['low'] /= $total_chance;
        $card_chances['medium'] /= $total_chance;
        $card_chances['high'] /= $total_chance;
    }
    
    logError("Card chances: " . json_encode($card_chances));
    
    // Stage 1 - Take direct reward
    if(($stage == 1) && $action == 'take') {
        try {
            // Begin transaction
            $conn->begin_transaction();
            
            $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : $base_reward;
            logError("Taking prize amount: " . $amount);
            
            // Get current user balance
            $stmt = $conn->prepare("SELECT balance FROM users WHERE id = ?");
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if (!$result) {
                throw new Exception("Error fetching user balance: " . $conn->error);
            }
            
            $user_data = $result->fetch_assoc();
            
            if (!$user_data) {
                throw new Exception("User data not found");
            }
            
            $before_balance = $user_data['balance']; // Current balance
            logError("Before balance: " . $before_balance);
            
            $after_balance = $before_balance + $amount; // New balance
            logError("After balance: " . $after_balance);
            
            // Update user balance
            $update_stmt = $conn->prepare("UPDATE users SET balance = ? WHERE id = ?");
            $update_stmt->bind_param('di', $after_balance, $user_id);

            if (!$update_stmt->execute()) {
                throw new Exception("Error updating balance: " . $conn->error);
            }
            
            logError("Balance updated successfully");
            
            // Record game attempt
            $attempt_stmt = $conn->prepare("INSERT INTO game_attempts (user_id, stage, attempt_result, win_amount, created_at) VALUES (?, 1, 'win', ?, NOW())");
            $attempt_stmt->bind_param('id', $user_id, $amount);
            
            if (!$attempt_stmt->execute()) {
                throw new Exception("Error recording attempt: " . $conn->error);
            }
            
            logError("Game attempt recorded successfully");
            
            // Record transaction
            $trans_stmt = $conn->prepare("INSERT INTO transactions (user_id, type, amount, description, status, before_balance, after_balance) VALUES (?, 'game', ?, 'Daily reward game earnings', 'completed', ?, ?)");
            $trans_stmt->bind_param('iddd', $user_id, $amount, $before_balance, $after_balance);
            
            if (!$trans_stmt->execute()) {
                throw new Exception("Error recording transaction: " . $conn->error);
            }
            
            logError("Transaction recorded successfully");
            
            // Process referral rewards
            processReferralRewards($conn, $user_id, $amount, $site_settings);
            
            // Commit the transaction
            $conn->commit();
            
            $response = [
                'status' => 'success',
                'message' => 'Reward added successfully',
                'amount' => $amount,
                'translated_strings' => [
                    'modals.result.title' => t('modals.result.title'),
                    'modals.result.message' => t('modals.result.message'),
                    'ok_button' => t('ok')
                ]
            ];
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            
            logError("Take reward error: " . $e->getMessage());
            
            // Return error message
            $response = [
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage(),
                'translated_strings' => [
                    'error_title' => t('error'),
                    'connection_error' => t('connection_error'),
                    'check_connection' => t('check_connection'),
                    'ok_button' => t('ok')
                ]
            ];
        }
    }
   // Stage 2 - Initial record (when Try Luck is clicked)
    elseif($stage == 2 && $card == 'initial') {
        try {
            // Begin transaction
            $conn->begin_transaction();
            
            // Önce kullanıcının bugün tamamlanmış oyun sayısını kontrol et
            $today = date('Y-m-d');
            $check_query = "SELECT COUNT(*) as attempt_count FROM game_attempts 
                        WHERE user_id = ? AND DATE(created_at) = ? 
                        AND (stage = 1 OR (stage = 2 AND attempt_result IN ('win', 'timeout', 'exit')))";
            $check_stmt = $conn->prepare($check_query);
            $check_stmt->bind_param('is', $user_id, $today);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            $check_row = $check_result->fetch_assoc();
            $used_attempts = intval($check_row['attempt_count']);
            
            // Kullanıcının kalan hakkı var mı kontrol et
            if ($used_attempts >= $max_attempts) {
                throw new Exception("Daily attempt limit reached");
            }
            
            // Varolan "initial" kayıtlarını temizle (eğer varsa)
            $clean_query = "UPDATE game_attempts SET attempt_result = 'exit' 
                        WHERE user_id = ? AND attempt_result = 'initial' 
                        AND DATE(created_at) = ?";
            $clean_stmt = $conn->prepare($clean_query);
            $clean_stmt->bind_param('is', $user_id, $today);
            $clean_stmt->execute();
            
            logError("Creating initial record");
            
            // Create initial record with 'initial' status
            $lowest_prize = $card_rewards['low'];
            
            // Insert initial record with 'initial' status
            $attempt_stmt = $conn->prepare("INSERT INTO game_attempts (user_id, stage, attempt_result, win_amount, created_at) 
                                        VALUES (?, 2, 'initial', ?, NOW())");
            $attempt_stmt->bind_param('id', $user_id, $lowest_prize);
            
            if (!$attempt_stmt->execute()) {
                throw new Exception("Error creating initial record: " . $conn->error);
            }
            
            // Get the inserted attempt ID
            $attempt_id = $conn->insert_id;
            logError("Initial record created with ID: " . $attempt_id);
            
            // Commit the transaction
            $conn->commit();
            
            $response = [
                'status' => 'success',
                'message' => 'Initial record created',
                'attempt_id' => $attempt_id,
                'translated_strings' => [
                    'confirmation_title' => t('modals.confirmation.title'),
                    'try_luck_warning' => t('modals.confirmation.try_luck_warning'),
                    'btn_confirm' => t('ok'),
                    'btn_cancel' => t('cancel')
                ]
            ];
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            
            logError("Initial record error: " . $e->getMessage());
            
            // Return error with translations
            $response = [
                'status' => 'error',
                'message' => $e->getMessage(),
                'translated_strings' => [
                    'error_title' => t('error'),
                    'error_message' => t('daily_limit_reached'),
                    'try_tomorrow' => t('try_tomorrow'),
                    'ok_button' => t('ok')
                ]
            ];
        }
    }
    // Stage 2 - Exit (when user closes the page)
    elseif($stage == 2 && $action == 'exit') {
        try {
            // Begin transaction
            $conn->begin_transaction();
            
            logError("Processing exit action for attempt_id: " . $attempt_id);
            
            if ($attempt_id > 0) {
                // Update the existing 'initial' record to 'exit'
                $update_stmt = $conn->prepare("UPDATE game_attempts SET attempt_result = 'exit', 
                                             updated_at = NOW() WHERE id = ? AND user_id = ? AND attempt_result = 'initial'");
                $update_stmt->bind_param('ii', $attempt_id, $user_id);
                
                if (!$update_stmt->execute()) {
                    throw new Exception("Error updating record to exit: " . $conn->error);
                }
                
                logError("Updated record to exit. Affected rows: " . $update_stmt->affected_rows);
                
                // Get default lowest prize
                $prize = $card_rewards['low'];
                
                // Update user balance with the lowest prize
                $stmt = $conn->prepare("SELECT balance FROM users WHERE id = ?");
                $stmt->bind_param('i', $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $user_data = $result->fetch_assoc();
                
                if ($user_data && $update_stmt->affected_rows > 0) {
                    $current_balance = $user_data['balance'];
                    $new_balance = $current_balance + $prize;
                    
                    // Update user balance
                    $update_balance = $conn->prepare("UPDATE users SET balance = ? WHERE id = ?");
                    $update_balance->bind_param('di', $new_balance, $user_id);
                    
                    if (!$update_balance->execute()) {
                        throw new Exception("Error updating balance for exit: " . $conn->error);
                    }
                    
                    // Record transaction
                    $transaction = $conn->prepare("INSERT INTO transactions (user_id, type, amount, description, status, before_balance, after_balance) 
                                                VALUES (?, 'game', ?, 'Daily game exit reward', 'completed', ?, ?)");
                    $transaction->bind_param('iddd', $user_id, $prize, $current_balance, $new_balance);
                    
                    if (!$transaction->execute()) {
                        throw new Exception("Error recording exit transaction: " . $conn->error);
                    }
                    
                    // Process referral rewards
                    processReferralRewards($conn, $user_id, $prize, $site_settings);
                }
            }
            
            // Commit the transaction
            $conn->commit();
            
            $response = [
                'status' => 'success',
                'message' => 'Exit processed'
            ];
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            
            logError("Exit processing error: " . $e->getMessage());
            
            $response = [
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage()
            ];
        }
    }
    // Stage 2 - Timeout (timer expired)
    elseif($stage == 2 && $card == 'timeout') {
        try {
            // Begin transaction
            $conn->begin_transaction();
            
            logError("Processing timeout selection");
            
            // Default to lowest prize
            $prize = $card_rewards['low'];
            $prize_type = 'low';
            
            // If attempt_id provided, update existing record
            if ($attempt_id > 0) {
                // Update the existing record to timeout
                $update_stmt = $conn->prepare("UPDATE game_attempts SET attempt_result = 'timeout', 
                                             updated_at = NOW() WHERE id = ? AND user_id = ?");
                $update_stmt->bind_param('ii', $attempt_id, $user_id);
                
                if (!$update_stmt->execute() || $update_stmt->affected_rows == 0) {
                    // If update fails, create new record
                    logError("Failed to update record, creating new one");
                    $new_stmt = $conn->prepare("INSERT INTO game_attempts (user_id, stage, attempt_result, win_amount, created_at) 
                                             VALUES (?, 2, 'timeout', ?, NOW())");
                    $new_stmt->bind_param('id', $user_id, $prize);
                    
                    if (!$new_stmt->execute()) {
                        throw new Exception("Error creating timeout record: " . $conn->error);
                    }
                    
                    // Get user balance and update
                    $stmt = $conn->prepare("SELECT balance FROM users WHERE id = ?");
                    $stmt->bind_param('i', $user_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $user_data = $result->fetch_assoc();
                    
                    if ($user_data) {
                        $current_balance = $user_data['balance'];
                        $new_balance = $current_balance + $prize;
                        
                        // Update user balance
                        $update_balance = $conn->prepare("UPDATE users SET balance = ? WHERE id = ?");
                        $update_balance->bind_param('di', $new_balance, $user_id);
                        
                        if (!$update_balance->execute()) {
                            throw new Exception("Error updating balance for timeout: " . $conn->error);
                        }
                        
                        // Record transaction
                        $transaction = $conn->prepare("INSERT INTO transactions (user_id, type, amount, description, status, before_balance, after_balance) 
                                                    VALUES (?, 'game', ?, 'Daily game timeout reward', 'completed', ?, ?)");
                        $transaction->bind_param('iddd', $user_id, $prize, $current_balance, $new_balance);
                        
                        if (!$transaction->execute()) {
                            throw new Exception("Error recording timeout transaction: " . $conn->error);
                        }
                    }
                } else {
                    logError("Updated existing record to timeout");
                    
                    // Get user balance and update
                    $stmt = $conn->prepare("SELECT balance FROM users WHERE id = ?");
                    $stmt->bind_param('i', $user_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $user_data = $result->fetch_assoc();
                    
                    if ($user_data) {
                        $current_balance = $user_data['balance'];
                        $new_balance = $current_balance + $prize;
                        
                        // Update user balance
                        $update_balance = $conn->prepare("UPDATE users SET balance = ? WHERE id = ?");
                        $update_balance->bind_param('di', $new_balance, $user_id);
                        
                        if (!$update_balance->execute()) {
                            throw new Exception("Error updating balance for timeout: " . $conn->error);
                        }
                        
                        // Record transaction
                        $transaction = $conn->prepare("INSERT INTO transactions (user_id, type, amount, description, status, before_balance, after_balance) 
                                                    VALUES (?, 'game', ?, 'Daily game timeout reward', 'completed', ?, ?)");
                        $transaction->bind_param('iddd', $user_id, $prize, $current_balance, $new_balance);
                        
                        if (!$transaction->execute()) {
                            throw new Exception("Error recording timeout transaction: " . $conn->error);
                        }
                        
                        // Process referral rewards
                        processReferralRewards($conn, $user_id, $prize, $site_settings);
                    }
                }
            } else {
                // Create new timeout record
                $attempt_stmt = $conn->prepare("INSERT INTO game_attempts (user_id, stage, attempt_result, win_amount, created_at) 
                                             VALUES (?, 2, 'timeout', ?, NOW())");
                $attempt_stmt->bind_param('id', $user_id, $prize);
                
                if (!$attempt_stmt->execute()) {
                    throw new Exception("Error creating timeout record: " . $conn->error);
                }
                
                // Get user balance and update
                $stmt = $conn->prepare("SELECT balance FROM users WHERE id = ?");
                $stmt->bind_param('i', $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $user_data = $result->fetch_assoc();
                
                if ($user_data) {
                    $current_balance = $user_data['balance'];
                    $new_balance = $current_balance + $prize;
                    
                    // Update user balance
                    $update_balance = $conn->prepare("UPDATE users SET balance = ? WHERE id = ?");
                    $update_balance->bind_param('di', $new_balance, $user_id);
                    
                    if (!$update_balance->execute()) {
                        throw new Exception("Error updating balance for timeout: " . $conn->error);
                    }
                    
                    // Record transaction
                    $transaction = $conn->prepare("INSERT INTO transactions (user_id, type, amount, description, status, before_balance, after_balance) 
                                                VALUES (?, 'game', ?, 'Daily game timeout reward', 'completed', ?, ?)");
                    $transaction->bind_param('iddd', $user_id, $prize, $current_balance, $new_balance);
                    
                    if (!$transaction->execute()) {
                        throw new Exception("Error recording timeout transaction: " . $conn->error);
                    }
                    
                    // Process referral rewards
                    processReferralRewards($conn, $user_id, $prize, $site_settings);
                }
            }
            
            // Commit the transaction
            $conn->commit();
            
            // Get translated strings based on current language
            $translatedTimeout = t('modals.timeout.title');
            $translatedMessage1 = t('modals.timeout.message1');
            $translatedMessage2 = t('modals.timeout.message2');
            $translatedOkButton = t('modals.timeout.btn_ok');
            
            $response = [
                'status' => 'success',
                'message' => 'Timeout processed',
                'prize' => $prize,
                'prize_type' => $prize_type,
                'translated_strings' => [
                    'modals' => [
                        'timeout' => [
                            'title' => $translatedTimeout,
                            'message1' => $translatedMessage1,
                            'message2' => $translatedMessage2,
                            'btn_ok' => $translatedOkButton
                        ]
                    ]
                ]
            ];
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            
            logError("Timeout error: " . $e->getMessage());
            
            $response = [
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage(),
                'translated_strings' => [
                    'error_title' => t('error'),
                    'connection_error' => t('connection_error'),
                    'check_connection' => t('check_connection'),
                    'ok_button' => t('ok')
                ]
            ];
        }
    }
    // Stage 2 - Card selection
    elseif($stage == 2 && $card != 'initial' && $card != 'timeout' && $action != 'retry') {
        try {
            // Begin transaction
            $conn->begin_transaction();
            
            logError("Processing card selection. Card: " . $card);
            
            // Generate all three card values for display
            $rand1 = mt_rand(1, 100);
            $rand2 = mt_rand(1, 100);
            $rand3 = mt_rand(1, 100);
            
            $low_threshold = $card_chances['low'] * 100;
            $medium_threshold = $card_chances['medium'] * 100;
            
            // Determine card types
            $card_types = [];
            $card_prizes = [];
            
            // Calculate prize for each card
            for ($i = 1; $i <= 3; $i++) {
                $rand = ${'rand' . $i};
                
                if ($rand <= $low_threshold) {
                    $card_types[$i] = 'low';
                    $card_prizes[$i] = $card_rewards['low'];
                } elseif ($rand <= ($low_threshold + $medium_threshold)) {
                    $card_types[$i] = 'medium';
                    $card_prizes[$i] = $card_rewards['medium'];
                } else {
                    $card_types[$i] = 'high';
                    $card_prizes[$i] = $card_rewards['high'];
                }
            }
            
            logError("Card types generated: " . json_encode($card_types));
            logError("Card prizes generated: " . json_encode($card_prizes));

            // Determine the prize based on card selection
            if (strpos($card, 'card') === 0) {
                // Extract card number from cardId (card1, card2, card3)
                $card_num = (int)substr($card, 4);
                
                if ($card_num >= 1 && $card_num <= 3) {
                    $prize = $card_prizes[$card_num];
                    $prize_type = $card_types[$card_num];
                } else {
                    // Invalid card number, use default
                    $prize = $card_rewards['low'];
                    $prize_type = 'low';
                }
                
                logError("Selected card: " . $card . ", Prize: " . $prize . " (" . $prize_type . ")");
            } else {
                // Unknown selection, default to lowest
                $prize = $card_rewards['low'];
                $prize_type = 'low';
                logError("Unknown card selection: " . $card . ", defaulting to lowest prize");
            }

            // Check if we have an attempt ID to update
            if ($attempt_id > 0) {
                // Check current status of the record
                $check_stmt = $conn->prepare("SELECT attempt_result FROM game_attempts WHERE id = ? AND user_id = ?");
                $check_stmt->bind_param('ii', $attempt_id, $user_id);
                $check_stmt->execute();
                $check_result = $check_stmt->get_result();
                $previous_status = '';
                
                if ($check_result && $check_result->num_rows > 0) {
                    $status_row = $check_result->fetch_assoc();
                    $previous_status = $status_row['attempt_result'];
                }
                
                // Update the attempt with the new win amount and result
                $update_stmt = $conn->prepare("UPDATE game_attempts SET attempt_result = 'win', win_amount = ?, 
                                            updated_at = NOW() WHERE id = ? AND user_id = ?");
                $update_stmt->bind_param('dii', $prize, $attempt_id, $user_id);
                
                $update_result = $update_stmt->execute();
                if (!$update_result || $update_stmt->affected_rows == 0) {
                    // If update fails, log it and continue
                    logError("Failed to update existing record, ID: " . $attempt_id);
                } else {
                    logError("Updated existing record successfully");
                    
                    // Update balance only if previous status was 'initial'
                    if ($previous_status == 'initial') {
                        // Get current user balance
                        $stmt = $conn->prepare("SELECT balance FROM users WHERE id = ?");
                        $stmt->bind_param('i', $user_id);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $user_data = $result->fetch_assoc();
                        
                        $current_balance = $user_data['balance'];
                        $new_balance = $current_balance + $prize;
                        $descriptioncard = "You won" . $prize . " USDT with daily reward game risk ";
                        
                        // Update with the full prize
                        $balance_stmt = $conn->prepare("UPDATE users SET balance = ? WHERE id = ?");
                        $balance_stmt->bind_param('di', $new_balance, $user_id);
                        
                        if (!$balance_stmt->execute()) {
                            throw new Exception("Error updating balance: " . $conn->error);
                        }
                        
                        // Record transaction for the prize
                        $trans_stmt = $conn->prepare("INSERT INTO transactions (user_id, type, amount, description, status, before_balance, after_balance) 
                                                VALUES (?, 'game', ?, ?, 'completed', ?, ?)");
                        $trans_stmt->bind_param('idsdd', $user_id, $prize, $descriptioncard, $current_balance, $new_balance);
                

                        if (!$trans_stmt->execute()) {
                            throw new Exception("Error recording transaction: " . $conn->error);
                        }
                        
                        // Process referral rewards only if first time selecting a card
                        $came_from_retry = false;

                        // Check if there are retry attempts today
                        $today_date = date('Y-m-d');
                        $retry_check_query = "SELECT COUNT(*) as retry_count FROM game_attempts 
                                            WHERE user_id = ? AND DATE(created_at) = ? AND 
                                            stage = 2 AND attempt_result = 'retry'";
                        $retry_check_stmt = $conn->prepare($retry_check_query);
                        $retry_check_stmt->bind_param('is', $user_id, $today_date);
                        $retry_check_stmt->execute();
                        $retry_check_result = $retry_check_stmt->get_result();

                        if ($retry_check_result && $retry_check_result->num_rows > 0) {
                            $retry_check_row = $retry_check_result->fetch_assoc();
                            if ($retry_check_row['retry_count'] > 0) {
                                $came_from_retry = true;
                                logError("User has retry records today. Skip referral rewards");
                            }
                        }

                        // Process referrals only if not from retry
                        if (!$came_from_retry) {
                            processReferralRewards($conn, $user_id, $prize, $site_settings);
                        }
                    }
                }
            } else {
                // No existing record to update, create new one
                $attempt_stmt = $conn->prepare("INSERT INTO game_attempts (user_id, stage, attempt_result, win_amount, created_at) 
                                            VALUES (?, 2, 'win', ?, NOW())");
                $attempt_stmt->bind_param('id', $user_id, $prize);
                
                if (!$attempt_stmt->execute()) {
                    throw new Exception("Error creating win record: " . $conn->error);
                }
                
                // Full balance update required
                $stmt = $conn->prepare("SELECT balance FROM users WHERE id = ?");
                $stmt->bind_param('i', $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $user_data = $result->fetch_assoc();
                
                $before_balance = $user_data['balance'];
                $after_balance = $before_balance + $prize;
                $descriptioncard = "You won" . $prize . " USDT with daily reward game risk ";
                // Update user balance
                $update_stmt = $conn->prepare("UPDATE users SET balance = ? WHERE id = ?");
                $update_stmt->bind_param('di', $after_balance, $user_id);
                
                if (!$update_stmt->execute()) {
                    throw new Exception("Error updating balance: " . $conn->error);
                }
                
                // Record transaction
                $trans_stmt = $conn->prepare("INSERT INTO transactions (user_id, type, amount, description, status, before_balance, after_balance) 
                                            VALUES (?, 'game', ?, ?, 'completed', ?, ?)");
                $trans_stmt->bind_param('idsdd', $user_id, $prize, $descriptioncard, $current_balance, $new_balance);
                
                if (!$trans_stmt->execute()) {
                    throw new Exception("Error recording transaction: " . $conn->error);
                }
                
                // Process referrals
                processReferralRewards($conn, $user_id, $prize, $site_settings);
            }
            
            // Check if user is eligible for retry option
            $can_try_again = false;
            $remaining_retries = 0;
            
            // Only offer retry for low prizes and if VIP level supports it
            if ($prize_type == 'low') {
                // Check VIP settings for retry
                $setting_key = "try_again";
                
                $try_again_query = "SELECT setting_value FROM game_settings WHERE setting_key = ? AND vip_level = ?";
                $try_again_stmt = $conn->prepare($try_again_query);
                $try_again_stmt->bind_param('si', $setting_key, $vip_level);
                $try_again_stmt->execute();
                $try_again_result = $try_again_stmt->get_result();
                
                if ($try_again_result && $try_again_result->num_rows > 0) {
                    $try_again_row = $try_again_result->fetch_assoc();
                    $max_retries = intval($try_again_row['setting_value']);
                    
                    if ($max_retries > 0) {
                        // Check if user has retry attempts left
                        $retry_query = "SELECT COUNT(*) as retry_count FROM game_attempts 
                                    WHERE user_id = ? AND DATE(created_at) = ? AND 
                                    stage = 2 AND attempt_result = 'retry'";
                        $retry_stmt = $conn->prepare($retry_query);
                        $retry_stmt->bind_param('is', $user_id, $today);
                        $retry_stmt->execute();
                        $retry_result = $retry_stmt->get_result();
                        $retry_row = $retry_result->fetch_assoc();
                        $retry_count = intval($retry_row['retry_count']);
                        
                        // If user has retries left, allow retry
                        if ($retry_count < $max_retries) {
                            $can_try_again = true;
                            $remaining_retries = $max_retries - $retry_count;
                            logError("User has retry opportunity. Retries used: $retry_count, Max: $max_retries, Remaining: $remaining_retries");
                        }
                    }
                }
            }
            
            // Commit the transaction
            $conn->commit();
            
            // Prepare all card information for the client
            $all_cards = [
                'card1' => ['type' => $card_types[1], 'prize' => $card_prizes[1]],
                'card2' => ['type' => $card_types[2], 'prize' => $card_prizes[2]],
                'card3' => ['type' => $card_types[3], 'prize' => $card_prizes[3]]
            ];
            
            // Get translated strings
            $translated_strings = [
                'congratulations' => t('congratulations'),
                'reward_added' => t('reward_added'),
                'ok_button' => t('ok'),
                'try_again' => t('modals.result.btn_try_again'),
                'try_again_message' => t('modals.result.try_again_message'),
                'remaining_retries' => t('modals.result.remaining_retries')
            ];
            
            // Return the response with all card values and translated strings
            $response = [
                'status' => 'success',
                'prize' => $prize,
                'prize_type' => $prize_type,
                'can_try_again' => $can_try_again,
                'remaining_retries' => $remaining_retries,
                'all_cards' => $all_cards,
                'translated_strings' => $translated_strings
            ];
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            
            logError("Card selection error: " . $e->getMessage());
            
            // Return error message with translations
            $response = [
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage(),
                'translated_strings' => [
                    'error_title' => t('error'),
                    'connection_error' => t('connection_error'),
                    'check_connection' => t('check_connection'),
                    'ok_button' => t('ok')
                ]
            ];
        }
    }
    // Stage 2 - Retry action
    elseif($stage == 2 && $action == 'retry') {
        try {
            // Begin transaction
            $conn->begin_transaction();
            
            // Get the current prize and attempt ID
            $current_prize = isset($_POST['current_prize']) ? floatval($_POST['current_prize']) : 0;
            $current_attempt_id = isset($_POST['attempt_id']) ? intval($_POST['attempt_id']) : 0;
            
            // Check VIP level's retry setting
            $setting_key = "try_again";
            
            $try_again_query = "SELECT setting_value FROM game_settings WHERE setting_key = ? AND vip_level = ?";
            $try_again_stmt = $conn->prepare($try_again_query);
            $try_again_stmt->bind_param('si', $setting_key, $vip_level);
            $try_again_stmt->execute();
            $try_again_result = $try_again_stmt->get_result();
            
            $max_retries = 0;
            if ($try_again_result && $try_again_result->num_rows > 0) {
                $try_again_row = $try_again_result->fetch_assoc();
                $max_retries = intval($try_again_row['setting_value']);
            }
            
            // Check if user has retries left
            $retry_query = "SELECT COUNT(*) as retry_count FROM game_attempts 
                        WHERE user_id = ? AND DATE(created_at) = ? AND 
                        stage = 2 AND attempt_result = 'retry'";
            $retry_stmt = $conn->prepare($retry_query);
            $retry_stmt->bind_param('is', $user_id, $today);
            $retry_stmt->execute();
            $retry_result = $retry_stmt->get_result();
            $retry_row = $retry_result->fetch_assoc();
            $retry_count = intval($retry_row['retry_count']);
            
            // Verify retry eligibility
            if ($retry_count >= $max_retries) {
                throw new Exception("No more retry attempts available for today");
            }
            
            // If we have current_attempt_id and current_prize, deduct the prize from user balance
            if ($current_attempt_id > 0 && $current_prize > 0) {
                // Get user balance
                $stmt = $conn->prepare("SELECT balance FROM users WHERE id = ?");
                $stmt->bind_param('i', $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result && $result->num_rows > 0) {
                    $user_data = $result->fetch_assoc();
                    $current_balance = $user_data['balance'];
                    
                    // Only proceed if user has enough balance
                    if ($current_balance >= $current_prize) {
                        $new_balance = $current_balance - $current_prize;
                        
                        // Update balance
                        $balance_stmt = $conn->prepare("UPDATE users SET balance = ? WHERE id = ?");
                        $balance_stmt->bind_param('di', $new_balance, $user_id);
                        
                        if (!$balance_stmt->execute()) {
                            throw new Exception("Error updating balance for retry: " . $conn->error);
                        }
                        
                        
                        if (!$trans_stmt->execute()) {
                            throw new Exception("Error recording retry transaction: " . $conn->error);
                        }
                        
                        // Note: We don't try to revert referral commissions as it's too complex
                    }
                }
            }
            
            // Record retry attempt - Update if attempt_id provided
            if ($current_attempt_id > 0) {
                $update_stmt = $conn->prepare("UPDATE game_attempts SET attempt_result = 'retry', win_amount = ?, updated_at = NOW() 
                                            WHERE id = ? AND user_id = ?");
                $update_stmt->bind_param('dii', $current_prize, $current_attempt_id, $user_id);
                
                if (!$update_stmt->execute() || $update_stmt->affected_rows == 0) {
                    // If update fails, create new record
                    $retry_stmt = $conn->prepare("INSERT INTO game_attempts (user_id, stage, attempt_result, win_amount, created_at) 
                                            VALUES (?, 2, 'retry', ?, NOW())");
                    $retry_stmt->bind_param('id', $user_id, $current_prize);
                    
                    if (!$retry_stmt->execute()) {
                        throw new Exception("Error recording retry attempt: " . $conn->error);
                    }
                }
            } else {
                // No attempt ID provided, create new record
                $retry_stmt = $conn->prepare("INSERT INTO game_attempts (user_id, stage, attempt_result, win_amount, created_at) 
                                        VALUES (?, 2, 'retry', ?, NOW())");
                $retry_stmt->bind_param('id', $user_id, $current_prize);
                
                if (!$retry_stmt->execute()) {
                    throw new Exception("Error recording retry attempt: " . $conn->error);
                }
            }
            
            // Create new 'initial' record for the next attempt
            $init_stmt = $conn->prepare("INSERT INTO game_attempts (user_id, stage, attempt_result, win_amount, created_at) 
                                    VALUES (?, 2, 'initial', ?, NOW())");
            $init_stmt->bind_param('id', $user_id, $card_rewards['low']);
            
            if (!$init_stmt->execute()) {
                throw new Exception("Error creating new initial record: " . $conn->error);
            }
            
            $new_init_id = $conn->insert_id;
            
            // Commit the transaction
            $conn->commit();
            
            // Calculate remaining retries
            $remaining_retries = $max_retries - ($retry_count + 1);
            
            // Get translations
            $translated_strings = [
                'congratulations' => t('congratulations'),
                'reward_added' => t('reward_added'),
                'ok_button' => t('ok'),
                'try_again' => t('modals.result.btn_try_again'),
                'try_again_message' => t('modals.result.try_again_message'),
                'remaining_retries' => t('modals.result.remaining_retries')
            ];
            
            $response = [
                'status' => 'success',
                'message' => 'Retry recorded successfully',
                'retries_used' => $retry_count + 1,
                'max_retries' => $max_retries,
                'attempt_id' => $new_init_id,
                'remaining_retries' => $remaining_retries,
                'translated_strings' => $translated_strings
            ];
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            
            logError("Retry error: " . $e->getMessage());
            
            $response = [
                'status' => 'error',
                'message' => $e->getMessage(),
                'translated_strings' => [
                    'error_title' => t('error'),
                    'connection_error' => t('connection_error'),
                    'check_connection' => t('check_connection'),
                    'ok_button' => t('ok')
                ]
            ];
        }
    } else {
        $response = [
            'status' => 'error',
            'message' => 'Invalid game stage or action',
            'translated_strings' => [
                'error_title' => t('error'),
                'ok_button' => t('ok')
            ]
        ];
    }
    
    echo json_encode($response);
    
} catch (Exception $e) {
    logError("General error: " . $e->getMessage());
    echo json_encode([
        'status' => 'error', 
        'message' => 'A system error occurred: ' . $e->getMessage(),
        'translated_strings' => [
            'error_title' => t('error'),
            'connection_error' => t('connection_error'),
            'check_connection' => t('check_connection'),
            'ok_button' => t('ok')
        ]
    ]);
}

// Get game setting with VIP level consideration
function getGameSetting($key, $vip_level, $game_settings, $default = null) {
    // First try VIP-specific setting
    $vip_key = $key . '_vip' . $vip_level;
    if (isset($game_settings[$vip_key])) {
        logError("Found VIP specific setting: " . $vip_key . " = " . $game_settings[$vip_key]);
        return floatval($game_settings[$vip_key]);
    }
    
    // Then try general setting
    if (isset($game_settings[$key])) {
        logError("Using general setting: " . $key . " = " . $game_settings[$key]);
        return floatval($game_settings[$key]);
    }
    
    // Return default value
    logError("Using default value for: " . $key . " = " . $default);
    return $default;
}

// Process referral rewards function - With transaction safety and validation
function processReferralRewards($conn, $user_id, $amount, $site_settings) {
    try {
        // Skip processing if amount is too small
        if ($amount <= 0.001) {
            logError("Skipping referral processing for amount too small: " . $amount);
            return;
        }
        
        // Check if referral system is active
        if (!isset($site_settings['referral_active']) || $site_settings['referral_active'] != '1') {
            logError("Referral system is not active");
            return;
        }
        
        // Get user's referrer_id
        $stmt = $conn->prepare("SELECT referrer_id FROM users WHERE id = ?");
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user_data = $result->fetch_assoc();
        
        // No tier 1 referrer
        if (!$user_data || !$user_data['referrer_id']) {
            logError("No referrer found for user " . $user_id);
            return;
        }
        
        $tier1_referrer_id = $user_data['referrer_id'];
        
        // Process tier 1 referral
        processReferralTier($conn, $tier1_referrer_id, $user_id, $amount, $site_settings, 1);
        
        // Process tier 2 referral
        $stmt = $conn->prepare("SELECT referrer_id FROM users WHERE id = ?");
        $stmt->bind_param('i', $tier1_referrer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $tier1_data = $result->fetch_assoc();
        
        if ($tier1_data && $tier1_data['referrer_id']) {
            $tier2_referrer_id = $tier1_data['referrer_id'];
            processReferralTier($conn, $tier2_referrer_id, $user_id, $amount, $site_settings, 2);
            
            // Process tier 3 referral
            $stmt = $conn->prepare("SELECT referrer_id FROM users WHERE id = ?");
            $stmt->bind_param('i', $tier2_referrer_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $tier2_data = $result->fetch_assoc();
            
            if ($tier2_data && $tier2_data['referrer_id']) {
                $tier3_referrer_id = $tier2_data['referrer_id'];
                processReferralTier($conn, $tier3_referrer_id, $user_id, $amount, $site_settings, 3);
            }
        }
        
    } catch (Exception $e) {
        logError("Referral processing error: " . $e->getMessage());
    }
}

// Process a single referral tier
function processReferralTier($conn, $referrer_id, $user_id, $amount, $site_settings, $tier) {
    logError("Processing tier " . $tier . " referral for referrer " . $referrer_id);
    
    // Get tier rate from settings
    $rate_key = 'referral_gametier' . $tier . '_rate';
    $default_rates = [1 => 0.02, 2 => 0.01, 3 => 0.005]; // Default rates
    $tier_rate = isset($site_settings[$rate_key]) ? 
                floatval($site_settings[$rate_key]) : $default_rates[$tier];
    
    // Calculate reward
    $reward = $amount * $tier_rate;
    
    // Only process if reward amount is significant
    if ($reward <= 0.001) {
        logError("Tier " . $tier . " reward too small (" . $reward . "), skipping");
        return;
    }
    
    logError("Tier " . $tier . " reward: " . $reward);
    
    // Add to referral earnings table
    $stmt = $conn->prepare("INSERT INTO referral_earnings (user_id, referred_user_id, amount, order_id, status, is_paid) 
                          VALUES (?, ?, ?, 0, 'pending', 0)");
    $stmt->bind_param('iid', $referrer_id, $user_id, $reward);
    
    if (!$stmt->execute()) {
        logError("Error inserting referral earning: " . $conn->error);
        return;
    }
    
    // Update referrer's balance
    $stmt = $conn->prepare("UPDATE users SET referral_balance = referral_balance + ? WHERE id = ?");
    $stmt->bind_param('di', $reward, $referrer_id);
    
    if (!$stmt->execute()) {
        logError("Error updating referrer balance: " . $conn->error);
        return;
    }
    
    // Get the username for the user_id
    $sql = "SELECT username FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $username = $user['username'];
    } else {
        $username = 'User #' . $user_id;
    }
    
    // Record transaction
    $description = "Level-" . $tier . " Referral Commission: " . $username . " game earnings";
    
    $stmt = $conn->prepare("INSERT INTO transactions (user_id, related_id, type, amount, status, description) 
                          VALUES (?, ?, 'referral', ?, 'completed', ?)");
    $stmt->bind_param('iids', $referrer_id, $user_id, $reward, $description);
    
    if (!$stmt->execute()) {
        logError("Error recording referral transaction: " . $conn->error);
        return;
    }
    
    logError("Tier " . $tier . " referral processed successfully");
}
?>