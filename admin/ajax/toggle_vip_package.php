<?php
session_start();
require_once '../../../includes/admin_userfunctions.php';
require_once '../../../includes/admin_functions.php';

// Admin oturum kontrolü
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Yetkisiz erişim']);
    exit;
}

// Ajax isteği kontrolü
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Geçersiz istek yöntemi']);
    exit;
}

// Parametreleri al
$package_id = isset($_POST['package_id']) ? (int)$_POST['package_id'] : 0;
$status = isset($_POST['status']) ? (int)$_POST['status'] : 0;

if ($package_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Geçersiz paket ID']);
    exit;
}

// Paketi getir
$package = getVipPackageById($package_id);

if (!$package) {
    echo json_encode(['status' => 'error', 'message' => 'Paket bulunamadı']);
    exit;
}

// Durum değişikliğini yap
global $db;
$conn = $db->getConnection();

$stmt = $conn->prepare("UPDATE vip_packages SET is_active = ?, updated_at = NOW() WHERE id = ?");

if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Veritabanı hatası: ' . $conn->error]);
    exit;
}

$stmt->bind_param("ii", $status, $package_id);
$result = $stmt->execute();

if ($result) {
    // Admin log kaydı
    addAdminLog(
        $_SESSION['admin_id'], 
        'toggle_vip_package_status', 
        "VIP Paketi durumu değiştirildi: {$package['name']} - " . ($status ? 'Aktif' : 'Pasif'), 
        $package_id, 
        'vip_package'
    );
    
    echo json_encode([
        'status' => 'success', 
        'message' => 'Paket durumu başarıyla değiştirildi',
        'new_status' => $status
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Durum güncellenirken bir hata oluştu: ' . $stmt->error]);
}

$stmt->close();