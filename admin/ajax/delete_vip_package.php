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

// Paketin kullanılıp kullanılmadığını kontrol et
global $db;
$conn = $db->getConnection();

$stmt = $conn->prepare("SELECT COUNT(*) as count FROM user_vip_packages WHERE package_id = ?");

if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Veritabanı hatası: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $package_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

// Eğer paket kullanılmışsa silme
if ($row['count'] > 0) {
    echo json_encode([
        'status' => 'error', 
        'message' => 'Bu paket kullanıcılar tarafından satın alınmıştır ve silinemez. Bunun yerine paketi devre dışı bırakabilirsiniz.'
    ]);
    exit;
}

// Paketi sil
$stmt = $conn->prepare("DELETE FROM vip_packages WHERE id = ?");

if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Veritabanı hatası: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $package_id);
$result = $stmt->execute();

if ($result) {
    // Admin log kaydı
    addAdminLog(
        $_SESSION['admin_id'], 
        'delete_vip_package', 
        "VIP Paketi silindi: {$package['name']}", 
        $package_id, 
        'vip_package'
    );
    
    echo json_encode([
        'status' => 'success', 
        'message' => 'Paket başarıyla silindi'
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Paket silinirken bir hata oluştu: ' . $stmt->error]);
}

$stmt->close();