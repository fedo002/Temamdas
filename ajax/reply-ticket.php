<?php
/**
 * Ticket'a cevap ekleyen AJAX endpoint
 */
require_once '../includes/config.php';

// CORS ve JSON header ayarları
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ticket_id = isset($_POST['ticketId']) ? intval($_POST['ticketId']) : 0;
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    
    if (empty($ticket_id) || empty($email) || empty($message)) {
        echo json_encode(['success' => false, 'message' => 'Please provide ticket ID, email and message.']);
        exit;
    }
    
    // Kullanıcıyı email ile bul
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
        exit;
    }
    
    $user = $result->fetch_assoc();
    $user_id = $user['id'];
    
    // Ticket'ın varlığını ve kullanıcıya ait olduğunu kontrol et
    $stmt = $conn->prepare("SELECT id, status FROM support_tickets WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $ticket_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Ticket not found or does not belong to you.']);
        exit;
    }
    
    $ticket = $result->fetch_assoc();
    
    // Ticket kapalı mı kontrol et
    if ($ticket['status'] === 'closed') {
        echo json_encode(['success' => false, 'message' => 'This ticket is closed. Please open a new ticket.']);
        exit;
    }
    
    try {
        // Transaction başlat
        $conn->begin_transaction();
        
        // Mesajı ekle
        $stmt = $conn->prepare("INSERT INTO support_messages (ticket_id, user_id, message, is_user_message, created_at) VALUES (?, ?, ?, 1, NOW())");
        $stmt->bind_param("iis", $ticket_id, $user_id, $message);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to add message.");
        }
        
        // Ticket durumunu güncelle
        $stmt = $conn->prepare("UPDATE support_tickets SET status = 'in_progress', last_updated = NOW() WHERE id = ?");
        $stmt->bind_param("i", $ticket_id);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to update ticket status.");
        }
        
        // Transaction'ı commit et
        $conn->commit();
        
        // Başarılı yanıt
        echo json_encode(['success' => true]);
        
    } catch (Exception $e) {
        // Hata durumunda rollback
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}