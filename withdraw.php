<?php
// mobile/withdraw.php - Withdraw funds mobile page
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'includes/config.php';
require_once 'includes/functions.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=withdraw.php');
    exit;
}

// Load translations
$lang = isset($_SESSION['language']) ? $_SESSION['language'] : 'en';
$translations = [];
$translation_file = "lang/{$lang}/withdraw.json";

if (file_exists($translation_file)) {
    $translations = json_decode(file_get_contents($translation_file), true);
} else {
    // Fallback to English if translation file doesn't exist
    $translation_file = "lang/en/withdraw.json";
    if (file_exists($translation_file)) {
        $translations = json_decode(file_get_contents($translation_file), true);
    }
}

// Translation helper function
function trans($key, $default = '') {
    global $translations;
    return isset($translations[$key]) ? $translations[$key] : ($default ?: $key);
}

// Get user information
$user_id = $_SESSION['user_id'];
$user = getUserDetails($user_id);

// Get payment settings
$settings = getPaymentSettings();
$minWithdraw = $settings['min_withdraw_amount'];
$withdrawFee = 0; // No platform fee as requested
$networkFee = isset($settings['network_fee']) ? $settings['network_fee'] : 1; // USDT TRC20 network fee in USD

// Get user's saved TRC20 address if exists
$savedTRC20Address = '';
$conn = $GLOBALS['db']->getConnection();
$stmt = $conn->prepare("SELECT trc20_address FROM users WHERE id = ? AND trc20_address IS NOT NULL AND trc20_address != ''");
if ($stmt) {
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $savedTRC20Address = $result->fetch_assoc()['trc20_address'];
    }
}

// Get recent withdrawals
$withdrawals = getUserWithdrawals($user_id, 5);

// Check security settings
$securityCheck = true;
$securityMessage = '';
$showSetPasswordForm = false;

// Get withdrawal security settings from database
$stmt = $conn->prepare("SELECT withdraw_verify, withdraw_password FROM users WHERE id = ?");
if ($stmt === false) {
    die("SQL Error: " . $conn->error);
}

$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$security = $result->fetch_assoc();

// Check if user has withdrawal security set
if ($security['withdraw_verify'] == 0 || empty($security['withdraw_password'])) {
    $securityCheck = false;
    $showSetPasswordForm = true;
    $securityMessage = trans('security.set_password_required', 'You need to set a security password for withdrawal operations.');
}

// Password setting process
if (isset($_POST['set_withdraw_password'])) {
    $password = $_POST['new_withdraw_password'];
    $confirmPassword = $_POST['confirm_withdraw_password'];
    
    if (strlen($password) < 6) {
        $securityMessage = trans('security.password_min_length', 'Password must be at least 6 characters long.');
    } elseif ($password !== $confirmPassword) {
        $securityMessage = trans('security.passwords_dont_match', 'Passwords do not match. Please try again.');
    } else {
        // Hash and save password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Save user preference (password or email verification)
        $verifyMethod = isset($_POST['verify_method']) ? (int)$_POST['verify_method'] : 1;
        
        $stmtUpdate = $conn->prepare("UPDATE users SET withdraw_password = ?, withdraw_verify = ? WHERE id = ?");
        if ($stmtUpdate) {
            $stmtUpdate->bind_param('sii', $hashedPassword, $verifyMethod, $user_id);
            
            if ($stmtUpdate->execute()) {
                // Security settings updated, refresh page
                header('Location: withdraw.php');
                exit;
            } else {
                $securityMessage = trans('security.save_error', 'An error occurred while saving the password. Please try again.');
            }
        }
    }
}

// Check for success parameter
$withdrawSuccess = false;
if (isset($_GET['success']) && $_GET['success'] == '1') {
    $withdrawSuccess = true;
}

// Set page title
$page_title = trans('withdraw.page_title', 'Withdraw Funds');
include 'includes/mobile-header.php';

