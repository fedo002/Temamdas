<?php
session_start();
require_once '../../../includes/admin_userfunctions.php';
require_once '../../../includes/admin_functions.php';

// Admin oturum kontrolü
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Yetkisiz erişim']);
    exit;
}

// Ajax isteği kontrolü
header('Content-Type: application/json');

// İstek tipini kontrol et
$type = isset($_GET['type']) ? $_GET['type'] : '';

// Bağlantı değişkenini al
global $db;
$conn = $db->getConnection();

switch ($type) {
    case 'distribution':
        // VIP paket dağılımı istatistikleri
        getVipDistribution();
        break;
        
    case 'sales':
        // VIP satış istatistikleri
        getVipSalesStats();
        break;
        
    default:
        echo json_encode(['status' => 'error', 'message' => 'Geçersiz istek tipi']);
}

/**
 * VIP paket dağılımını hesapla
 */
function getVipDistribution() {
    global $conn;
    
    // Tüm aktif paketleri getir
    $stmt = $conn->prepare("
        SELECT id, name 
        FROM vip_packages 
        WHERE is_active = 1
        ORDER BY price ASC
    ");
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $packages = [];
    while ($row = $result->fetch_assoc()) {
        $packages[$row['id']] = $row['name'];
    }
    $stmt->close();
    
    // Her paketi satın alan aktif kullanıcı sayısını hesapla
    $package_counts = [];
    $package_names = [];
    
    foreach ($packages as $id => $name) {
        $stmt = $conn->prepare("
            SELECT COUNT(*) as count 
            FROM user_vip_packages 
            WHERE package_id = ? AND is_active = 1 AND end_date > NOW()
        ");
        
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        $package_counts[] = (int)$row['count'];
        $package_names[] = $name;
        
        $stmt->close();
    }
    
    // VIP olmayan kullanıcı sayısını ekleyelim
    $stmt = $conn->prepare("
        SELECT COUNT(*) as count 
        FROM users 
        WHERE (vip_level = 0 OR vip_level IS NULL OR vip_expires IS NULL OR vip_expires < NOW())
    ");
    
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    $package_counts[] = (int)$row['count'];
    $package_names[] = 'VIP Değil';
    
    $stmt->close();
    
    // Sonuçları döndür
    echo json_encode([
        'status' => 'success',
        'package_names' => $package_names,
        'user_counts' => $package_counts
    ]);
}

/**
 * Aylık VIP satış istatistiklerini getir
 */
function getVipSalesStats() {
    global $conn;
    
    // Son 6 aylık istatistikleri getir
    $months = [];
    $sales_count = [];
    $sales_amount = [];
    
    for ($i = 0; $i < 6; $i++) {
        $month = date('Y-m', strtotime("-$i month"));
        $start_date = $month . '-01 00:00:00';
        $end_date = date('Y-m-t 23:59:59', strtotime($start_date));
        
        $months[] = date('M Y', strtotime($start_date));
        
        // Satış sayısı
        $stmt = $conn->prepare("
            SELECT COUNT(*) as count 
            FROM user_vip_packages 
            WHERE created_at BETWEEN ? AND ?
        ");
        
        $stmt->bind_param("ss", $start_date, $end_date);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        $sales_count[] = (int)$row['count'];
        $stmt->close();
        
        // Satış tutarı
        $stmt = $conn->prepare("
            SELECT SUM(price) as total 
            FROM user_vip_packages 
            WHERE created_at BETWEEN ? AND ?
        ");
        
        $stmt->bind_param("ss", $start_date, $end_date);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        $sales_amount[] = round($row['total'] ?? 0, 2);
        $stmt->close();
    }
    
    // Ayları ters çevir (en yeni ay en sona gelsin)
    $months = array_reverse($months);
    $sales_count = array_reverse($sales_count);
    $sales_amount = array_reverse($sales_amount);
    
    // Sonuçları döndür
    echo json_encode([
        'status' => 'success',
        'months' => $months,
        'sales_count' => $sales_count,
        'sales_amount' => $sales_amount
    ]);
}