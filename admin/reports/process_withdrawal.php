<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/admin_functions.php';

// Admin oturum kontrolü
if(!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

// Bağlantı değişkenini al
$conn = $GLOBALS['db']->getConnection();

// İşlem parametrelerini al
$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : '');
$id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['withdrawal_id']) ? (int)$_POST['withdrawal_id'] : 0);

// Geçerli bir işlem ve ID kontrolü
if (empty($action) || $id <= 0) {
    setFlashMessage('error', 'Geçersiz istek parametreleri.');
    header('Location: withdrawals.php');
    exit;
}

// Para çekme bilgilerini getir
$withdrawal = getWithdrawalById($id);

if (!$withdrawal) {
    setFlashMessage('error', 'Para çekme işlemi bulunamadı.');
    header('Location: withdrawals.php');
    exit;
}

// İşlem durumunu kontrol et (sadece pending ve processing durumlarında işlem yapılabilir)
if (($withdrawal['status'] !== 'pending' && $withdrawal['status'] !== 'processing') && $action !== 'view') {
    setFlashMessage('error', 'Sadece beklemede veya işlemde olan çekimler üzerinde değişiklik yapılabilir.');
    header('Location: withdrawals.php');
    exit;
}

// İşlemi gerçekleştir
$result = false;
$message = '';

switch ($action) {
    case 'process':
        // İşlemi işleniyor durumuna al
        $result = processWithdrawal($id);
        $message = $result ? 'Para çekme işlemi "İşleniyor" durumuna alındı.' : 'İşlem durumu değiştirilirken bir hata oluştu.';
        break;
        
    case 'complete':
        // İşlemi tamamlandı olarak işaretle
        if (isset($_POST['transaction_hash']) && !empty($_POST['transaction_hash'])) {
            $transaction_hash = $_POST['transaction_hash'];
            $admin_note = isset($_POST['admin_note']) ? $_POST['admin_note'] : '';
        } else {
            // URL'den istenmişse veya hash yoksa, otomatik hash oluştur
            $transaction_hash = "AUTO_" . date('YmdHis') . "_" . substr(md5(uniqid()), 0, 8);
            $admin_note = "Otomatik tamamlama: " . date('Y-m-d H:i:s');
            error_log("Otomatik hash oluşturuldu: $transaction_hash");
        }
        
        error_log("completeWithdrawal çağrılmadan önce: id=$id, hash=$transaction_hash");
        $result = completeWithdrawal($id, $transaction_hash, $admin_note);
        error_log("completeWithdrawal sonucu: " . ($result ? "başarılı" : "başarısız"));
        
        $message = $result ? 'Para çekme işlemi başarıyla tamamlandı.' : 'İşlem tamamlanırken bir hata oluştu.';
    
        break;
        
    case 'cancel':
        // İşlemi iptal et ve tutarı kullanıcıya iade et
        $result = cancelWithdrawal($id);
        $message = $result ? 'Para çekme işlemi iptal edildi ve tutar kullanıcıya iade edildi.' : 'İşlem iptal edilirken bir hata oluştu.';
        break;
        
    default:
        $message = 'Geçersiz işlem.';
        break;
}

// İşlem sonucuna göre yönlendir
if ($result) {
    setFlashMessage('success', $message);
} else {
    setFlashMessage('error', $message);
}

// Geri yönlendir
header('Location: withdrawals.php');
exit;

/**
 * Para çekme işlemini ID'ye göre getir
 * 
 * @param int $withdrawal_id Para çekme ID
 * @return array|false Para çekme bilgileri veya false
 */