// Direct English translations embedded in PHP
$translations = [
    'withdraw' => [
        'page_title' => 'Withdraw Funds',
        'available_balance' => 'Available Balance',
        'usdt_withdrawal' => 'USDT TRC-20 Withdrawal',
        'amount' => 'Withdrawal Amount (USDT)',
        'min' => 'Minimum',
        'max' => 'Maximum',
        'min_withdrawal' => 'Minimum withdrawal amount',
        'trc20_address' => 'TRC20 USDT Wallet Address',
        'saved_address' => 'Saved Address',
        'address_hint' => 'Please enter your correct TRC20 network USDT wallet address.',
        'withdrawal_amount' => 'Withdrawal Amount',
        'network_fee' => 'Network Fee',
        'total_amount' => 'Total Amount',
        'important' => 'Important',
        'address_warning' => 'Please make sure you enter the correct TRC20 wallet address. Payments sent to incorrect addresses cannot be recovered.',
        'create_request' => 'Create Withdrawal Request',
        'information' => 'Information',
        'vip_level' => 'VIP Level',
        'daily_limit' => 'Daily Withdrawal Limit',
        'times_per_day' => 'times/day',
        'payment_method' => 'Payment Method',
        'processing_time' => 'Processing Time',
        'processing_time_value' => 'Usually within 24 hours',
        'recent_withdrawals' => 'Your Recent Withdrawals',
        'view_all' => 'View All Withdrawals',
        'status_completed' => 'Completed',
        'status_pending' => 'Pending',
        'status_processing' => 'Processing',
        'status_failed' => 'Failed',
        'success_title' => 'Withdrawal Request Received!',
        'success_message' => 'Your request will be processed after review. You can track the status in "My Transactions".',
        'my_transactions' => 'My Transactions',
        'new_request' => 'New Request',
        'vip_required_title' => 'VIP Membership Required',
        'vip_required_message' => 'You need to be a VIP member to make withdrawals. You can use this feature by purchasing a VIP membership.',
        'upgrade_to_vip' => 'Purchase VIP Membership',
        'withdrawal_info' => 'Withdrawal requests are processed after security verification and usually completed within 24 hours.'
    ],
    'security' => [
        'set_title' => 'Set Withdrawal Security',
        'set_password_required' => 'You need to set a security password for withdrawal operations.',
        'password' => 'Withdrawal Security Password',
        'password_min_chars' => 'Minimum 6 characters',
        'confirm_password' => 'Re-enter Password',
        'confirm_password_placeholder' => 'Re-enter your password',
        'verification_method' => 'Verification Method',
        'password_only' => 'Password verification only',
        'password_email' => 'Password and email verification (2FA)',
        'info' => 'Information',
        '2fa_info' => 'Two-factor authentication (2FA) provides extra security for your account. For each withdrawal, you need to enter both your password and a one-time code sent to your email.',
        'save_settings' => 'Save Security Settings',
        'password_min_length' => 'Password must be at least 6 characters long.',
        'passwords_dont_match' => 'Passwords do not match. Please try again.',
        'save_error' => 'An error occurred while saving the password. Please try again.',
        'security' => 'Security',
        'enhanced' => 'Enhanced (2FA)',
        'standard' => 'Standard'
    ]
];

// Translation helper function that uses our embedded translations
function t($key, $default = '') {
    global $translations;
    $parts = explode('.', $key);
    if (count($parts) === 2 && isset($translations[$parts[0]][$parts[1]])) {
        return $translations[$parts[0]][$parts[1]];
    }
    return $default ?: $key;
}
?>

