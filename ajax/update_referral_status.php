<?php
/**
 * Referans kazancı durumunu güncelleyen AJAX endpoint
 */

// Oturum ve yetki kontrolü
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Yetkiniz yok.']);
    exit;
}

// Admin fonksiyonlarını dahil et
require_once '../../includes/admin_functions.php';
require_once '../../includes/admin_userfunctions.php';

// JSON verisini al
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id']) || !isset($data['status'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Geçersiz parametreler.']);
    exit;
}

$id = intval($data['id']);
$status = $data['status'];

// Veritabanı bağlantısı
$conn = $GLOBALS['db']->getConnection();

try {
    // İşlemi başlat
    $conn->begin_transaction();
    
    // Önce mevcut kaydı al
    $stmt = $conn->prepare("SELECT * FROM referral_earnings WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Kayıt bulunamadı.");
    }
    
    $referral = $result->fetch_assoc();
    
    // Durum güncellemesi
    $stmt = $conn->prepare("UPDATE referral_earnings SET status = ?, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    
    if (!$stmt->execute()) {
        throw new Exception("Durum güncellenirken bir hata oluştu.");
    }
    
    // Eğer durum "paid" olarak güncellendiyse is_paid alanını güncelle
    if ($status === 'paid') {
        $stmt = $conn->prepare("UPDATE referral_earnings SET is_paid = 1 WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if (!$stmt->execute()) {
            throw new Exception("Ödeme durumu güncellenirken bir hata oluştu.");
        }
    }
    
    // Admin log tablosuna kaydet
    $admin_id = $_SESSION['admin_id'];
    $ip = $_SERVER['REMOTE_ADDR'];
    $description = "Referans kazancı #$id durumu '$status' olarak güncellendi.";
    
    $stmt = $conn->prepare("INSERT INTO admin_logs (admin_id, action, description, related_id, related_type, ip_address) VALUES (?, 'update_referral_status', ?, ?, 'referral_earning', ?)");
    $stmt->bind_param("isis", $admin_id, $description, $id, $ip);
    $stmt->execute();
    
    // İşlemi tamamla
    $conn->commit();
    
    // Başarılı yanıt döndür
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    
} catch (Exception $e) {
    // Hata durumunda geri al
    $conn->rollback();
    
    // Hata yanıtı döndür
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}