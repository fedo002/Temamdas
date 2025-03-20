<?php
/**
 * Haftalık istatistikler için AJAX endpoint
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

// Haftalık istatistikleri al
$stats = getWeeklyStats();

// JSON olarak döndür
header('Content-Type: application/json');
echo json_encode($stats);