<div class="withdraw-page">
    <h1 class="page-title"><?= t('withdraw.page_title', 'Withdraw Funds') ?></h1>
    
    <?php if ($withdrawSuccess): ?>
    <!-- Success Message -->
    <div class="success-container">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <h2><?= t('withdraw.success_title', 'Withdrawal Request Received!') ?></h2>
        <p><?= t('withdraw.success_message', 'Your request will be processed after review. You can track the status in "My Transactions".') ?></p>
        
        <div class="action-buttons">
            <a href="transactions.php?filter=withdraw" class="btn btn-primary">
                <i class="fas fa-list"></i> <?= t('withdraw.my_transactions', 'My Transactions') ?>
            </a>
            <a href="withdraw.php" class="btn btn-outline">
                <i class="fas fa-plus"></i> <?= t('withdraw.new_request', 'New Request') ?>
            </a>
        </div>
    </div>
    <?php elseif ($showSetPasswordForm): ?>
    <!-- Security Setup Required -->
    <div class="card withdraw-form-card">
        <h2><i class="fas fa-shield-alt"></i> <?= t('security.set_title', 'Set Withdrawal Security') ?></h2>
        
        <?php if (!empty($securityMessage)): ?>
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i>
            <?= $securityMessage ?>
        </div>
        <?php endif; ?>
        
        <form method="POST" action="withdraw.php" id="securityForm">
            <div class="form-group">
                <label for="new_withdraw_password"><?= t('security.password', 'Withdrawal Security Password') ?></label>
                <input type="password" id="new_withdraw_password" name="new_withdraw_password" placeholder="<?= t('security.password_min_chars', 'Minimum 6 characters') ?>" required>
            </div>
            
            <div class="form-group">
                <label for="confirm_withdraw_password"><?= t('security.confirm_password', 'Re-enter Password') ?></label>
                <input type="password" id="confirm_withdraw_password" name="confirm_withdraw_password" placeholder="<?= t('security.confirm_password_placeholder', 'Re-enter your password') ?>" required>
            </div>
            
            <div class="form-group">
                <label><?= t('security.verification_method', 'Verification Method') ?></label>
                <div class="radio-group">
                    <label class="radio-label">
                        <input type="radio" name="verify_method" value="1" checked>
                        <span><?= t('security.password_only', 'Password verification only') ?></span>
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="verify_method" value="2">
                        <span><?= t('security.password_email', 'Password and email verification (2FA)') ?></span>
                    </label>
                </div>
            </div>
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <strong><?= t('security.info', 'Information') ?>:</strong> <?= t('security.2fa_info', 'Two-factor authentication (2FA) provides extra security for your account. For each withdrawal, you need to enter both your password and a one-time code sent to your email.') ?>
            </div>
            
            <button type="submit" name="set_withdraw_password" class="btn btn-primary btn-block">
                <i class="fas fa-save"></i> <?= t('security.save_settings', 'Save Security Settings') ?>
            </button>
        </form>
    </div>
    <?php else: ?>
    <!-- Main Content -->
    <div class="withdraw-container">
        <!-- Balance Card -->
        <div class="balance-card">
            <div class="balance-label"><?= t('withdraw.available_balance', 'Available Balance') ?></div>
            <div class="balance-value"><?= number_format($user['balance'], 2) ?> USDT</div>
        </div>
        <?php if ($user['vip_level'] <= 0): ?>
        <!-- Information for non-VIP users -->
        <div class="card withdraw-form-card">
            <h2><i class="fas fa-lock"></i> <?= t('withdraw.vip_required_title', 'VIP Membership Required') ?></h2>
            
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <?= t('withdraw.vip_required_message', 'You need to be a VIP member to make withdrawals. You can use this feature by purchasing a VIP membership.') ?>
            </div>
            
            <a href="vip.php" class="btn btn-primary btn-block">
                <i class="fas fa-crown"></i> <?= t('withdraw.upgrade_to_vip', 'Purchase VIP Membership') ?>
            </a>
        </div>
        <?php else: ?>
        <!-- Withdraw Form -->
        <div class="card withdraw-form-card">
            <h2><i class="fas fa-money-bill-wave"></i> <?= t('withdraw.usdt_withdrawal', 'USDT TRC-20 Withdrawal') ?></h2>
            
            <form method="POST" action="verify_withdraw.php" id="withdrawForm">
                <div class="form-group">
                    <label for="amount"><?= t('withdraw.amount', 'Withdrawal Amount (USDT)') ?></label>
                    <div class="input-group">
                        <span class="input-prefix">$</span>
                        <input type="number" id="amount" name="amount" min="<?= $minWithdraw ?>" step="1" placeholder="<?= t('withdraw.min', 'Minimum') ?>: <?= $minWithdraw ?> USDT" required
                               value="<?= isset($_SESSION['withdraw_data']['amount']) ? htmlspecialchars($_SESSION['withdraw_data']['amount']) : '' ?>">
                    </div>
                    <div class="form-hint"><?= t('withdraw.min_withdrawal', 'Minimum withdrawal amount') ?>: <?= number_format($minWithdraw, 2) ?> USDT</div>
                </div>
                
                <div class="amount-shortcuts">
                    <button type="button" class="amount-btn" data-amount="<?= $minWithdraw ?>"><?= $minWithdraw ?></button>
                    <button type="button" class="amount-btn" data-amount="50">50</button>
                    <button type="button" class="amount-btn" data-amount="100">100</button>
                    <button type="button" class="amount-btn" data-amount="max"><?= t('withdraw.max', 'Maximum') ?></button>
                </div>
                
                <div class="form-group">
                    <label for="trc20_address"><?= t('withdraw.trc20_address', 'TRC20 USDT Wallet Address') ?></label>
                    <?php if (!empty($savedTRC20Address)): ?>
                    <div class="saved-address">
                        <input type="text" id="trc20_address" name="trc20_address" value="<?= htmlspecialchars($savedTRC20Address) ?>" required>
                        <div class="saved-address-label">
                            <i class="fas fa-bookmark"></i> <?= t('withdraw.saved_address', 'Saved Address') ?>
                        </div>
                    </div>
                    <?php else: ?>
                    <input type="text" id="trc20_address" name="trc20_address" placeholder="TLkh4TXUvV6PxdC..." required
                           value="<?= isset($_SESSION['withdraw_data']['trc20_address']) ? htmlspecialchars($_SESSION['withdraw_data']['trc20_address']) : '' ?>">
                    <?php endif; ?>
                    <div class="form-hint"><?= t('withdraw.address_hint', 'Please enter your correct TRC20 network USDT wallet address.') ?></div>
                </div>
                
                <div class="fee-calculator">
                    <div class="fee-row">
                        <span><?= t('withdraw.withdrawal_amount', 'Withdrawal Amount') ?>:</span>
                        <span id="withdrawAmount">0.00 USDT</span>
                    </div>
                    <div class="fee-row">
                        <span><?= t('withdraw.network_fee', 'Network Fee') ?> (TRC20):</span>
                        <span id="networkFee"><?= number_format($networkFee, 2) ?> USDT</span>
                    </div>
                    <div class="fee-row total">
                        <span><?= t('withdraw.total_amount', 'Total Amount') ?>:</span>
                        <span id="totalWithdraw">0.00 USDT</span>
                    </div>
                </div>
                
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong><?= t('withdraw.important', 'Important') ?>:</strong> <?= t('withdraw.address_warning', 'Please make sure you enter the correct TRC20 wallet address. Payments sent to incorrect addresses cannot be recovered.') ?>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-paper-plane"></i> <?= t('withdraw.create_request', 'Create Withdrawal Request') ?>
                </button>
            </form>
        </div>
        <?php endif; ?>
        
        <!-- Information Card -->
        <div class="card info-card">
            <h3><i class="fas fa-info-circle"></i> <?= t('withdraw.information', 'Information') ?></h3>
            
            <ul class="info-list">
                <li>
                    <i class="fas fa-check-circle"></i>
                    <div>
                        <strong><?= t('withdraw.min_withdrawal', 'Minimum Withdrawal Amount') ?>:</strong>
                        <span><?= number_format($minWithdraw, 2) ?> USDT</span>
                    </div>
                </li>
                <li>
                    <i class="fas fa-crown"></i>
                    <div>
                        <strong><?= t('withdraw.vip_level', 'VIP Level') ?>:</strong>
                        <span>Level <?= $user['vip_level'] ?></span>
                    </div>
                </li>
                <li>
                    <i class="fas fa-calendar-day"></i>
                    <div>
                        <strong><?= t('withdraw.daily_limit', 'Daily Withdrawal Limit') ?>:</strong>
                        <span>
                            <?php
                            $dailyLimit = 1; // Base limit
                            if ($user['vip_level'] >= 2) $dailyLimit = 2;
                            if ($user['vip_level'] >= 3) $dailyLimit = 3;
                            echo $dailyLimit . ' ' . t('withdraw.times_per_day', 'times/day');
                            ?>
                        </span>
                    </div>
                </li>
                <li>
                    <i class="fas fa-check-circle"></i>
                    <div>
                        <strong><?= t('withdraw.payment_method', 'Payment Method') ?>:</strong>
                        <span>USDT (TRC-20)</span>
                    </div>
                </li>
                <li>
                    <i class="fas fa-check-circle"></i>
                    <div>
                        <strong><?= t('withdraw.processing_time', 'Processing Time') ?>:</strong>
                        <span><?= t('withdraw.processing_time_value', 'Usually within 24 hours') ?></span>
                    </div>
                </li>
                <li>
                    <i class="fas fa-check-circle"></i>
                    <div>
                        <strong><?= t('withdraw.network_fee', 'Network Fee') ?>:</strong>
                        <span><?= number_format($networkFee, 2) ?> USDT</span>
                    </div>
                </li>
                <li>
                    <i class="fas fa-shield-alt"></i>
                    <div>
                        <strong><?= t('security.security', 'Security') ?>:</strong>
                        <span><?= $security['withdraw_verify'] == 2 ? t('security.enhanced', 'Enhanced (2FA)') : t('security.standard', 'Standard') ?></span>
                    </div>
                </li>
            </ul>
            
            <div class="alert alert-info">
                <i class="fas fa-exclamation-triangle"></i>
                <strong><?= t('withdraw.important', 'Important') ?>:</strong> <?= t('withdraw.withdrawal_info', 'Withdrawal requests are processed after security verification and usually completed within 24 hours.') ?>
            </div>
        </div>
        
        <!-- Recent Withdrawals -->
        <?php if(count($withdrawals) > 0): ?>
        <div class="card recent-withdrawals-card">
            <h3><i class="fas fa-history"></i> <?= t('withdraw.recent_withdrawals', 'Your Recent Withdrawals') ?></h3>
            
            <div class="withdrawals-list">
                <?php foreach($withdrawals as $withdrawal): ?>
                <div class="withdrawal-item">
                    <div class="withdrawal-details">
                        <div class="withdrawal-amount"><?= number_format($withdrawal['amount'], 2) ?> USDT</div>
                        <div class="withdrawal-date"><?= date('d.m.Y H:i', strtotime($withdrawal['created_at'])) ?></div>
                    </div>
                    <div class="withdrawal-status">
                        <?php if($withdrawal['status'] == 'completed'): ?>
                            <span class="status-badge status-completed"><?= t('withdraw.status_completed', 'Completed') ?></span>
                        <?php elseif($withdrawal['status'] == 'pending'): ?>
                            <span class="status-badge status-pending"><?= t('withdraw.status_pending', 'Pending') ?></span>
                        <?php elseif($withdrawal['status'] == 'processing'): ?>
                            <span class="status-badge status-processing"><?= t('withdraw.status_processing', 'Processing') ?></span>
                        <?php else: ?>
                            <span class="status-badge status-failed"><?= t('withdraw.status_failed', 'Failed') ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <a href="transactions.php?filter=withdraw" class="btn btn-outline btn-sm btn-block">
                <i class="fas fa-list"></i> <?= t('withdraw.view_all', 'View All Withdrawals') ?>
            </a>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<style>