function getWithdrawalById($withdrawal_id) {
    global $conn;
    
    if (!$conn) {
        error_log("Veritabanı bağlantısı kurulamadı");
        return false;
    }
    
    $query = "
        SELECT w.*, u.username 
        FROM withdrawals w
        LEFT JOIN users u ON w.user_id = u.id
        WHERE w.id = ?
    ";
    
    $stmt = $conn->prepare($query);
    
    if ($stmt === false) {
        error_log("SQL sorgusu hazırlanamadı: " . $conn->error);
        return false;
    }
    
    $stmt->bind_param("i", $withdrawal_id);
    $stmt->execute();
    
    $result = $stmt->get_result();
    if ($result === false) {
        error_log("Sorgu sonucu alınamadı: " . $stmt->error);
        $stmt->close();
        return false;
    }
    
    $withdrawal = $result->fetch_assoc();
    $stmt->close();
    
    return $withdrawal;
}

/**
 * Kullanıcı bilgilerini ID'ye göre getir
 * 
 * @param int $user_id Kullanıcı ID
 * @return array|false Kullanıcı bilgileri veya false
 */
function getUserDetails($user_id) {
    global $conn;
    
    if (!$conn) {
        error_log("Veritabanı bağlantısı kurulamadı");
        return false;
    }
    
    $query = "SELECT * FROM users WHERE id = ?";
    
    $stmt = $conn->prepare($query);
    
    if ($stmt === false) {
        error_log("SQL sorgusu hazırlanamadı: " . $conn->error);
        return false;
    }
    
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    
    $result = $stmt->get_result();
    if ($result === false) {
        error_log("Sorgu sonucu alınamadı: " . $stmt->error);
        $stmt->close();
        return false;
    }
    
    $user = $result->fetch_assoc();
    $stmt->close();
    
    return $user;
}

/**
 * Para çekme işlemini işleniyor durumuna al
 * 
 * @param int $withdrawal_id Para çekme ID
 * @return bool İşlem başarılı ise true, değilse false
 */
function processWithdrawal($withdrawal_id) {
    global $conn;
    
    // İşlem başlat
    $conn->begin_transaction();
    
    try {
        // Çekim bilgilerini getir
        $withdrawal = getWithdrawalById($withdrawal_id);
        
        if (!$withdrawal || $withdrawal['status'] !== 'pending') {
            throw new Exception("Geçersiz para çekme işlemi veya durum.");
        }
        
        // Kullanıcı bilgilerini getir
        $user = getUserDetails($withdrawal['user_id']);
        
        if (!$user) {
            throw new Exception("Kullanıcı bulunamadı.");
        }
        
        // İşlem durumunu güncelle
        $stmt = $conn->prepare("UPDATE withdrawals SET status = 'processing', updated_at = NOW() WHERE id = ?");
        if (!$stmt) {
            throw new Exception("SQL hatası: " . $conn->error);
        }
        
        $stmt->bind_param("i", $withdrawal_id);
        $stmt->execute();
        
        if ($stmt->affected_rows === 0) {
            throw new Exception("İşlem durumu güncellenemedi.");
        }
        $stmt->close();
        
        // İşlemi tamamla
        $conn->commit();
        
        // İşlem log'u
        addActionLog($_SESSION['admin_id'], 'withdrawal_process', "Para çekme işlemi 'İşleniyor' durumuna alındı ID: $withdrawal_id, Kullanıcı: {$user['username']}, Tutar: {$withdrawal['amount']} USDT");
        
        return true;
    } catch (Exception $e) {
        // Hata durumunda geri al
        $conn->rollback();
        
        // Hata log'u
        error_log("Para çekme işlemi durumu değiştirilirken hata: " . $e->getMessage());
        
        return false;
    }
}

/**
 * Para çekme işlemini tamamla
 * 
 * @param int $withdrawal_id Para çekme ID
 * @param string $transaction_hash İşlem hash
 * @param string $admin_note Admin notu
 * @return bool İşlem başarılı ise true, değilse false
 */
