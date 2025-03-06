<?php
/**
 * Site Yapılandırma Dosyası
 */

// Site URLsi
define('SITE_URL', 'http://localhost/');

// Veritabanı Ayarları
define('DB_HOST', 'localhost');
define('DB_NAME', 'kazanc_platformu');
define('DB_USER', 'root');
define('DB_PASS', '');

// Uygulama Ayarları
define('APP_NAME', 'Kazanç Platformu');
define('APP_VERSION', '1.0.0');
define('APP_TIMEZONE', 'Europe/Istanbul');

// Oturum Ayarları
define('SESSION_NAME', 'kazanc_session');
define('SESSION_LIFETIME', 86400); // 1 gün

// Güvenlik Ayarları
define('HASH_COST', 12); // Password bcrypt cost
define('TOKEN_LIFETIME', 3600); // 1 saat

// Zaman dilimini ayarla
date_default_timezone_set(APP_TIMEZONE);

// Hata raporlama ayarları
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');

// Geliştirme modunda hata raporlamasını etkinleştir
if ($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_NAME'] == '127.0.0.1') {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    define('DEV_MODE', true);
} else {
    error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
    define('DEV_MODE', false);
}

// Gerekli dosyaları dahil et
require_once 'db.php';
require_once 'functions.php';

// Veritabanı bağlantısı fonksiyonu - includes/config.php içine ekleyin
function dbConnect() {
    static $conn;
    
    if ($conn === null) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($conn->connect_error) {
            die("Veritabanı bağlantısı başarısız: " . $conn->connect_error);
        }
        
        $conn->set_charset("utf8mb4");
    }
    
    return $conn;
}