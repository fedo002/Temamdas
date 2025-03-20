<?php
// webhook.php - Telegram Webhook Handler
require_once 'config.php';
require_once 'messages.php';

// Log fonksiyonu
function webhookLog($message) {
    $logFile = __DIR__ . '/logs/webhook.log';
    $logDir = dirname($logFile);
    
    if (!file_exists($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message" . PHP_EOL;
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

// Gelen webhook'u işle
try {
    // Gelen JSON verisini al
    $update_json = file_get_contents('php://input');
    webhookLog("Webhook received: " . $update_json);
    
    $update = json_decode($update_json, true);
    
    // Mesaj kontrolü
    if (isset($update['message'])) {
        $chat_id = $update['message']['chat']['id'];
        $message_text = $update['message']['text'] ?? '[Medya içeriği]';
        
        // Veritabanı bağlantısı
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($conn->connect_error) {
            webhookLog("Database connection failed: " . $conn->connect_error);
            http_response_code(500);
            exit;
        }
        
        // Ticket ID'yi bul
        $stmt = $conn->prepare("SELECT ticket_id FROM active_sessions WHERE user_id = ? AND status IN ('waiting', 'connected')");
        $stmt->bind_param("s", $chat_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $ticket_id = $row['ticket_id'];
            webhookLog("Found ticket_id: {$ticket_id} for user {$chat_id}");
            
            // Mesaj geçmişine ekle
            $messages = new MessageHistory();
            $messages->addMessage($ticket_id, $chat_id, $message_text, false);
            $messages->close();
            
            // Son aktivite zamanını güncelle
            $update_stmt = $conn->prepare("UPDATE active_sessions SET last_message_time = NOW() WHERE ticket_id = ?");
            $update_stmt->bind_param("s", $ticket_id);
            $update_stmt->execute();
        } else {
            webhookLog("No active session found for user {$chat_id}");
        }
        
        $conn->close();
    }
    
    // Başarı yanıtı
    http_response_code(200);
    echo json_encode(['status' => 'success']);
} catch (Exception $e) {
    webhookLog("Error processing webhook: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>