function completeWithdrawal($withdrawal_id, $transaction_hash, $admin_note = '') {
    global $conn;
    error_log("completeWithdrawal fonksiyonu çağrıldı");
    error_log("Parametreler: withdrawal_id=$withdrawal_id, transaction_hash=$transaction_hash, admin_note=$admin_note");
    
    // İşlem başlat
    $conn->begin_transaction();
    
    try {
        // Çekim bilgilerini getir
        $withdrawal = getWithdrawalById($withdrawal_id);
        
        if (!$withdrawal) {
            error_log("Çekim bulunamadı - ID: $withdrawal_id");
            throw new Exception("Para çekme işlemi bulunamadı.");
        }
        
        error_log("Çekim durumu: " . $withdrawal['status'] . " - ID: $withdrawal_id");
        
        if ($withdrawal['status'] !== 'pending' && $withdrawal['status'] !== 'processing') {
            error_log("Geçersiz çekim durumu: " . $withdrawal['status'] . " - ID: $withdrawal_id");
            throw new Exception("Geçersiz para çekme işlemi durumu.");
        }
        
        // Kullanıcı bilgilerini getir
        $user = getUserDetailsi($withdrawal['user_id']); // getUserDetailsi kullan
        
        if (!$user) {
            error_log("Kullanıcı bulunamadı - ID: " . $withdrawal['user_id']);
            throw new Exception("Kullanıcı bulunamadı.");
        }
        
        // Kullanıcı bakiyesi henüz düşülmediyse (pending durumunda), bakiyeyi düş
        if ($withdrawal['status'] === 'pending') {
            error_log("Bekleyen çekim durumu, bakiye düşülecek - ID: $withdrawal_id");
            
            // Toplam tutarı hesapla (çekim tutarı + işlem ücreti)
            $total_amount = $withdrawal['amount'] + $withdrawal['fee'];
            
            // Bakiye kontrolü
            if ($user['balance'] < $total_amount) {
                error_log("Yetersiz bakiye - Mevcut: " . $user['balance'] . ", Gerekli: " . $total_amount);
                throw new Exception("Kullanıcının bakiyesi yetersiz. Mevcut: " . number_format($user['balance'], 2) . ", Gerekli: " . number_format($total_amount, 2));
            }
            
            // Bakiyeyi güncelle
            $current_balance = $user['balance'];
            $new_balance = $current_balance - $total_amount;
            
            error_log("Bakiye güncelleniyor - Eski: $current_balance, Yeni: $new_balance");
            
            $stmt = $conn->prepare("UPDATE users SET balance = ? WHERE id = ?");
            if (!$stmt) {
                error_log("SQL hazırlama hatası (bakiye): " . $conn->error);
                throw new Exception("SQL hatası: " . $conn->error);
            }
            
            $stmt->bind_param("di", $new_balance, $withdrawal['user_id']);
            $result = $stmt->execute();
            
            if (!$result) {
                error_log("SQL yürütme hatası (bakiye): " . $stmt->error);
                throw new Exception("Bakiye güncellenemedi: " . $stmt->error);
            }
            
            if ($stmt->affected_rows === 0) {
                error_log("Bakiye güncellenmedi - Etkilenen satır: 0");
                throw new Exception("Kullanıcı bakiyesi güncellenemedi.");
            }
            
            error_log("Bakiye başarıyla güncellendi");
            $stmt->close();
            
            // İşlem geçmişi ekle
            error_log("İşlem kaydı ekleniyor");
            
            $transaction_id = addTransaction(
                $withdrawal['user_id'],
                'withdraw',
                -$total_amount,
                $current_balance,
                $new_balance,
                'completed',
                $withdrawal_id,
                'Para çekme işlemi'
            );
            
            if (!$transaction_id) {
                error_log("İşlem kaydı eklenemedi");
                throw new Exception("İşlem kaydı eklenemedi.");
            }
            
            error_log("İşlem kaydı eklendi - ID: $transaction_id");
        } else {
            error_log("İşleniyor durumu - bakiye zaten düşülmüş olmalı - ID: $withdrawal_id");
        }
        
        // Para çekme işlemini güncelle
        error_log("Çekim durumu güncelleniyor - ID: $withdrawal_id");
        
        // Önceki durumu kaydedelim
        $prev_status = $withdrawal['status'];
        
        $update_sql = "
            UPDATE withdrawals 
            SET status = 'completed', 
                transaction_hash = ?, 
                admin_note = CONCAT(IFNULL(admin_note, ''), '\n', ?),
                completed_at = NOW(),
                updated_at = NOW()
            WHERE id = ?
        ";
        
        error_log("Çekim güncelleme SQL: $update_sql");
        error_log("Parametreler: Hash=$transaction_hash, Note=$admin_note, ID=$withdrawal_id");
        
        $stmt = $conn->prepare($update_sql);
        if (!$stmt) {
            error_log("SQL hazırlama hatası (çekim): " . $conn->error);
            throw new Exception("SQL hatası: " . $conn->error);
        }
        
        $stmt->bind_param("ssi", $transaction_hash, $admin_note, $withdrawal_id);
        $result = $stmt->execute();
        
        if (!$result) {
            error_log("SQL yürütme hatası (çekim): " . $stmt->error);
            throw new Exception("Çekim güncellenemedi: " . $stmt->error);
        }
        
        error_log("Çekim güncelleme sonucu - Etkilenen satır: " . $stmt->affected_rows);
        
        if ($stmt->affected_rows === 0) {
            // Satır etkilenmemiş olabilir çünkü mevcut değerler aynı olabilir
            // Tekrar kontrol edelim
            $check = getWithdrawalById($withdrawal_id);
            if ($check && $check['status'] === 'completed' && $check['transaction_hash'] === $transaction_hash) {
                error_log("Çekim zaten güncellenmiş - status: completed, hash: $transaction_hash");
            } else {
                error_log("Çekim güncellenemedi - Etkilenen satır: 0");
                throw new Exception("Para çekme işlemi güncellenemedi.");
            }
        }
        
        $stmt->close();
        
        // İşlemi tamamla
        error_log("Transaction işlemi tamamlanıyor");
        $conn->commit();
        
        // İşlem log'u
        error_log("İşlem logu ekleniyor");
        addActionLog($_SESSION['admin_id'], 'withdrawal_complete', "Para çekme işlemi tamamlandı ID: $withdrawal_id, Kullanıcı: {$user['username']}, Tutar: {$withdrawal['amount']} USDT, Hash: $transaction_hash");
        
        error_log("completeWithdrawal başarıyla tamamlandı - ID: $withdrawal_id");
        return true;
    } catch (Exception $e) {
        // Hata durumunda geri al
        error_log("HATA: " . $e->getMessage() . " - Rollback yapılıyor");
        $conn->rollback();
        
        // Hata log'u
        error_log("Para çekme işlemi tamamlanırken hata: " . $e->getMessage());
        
        return false;
    }
}

