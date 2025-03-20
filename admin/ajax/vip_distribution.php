<?php
/**
 * VIP seviye dağılımı verilerini döndüren AJAX endpoint
 */

// Oturum ve yetki kontrolü
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Yetkiniz yok.']);
    exit;
}

// Admin fonksiyonlarını dahil et
require_once '../../includes/admin_userfunctions.php';
require_once '../../includes/admin_functions.php';

// VIP seviye dağılımını al
try {
    $conn = $GLOBALS['db']->getConnection();
    
    // Tüm VIP paketlerini al
    $vip_packages = getVipPackages();
    
    // Her paket için kullanıcı sayısı
    $vip_distribution = [];
    $labels = [];
    $data = [];
    
    foreach ($vip_packages as $package) {
        $package_id = $package['id'];
        $package_name = $package['name'];
        
        // Paket ID'sine sahip kullanıcı sayısını sor
        $query = "SELECT COUNT(*) as count FROM users WHERE vip_level = $package_id";
        $result = $conn->query($query);
        $count = $result->fetch_assoc()['count'];
        
        $labels[] = $package_name;
        $data[] = $count;
    }
    
    // JSON olarak döndür
    header('Content-Type: application/json');
    echo json_encode([
        'labels' => $labels,
        'data' => $data
    ]);
    
} catch (Exception $e) {
    // Hata durumunda
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Veri alınamadı: ' . $e->getMessage()]);
}