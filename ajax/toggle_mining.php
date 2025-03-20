<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

// JSON yanıt formatı
header('Content-Type: application/json');

// Kullanıcı kontrolü
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Oturum açmanız gerekiyor.'
    ]);
    exit;
}

// Bağlantı değişkenini al
$conn = $GLOBALS['db']->getConnection();

// POST verilerini kontrol et
if (!isset($_POST['package_id']) || !isset($_POST['action'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Geçersiz istek.'
    ]);
    exit;
}

$package_id = (int)$_POST['package_id'];
$action = $_POST['action'];
$user_id = $_SESSION['user_id'];

// Aksiyon kontrolü
if ($action !== 'pause' && $action !== 'resume') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Geçersiz aksiyon.'
    ]);
    exit;
}

// Kullanıcıya ait paket kontrolü
$stmt = $conn->prepare("SELECT id, status FROM user_mining_packages WHERE id = ? AND user_id = ?");
if (!$stmt) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Veritabanı hatası.'
    ]);
    exit;
}

$stmt->bind_param("ii", $package_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Mining paketi bulunamadı veya size ait değil.'
    ]);
    exit;
}

$package = $result->fetch_assoc();

// Mevcut durumu kontrol et
if (($action === 'pause' && $package['status'] === 'paused') || 
    ($action === 'resume' && $package['status'] === 'active')) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Mining paketi zaten ' . ($action === 'pause' ? 'duraklatılmış' : 'aktif') . ' durumda.'
    ]);
    exit;
}

// Durumu güncelle
$new_status = ($action === 'pause') ? 'paused' : 'active';
$stmt = $conn->prepare("UPDATE user_mining_packages SET status = ? WHERE id = ? AND user_id = ?");

if (!$stmt) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Veritabanı hatası.'
    ]);
    exit;
}

$stmt->bind_param("sii", $new_status, $package_id, $user_id);
$result = $stmt->execute();

if ($result) {
    // İşlem logu
    $action_desc = ($action === 'pause') ? 'Mining paketi duraklatıldı' : 'Mining paketi devam ettirildi';
    logUserAction($user_id, 'mining_toggle', $action_desc, $package_id);
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Mining durumu başarıyla güncellendi.'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Durum güncellenirken bir hata oluştu.'
    ]);
}