:root {
    --primary-color: #3F88F6;
    --primary-dark: #2d6ed6;
    --primary-color-rgb: 63, 136, 246;
    --danger-color: #dc3545;
    --warning-color: #ffc107;
    --success-color: #28c76f;
    --info-color: #17a2b8;
    --gray-light: #f8f9fa;
    --gray: #6e6b7b;
    --border-color: #ddd;
}

.withdraw-page {
    padding: 15px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #333;
    background-color: #f8f9fa;
}

.page-title {
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 20px;
    color: var(--primary-color);
}

.withdraw-container {
    display: flex;
    flex-direction: column;
    gap: 20px;
    max-width: 800px;
    margin: 0 auto;
}

/* Balance Card */
.balance-card {
    background-color: var(--primary-color);
    color: white;
    border-radius: 12px;
    padding: 20px;
    text-align: center;
    box-shadow: 0 4px 15px rgba(var(--primary-color-rgb), 0.3);
}

.balance-label {
    font-size: 1rem;
    opacity: 0.9;
    margin-bottom: 10px;
}

.balance-value {
    font-size: 2rem;
    font-weight: 700;
}

/* Cards */
.card {
    background-color: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.card h2 {
    font-size: 1.3rem;
    font-weight: 600;
    margin: 0 0 20px;
    display: flex;
    align-items: center;
}

.card h2 i {
    margin-right: 10px;
    color: var(--primary-color);
}

.card h3 {
    font-size: 1.1rem;
    font-weight: 600;
    margin: 0 0 15px;
    display: flex;
    align-items: center;
}

.card h3 i {
    margin-right: 10px;
    color: var(--primary-color);
}

/* Form Styles */
.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    font-size: 0.9rem;
}

