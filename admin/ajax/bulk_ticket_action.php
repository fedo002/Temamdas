<?php
/**
 * Toplu destek talebi işlemlerini gerçekleştiren AJAX endpoint
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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tickets']) && isset($_POST['action'])) {
    // Verileri al ve işle
    $tickets = json_decode($_POST['tickets'], true);
    $action = $_POST['action'];
    
    // Geçerli işlem kontrolü
    if (!in_array($action, ['close', 'delete'])) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Geçersiz işlem.']);
        exit;
    }
    
    // Talep ID'lerini doğrula
    if (!is_array($tickets) || empty($tickets)) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Geçersiz talep listesi.']);
        exit;
    }
    
    // Toplu işlemi gerçekleştir
    $result = bulkTicketAction($tickets, $action);
    
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