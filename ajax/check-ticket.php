<?php
/**
 * Ticket durumunu kontrol eden AJAX endpoint
 */
require_once '../includes/config.php';

// CORS ve JSON header ayarları
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ticket_id = isset($_POST['ticketId']) ? intval($_POST['ticketId']) : 0;
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    
    if (empty($ticket_id) || empty($email)) {
        echo json_encode(['success' => false, 'message' => 'Please provide both ticket ID and email.']);
        exit;
    }
    
    // Kullanıcıyı email ile bul
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'No ticket found with the provided information.']);
        exit;
    }
    
    $user = $result->fetch_assoc();
    $user_id = $user['id'];
    
    // Ticket bilgilerini getir
    $stmt = $conn->prepare("SELECT t.*, u.username, u.email 
                            FROM support_tickets t
                            JOIN users u ON t.user_id = u.id
                            WHERE t.id = ? AND t.user_id = ?");
    $stmt->bind_param("ii", $ticket_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'No ticket found with the provided information.']);
        exit;
    }
    
    $ticket = $result->fetch_assoc();
    
    // Konuşmaları getir
    $stmt = $conn->prepare("SELECT sm.*, 
                            CASE WHEN sm.is_user_message = 0 THEN 1 ELSE 0 END as is_admin
                            FROM support_messages sm
                            WHERE sm.ticket_id = ?
                            ORDER BY sm.created_at ASC");
    $stmt->bind_param("i", $ticket_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $conversations = [];
    while ($row = $result->fetch_assoc()) {
        $conversations[] = [
            'is_admin' => (bool)$row['is_admin'],
            'message' => htmlspecialchars($row['message']),
            'created_at' => date('d M Y, H:i', strtotime($row['created_at']))
        ];
    }
    
    // Yanıt formatı
    $response = [
        'success' => true,
        'ticket' => [
            'id' => $ticket['id'],
            'status' => ucfirst($ticket['status']),
            'subject' => htmlspecialchars($ticket['subject']),
            'category' => ucfirst($ticket['priority']), // Kategoriniz yoksa priority'yi kullanabilirsiniz
            'created_at' => date('d M Y, H:i', strtotime($ticket['created_at'])),
            'updated_at' => $ticket['last_updated'] 
                ? date('d M Y, H:i', strtotime($ticket['last_updated'])) 
                : date('d M Y, H:i', strtotime($ticket['created_at']))
        ],
        'conversations' => $conversations
    ];
    
    echo json_encode($response);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