.input-group {
    display: flex;
    align-items: center;
}

.input-prefix {
    background-color: var(--gray-light);
    border: 1px solid var(--border-color);
    border-right: none;
    padding: 12px 15px;
    border-radius: 8px 0 0 8px;
    color: var(--gray);
}

.form-group input {
    flex: 1;
    padding: 12px 15px;
    border: 1px solid var(--border-color);
    border-radius: 0 8px 8px 0;
    font-size: 1rem;
    width: 100%;
    outline: none;
    transition: border-color 0.3s;
}

.form-group input:focus {
    border-color: var(--primary-color);
}

.form-group input#trc20_address {
    border-radius: 8px;
}

.saved-address {
    position: relative;
}

.saved-address-label {
    position: absolute;
    top: -10px;
    right: 10px;
    background-color: var(--primary-color);
    color: white;
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 0.7rem;
    font-weight: 500;
}

.form-hint {
    font-size: 0.8rem;
    color: var(--gray);
    margin-top: 5px;
}

/* Amount Shortcuts */
.amount-shortcuts {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.amount-btn {
    background-color: var(--gray-light);
    border: 1px solid var(--border-color);
    border-radius: 20px;
    padding: 8px 15px;
    font-size: 0.9rem;
    color: #333;
    cursor: pointer;
    transition: all 0.2s;
}

.amount-btn:hover {
    background-color: #e9ecef;
}

.amount-btn:active {
    transform: scale(0.98);
}

/* Fee Calculator */
.fee-calculator {
    background-color: var(--gray-light);
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 20px;
}

.fee-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
    font-size: 0.9rem;
}

