<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Hata ayıklama
error_reporting(E_ALL);
ini_set('display_errors', 1);

// JSON yanıtı için header
header('Content-Type: application/json');

// Kullanıcı oturum kontrolü
if(!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Oturum bulunamadı']);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // Veritabanı bağlantısı
    $conn = dbConnect();
    
    // Kullanıcı ve VIP bilgilerini al
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    $vip_level = $user['vip_level'];
    
    // VIP detaylarını al
    $stmt = $conn->prepare("SELECT * FROM vip_packages WHERE id = ?");
    $stmt->bind_param('i', $vip_level);
    $stmt->execute();
    $result = $stmt->get_result();
    $vip_details = $result->fetch_assoc();
    
    if (!$vip_details) {
        // Varsayılan değerler
        $vip_details = [
            'daily_game_limit' => 5,
            'game_max_win_chance' => 0.15
        ];
    }
    
    // Günlük kalan deneme hakkını kontrol et
    $today = date('Y-m-d');
    $stmt = $conn->prepare("SELECT COUNT(*) as attempt_count FROM game_attempts WHERE user_id = ? AND DATE(created_at) = ?");
    $stmt->bind_param('is', $user_id, $today);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $daily_attempts = intval($row['attempt_count']);
    
    $max_attempts = $vip_details['daily_game_limit'];
    $remaining_attempts = $max_attempts - $daily_attempts;
    
    if($remaining_attempts <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Günlük deneme hakkınız doldu']);
        exit;
    }
    
    $stage = isset($_POST['stage']) ? intval($_POST['stage']) : 1;
    
    // Oyun ayarları - başlangıçta sabit değerler kullanacağız
    $stage1_base_reward = 5; // USDT
    $stage1_win_chance = $vip_details['game_max_win_chance']; // VIP seviyesine göre kazanma şansı
    
    $stage2_rewards = [
        'low' => 3 + ($vip_level * 0.5),
        'medium' => 7 + ($vip_level * 1),
        'high' => 10 + ($vip_level * 2)
    ];
    
    // İlk aşama
    if($stage == 1) {
        // Yeni bir oyun girişimi kaydı oluştur
        $stmt = $conn->prepare("INSERT INTO game_attempts (user_id) VALUES (?)");
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        
        // Kazanma şansını hesapla
        $win_threshold = $stage1_win_chance * 100;
        $random_number = mt_rand(1, 100);
        $win = ($random_number <= $win_threshold);
        
        if($win) {
            $response = [
                'status' => 'win',
                'reward' => $stage1_base_reward,
                'remaining_attempts' => $remaining_attempts - 1,
                'debug' => "Random: $random_number, Threshold: $win_threshold" // Debug bilgisi
            ];
        } else {
            $response = [
                'status' => 'lose',
                'remaining_attempts' => $remaining_attempts - 1,
                'debug' => "Random: $random_number, Threshold: $win_threshold" // Debug bilgisi
            ];
        }
    }
    elseif($stage == 2 && isset($_POST['action']) && $_POST['action'] == 'take') {
        $amount = floatval($_POST['amount']);
        
        // Kullanıcı bakiyesini güncelle
        $stmt = $conn->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
        $stmt->bind_param('di', $amount, $user_id);
        
        if (!$stmt->execute()) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Bakiye güncellenirken bir hata oluştu: ' . $conn->error
            ]);
            exit;
        }
        
        // İşlemi kaydet
        $stmt = $conn->prepare("INSERT INTO transactions (user_id, type, amount, description, status) VALUES (?, 'game', ?, 'Günlük ödül oyunu kazancı', 'completed')");
        $stmt->bind_param('id', $user_id, $amount);
        
        if (!$stmt->execute()) {
            echo json_encode([
                'status' => 'error',
                'message' => 'İşlem kaydedilirken bir hata oluştu: ' . $conn->error
            ]);
            exit;
        }
        
        $response = [
            'status' => 'success',
            'message' => 'Ödül başarıyla eklendi',
            'amount' => $amount
        ];
    }
    
    // Üçüncü aşama - Şansını dene
    elseif($stage == 3) {
        // Kartı seç
        $card = isset($_POST['card']) ? $_POST['card'] : null;
        
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
        
        // Kullanıcı bakiyesini güncelle
        $stmt = $conn->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
        $stmt->bind_param('di', $prize, $user_id);
        
        if (!$stmt->execute()) {
            throw new Exception("Bakiye güncellenirken hata: " . $conn->error);
        }
        
        // İşlemi kaydet
        $stmt = $conn->prepare("INSERT INTO transactions (user_id, type, amount, description, status) VALUES (?, 'game', ?, 'Günlük ödül oyunu bonus kazancı', 'completed')");
        $stmt->bind_param('id', $user_id, $prize);
        
        if (!$stmt->execute()) {
            throw new Exception("İşlem kaydedilirken hata: " . $conn->error);
        }
        
        // Başarılı yanıt
        $response = [
            'status' => 'success',
            'prize' => $prize,
            'prize_type' => $prize_type
        ];
    }
    
    echo json_encode($response);
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Bir hata oluştu: ' . $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}
?>