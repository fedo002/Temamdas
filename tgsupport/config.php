<?php
// Veritabanı bağlantı bilgileri
define('DB_HOST', 'localhost');
define('DB_USER', 'digiminex');
define('DB_PASS', 'digiminex');
define('DB_NAME', 'admin_digi');
// Oturum ayarları
define('SESSION_LIFETIME', 3600); // 1 saat

// Bot ayarları
define('BOT_TOKEN', '7744363269:AAFXjbRz3LF-YlfQTuKDiz4l2WbDu6-PMVY'); // Telegram bot token

// Hata log ayarları
define('LOG_ERRORS', true);
define('ERROR_LOG_FILE', __DIR__ . '/logs/error.log');

// Oturum ayarlarını yapılandır
ini_set('session.cookie_lifetime', SESSION_LIFETIME);
ini_set('session.gc_maxlifetime', SESSION_LIFETIME);

// Zaman dilimi ayarları
date_default_timezone_set('Europe/Istanbul');

// Hata raporlama - prod'da kapatın
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Hata log fonksiyonu
function logError($message) {
    if (LOG_ERRORS) {
        // Log klasörünü kontrol et, yoksa oluştur
        $logDir = dirname(ERROR_LOG_FILE);
        if (!file_exists($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] $message" . PHP_EOL;
        error_log($logMessage, 3, ERROR_LOG_FILE);
    }
}
?>