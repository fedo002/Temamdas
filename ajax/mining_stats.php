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

// Son 30 günlük mining istatistiklerini al
$dates = [];
$earnings = [];
$costs = [];
$net_earnings = [];

// İstatistik verilerini hesapla
$query = "SELECT date, SUM(revenue) as total_revenue, SUM(electricity_cost) as total_cost, SUM(net_revenue) as total_net 
          FROM mining_earnings 
          WHERE user_id = ? 
          GROUP BY date 
          ORDER BY date ASC 
          LIMIT 30";

$stmt = $conn->prepare($query);
if (!$stmt) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Veritabanı hatası.'
    ]);
    exit;
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $dates[] = date('d M', strtotime($row['date']));
        $earnings[] = number_format($row['total_revenue'], 6, '.', '');
        $costs[] = number_format($row['total_cost'], 6, '.', '');
        $net_earnings[] = number_format($row['total_net'], 6, '.', '');
    }
} else {
    // Veri yoksa son 30 günü boş olarak ekle
    for ($i = 29; $i >= 0; $i--) {
        $date = date('d M', strtotime("-$i days"));
        $dates[] = $date;
        $earnings[] = "0";
        $costs[] = "0";
        $net_earnings[] = "0";
    }
}

echo json_encode([
    'status' => 'success',
    'dates' => $dates,
    'earnings' => $earnings,
    'costs' => $costs,
    'net_earnings' => $net_earnings
]);