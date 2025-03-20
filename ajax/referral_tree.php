<?php
/**
 * Referans ağacı verilerini döndüren AJAX endpoint
 */

// Oturum ve yetki kontrolü
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Yetkiniz yok.']);
    exit;
}

// Admin fonksiyonlarını dahil et
require_once '../../includes/admin_functions.php';
require_once '../../includes/admin_userfunctions.php';

// Referans ağacı istatistiklerini al
try {
    $stats = getReferralTreeStats();
    
    // JSON olarak döndür
    header('Content-Type: application/json');
    echo json_encode($stats);
} catch (Exception $e) {
    // Hata durumunda
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Veri alınamadı: ' . $e->getMessage()]);
}