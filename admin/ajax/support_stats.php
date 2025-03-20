<?php
/**
 * Destek istatistiklerini döndüren AJAX endpoint
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

// İstatistik tipini al
$type = $_GET['type'] ?? '';

try {
    // Destek istatistiklerini al
    $stats = getSupportStats();
    
    // İstek tipine göre veri döndür
    if ($type === 'distribution') {
        // Talep dağılımı verileri
        $result = [
            'open' => (int)$stats['distribution']['open'],
            'in_progress' => (int)$stats['distribution']['in_progress'],
            'closed' => (int)$stats['distribution']['closed']
        ];
    } elseif ($type === 'response_time') {
        // Yanıt süresi verileri
        $result = [
            'dates' => $stats['dates'],
            'response_times' => $stats['response_times']
        ];
    } else {
        // Tüm istatistikleri döndür
        $result = $stats;
    }
    
    // JSON olarak döndür
    header('Content-Type: application/json');
    echo json_encode($result);
    
} catch (Exception $e) {
    // Hata durumunda
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Veri alınamadı: ' . $e->getMessage()]);
}