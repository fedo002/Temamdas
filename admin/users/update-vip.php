<?php
session_start();
require_once '../../includes/admin_userfunctions.php';

// Admin oturum kontrolü
if(!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

// POST verilerini kontrol et
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id']) && isset($_POST['vip_level'])) {
    $user_id = (int)$_POST['user_id'];
    $vip_level = (int)$_POST['vip_level'];
    $note = isset($_POST['note']) ? trim($_POST['note']) : '';
    $charge_user = isset($_POST['charge_user']) && $_POST['charge_user'] === 'on';
    
    // Kullanıcının VIP seviyesini güncelle
    $result = updateUserVipLevel($user_id, $vip_level, $note, $charge_user);
    
    if($result === true) {
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Kullanıcının VIP seviyesi başarıyla güncellendi.'];
    } else {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Hata: ' . $result];
    }
    
    // Referer veya varsayılan sayfaya yönlendir
    $referer = $_SERVER['HTTP_REFERER'] ?? 'vip-users.php';
    header('Location: ' . $referer);
    exit;
} else {
    // Geçersiz istek
    $_SESSION['message'] = ['type' => 'danger', 'text' => 'Geçersiz istek.'];
    header('Location: vip-users.php');
    exit;
}