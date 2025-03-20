<?php
session_start();
require_once '../../includes/admin_userfunctions.php';

// Admin oturum kontrolü
if(!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

// POST verilerini kontrol et
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id']) && isset($_POST['status'])) {
    $user_id = (int)$_POST['user_id'];
    $status = $_POST['status'];
    
    // Kullanıcı durumunu güncelle
    $result = updateUserStatus($user_id, $status);
    
    if($result === true) {
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Kullanıcı durumu başarıyla güncellendi.'];
    } else {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Hata: ' . $result];
    }
    
    // Referer veya varsayılan sayfaya yönlendir
    $referer = $_SERVER['HTTP_REFERER'] ?? 'list.php';
    header('Location: ' . $referer);
    exit;
} else {
    // Geçersiz istek
    $_SESSION['message'] = ['type' => 'danger', 'text' => 'Geçersiz istek.'];
    header('Location: list.php');
    exit;
}