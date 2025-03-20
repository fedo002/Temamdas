<?php
/**
 * Destek talebini silen AJAX endpoint
 */

// Oturum ve yetki kontrolü
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Yetkiniz yok.']);
    exit;
}

// Admin fonksiyonlarını dahil et
require_once '../../includes/admin_userfunctions.php';

// POST verilerini kontrol et
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ticket_id'])) {
    $ticket_id = (int)$_POST['ticket_id'];
    
    // Talebi sil
    $result = deleteTicket($ticket_id);
    
    if ($result === true) {
        // Başarılı yanıt
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success']);
    } else {
        // Hata yanıtı
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => $result]);
    }
} else {
    // Geçersiz istek
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Geçersiz istek.']);
}