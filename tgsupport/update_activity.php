<?php
// Temsilcinin son aktivitesini günceller
session_start();
require_once 'config.php';

// Giriş kontrolü
if (!isset($_SESSION['support_rep_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$rep_id = $_SESSION['support_rep_id'];

// Veritabanı bağlantısı
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'DB connection error']);
    exit;
}

try {
    // Son aktiviteyi güncelle
    $update = $conn->prepare("UPDATE support_reps SET last_activity = NOW() WHERE id = ?");
    $update->bind_param("i", $rep_id);
    $result = $update->execute();
    
    echo json_encode(['success' => $result]);
} catch (Exception $e) {
    logError("Update Activity Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error updating activity']);
} finally {
    $conn->close();
}
?>