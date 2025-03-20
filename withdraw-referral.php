<?php
// mobile/withdraw-referral.php - Transfer referral balance to main balance
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=withdraw-referral.php');
    exit;
}

// Get database connection
$conn = $GLOBALS['db']->getConnection();

// Get user details
$userId = $_SESSION['user_id'];
$user = getUserDetails($userId);

if (!$user) {
    $page_title = "Error";
    include 'includes/mobile-header.php';
    echo '<div class="alert alert-danger">User not found. Please login again.</div>';
    include 'includes/mobile-footer.php';
    exit;
}

// Get user's referral balance
$referralBalance = $user['referral_balance'] ?? 0;

// Check if referral_earnings table exists
$tablesExist = true;
$result = $conn->query("SHOW TABLES LIKE 'referral_earnings'");
if ($result->num_rows === 0) {
    $tablesExist = false;
}

// Get transfer history
$transferHistory = [];
$stmt = $conn->prepare("SELECT * FROM transactions WHERE user_id = ? AND type = 'referral_transfer' ORDER BY created_at DESC");
if ($stmt) {
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $transferHistory[] = $row;
    }
}

// Process transfer form
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['transfer_balance'])) {
    $amount = floatval($_POST['amount'] ?? 0);
    
    // Basic validation
    if ($amount <= 0) {
        $message = 'Please enter a valid amount.';
        $messageType = 'error';
    } elseif ($amount > $referralBalance) {
        $message = 'Transfer amount exceeds your referral balance.';
        $messageType = 'error';
    } else {
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // Decrease referral balance
            $stmt = $conn->prepare("UPDATE users SET referral_balance = referral_balance - ? WHERE id = ?");
            $stmt->bind_param("di", $amount, $userId);
            $stmt->execute();
            
            // Increase main balance
            $stmt = $conn->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
            $stmt->bind_param("di", $amount, $userId);
            $stmt->execute();
            
            // Record transaction
            $beforeBalance = $user['balance'];
            $afterBalance = $beforeBalance + $amount;
            $description = "Referral earnings transfer to main balance";
            
            $stmt = $conn->prepare("INSERT INTO transactions (user_id, type, amount, before_balance, after_balance, status, description) 
                                  VALUES (?, 'referral_transfer', ?, ?, ?, 'completed', ?)");
            $stmt->bind_param("iddds", $userId, $amount, $beforeBalance, $afterBalance, $description);
            $stmt->execute();
            
            // Commit transaction
            $conn->commit();
            
            // Success message
            $message = "Successfully transferred $amount USD from your referral balance to main balance.";
            $messageType = 'success';
            
            // Update user data
            $user = getUserDetails($userId);
            $referralBalance = $user['referral_balance'] ?? 0;
            
            // Refresh transfer history
            $stmt = $conn->prepare("SELECT * FROM transactions WHERE user_id = ? AND type = 'referral_transfer' ORDER BY created_at DESC");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $transferHistory = [];
            while ($row = $result->fetch_assoc()) {
                $transferHistory[] = $row;
            }
            
        } catch (Exception $e) {
            // Rollback on error
            $conn->rollback();
            $message = 'An error occurred: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
}

$page_title = "Transfer Referral Balance";
include 'includes/mobile-header.php';
?>

<div class="withdraw-referral-page">
    <div class="page-header">
        <h1>Transfer Referral Earnings</h1>
        <a href="referrals.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Back to Referrals
        </a>
    </div>
    
    <?php if (!$tablesExist): ?>
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i>
        <span>Referral system database tables have not been created yet. Please contact support.</span>
    </div>
    <a href="referrals.php" class="btn btn-primary">
        <i class="fas fa-arrow-left"></i> Back to Referrals
    </a>
    <?php else: ?>
    
    <?php if (!empty($message)): ?>
    <div class="alert alert-<?php echo $messageType === 'success' ? 'success' : 'danger'; ?>">
        <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
        <span><?php echo htmlspecialchars($message); ?></span>
    </div>
    <?php endif; ?>
    
    <div class="card-container">
        <!-- Transfer Form Card -->
        <div class="balance-card">
            <h2><i class="fas fa-exchange-alt"></i> Transfer to Balance</h2>
            
            <div class="balance-info">
                <div class="balance-item">
                    <span class="balance-label">Referral Balance</span>
                    <span class="balance-value"><?php echo number_format($referralBalance, 2); ?> USD</span>
                </div>
                
                <div class="balance-item">
                    <span class="balance-label">Main Balance</span>
                    <span class="balance-value"><?php echo number_format($user['balance'], 2); ?> USD</span>
                </div>
            </div>
            
            <?php if ($referralBalance > 0): ?>
            <form method="POST" action="" class="transfer-form">
                <div class="form-group">
                    <label for="amount">Transfer Amount</label>
                    <div class="input-group">
                        <span class="input-prefix">$</span>
                        <input type="number" id="amount" name="amount" step="0.01" min="0.01" max="<?php echo $referralBalance; ?>" value="<?php echo $referralBalance; ?>" required>
                    </div>
                    <div class="input-hint">
                        <button type="button" class="max-btn" onclick="document.getElementById('amount').value='<?php echo $referralBalance; ?>'">
                            Transfer All
                        </button>
                    </div>
                </div>
                
                <div class="form-check">
                    <input type="checkbox" id="confirmCheck" required>
                    <label for="confirmCheck">
                        I confirm that I want to transfer my referral balance to my main balance.
                    </label>
                </div>
                
                <button type="submit" name="transfer_balance" class="btn btn-primary">
                    <i class="fas fa-exchange-alt"></i> Transfer to Balance
                </button>
            </form>
            <?php else: ?>
            <div class="no-balance">
                <i class="fas fa-coin"></i>
                <p>You don't have any referral balance to transfer.</p>
                <a href="referrals.php" class="btn btn-outline">
                    <i class="fas fa-users"></i> Earn Referrals
                </a>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Info Card -->
        <div class="info-card">
            <h2><i class="fas fa-info-circle"></i> About Referral System</h2>
            
            <div class="info-section">
                <h3>How It Works</h3>
                <p>Commissions from your referrals accumulate in your referral balance. You can transfer this balance to your main balance and use it as you wish.</p>
            </div>
            
            <div class="info-section">
                <h3>Referral Commission Rates</h3>
                <ul class="info-list">
                    <li><i class="fas fa-check"></i> <strong>10%</strong> on first purchase</li>
                    <li><i class="fas fa-check"></i> <strong>5%</strong> on subsequent purchases</li>
                </ul>
                <p>Commission rates may increase with higher VIP levels.</p>
            </div>
            
            <div class="info-section">
                <h3>Usage</h3>
                <p>You can use your transferred balance for:</p>
                <ul class="info-list">
                    <li><i class="fas fa-check"></i> Purchasing packages</li>
                    <li><i class="fas fa-check"></i> Other services on the platform</li>
                    <li><i class="fas fa-check"></i> Withdrawal to your wallet</li>
                </ul>
            </div>
        </div>
        
        <!-- Transfer History -->
        <?php if (!empty($transferHistory)): ?>
        <div class="history-card">
            <h2><i class="fas fa-history"></i> Transfer History</h2>
            
            <div class="history-list">
                <?php foreach ($transferHistory as $transfer): ?>
                <div class="history-item">
                    <div class="history-details">
                        <div class="history-amount"><?php echo number_format($transfer['amount'], 2); ?> USD</div>
                        <div class="history-date"><?php echo date('d.m.Y H:i', strtotime($transfer['created_at'])); ?></div>
                    </div>
                    <div class="history-status">
                        <span class="status-badge status-completed">Completed</span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<style>
/* Withdraw Referral Page Styles */
.withdraw-referral-page {
    padding: 15px;
}

.page-header {
    margin-bottom: 20px;
}

.page-header h1 {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0 0 10px;
    color: var(--primary-color);
}

.back-btn {
    display: inline-flex;
    align-items: center;
    color: var(--primary-color);
    font-size: 0.9rem;
    text-decoration: none;
}

.back-btn i {
    margin-right: 5px;
}

.card-container {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

/* Cards */
.balance-card,
.info-card,
.history-card {
    background-color: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    padding: 20px;
}

.balance-card h2,
.info-card h2,
.history-card h2 {
    font-size: 1.2rem;
    font-weight: 600;
    margin: 0 0 15px;
    color: #333;
    display: flex;
    align-items: center;
}

.balance-card h2 i,
.info-card h2 i,
.history-card h2 i {
    margin-right: 8px;
    color: var(--primary-color);
}

/* Balance Info */
.balance-info {
    background-color: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 20px;
}

.balance-item {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px dashed #dee2e6;
}

.balance-item:last-child {
    border-bottom: none;
}

.balance-label {
    font-weight: 500;
    color: #333;
}

.balance-value {
    font-weight: 600;
    font-size: 1.1rem;
}

/* Transfer Form */
.transfer-form {
    margin-top: 20px;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
}

.input-group {
    display: flex;
    align-items: center;
}

.input-prefix {
    background-color: #f8f9fa;
    border: 1px solid #ddd;
    border-right: none;
    padding: 12px 15px;
    border-radius: 8px 0 0 8px;
    color: #666;
}

.form-group input {
    flex: 1;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 0 8px 8px 0;
    font-size: 1rem;
    width: 100%;
}

.input-hint {
    display: flex;
    justify-content: flex-end;
    margin-top: 5px;
}

.max-btn {
    background: none;
    border: none;
    color: var(--primary-color);
    font-size: 0.85rem;
    cursor: pointer;
    text-decoration: underline;
}

.form-check {
    display: flex;
    align-items: flex-start;
    margin-bottom: 20px;
}

.form-check input {
    margin-top: 3px;
    margin-right: 10px;
}

.form-check label {
    font-size: 0.9rem;
    line-height: 1.4;
}

/* Buttons */
.btn {
    display: block;
    width: 100%;
    padding: 12px;
    border-radius: 8px;
    font-weight: 500;
    text-align: center;
    cursor: pointer;
    border: none;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn i {
    margin-right: 8px;
}

.btn-primary {
    background-color: var(--primary-color);
    color: white;
}

.btn-outline {
    background-color: transparent;
    border: 1px solid var(--primary-color);
    color: var(--primary-color);
}

/* No Balance */
.no-balance {
    text-align: center;
    padding: 20px 0;
}

.no-balance i {
    font-size: 2.5rem;
    color: #ddd;
    margin-bottom: 15px;
}

.no-balance p {
    margin-bottom: 20px;
    color: var(--text-muted);
}

/* Info Sections */
.info-section {
    margin-bottom: 20px;
}

.info-section:last-child {
    margin-bottom: 0;
}

.info-section h3 {
    font-size: 1rem;
    font-weight: 600;
    margin: 0 0 10px;
    color: #333;
}

.info-section p {
    color: #666;
    margin: 0 0 10px;
    font-size: 0.9rem;
}

.info-list {
    list-style-type: none;
    padding: 0;
    margin: 0 0 10px;
}

.info-list li {
    display: flex;
    align-items: flex-start;
    margin-bottom: 8px;
    font-size: 0.9rem;
}

.info-list li i {
    color: #28c76f;
    margin-right: 8px;
    margin-top: 3px;
}

/* History List */
.history-list {
    display: flex;
    flex-direction: column;
}

.history-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #eee;
}

.history-item:last-child {
    border-bottom: none;
}

.history-amount {
    font-weight: 600;
    margin-bottom: 5px;
}

.history-date {
    font-size: 0.8rem;
    color: var(--text-muted);
}

.status-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
}

.status-completed {
    background-color: #d4edda;
    color: #155724;
}

.status-pending {
    background-color: #fff3cd;
    color: #856404;
}

.status-failed {
    background-color: #f8d7da;
    color: #721c24;
}

/* Alert */
.alert {
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: flex;
    align-items: flex-start;
}

.alert i {
    margin-right: 10px;
    font-size: 1.2rem;
    margin-top: 2px;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
}

.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
}

.alert-warning {
    background-color: #fff3cd;
    color: #856404;
}
</style>

<?php include 'includes/mobile-footer.php'; ?>