<?php
/**
 * Veritabanı Bağlantı Sınıfı
 * 
 * Bu sınıf, veritabanı bağlantısını yönetir
 */
class DB {
    private $conn;
    private static $instance;
    
    /**
     * Sınıf oluşturucusu
     * 
     * Veritabanı bağlantısını başlatır
     */
    public function __construct() {
        try {
            $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            // Bağlantı hatası kontrolü
            if ($this->conn->connect_error) {
                throw new Exception("Veritabanı bağlantısı başarısız: " . $this->conn->connect_error);
            }
            
            // Karakter seti ayarla
            $this->conn->set_charset("utf8mb4");
        } catch (Exception $e) {
            error_log($e->getMessage());
            // Hata durumunda kritik hata mesajı
            die("Veritabanı bağlantısı yapılamadı. Lütfen daha sonra tekrar deneyiniz.");
        }
    }
    
    /**
     * Singleton örneği alır
     * 
     * @return DB Sınıf örneği
     */
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    /**
     * Veritabanı bağlantısını döndürür
     * 
     * @return mysqli Veritabanı bağlantısı
     */
    public function getConnection() {
        return $this->conn;
    }
    
    /**
     * Bir sorgu çalıştırır
     * 
     * @param string $query SQL sorgusu
     * @return mysqli_result|bool Sorgu sonucu veya başarısız ise false
     */
    public function query($query) {
        return $this->conn->query($query);
    }
    
    /**
     * Bir prepared statement hazırlar
     * 
     * @param string $query SQL sorgusu
     * @return mysqli_stmt Prepared statement
     */
    public function prepare($query) {
        return $this->conn->prepare($query);
    }
    
    /**
     * Son eklenen satırın ID'sini döndürür
     * 
     * @return int Son eklenen satır ID'si
     */
    public function insertId() {
        return $this->conn->insert_id;
    }
    
    /**
     * Son sorgunun etkilediği satır sayısını döndürür
     * 
     * @return int Etkilenen satır sayısı
     */
    public function affectedRows() {
        return $this->conn->affected_rows;
    }
    
    /**
     * Bir işlem (transaction) başlatır
     */
    public function beginTransaction() {
        $this->conn->begin_transaction();
    }
    
    /**
     * İşlemi onaylar (commit)
     */
    public function commit() {
        $this->conn->commit();
    }
    
    /**
     * İşlemi geri alır (rollback)
     */
    public function rollback() {
        $this->conn->rollback();
    }
    
    /**
     * Bağlantıyı kapatır
     */
    public function close() {
        $this->conn->close();
    }
    
    /**
     * Destructor - sınıf yok edildiğinde bağlantıyı kapatır
     */
    public function __destruct() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
    
    /**
     * Veritabanındaki girişleri güvenli hale getirir
     * 
     * @param string $string Güvenli hale getirilecek string
     * @return string Güvenli hale getirilmiş string
     */
    public function escape($string) {
        return $this->conn->real_escape_string($string);
    }
    
    /**
     * Son oluşan hata mesajını döndürür
     * 
     * @return string Hata mesajı
     */
    public function error() {
        return $this->conn->error;
    }
}

// Global veritabanı örneğini oluştur
$db = DB::getInstance();