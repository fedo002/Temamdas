<?php
/**
 * Admin paneli için ödeme yönetimi fonksiyonları
 */

// Tüm depozitleri getir
function getAllDeposits($status = null, $limit = null, $offset = 0) {
    global $db;
    
    $sql = "SELECT d.*, u.username FROM deposits d 
            INNER JOIN users u ON d.user_id = u.id";
    
    if ($status) {
        $sql .= " WHERE d.status = ?";
    }
    
    $sql .= " ORDER BY d.created_at DESC";
    
    if ($limit) {
        $sql .= " LIMIT ?, ?";
    }
    
    $stmt = $db->prepare($sql);
    
    if ($status && $limit) {
        $stmt->bind_param("sii", $status, $offset, $limit);
    } elseif ($status) {
        $stmt->bind_param("s", $status);
    } elseif ($limit) {
        $stmt->bind_param("ii", $offset, $limit);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Tüm çekimleri getir
function getAllWithdrawals($status = null, $limit = null, $offset = 0) {
    global $db;
    
    $sql = "SELECT w.*, u.username FROM withdrawals w 
            INNER JOIN users u ON w.user_id = u.id";
    
    if ($status) {
        $sql .= " WHERE w.status = ?";
    }
    
    $sql .= " ORDER BY w.created_at DESC";
    
    if ($limit) {
        $sql .= " LIMIT ?, ?";
    }
    
    $stmt = $db->prepare($sql);
    
    if ($status && $limit) {
        $stmt->bind_param("sii", $status, $offset, $limit);
    } elseif ($status) {
        $stmt->bind_param("s", $status);
    } elseif ($limit) {
        $stmt->bind_param("ii", $offset, $limit);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Para çekme talebini onayla
function approveWithdrawal($withdrawal_id, $admin_id, $tx_hash = null, $note = null) {
    global $db;
    
    // İşlemi bul
    $stmt = $db->prepare("SELECT * FROM withdrawals WHERE id = ? AND status = 'pending'");
    $stmt->bind_param("i", $withdrawal_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $withdrawal = $result->fetch_assoc();
    
    if (!$withdrawal) {
        return [
            'success' => false,
            'message' => 'Çekim talebi bulunamadı veya zaten işlendi.'
        ];
    }
    
    // Transaction başlat
    $db->begin_transaction();
    
    try {
        // Çekim durumunu güncelle
        $status = 'completed';
        $processed_at = date('Y-m-d H:i:s');
        
        $stmt = $db->prepare("UPDATE withdrawals SET status = ?, admin_id = ?, transaction_hash = ?, admin_note = ?, processed_at = ? WHERE id = ?");
        $stmt->bind_param("sisssi", $status, $admin_id, $tx_hash, $note, $processed_at, $withdrawal_id);
        $stmt->execute();
        
        // İşlem kaydını güncelle
        $stmt = $db->prepare("UPDATE transactions SET status = 'completed' WHERE related_id = ? AND type = 'withdraw'");
        $stmt->bind_param("i", $withdrawal_id);
        $stmt->execute();
        
        $db->commit();
        
        return [
            'success' => true,
            'message' => 'Çekim talebi onaylandı.'
        ];
    } catch (Exception $e) {
        $db->rollback();
        
        return [
            'success' => false,
            'message' => 'İşlem sırasında bir hata oluştu: ' . $e->getMessage()
        ];
    }
}

// Para çekme talebini reddet
function rejectWithdrawal($withdrawal_id, $admin_id, $note = null) {
    global $db;
    
    // İşlemi bul
    $stmt = $db->prepare("SELECT w.*, u.balance FROM withdrawals w 
                         INNER JOIN users u ON w.user_id = u.id 
                         WHERE w.id = ? AND w.status = 'pending'");
    $stmt->bind_param("i", $withdrawal_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $withdrawal = $result->fetch_assoc();
    
    if (!$withdrawal) {
        return [
            'success' => false,
            'message' => 'Çekim talebi bulunamadı veya zaten işlendi.'
        ];
    }
    
    // Transaction başlat
    $db->begin_transaction();
    
    try {
        // Çekim durumunu güncelle
        $status = 'cancelled';
        $processed_at = date('Y-m-d H:i:s');
        
        $stmt = $db->prepare("UPDATE withdrawals SET status = ?, admin_id = ?, admin_note = ?, processed_at = ? WHERE id = ?");
        $stmt->bind_param("sissi", $status, $admin_id, $note, $processed_at, $withdrawal_id);
        $stmt->execute();
        
        // Kullanıcı bakiyesini iade et
        $totalAmount = $withdrawal['amount'] + $withdrawal['fee'];
        $stmt = $db->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
        $stmt->bind_param("di", $totalAmount, $withdrawal['user_id']);
        $stmt->execute();
        
        // İşlem kaydını güncelle
        $stmt = $db->prepare("UPDATE transactions SET status = 'cancelled' WHERE related_id = ? AND type = 'withdraw'");
        $stmt->bind_param("i", $withdrawal_id);
        $stmt->execute();
        
        // İade işlemi ekle
        $current_balance = $withdrawal['balance'] + $totalAmount;
        $stmt = $db->prepare("INSERT INTO transactions (user_id, related_id, type, amount, before_balance, after_balance, status, description) 
                            VALUES (?, ?, 'other', ?, ?, ?, 'completed', ?)");
        $description = "Refund for rejected withdrawal #$withdrawal_id";
        $stmt->bind_param("iiddds", $withdrawal['user_id'], $withdrawal_id, $totalAmount, $withdrawal['balance'], $current_balance, $description);
        $stmt->execute();
        
        $db->commit();
        
        return [
            'success' => true,
            'message' => 'Çekim talebi reddedildi ve bakiye iade edildi.'
        ];
    } catch (Exception $e) {
        $db->rollback();
        
        return [
            'success' => false,
            'message' => 'İşlem sırasında bir hata oluştu: ' . $e->getMessage()
        ];
    }
}

// Ödeme istatistiklerini getir
function getPaymentStats() {
    global $db;
    
    $stats = [
        'total_deposits' => 0,
        'total_withdrawals' => 0,
        'monthly_deposits' => 0,
        'monthly_withdrawals' => 0,
        'pending_withdrawals' => 0,
        'deposit_count' => 0,
        'withdraw_count' => 0
    ];
    
    // Toplam yatırım
    $stmt = $db->prepare("SELECT SUM(amount) as total FROM deposits WHERE status = 'confirmed'");
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stats['total_deposits'] = $row['total'] ?: 0;
    
    // Toplam çekim
    $stmt = $db->prepare("SELECT SUM(amount) as total FROM withdrawals WHERE status = 'completed'");
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stats['total_withdrawals'] = $row['total'] ?: 0;
    
    // Bu ayki yatırımlar
    $start_date = date('Y-m-01 00:00:00');
    $end_date = date('Y-m-t 23:59:59');
    
    $stmt = $db->prepare("SELECT SUM(amount) as total FROM deposits WHERE status = 'confirmed' AND created_at BETWEEN ? AND ?");
    $stmt->bind_param("ss", $start_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stats['monthly_deposits'] = $row['total'] ?: 0;
    
    // Bu ayki çekimler
    $stmt = $db->prepare("SELECT SUM(amount) as total FROM withdrawals WHERE status = 'completed' AND processed_at BETWEEN ? AND ?");
    $stmt->bind_param("ss", $start_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stats['monthly_withdrawals'] = $row['total'] ?: 0;
    
    // Bekleyen çekimler
    $stmt = $db->prepare("SELECT SUM(amount) as total, COUNT(*) as count FROM withdrawals WHERE status = 'pending'");
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stats['pending_withdrawals'] = $row['total'] ?: 0;
    $stats['pending_withdraw_count'] = $row['count'] ?: 0;
    
    // İşlem sayıları
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM deposits WHERE status = 'confirmed'");
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stats['deposit_count'] = $row['count'] ?: 0;
    
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM withdrawals WHERE status = 'completed'");
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stats['withdraw_count'] = $row['count'] ?: 0;
    
    return $stats;
}

// Manuel yatırım işlemi
function addManualDeposit($user_id, $amount, $note = null, $admin_id = null) {
    global $db;
    
    // Kullanıcıyı kontrol et
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if (!$user) {
        return [
            'success' => false,
            'message' => 'Kullanıcı bulunamadı.'
        ];
    }
    
    // Transaction başlat
    $db->begin_transaction();
    
    try {
        // Yatırım kaydı oluştur
        $status = 'confirmed';
        $payment_method = 'manual';
        $order_id = 'MANUAL-' . time();
        
        $stmt = $db->prepare("INSERT INTO deposits (user_id, amount, status, payment_method, order_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("idsss", $user_id, $amount, $status, $payment_method, $order_id);
        $stmt->execute();
        $deposit_id = $db->insert_id;
        
        // Kullanıcı bakiyesini güncelle
        $before_balance = $user['balance'];
        $after_balance = $before_balance + $amount;
        
        $stmt = $db->prepare("UPDATE users SET balance = balance + ?, total_deposit = total_deposit + ? WHERE id = ?");
        $stmt->bind_param("ddi", $amount, $amount, $user_id);
        $stmt->execute();
        
        // İşlem kaydını ekle
        $description = "Manual deposit by admin" . ($note ? ": $note" : "");
        $stmt = $db->prepare("INSERT INTO transactions (user_id, related_id, type, amount, before_balance, after_balance, status, description) 
                              VALUES (?, ?, 'deposit', ?, ?, ?, 'completed', ?)");
        $stmt->bind_param("iiddds", $user_id, $deposit_id, $amount, $before_balance, $after_balance, $description);
        $stmt->execute();
        
        // Admin log kaydı
        if ($admin_id) {
            $log_description = "Added manual deposit of $amount USDT to user #$user_id" . ($note ? ": $note" : "");
            $stmt = $db->prepare("INSERT INTO admin_logs (admin_id, action, description, related_id, related_type) 
                                 VALUES (?, 'manual_deposit', ?, ?, 'deposit')");
            $stmt->bind_param("isi", $admin_id, $log_description, $deposit_id);
            $stmt->execute();
        }
        
        $db->commit();
        
        // Referans komisyonunu işle
        processReferralCommission($user_id, $amount);
        
        return [
            'success' => true,
            'message' => 'Manuel yatırım başarıyla eklendi.',
            'deposit_id' => $deposit_id
        ];
    } catch (Exception $e) {
        $db->rollback();
        
        return [
            'success' => false,
            'message' => 'İşlem sırasında bir hata oluştu: ' . $e->getMessage()
        ];
    }
}