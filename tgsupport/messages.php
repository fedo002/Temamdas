<?php
// messages.php - Mesaj Geçmişi Yönetim Sınıfı

class MessageHistory {
    private $db;
    
    public function __construct() {
        // SQLite veritabanını oluştur/aç
        $db_file = __DIR__ . '/chat_messages.sqlite';
        $this->db = new SQLite3($db_file);
        
        // Tablo oluştur (eğer yoksa)
        $this->db->exec('
            CREATE TABLE IF NOT EXISTS messages (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                ticket_id TEXT NOT NULL,
                user_id TEXT NOT NULL,
                message TEXT NOT NULL,
                is_support INTEGER NOT NULL,
                timestamp INTEGER NOT NULL
            )
        ');
        
        // Performans için index oluştur
        $this->db->exec('CREATE INDEX IF NOT EXISTS idx_ticket_id ON messages (ticket_id)');
    }
    
    // Yeni mesaj ekle
    public function addMessage($ticket_id, $user_id, $message, $is_support = false) {
        $stmt = $this->db->prepare('
            INSERT INTO messages (ticket_id, user_id, message, is_support, timestamp)
            VALUES (:ticket_id, :user_id, :message, :is_support, :timestamp)
        ');
        
        $stmt->bindValue(':ticket_id', $ticket_id, SQLITE3_TEXT);
        $stmt->bindValue(':user_id', $user_id, SQLITE3_TEXT);
        $stmt->bindValue(':message', $message, SQLITE3_TEXT);
        $stmt->bindValue(':is_support', $is_support ? 1 : 0, SQLITE3_INTEGER);
        $stmt->bindValue(':timestamp', time(), SQLITE3_INTEGER);
        
        return $stmt->execute();
    }
    
    // Bir ticket için mesaj geçmişini al
    public function getMessages($ticket_id, $limit = 100) {
        $stmt = $this->db->prepare('
            SELECT * FROM messages
            WHERE ticket_id = :ticket_id
            ORDER BY timestamp ASC
            LIMIT :limit
        ');
        
        $stmt->bindValue(':ticket_id', $ticket_id, SQLITE3_TEXT);
        $stmt->bindValue(':limit', $limit, SQLITE3_INTEGER);
        
        $result = $stmt->execute();
        $messages = [];
        
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $messages[] = $row;
        }
        
        return $messages;
    }
    
    // Biletleri temizle - X günden eski mesajları sil (opsiyonel)
    public function cleanupOldMessages($days = 30) {
        $cutoff = time() - ($days * 86400); // 86400 saniye = 1 gün
        
        $stmt = $this->db->prepare('
            DELETE FROM messages
            WHERE timestamp < :cutoff
        ');
        
        $stmt->bindValue(':cutoff', $cutoff, SQLITE3_INTEGER);
        return $stmt->execute();
    }
    
    // Veri tabanını kapat
    public function close() {
        $this->db->close();
    }
}
?>