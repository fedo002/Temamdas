<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Güvenlik kontrolleri
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Oturum açık değil'
    ]);
    exit;
}

$user_id = $_SESSION['user_id'];

// Veritabanı bağlantısını kontrol et
global $db;
$conn = $db->getConnection();

if (!$conn) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Veritabanı bağlantısı kurulamadı'
    ]);
    exit;
}

function selectRandomPrize($vip_level, $stage) {
    global $db;
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("SELECT prize1_amount, prize1_chance, prize2_amount, prize2_chance, prize3_amount, prize3_chance FROM game_rewards WHERE vip_level = ? AND stage = ?");
    $stmt->bind_param('ii', $vip_level, $stage);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $prizes = $result->fetch_assoc();
        
        $randomNumber = mt_rand(1, 100);
        
        if ($randomNumber <= $prizes['prize1_chance']) {
            return ['amount' => $prizes['prize1_amount'], 'chance' => $prizes['prize1_chance']];
        } elseif ($randomNumber <= ($prizes['prize1_chance'] + $prizes['prize2_chance'])) {
            return ['amount' => $prizes['prize2_amount'], 'chance' => $prizes['prize2_chance']];
        } else {
            return ['amount' => $prizes['prize3_amount'], 'chance' => $prizes['prize3_chance']];
        }
    }
    
    return ['amount' => 3.00, 'chance' => 50.00];
}

function playGameStage($stage, $user_id, $card = null, $action = null, $amount = 0) {
    global $db;
    $conn = $db->getConnection();

    if (!$conn) {
        return ['status' => 'error', 'message' => 'Veritabanı bağlantısı kurulamadı'];
    }

    $user = getUserDetails($user_id);
    $vip_level = $user['vip_level'];

    if ($stage == 2) {
        $prize = selectRandomPrize($vip_level, $stage);
        $win_amount = $prize['amount'];
    } else {
        $game_rewards = getGameRewards($vip_level, $stage);
        $win_chance = $game_rewards['win_chance'] / 100;
        $is_winner = (mt_rand(1, 100) / 100) <= $win_chance;
        $stake_amount = $game_rewards['reward_amount'];
        $win_multiplier = $is_winner ? mt_rand($game_rewards['min_multiplier'] * 100, $game_rewards['max_multiplier'] * 100) / 100 : 0;
        $win_amount = $stake_amount * $win_multiplier;
        $win_amount = min($win_amount, $game_rewards['max_prize']);
    }
    $stmt = $conn->prepare("INSERT INTO game_attempts (user_id, attempt_result, stake_amount, win_amount, stage, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    
    if ($stmt === false) {
        return ['status' => 'error', 'message' => 'Veritabanı sorgusu hazırlanamadı: ' . $conn->error];
    }
    
    $game_type = "stage$stage";
    $result = ($win_amount > 0) ? 'win' : 'lose';
    
    if (!$stmt->bind_param('issddddd', $user_id, $game_type, $result, $stake_amount, $win_amount, $stage, $win_chance)) {
        return ['status' => 'error', 'message' => 'Parametre bağlama hatası: ' . $stmt->error];
    }
    
    if (!$stmt->execute()) {
        return ['status' => 'error', 'message' => 'Sorgu çalıştırılamadı: ' . $stmt->error];
    }
    
    return [
        'status' => ($win_amount > 0) ? 'win' : 'lose',
        'result' => $result,
        'stake_amount' => $stake_amount,
        'win_amount' => $win_amount,
        'win_chance' => $win_chance * 100,
        'remaining_attempts' => getUserDailyAttempts($user_id)['remaining_attempts']
    ];
}

try {
    $stage = $_POST['stage'] ?? null;
    $card = $_POST['card'] ?? null;
    
    $result = playGameStage($stage, $user_id, $card);
    echo json_encode($result);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