.fee-row.total {
    margin-top: 8px;
    padding-top: 8px;
    border-top: 1px dashed var(--border-color);
    font-weight: 600;
}

/* Alerts */
.alert {
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 0.9rem;
    display: flex;
    align-items: flex-start;
}

.alert i {
    margin-right: 10px;
    font-size: 1.1rem;
    margin-top: 2px;
}

.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
}

.alert-warning {
    background-color: #fff3cd;
    color: #856404;
}

.alert-info {
    background-color: #d1ecf1;
    color: #0c5460;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
}

/* Buttons */
/* Buttons */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 12px 20px;
    border-radius: 8px;
    font-weight: 500;
    text-decoration: none;
    cursor: pointer;
    transition: background-color 0.3s, transform 0.1s;
    border: none;
    font-size: 1rem;
}

.btn:active {
    transform: translateY(1px);
}

.btn i {
    margin-right: 8px;
}

.btn-block {
    display: flex;
    width: 100%;
}

.btn-primary {
    background-color: var(--primary-color);
    color: white;
}

.btn-primary:hover {
    background-color: var(--primary-dark);
}

.btn-outline {
    background-color: transparent;
    border: 1px solid var(--primary-color);
    color: var(--primary-color);
}

.btn-outline:hover {
    background-color: rgba(var(--primary-color-rgb), 0.05);
}

