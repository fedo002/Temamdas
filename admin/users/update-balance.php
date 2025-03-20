<?php
session_start();
require_once '../../includes/admin_userfunctions.php';

// Admin oturum kontrolü
if(!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

// POST verilerini kontrol et
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id']) && isset($_POST['amount']) && isset($_POST['type'])) {
    $user_id = (int)$_POST['user_id'];
    $amount = (float)$_POST['amount'];
    $type = $_POST['type'];
    $note = $_POST['note'] ?? '';
    
    // Kullanıcı bakiyesini güncelle
    $result = updateUserBalance($user_id, $amount, $type, $note);
    
    if($result === true) {
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Kullanıcı bakiyesi başarıyla güncellendi.'];
    } else {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Hata: ' . $result];
    }
    
    // Referer veya varsayılan sayfaya yönlendir
    $referer = $_SERVER['HTTP_REFERER'] ?? "details.php?id=$user_id";
    header('Location: ' . $referer);
    exit;
} else {
    // Geçersiz istek
    $_SESSION['message'] = ['type' => 'danger', 'text' => 'Geçersiz istek.'];
    header('Location: list.php');
    exit;
}