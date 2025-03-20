<?php
// Ana dizindeki config.php dosyasını içe aktarın
require_once '../config.php';

// Hata raporlamayı açma
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// CORS ayarları - gerekli olabilir
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// OPTIONS requestleri için erken yanıt
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Veritabanı bağlantısı için fonksiyon
function getDbConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die(json_encode([
            "success" => false,
            "message" => "Connection failed: " . $conn->connect_error
        ]));
    }
    
    // UTF-8 karakter seti
    $conn->set_charset("utf8mb4");
    
    return $conn;
}

// Hata logla
function logApiError($message) {
    $logFile = "../logs/api_errors.log";
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message" . PHP_EOL;
    
    // Log klasörünü kontrol et
    $logDir = dirname($logFile);
    if (!file_exists($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    error_log($logMessage, 3, $logFile);
}

// API endpoint'leri için router
$request_uri = $_SERVER['REQUEST_URI'];
$request_method = $_SERVER['REQUEST_METHOD'];

// Debug log
logApiError("Request URI: " . $request_uri . ", Method: " . $request_method);

// URL'den endpoint'i çıkar
$endpoint = null;
if (preg_match('/\/tgsupport\/api\/([^\/\?]+)/', $request_uri, $matches)) {
    $endpoint = $matches[1];
} else {
    // Endpoint belirtilmemişse
    sendResponse(200, ["success" => true, "message" => "API is working"]);
    exit;
}

// Debug log
logApiError("Endpoint: " . $endpoint);

// Endpoint'e göre işlem yap
switch ($endpoint) {
    case 'verify':
        if ($request_method === 'POST') {
            verifyUser();
        } else {
            sendResponse(405, ["success" => false, "message" => "Method not allowed"]);
        }
        break;
        
    case 'availability':
        if ($request_method === 'GET') {
            checkAvailability();
        } else {
            sendResponse(405, ["success" => false, "message" => "Method not allowed"]);
        }
        break;
        
    case 'support_session':
        if ($request_method === 'POST') {
            handleSupportSession();
        } else {
            sendResponse(405, ["success" => false, "message" => "Method not allowed"]);
        }
        break;
        
    default:
        sendResponse(404, ["success" => false, "message" => "Endpoint not found: " . $endpoint]);
        break;
}

// Kullanıcı doğrulama fonksiyonu
function verifyUser() {
    // JSON verisini al
    $json_data = file_get_contents("php://input");
    logApiError("Verify Request Data: " . $json_data);
    $data = json_decode($json_data, true);
    
    // Gerekli veriler var mı kontrol et
    if (!$data) {
        sendResponse(400, [
            "success" => false,
            "message" => "Invalid JSON data",
            "is_member" => false,
            "has_telegram_support" => false
        ]);
        return;
    }
    
    // Veritabanı bağlantısı
    $conn = getDbConnection();
    
    try {
        // Test için her zaman doğrula
        // Gerçek uygulamada VIP level kontrolü yapılmalı
        
        sendResponse(200, [
            "success" => true,
            "message" => "User verified for testing purposes",
            "is_member" => true,
            "has_telegram_support" => true
        ]);
        
        
        // Aşağıdaki kod, gerçek doğrulama için kullanılabilir
        /*
        // Doğrulama bilgisini al (email veya username)
        $verification_info = $data['verification_info'] ?? '';
        $telegram_id = $data['user_id'] ?? null;
        
        // Kullanıcıyı email veya username ile kontrol et
        $stmt = $conn->prepare("SELECT id, username, email, vip_level, telegram_id FROM users WHERE email = ? OR username = ?");
        $stmt->bind_param("ss", $verification_info, $verification_info);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        // Varsayılan yanıt
        $response = [
            "success" => false,
            "message" => "User not found or not authorized for Telegram support",
            "is_member" => false,
            "has_telegram_support" => false
        ];
        
        if ($user) {
            // Kullanıcı mevcut
            $response["is_member"] = true;
            
            // VIP seviyesini kontrol et
            if ($user['vip_level'] >= 3) {
                $response["has_telegram_support"] = true;
                $response["success"] = true;
                $response["message"] = "User verified and has Telegram support access";
                
                // Telegram ID'yi güncelle (eğer mevcut değilse)
                if ($telegram_id && (empty($user['telegram_id']))) {
                    $update_stmt = $conn->prepare("UPDATE users SET telegram_id = ? WHERE id = ?");
                    $update_stmt->bind_param("si", $telegram_id, $user['id']);
                    $update_stmt->execute();
                }
            } else {
                $response["message"] = "User found but does not have Telegram support access (VIP level < 3)";
            }
        }
        
        sendResponse(200, $response);
        */
    } catch (Exception $e) {
        logApiError("Verify Error: " . $e->getMessage());
        sendResponse(500, [
            "success" => false,
            "message" => "Error during verification: " . $e->getMessage(),
            "is_member" => false,
            "has_telegram_support" => false
        ]);
    } finally {
        $conn->close();
    }
}

// Destek temsilcilerinin durumunu kontrol et
function checkAvailability() {
    $conn = getDbConnection();
    
    try {
        // Aktif destek temsilcilerini kontrol et
        $query = "SELECT COUNT(*) as active_reps FROM support_reps WHERE is_online = 1 AND last_activity > DATE_SUB(NOW(), INTERVAL 10 MINUTE)";
        $result = $conn->query($query);
        $row = $result->fetch_assoc();
        
        $available = isset($row['active_reps']) && $row['active_reps'] > 0;
        
        // Debug log
        logApiError("Availability check: Active reps = " . ($row['active_reps'] ?? 0));
        
        sendResponse(200, [
            "success" => true,
            "available" => $available,
            "active_reps" => $row['active_reps'] ?? 0
        ]);
    } catch (Exception $e) {
        logApiError("Availability Error: " . $e->getMessage());
        sendResponse(500, [
            "success" => false,
            "message" => "Error checking availability: " . $e->getMessage(),
            "available" => false
        ]);
    } finally {
        $conn->close();
    }
}

// Destek oturumlarını yönetmek için fonksiyon
function handleSupportSession() {
    // JSON verisini al
    $json_data = file_get_contents("php://input");
    logApiError("Support Session Request Data: " . $json_data);
    $data = json_decode($json_data, true);
    
    if (!$data || !isset($data['action'])) {
        sendResponse(400, ["success" => false, "message" => "Missing required data"]);
        return;
    }
    
    $action = $data['action'];
    $conn = getDbConnection();
    
    try {
        switch ($action) {
            case 'create_session':
                // Yeni oturum oluştur
                if (!isset($data['ticket_id']) || !isset($data['user_id'])) {
                    sendResponse(400, ["success" => false, "message" => "Missing ticket_id or user_id"]);
                    return;
                }
                
                $ticket_id = $data['ticket_id'];
                $user_id = $data['user_id'];
                $status = $data['status'] ?? 'waiting';
                
                // Önce aynı ticket_id ile kayıt var mı kontrol et
                $check = $conn->prepare("SELECT id FROM active_sessions WHERE ticket_id = ?");
                $check->bind_param("s", $ticket_id);
                $check->execute();
                $check_result = $check->get_result();
                
                if ($check_result->num_rows > 0) {
                    // Varolan kaydı güncelle
                    $update = $conn->prepare("UPDATE active_sessions SET status = ?, last_message_time = NOW() WHERE ticket_id = ?");
                    $update->bind_param("ss", $status, $ticket_id);
                    $update->execute();
                    
                    sendResponse(200, [
                        "success" => true,
                        "message" => "Session updated successfully",
                        "session_id" => $check_result->fetch_assoc()['id']
                    ]);
                } else {
                    // Yeni kayıt oluştur
                    $stmt = $conn->prepare("INSERT INTO active_sessions (ticket_id, user_id, status) VALUES (?, ?, ?)");
                    $stmt->bind_param("sss", $ticket_id, $user_id, $status);
                    $stmt->execute();
                    
                    sendResponse(200, [
                        "success" => true,
                        "message" => "Session created successfully",
                        "session_id" => $conn->insert_id
                    ]);
                }
                break;
                
            case 'update_session':
                // Oturum güncelle
                if (!isset($data['ticket_id'])) {
                    sendResponse(400, ["success" => false, "message" => "Missing ticket_id"]);
                    return;
                }
                
                $ticket_id = $data['ticket_id'];
                $updates = [];
                $params = [];
                $types = "";
                
                // Güncellenecek alanları kontrol et
                if (isset($data['status'])) {
                    $updates[] = "status = ?";
                    $params[] = $data['status'];
                    $types .= "s";
                }
                
                if (isset($data['support_rep_id'])) {
                    $updates[] = "support_rep_id = ?";
                    $params[] = $data['support_rep_id'];
                    $types .= "i";
                }
                
                if (isset($data['last_message_time'])) {
                    $updates[] = "last_message_time = NOW()";
                    // NOW() kullanıldığı için parametre eklemeye gerek yok
                } else {
                    // Her zaman last_message_time'ı güncelle
                    $updates[] = "last_message_time = NOW()";
                }
                
                if (empty($updates)) {
                    sendResponse(400, ["success" => false, "message" => "No fields to update"]);
                    return;
                }
                
                // ticket_id için parametre ekle
                $params[] = $ticket_id;
                $types .= "s";
                
                $query = "UPDATE active_sessions SET " . implode(", ", $updates) . " WHERE ticket_id = ?";
                $stmt = $conn->prepare($query);
                
                if (!empty($types)) {
                    $stmt->bind_param($types, ...$params);
                }
                
                $stmt->execute();
                
                sendResponse(200, [
                    "success" => true,
                    "message" => "Session updated successfully",
                    "affected_rows" => $stmt->affected_rows
                ]);
                break;
                
            case 'get_session':
                // Oturum bilgilerini getir
                if (!isset($data['ticket_id'])) {
                    sendResponse(400, ["success" => false, "message" => "Missing ticket_id"]);
                    return;
                }
                
                $ticket_id = $data['ticket_id'];
                
                $stmt = $conn->prepare("SELECT * FROM active_sessions WHERE ticket_id = ?");
                $stmt->bind_param("s", $ticket_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $session = $result->fetch_assoc();
                
                if ($session) {
                    sendResponse(200, [
                        "success" => true,
                        "session" => $session
                    ]);
                } else {
                    sendResponse(404, [
                        "success" => false,
                        "message" => "Session not found"
                    ]);
                }
                break;
                
            default:
                sendResponse(400, ["success" => false, "message" => "Invalid action: " . $action]);
                break;
        }
    } catch (Exception $e) {
        logApiError("Support Session Error: " . $e->getMessage());
        sendResponse(500, [
            "success" => false,
            "message" => "Error handling support session: " . $e->getMessage()
        ]);
    } finally {
        $conn->close();
    }
}

// JSON yanıtı gönder
function sendResponse($status_code, $data) {
    http_response_code($status_code);
    $json = json_encode($data);
    if ($json === false) {
        logApiError("JSON encode error: " . json_last_error_msg());
        $json = json_encode(["success" => false, "message" => "JSON encoding error"]);
    }
    echo $json;
    exit;
}
?>