/**
 * Para çekme işlemini iptal et ve tutarı kullanıcıya iade et
 * 
 * @param int $withdrawal_id Para çekme ID
 * @return bool İşlem başarılı ise true, değilse false
 */
function cancelWithdrawal($withdrawal_id) {
    global $conn;
    
    // İşlem başlat
    $conn->begin_transaction();
    
    try {
        // Çekim bilgilerini getir
        $withdrawal = getWithdrawalById($withdrawal_id);
        
        if (!$withdrawal || ($withdrawal['status'] !== 'pending' && $withdrawal['status'] !== 'processing')) {
            throw new Exception("Geçersiz para çekme işlemi veya durum.");
        }
        
        // Kullanıcı bilgilerini getir
        $user = getUserDetailsi($withdrawal['user_id']);
        
        if (!$user) {
            throw new Exception("Kullanıcı bulunamadı.");
        }
        
        // Toplam tutarı hesapla (çekim tutarı + işlem ücreti)
        $total_amount = $withdrawal['amount'] + $withdrawal['fee'];
        
        // Kullanıcı bakiyesini güncelle (iade)
        $current_balance = $user['balance'];
        $new_balance = $current_balance + $total_amount;
        
        // Users tablosunun yapısını kontrol et
        $tableInfo = $conn->query("DESCRIBE users");
        if (!$tableInfo) {
            throw new Exception("Users tablo yapısı alınamadı: " . $conn->error);
        }
        
        $userColumns = [];
        while ($row = $tableInfo->fetch_assoc()) {
            $userColumns[] = $row['Field'];
        }
        
        $updateSql = "UPDATE users SET balance = ?";
        
        if (in_array('updated_at', $userColumns)) {
            $updateSql .= ", updated_at = NOW()";
        }
        
        $updateSql .= " WHERE id = ?";
        
        $stmt = $conn->prepare($updateSql);
        if (!$stmt) {
            throw new Exception("SQL hatası: " . $conn->error);
        }
        
        $stmt->bind_param("di", $new_balance, $withdrawal['user_id']);
        $stmt->execute();
        
        if ($stmt->affected_rows === 0) {
            throw new Exception("Kullanıcı bakiyesi güncellenemedi.");
        }
        $stmt->close();
        
        // Para çekme işlemini güncelle - tabloyu kontrol et
        $tableInfo = $conn->query("DESCRIBE withdrawals");
        if (!$tableInfo) {
            throw new Exception("Withdrawals tablo yapısı alınamadı: " . $conn->error);
        }
        
        $withdrawalColumns = [];
        while ($row = $tableInfo->fetch_assoc()) {
            $withdrawalColumns[] = $row['Field'];
        }
        
        // Güncellenecek alanlar
        $updateFields = ["status = 'cancelled'"];
        $updateParams = [];
        $updateTypes = "";
        
        // Admin notu
        if (in_array('admin_note', $withdrawalColumns)) {
            $updateFields[] = "admin_note = CONCAT(IFNULL(admin_note, ''), '\nİptal edildi. İşlem tarihi: ', NOW())";
        }
        
        // Son güncelleme zamanı
        if (in_array('updated_at', $withdrawalColumns)) {
            $updateFields[] = "updated_at = NOW()";
        }
        
        // İşlemi iptal eden admin
        if (in_array('cancelled_by', $withdrawalColumns) && isset($_SESSION['admin_id'])) {
            $updateFields[] = "cancelled_by = ?";
            $updateParams[] = $_SESSION['admin_id'];
            $updateTypes .= "i";
        }
        
        // SQL sorgusunu oluştur
        $sql = "UPDATE withdrawals SET " . implode(", ", $updateFields) . " WHERE id = ?";
        $updateParams[] = $withdrawal_id;
        $updateTypes .= "i";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Güncelleme sorgusu hazırlanamadı: " . $conn->error);
        }
        
        if (!empty($updateParams)) {
            $stmt->bind_param($updateTypes, ...$updateParams);
        }
        
        $stmt->execute();
        
        if ($stmt->affected_rows === 0) {
            throw new Exception("Para çekme işlemi güncellenemedi.");
        }
        $stmt->close();
        
        // İşlem geçmişi ekle
        $transactionId = addTransaction(
            $withdrawal['user_id'],
            'withdraw_refund',
            $total_amount,
            $current_balance,
            $new_balance,
            'completed',
            $withdrawal_id,
            'Para çekme işlemi iptali ve iade'
        );
        
        if (!$transactionId) {
            throw new Exception("İşlem geçmişi eklenemedi.");
        }
        
        // İşlemi tamamla
        $conn->commit();
        
        // İşlem log'u
        addActionLog($_SESSION['admin_id'], 'withdrawal_cancel', "Para çekme işlemi iptal edildi ID: $withdrawal_id, Kullanıcı: {$user['username']}, Tutar: {$withdrawal['amount']} USDT");
        
        return true;
    } catch (Exception $e) {
        // Hata durumunda geri al
        $conn->rollback();
        
        // Hata log'u
        error_log("Para çekme işlemi iptal edilirken hata: " . $e->getMessage());
        
        return false;
    }
}

    
    
    /**
     * Flash mesajı ayarla
     * 
     * @param string $type Mesaj tipi (success, error, warning, info)
     * @param string $message Mesaj içeriği
     * @return void
     */
    function setFlashMessage($type, $message) {
        $_SESSION['flash_message'] = [
            'type' => $type,
            'message' => $message
        ];
    }