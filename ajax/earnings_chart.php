<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

// JSON yanıt formatı
header('Content-Type: application/json');

// Kullanıcı kontrolü
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Oturum açmanız gerekiyor.'
    ]);
    exit;
}

// Bağlantı değişkenini al
$conn = $GLOBALS['db']->getConnection();
$user_id = $_SESSION['user_id'];

// Periyodu kontrol et (varsayılan olarak haftalık)
$period = isset($_GET['period']) ? $_GET['period'] : 'weekly';

// Tarih aralığını belirle
$days = 7; // Varsayılan olarak haftalık (7 gün)
if ($period === 'monthly') {
    $days = 30; // Aylık için 30 gün
}

// Sonuç verilerini tutacak diziler
$dates = [];
$earnings = [];
$total_earnings = [];

// Tarih aralığı için SQL
$start_date = date('Y-m-d', strtotime("-$days days"));
$end_date = date('Y-m-d');

// Mining kazançlarını sorgula
$query = "SELECT date, SUM(net_revenue) as daily_earnings 
          FROM mining_earnings 
          WHERE user_id = ? AND date BETWEEN ? AND ? 
          GROUP BY date 
          ORDER BY date ASC";

$stmt = $conn->prepare($query);
if (!$stmt) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Veritabanı sorgusu hazırlanamadı.'
    ]);
    exit;
}

$stmt->bind_param("iss", $user_id, $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();

// Sonuçları işle
$mining_data = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $mining_data[$row['date']] = [
            'earnings' => (float)$row['daily_earnings']
        ];
    }
}

// Diğer kazançları sorgula (oyun, referans, bonus vs.)
$query = "SELECT DATE(created_at) as date, SUM(CASE WHEN amount > 0 THEN amount ELSE 0 END) as other_earnings 
          FROM transactions 
          WHERE user_id = ? AND status = 'completed' 
          AND type IN ('game', 'referral', 'bonus', 'referral_transfer', 'miningdeposit') 
          AND DATE(created_at) BETWEEN ? AND ? 
          GROUP BY DATE(created_at) 
          ORDER BY DATE(created_at) ASC";

$stmt = $conn->prepare($query);
if (!$stmt) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Veritabanı sorgusu hazırlanamadı.'
    ]);
    exit;
}

$stmt->bind_param("iss", $user_id, $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();

// Diğer kazançları da ekle
$other_data = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $other_data[$row['date']] = [
            'earnings' => (float)$row['other_earnings']
        ];
    }
}

// Tarih aralığındaki her gün için veri oluştur
$current_date = new DateTime($start_date);
$end = new DateTime($end_date);
$end->modify('+1 day'); // Bitiş tarihini de dahil etmek için

// Toplam veriler için değişken
$total_mining_earnings = 0;

while ($current_date < $end) {
    $current_date_str = $current_date->format('Y-m-d');
    
    // Tarih formatını ayarla
    if ($period === 'weekly') {
        $display_date = $current_date->format('d M');
    } else {
        $display_date = $current_date->format('d M');
    }
    
    // Mining kazançları
    $mining_earning = isset($mining_data[$current_date_str]) ? $mining_data[$current_date_str]['earnings'] : 0;
    
    // Diğer kazançlar
    $other_earning = isset($other_data[$current_date_str]) ? $other_data[$current_date_str]['earnings'] : 0;
    
    // Toplam kazanç
    $total_earning = $mining_earning + $other_earning;
    
    // Toplam mining kazancını güncelle
    $total_mining_earnings += $mining_earning;
    
    // Dizilere ekle
    $dates[] = $display_date;
    $earnings[] = number_format($mining_earning, 6, '.', '');
    $total_earnings[] = number_format($total_earning, 6, '.', '');
    
    // Bir sonraki güne geç
    $current_date->modify('+1 day');
}

// Özet istatistikleri hesapla
$avg_daily_mining = $days > 0 ? $total_mining_earnings / $days : 0;
$estimated_monthly = $avg_daily_mining * 30;
$estimated_yearly = $avg_daily_mining * 365;

// Yanıtı oluştur
echo json_encode([
    'status' => 'success',
    'dates' => $dates,
    'earnings' => $earnings,
    'total_earnings' => $total_earnings,
    'summary' => [
        'period' => $period,
        'days' => $days,
        'total_mining_earnings' => number_format($total_mining_earnings, 6, '.', ''),
        'avg_daily_mining' => number_format($avg_daily_mining, 6, '.', ''),
        'estimated_monthly' => number_format($estimated_monthly, 6, '.', ''),
        'estimated_yearly' => number_format($estimated_yearly, 6, '.', '')
    ]
]);