.btn-sm {
    padding: 8px 15px;
    font-size: 0.9rem;
}

.btn-link {
    background: none;
    color: var(--primary-color);
    padding: 8px;
    border: none;
    text-decoration: underline;
}

.btn-link:hover {
    text-decoration: none;
}

.mt-3 {
    margin-top: 15px;
}

.text-center {
    text-align: center;
}

.d-inline {
    display: inline;
}

/* Radio Group */
.radio-group {
    margin-top: 10px;
}

.radio-label {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
    cursor: pointer;
}

.radio-label input[type="radio"] {
    margin-right: 10px;
}

/* Info List */
.info-list {
    list-style-type: none;
    padding: 0;
    margin: 0 0 20px;
}

.info-list li {
    display: flex;
    align-items: flex-start;
    margin-bottom: 15px;
}

.info-list li i {
    color: var(--success-color);
    margin-right: 10px;
    margin-top: 2px;
}

.info-list li div {
    flex: 1;
}

.info-list li strong {
    display: block;
    margin-bottom: 3px;
}

.info-list li span {
    font-size: 0.9rem;
    color: var(--gray);
}

/* Recent Withdrawals */
.withdrawals-list {
    margin-bottom: 15px;
}

.withdrawal-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 0;
    border-bottom: 1px solid #eee;
}

.withdrawal-item:last-child {
    border-bottom: none;
}

.withdrawal-amount {
    font-weight: 600;
    margin-bottom: 5px;
}

.withdrawal-date {
    font-size: 0.8rem;
    color: var(--gray);
}

