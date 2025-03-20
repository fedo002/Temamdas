<?php
// Destek temsilcisi kontrol paneli
session_start();
require_once 'config.php';
require_once 'messages.php';

// Giriş kontrolü
if (!isset($_SESSION['support_rep_id'])) {
    header("Location: index.php");
    exit;
}

$rep_id = $_SESSION['support_rep_id'];
$rep_username = $_SESSION['support_rep_username'];

// Veritabanı bağlantısı
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

// Mesaj geçmişi sınıfını başlat
$messageHistory = new MessageHistory();

// Telegram kullanıcı bilgilerini çekme fonksiyonu
function getTelegramUserInfo($user_id) {
    $bot_token = BOT_TOKEN; // config.php'den
    $url = "https://api.telegram.org/bot{$bot_token}/getChat?chat_id={$user_id}";
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    
    $data = json_decode($response, true);
    
    if (isset($data['ok']) && $data['ok'] === true && isset($data['result'])) {
        return $data['result'];
    }
    
    return null;
}

try {
    // Son aktiviteyi güncelle
    $update = $conn->prepare("UPDATE support_reps SET last_activity = NOW() WHERE id = ?");
    $update->bind_param("i", $rep_id);
    $update->execute();

    // Çıkış yapma işlemi
    if (isset($_GET['logout'])) {
        // Çevrimdışı durumuna ayarla
        $offline = $conn->prepare("UPDATE support_reps SET is_online = 0 WHERE id = ?");
        $offline->bind_param("i", $rep_id);
        $offline->execute();
        
        // Oturumu sonlandır
        session_unset();
        session_destroy();
        header("Location: index.php");
        exit;
    }

    // Aktif chat sayısını al
    $stmt = $conn->prepare("SELECT COUNT(*) as active_chats FROM active_sessions WHERE support_rep_id = ? AND status = 'connected'");
    $stmt->bind_param("i", $rep_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $chats_data = $result->fetch_assoc();
    $active_chats = $chats_data['active_chats'] ?? 0;

    // Bekleyen chat isteklerini al
    $stmt = $conn->prepare("SELECT * FROM active_sessions WHERE status = 'waiting' ORDER BY start_time ASC");
    $stmt->execute();
    $waiting_chats = $stmt->get_result();
    $waiting_count = $waiting_chats->num_rows;

    // Aktif chatları al
    $stmt = $conn->prepare("SELECT s.* FROM active_sessions s 
                           WHERE s.support_rep_id = ? AND s.status = 'connected'");
    $stmt->bind_param("i", $rep_id);
    $stmt->execute();
    $active_sessions = $stmt->get_result();

    // Chat isteğini kabul etme
    if (isset($_POST['accept_chat']) && isset($_POST['ticket_id'])) {
        $ticket_id = $_POST['ticket_id'];
        
        // Chatı kabul et
        $accept = $conn->prepare("UPDATE active_sessions SET status = 'connected', support_rep_id = ? WHERE ticket_id = ?");
        $accept->bind_param("is", $rep_id, $ticket_id);
        $accept->execute();
        
        // Sayfayı yeniden yükle
        header("Location: dashboard.php");
        exit;
    }

    // Mesaj gönderme
    if (isset($_POST['send_message']) && isset($_POST['ticket_id']) && isset($_POST['message'])) {
        $ticket_id = $_POST['ticket_id'];
        $message = $_POST['message'];
        
        // Kullanıcı ID'sini al
        $user_query = $conn->prepare("SELECT user_id FROM active_sessions WHERE ticket_id = ?");
        $user_query->bind_param("s", $ticket_id);
        $user_query->execute();
        $user_result = $user_query->get_result();
        $user_data = $user_result->fetch_assoc();
        
        if ($user_data) {
            $bot_token = BOT_TOKEN; // config.php'den alınır
            $chat_id = $user_data['user_id'];
            $text = "Support Representative: " . $message;
            
            // Telegram API'sine istek gönder
            $telegram_api_url = "https://api.telegram.org/bot$bot_token/sendMessage";
            $params = [
                'chat_id' => $chat_id,
                'text' => $text
            ];
            
            // cURL isteği
            $ch = curl_init($telegram_api_url);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $result = curl_exec($ch);
            
            // Hata kontrol
            if ($result === false) {
                $error_message = "Mesaj gönderilemedi: " . curl_error($ch);
                logError("Telegram API Error: " . curl_error($ch));
            } else {
                $response = json_decode($result, true);
                if (isset($response['ok']) && $response['ok'] === true) {
                    // Başarılı
                    $success_message = "Mesaj gönderildi!";
                    
                    // Mesajı SQLite'a ekle
                    $messageHistory->addMessage($ticket_id, $chat_id, $message, true);
                    
                    // Oturum son mesaj zamanını güncelle
                    $update_time = $conn->prepare("UPDATE active_sessions SET last_message_time = NOW() WHERE ticket_id = ?");
                    $update_time->bind_param("s", $ticket_id);
                    $update_time->execute();
                    
                    // Sayfa yenilemesi için yönlendirme
                    header("Location: dashboard.php");
                    exit;
                } else {
                    $error_message = "Telegram hatası: " . ($response['description'] ?? 'Bilinmeyen hata');
                    logError("Telegram API Error: " . json_encode($response));
                }
            }
            
            curl_close($ch);
        } else {
            $error_message = "Oturum bulunamadı!";
        }
    }
    
    // Chat'i kapat
    if (isset($_POST['close_chat']) && isset($_POST['ticket_id'])) {
        $ticket_id = $_POST['ticket_id'];
        
        // Kullanıcıya bildirim gönder
        $user_query = $conn->prepare("SELECT user_id FROM active_sessions WHERE ticket_id = ?");
        $user_query->bind_param("s", $ticket_id);
        $user_query->execute();
        $user_result = $user_query->get_result();
        $user_data = $user_result->fetch_assoc();
        
        if ($user_data) {
            $bot_token = BOT_TOKEN; // config.php'den
            $chat_id = $user_data['user_id'];
            $text = "This support session has been closed. If you need further assistance, please start a new chat with the bot.";
            
            // Telegram API'sine istek gönder
            $telegram_api_url = "https://api.telegram.org/bot$bot_token/sendMessage";
            $params = [
                'chat_id' => $chat_id,
                'text' => $text
            ];
            
            // cURL isteği
            $ch = curl_init($telegram_api_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_exec($ch);
            curl_close($ch);
            
            // Kapanış mesajını geçmişe ekle
            $messageHistory->addMessage($ticket_id, $chat_id, "Support session closed", true);
        }
        
        // Oturumu kapat
        $close = $conn->prepare("UPDATE active_sessions SET status = 'closed' WHERE ticket_id = ?");
        $close->bind_param("s", $ticket_id);
        $close->execute();
        
        $success_message = "Chat başarıyla kapatıldı.";
        
        // Sayfayı yeniden yükle
        header("Location: dashboard.php");
        exit;
    }
} catch (Exception $e) {
    logError("Dashboard Error: " . $e->getMessage());
    $error_message = "Bir hata oluştu: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Destek Temsilcisi Paneli</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .chat-container {
            height: 400px;
            overflow-y: auto;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .message {
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 5px;
        }
        .user-message {
            background-color: #e9ecef;
            margin-right: 20%;
        }
        .support-message {
            background-color: #cfe2ff;
            margin-left: 20%;
        }
        .status-badge {
            font-size: 0.8rem;
        }
        .refresh-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }
    </style>
    <script>
        // Her 30 saniyede bir aktiviteyi güncelle
        setInterval(function() {
            $.get('update.php'); 
        }, 30000);
        
        // Her 20 saniyede bir sayfayı yenile (chat güncellemeleri için)
        setInterval(function() {
            location.reload();
        }, 20000);
    </script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">Telegram Destek Paneli</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="nav-link text-white">Merhaba, <?php echo htmlspecialchars($rep_username); ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="?logout=1"><i class="bi bi-box-arrow-right"></i> Çıkış Yap</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Durum Bilgisi</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 text-center">
                                <h6>Durum</h6>
                                <span class="badge bg-success">Çevrimiçi</span>
                            </div>
                            <div class="col-md-4 text-center">
                                <h6>Aktif Chatler</h6>
                                <span class="badge bg-primary"><?php echo $active_chats; ?></span>
                            </div>
                            <div class="col-md-4 text-center">
                                <h6>Bekleyen İstekler</h6>
                                <span class="badge bg-warning text-dark"><?php echo $waiting_count; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Bekleyen İstekler</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($waiting_count > 0): ?>
                            <ul class="list-group">
                                <?php while ($chat = $waiting_chats->fetch_assoc()): 
                                    // Telegram kullanıcı bilgilerini çek
                                    $user_info = getTelegramUserInfo($chat['user_id']);
                                    $username = isset($user_info['username']) ? '@' . $user_info['username'] : '';
                                    $first_name = $user_info['first_name'] ?? '';
                                    $last_name = $user_info['last_name'] ?? '';
                                    $full_name = trim("$first_name $last_name");
                                    
                                    // Eğer kullanıcı bilgisi bulunamazsa, sadece ID'yi kullan
                                    $display_name = !empty($full_name) ? $full_name : 'Kullanıcı';
                                    $display_username = !empty($username) ? " ($username)" : '';
                                ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>Ticket ID:</strong> <?php echo htmlspecialchars($chat['ticket_id']); ?><br>
                                            <strong>Kullanıcı:</strong> <?php echo htmlspecialchars($display_name . $display_username); ?><br>
                                            <small>Başlangıç: <?php echo date('H:i:s d/m/Y', strtotime($chat['start_time'])); ?></small>
                                        </div>
                                        <form method="post" action="">
                                            <input type="hidden" name="ticket_id" value="<?php echo htmlspecialchars($chat['ticket_id']); ?>">
                                            <button type="submit" name="accept_chat" class="btn btn-sm btn-success">Kabul Et</button>
                                        </form>
                                    </li>
                                <?php endwhile; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-center">Bekleyen istek yok</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Aktif Chatler</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($active_sessions && $active_sessions->num_rows > 0): ?>
                            <div class="accordion" id="chatAccordion">
                                <?php while ($session = $active_sessions->fetch_assoc()): 
                                    // Telegram kullanıcı bilgilerini çek
                                    $user_info = getTelegramUserInfo($session['user_id']);
                                    $username = isset($user_info['username']) ? '@' . $user_info['username'] : '';
                                    $first_name = $user_info['first_name'] ?? '';
                                    $last_name = $user_info['last_name'] ?? '';
                                    $full_name = trim("$first_name $last_name");
                                    
                                    // Eğer kullanıcı bilgisi bulunamazsa, sadece ID'yi kullan
                                    $display_name = !empty($full_name) ? $full_name : 'Kullanıcı';
                                    $display_username = !empty($username) ? " ($username)" : '';
                                ?>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#chat-<?php echo $session['id']; ?>">
                                                Ticket: <?php echo htmlspecialchars($session['ticket_id']); ?>
                                                - <?php echo htmlspecialchars($display_name . $display_username); ?>
                                            </button>
                                        </h2>
                                        <div id="chat-<?php echo $session['id']; ?>" class="accordion-collapse collapse" data-bs-parent="#chatAccordion">
                                            <div class="accordion-body">
                                                <div class="chat-container" id="chat-container-<?php echo $session['id']; ?>">
                                                    <?php
                                                    // Mesaj geçmişini SQLite'dan al
                                                    $messages = $messageHistory->getMessages($session['ticket_id']);
                                                    
                                                    // İlk sistem mesajı
                                                    echo '<div class="message support-message">
                                                        <strong>Sistem:</strong> Bu oturum ' . date('d/m/Y H:i', strtotime($session['start_time'])) . ' tarihinde başladı.
                                                      </div>';
                                                    
                                                    // Kullanıcı kayıt mesajı
                                                    if (empty($messages)) {
                                                        echo '<div class="message user-message">
                                                            <strong>' . htmlspecialchars($display_name . $display_username) . ':</strong> Destek talebinde bulundu.
                                                          </div>';
                                                    } else {
                                                        // Mesaj geçmişini göster
                                                        foreach ($messages as $msg) {
                                                            $message_class = $msg['is_support'] ? 'support-message' : 'user-message';
                                                            $sender = $msg['is_support'] ? 'Destek Ekibi' : htmlspecialchars($display_name . $display_username);
                                                            
                                                            echo '<div class="message ' . $message_class . '">
                                                                <strong>' . $sender . ':</strong> ' . htmlspecialchars($msg['message']) . '
                                                                <small class="d-block text-muted mt-1">' . date('H:i d/m/Y', $msg['timestamp']) . '</small>
                                                              </div>';
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                                <form method="post" action="">
                                                    <input type="hidden" name="ticket_id" value="<?php echo htmlspecialchars($session['ticket_id']); ?>">
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" name="message" placeholder="Mesajınızı yazın..." required>
                                                        <button type="submit" name="send_message" class="btn btn-primary">Gönder</button>
                                                    </div>
                                                </form>
                                                <div class="mt-3">
                                                    <form method="post" action="" onsubmit="return confirm('Bu sohbeti kapatmak istediğinizden emin misiniz?');">
                                                        <input type="hidden" name="ticket_id" value="<?php echo htmlspecialchars($session['ticket_id']); ?>">
                                                        <button type="submit" name="close_chat" class="btn btn-sm btn-danger w-100">
                                                            <i class="bi bi-x-circle"></i> Bu Sohbeti Kapat
                                                        </button>
                                                    </form>
                                                </div>
                                                <?php if (!empty($username)): ?>
                                                <div class="mt-2">
                                                    <a href="https://t.me/<?php echo str_replace('@', '', $username); ?>" target="_blank" class="btn btn-sm btn-info w-100">
                                                        <i class="bi bi-telegram"></i> Telegram Profilini Aç
                                                    </a>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-center">Aktif chat yok</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <a href="dashboard.php" class="btn btn-lg btn-primary refresh-btn">
        <i class="bi bi-arrow-clockwise"></i>
    </a>
</body>
</html>
<?php
// SQLite bağlantısını kapat
$messageHistory->close();

// MySQL bağlantısını kapat
$conn->close();
?>