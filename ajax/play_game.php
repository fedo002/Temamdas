<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Kullanıcı oturum kontrolü
if(!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Oturum bulunamadı']);
    exit;
}

$user_id = $_SESSION['user_id'];
$user = getUserDetails($user_id);
$vip_level = $user['vip_level'];
$vip_details = getVipDetails($vip_level);

// Oyun ayarlarını veritabanından al
$game_settings = getGameSettings();

// VIP seviyesine göre ödül ve şans değerlerini hesapla
$vip_bonus_multiplier = floatval($game_settings['vip_bonus_multiplier']);
$stage1_base_reward = floatval($game_settings['stage1_base_reward']); 
$stage1_win_chance = $vip_details['game_max_win_chance']; 

// VIP seviyesine göre ödülleri hesapla
$stage2_rewards = [
    'low' => floatval($game_settings['stage2_low_reward']) + ($vip_level * $vip_bonus_multiplier),
    'medium' => floatval($game_settings['stage2_medium_reward']) + ($vip_level * $vip_bonus_multiplier * 2),
    'high' => floatval($game_settings['stage2_high_reward']) + ($vip_level * $vip_bonus_multiplier * 4)
];

// VIP seviyesine göre şansları hesapla
$vip_chance_adjustment = $vip_level * 0.05;
$stage2_chances = [
    'low' => max(0.1, floatval($game_settings['stage2_low_chance']) - $vip_chance_adjustment),
    'medium' => floatval($game_settings['stage2_medium_chance']),
    'high' => min(0.9, floatval($game_settings['stage2_high_chance']) + $vip_chance_adjustment)
];

// Şansların toplamını 1.0'a normalize et
$total_chance = $stage2_chances['low'] + $stage2_chances['medium'] + $stage2_chances['high'];
if (abs($total_chance - 1.0) > 0.01) {
    $stage2_chances['low'] /= $total_chance;
    $stage2_chances['medium'] /= $total_chance;
    $stage2_chances['high'] /= $total_chance;
}

$stage = isset($_POST['stage']) ? intval($_POST['stage']) : 1;
$response = [];

// Günlük kalan deneme hakkını kontrol et
$daily_attempts = getUserDailyAttempts($user_id);
$max_attempts = $vip_details['daily_game_limit'];
$remaining_attempts = $max_attempts - intval($daily_attempts);

if($remaining_attempts <= 0) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Günlük deneme hakkınız doldu']);
    exit;
}

// İlk aşama
if($stage == 1) {
    // Kart ID'si
    $card_id = isset($_POST['card']) ? $_POST['card'] : null;
    
    // Yeni bir oyun girişimi kaydı oluştur
    addGameAttempt($user_id);
    
    // Kazanma şansını hesapla
    $win = (mt_rand(1, 100) <= ($stage1_win_chance * 100));
    
    if($win) {
        $response = [
            'status' => 'win',
            'reward' => $stage1_base_reward,
            'remaining_attempts' => $remaining_attempts - 1
        ];
    } else {
        $response = [
            'status' => 'lose',
            'remaining_attempts' => $remaining_attempts - 1
        ];
    }
}
// İkinci aşama - ödülü alma
elseif($stage == 2 && isset($_POST['action']) && $_POST['action'] == 'take') {
    $amount = floatval($_POST['amount']);
    
    // Ödülü kullanıcıya ekle
    addRewardToUser($user_id, $amount);
    
    $response = [
        'status' => 'success',
        'message' => 'Ödül başarıyla eklendi'
    ];
}
// Üçüncü aşama - Şansını dene
elseif($stage == 3) {
    // Seçilen kart
    $card_id = isset($_POST['card']) ? $_POST['card'] : null;
    
    // Kazanılan ödülü belirle
    $rand = mt_rand(1, 100);
    $low_chance = $stage2_chances['low'] * 100;
    $medium_chance = $stage2_chances['medium'] * 100;
    
    if($rand <= $low_chance) {
        $prize = $stage2_rewards['low'];
    } elseif($rand <= $low_chance + $medium_chance) {
        $prize = $stage2_rewards['medium'];
    } else {
        $prize = $stage2_rewards['high'];
    }
    
    // Ödülü kullanıcıya ekle
    addRewardToUser($user_id, $prize);
    
    $response = [
        'status' => 'success',
        'prize' => $prize
    ];
}

// JSON yanıtı döndür
header('Content-Type: application/json');
echo json_encode($response);
exit;

// Yardımcı fonksiyonlar
function addGameAttempt($user_id) {
    $conn = dbConnect();
    $stmt = $conn->prepare("INSERT INTO game_attempts (user_id) VALUES (?)");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
}

function addRewardToUser($user_id, $amount) {
    $conn = dbConnect();
    
    // Kullanıcının mevcut bakiyesini al
    $stmt = $conn->prepare("SELECT balance FROM users WHERE id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    // Bakiyeyi güncelle
    $new_balance = $user['balance'] + $amount;
    $stmt = $conn->prepare("UPDATE users SET balance = ? WHERE id = ?");
    $stmt->bind_param('di', $new_balance, $user_id);
    $stmt->execute();
    
    // Oyun sonucunu kaydet
    $stmt = $conn->prepare("INSERT INTO transactions (user_id, type, amount, description, status) VALUES (?, 'game', ?, 'Günlük ödül oyunu kazancı', 'completed')");
    $stmt->bind_param('id', $user_id, $amount);
    $stmt->execute();
}

// Kullanıcının günlük oyun girişimlerini sayan fonksiyon
function getUserDailyAttempts($user_id) {
    $conn = dbConnect();
    
    $today = date('Y-m-d');
    $stmt = $conn->prepare("SELECT COUNT(*) as attempt_count FROM game_attempts WHERE user_id = ? AND DATE(created_at) = ?");
    $stmt->bind_param('is', $user_id, $today);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return intval($row['attempt_count']);
}
?>