.status-badge {
    display: inline-block;
    padding: 5px 10px;
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

.status-processing {
    background-color: #d1ecf1;
    color: #0c5460;
}

.status-failed {
    background-color: #f8d7da;
    color: #721c24;
}

/* Success Container */
.success-container {
    text-align: center;
    background-color: white;
    border-radius: 12px;
    padding: 30px 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    max-width: 600px;
    margin: 0 auto;
}

.success-icon {
    font-size: 4rem;
    color: var(--success-color);
    margin-bottom: 20px;
}

.success-container h2 {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 15px;
}

.success-container p {
    color: var(--gray);
    margin-bottom: 25px;
}

.action-buttons {
    display: flex;
    gap: 10px;
    justify-content: center;
}

.withdraw-form-card {
    max-width: 600px;
    margin: 0 auto;
}

@media (max-width: 500px) {
    .action-buttons {
        flex-direction: column;
    }
    
    .page-title {
        font-size: 1.5rem;
    }
    
    .balance-value {
        font-size: 1.8rem;
    }
    
    .card {
        padding: 15px;
    }
    
    .form-group input, 
    .input-prefix {
        padding: 10px 12px;
    }
    
    .btn {
        padding: 10px 16px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Amount buttons
    const amountBtns = document.querySelectorAll('.amount-btn');
    const amountInput = document.getElementById('amount');
    
    // Get settings from hidden inputs
    const maxBalance = parseFloat(document.getElementById('user-balance')?.value || 0);
    const networkFee = parseFloat(document.getElementById('network-fee')?.value || 1);
    const minWithdraw = parseFloat(document.getElementById('min-withdraw')?.value || 10);
    
    // Fee calculator elements
    const withdrawAmountText = document.getElementById('withdrawAmount');
    const networkFeeText = document.getElementById('networkFee');
    const totalWithdrawText = document.getElementById('totalWithdraw');
    
    // Fee calculation function - No platform fee
    function calculateFee() {
        const amount = parseFloat(amountInput.value) || 0;
        const total = amount + networkFee; // Only add network fee, no percentage fee
        
        if (withdrawAmountText) withdrawAmountText.textContent = amount.toFixed(2) + ' USDT';
        if (networkFeeText) networkFeeText.textContent = networkFee.toFixed(2) + ' USDT';
        if (totalWithdrawText) totalWithdrawText.textContent = total.toFixed(2) + ' USDT';
    }
    
    // Amount button event listeners
    if (amountBtns) {
        amountBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                if (this.dataset.amount === 'max') {
                    // Calculate maximum withdrawable amount accounting for network fee only
                    const maxWithdrawAmount = Math.max(0, maxBalance - networkFee);
                    amountInput.value = Math.floor(maxWithdrawAmount * 100) / 100;
                } else {
                    const amount = parseFloat(this.dataset.amount);
                    amountInput.value = amount.toFixed(2);
                }
                
                calculateFee();
            });
        });
    }
    
    // Update fee on amount input
    if (amountInput) {
        amountInput.addEventListener('input', calculateFee);
        // Calculate fee on page load
        calculateFee();
    }
    
    // Password confirmation check
    const newPasswordInput = document.getElementById('new_withdraw_password');
    const confirmPasswordInput = document.getElementById('confirm_withdraw_password');
    const securityForm = document.getElementById('securityForm');
    
    if (securityForm && newPasswordInput && confirmPasswordInput) {
        securityForm.addEventListener('submit', function(e) {
            if (newPasswordInput.value !== confirmPasswordInput.value) {
                e.preventDefault();
                showAlert('Passwords do not match. Please check again.');
                return false;
            }
            
            if (newPasswordInput.value.length < 6) {
                e.preventDefault();
                showAlert('Password must be at least 6 characters long.');
                return false;
            }
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            }
        });
    }
    
    // Withdrawal form validation
    const withdrawForm = document.getElementById('withdrawForm');
    const trc20AddressInput = document.getElementById('trc20_address');
    
    if (withdrawForm && amountInput && trc20AddressInput) {
        withdrawForm.addEventListener('submit', function(e) {
            const amount = parseFloat(amountInput.value) || 0;
            const address = trc20AddressInput.value.trim();
            
            let hasError = false;
            
            // Amount validation
            if (amount < minWithdraw) {
                e.preventDefault();
                showAlert(`Minimum withdrawal amount must be ${minWithdraw} USDT.`);
                hasError = true;
            }
            
            // Calculate total amount with fees (network fee only, no platform fee)
            const totalAmount = amount + networkFee;
            
            if (totalAmount > maxBalance) {
                e.preventDefault();
                showAlert(`Insufficient balance. Total amount (including network fee): ${totalAmount.toFixed(2)} USDT`);
                hasError = true;
            }
            
            // TRC20 address validation
            if (address === '') {
                e.preventDefault();
                showAlert('TRC20 wallet address is required.');
                hasError = true;
            } else if (address.length !== 34 || address.charAt(0) !== 'T') {
                e.preventDefault();
                showAlert('Invalid TRC20 wallet address.');
                hasError = true;
            }
            
            if (hasError) {
                return false;
            }
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            }
        });
    }
    
    // Show alert message
    function showAlert(message) {
        // Check if there's already an alert
        const existingAlert = document.querySelector('.alert-danger');
        
        if (existingAlert) {
            existingAlert.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
        } else {
            // Create new alert
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-danger';
            alertDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
            
            // Insert at the beginning of the form's parent
            const form = document.querySelector('form');
            if (form && form.parentNode) {
                form.parentNode.insertBefore(alertDiv, form);
            }
        }
        
        // Scroll to alert
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }
});
</script>

<?php include 'includes/mobile-footer.php'; ?>