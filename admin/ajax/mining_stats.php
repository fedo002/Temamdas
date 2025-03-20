<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/admin_functions.php';

// JSON yanıt formatı
header('Content-Type: application/json');

// Admin oturum kontrolü
if (!isset($_SESSION['admin_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Yetkilendirme başarısız'
    ]);
    exit;
}

// Bağlantı değişkenini al
$conn = $GLOBALS['db']->getConnection();

// İstenilen veri tipini kontrol et
$type = $_GET['type'] ?? 'packages';

if ($type === 'packages') {
    // En çok kullanılan paketlerin istatistiklerini getir
    $result = $conn->query("
        SELECT mp.name, COUNT(ump.id) as count
        FROM user_mining_packages ump
        JOIN mining_packages mp ON ump.package_id = mp.id
        WHERE ump.status = 'active'
        GROUP BY ump.package_id
        ORDER BY count DESC
        LIMIT 8
    ");
    
    $names = [];
    $counts = [];
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $names[] = $row['name'];
            $counts[] = $row['count'];
        }
    } else {
        // Veri yoksa örnek veri ekle
        $names = ['Veri Bulunamadı'];
        $counts = [1];
    }
    
    echo json_encode([
        'status' => 'success',
        'names' => $names,
        'counts' => $counts
    ]);
    
} elseif ($type === 'earnings') {
    // Son 7 günlük kazanç verilerini getir
    $dates = [];
    $earnings = [];
    
    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $dates[] = date('d M', strtotime($date));
        
        $result = $conn->query("SELECT SUM(net_revenue) as total FROM mining_earnings WHERE date = '$date'");
        
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $earnings[] = $row['total'] ?: 0;
        } else {
            $earnings[] = 0;
        }
    }
    
    echo json_encode([
        'status' => 'success',
        'dates' => $dates,
        'earnings' => $earnings
    ]);
    
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Geçersiz veri tipi'
    ]);
}
?>