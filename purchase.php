<?php
// purchase.php - VIP veya Mining paketi satın alma sayfası
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

// Get user ID
$user_id = $_SESSION['user_id'];

// Get database connection
$conn = $GLOBALS['db']->getConnection();

// Get package type and ID from URL
$package_type = isset($_GET['type']) ? $_GET['type'] : '';
$package_id = isset($_GET['package_id']) ? intval($_GET['package_id']) : 0;

// Validate package type
if (!in_array($package_type, ['vip', 'mining'])) {
    header('Location: packages.php');
    exit;
}

// Get user details
$user = getUserDetails($user_id);
if (!$user) {
    header('Location: login.php');
    exit;
}

// Initialize variables
$package = null;
$error_message = '';
$success_message = '';
$current_vip_level = isset($user['vip_level']) ? $user['vip_level'] : 0;
$current_balance = isset($user['balance']) ? $user['balance'] : 0;
$confirm_action = false;

// Get package details before processing POST
if ($package_type === 'vip') {
    $stmt = $conn->prepare("SELECT * FROM vip_packages WHERE id = ? AND is_active = 1");
    $stmt->bind_param('i', $package_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $package = $result->fetch_assoc();
    } else {
        $error_message = "Invalid VIP package selection.";
    }
} elseif ($package_type === 'mining') {
    $stmt = $conn->prepare("SELECT * FROM mining_packages WHERE id = ? AND is_active = 1");
    $stmt->bind_param('i', $package_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $package = $result->fetch_assoc();
    } else {
        $error_message = "Invalid Mining package selection.";
    }
}

// If package not found, redirect
if (!$package) {
    header('Location: packages.php?type=' . $package_type);
    exit;
}

// Calculate price
$package_price = ($package_type === 'vip') ? 
    (isset($package['price']) ? $package['price'] : 0) : 
    (isset($package['package_price']) ? $package['package_price'] : 0);
$can_afford = ($current_balance >= $package_price);

// Process POST request for confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_purchase'])) {
    $confirm_action = true;
    
    $post_package_id = isset($_POST['package_id']) ? intval($_POST['package_id']) : 0;
    $post_package_type = isset($_POST['package_type']) ? $_POST['package_type'] : '';
    
    if ($post_package_id === $package_id && $post_package_type === $package_type) {
        // Begin transaction
        $conn->begin_transaction();
        
        try {
            if ($package_type === 'vip') {
                // Check if user already has this VIP level or higher
                if ($current_vip_level >= $package_id) {
                    throw new Exception("You already have this VIP level or higher.");
                }
                
                // Check if user has enough balance
                if ($current_balance < $package_price) {
                    throw new Exception("Insufficient balance. You need " . number_format($package_price, 2) . " USDT to purchase this package.");
                }
                
                // Update user's VIP level and deduct balance
                $stmt = $conn->prepare("UPDATE users SET vip_level = ?, balance = balance - ? WHERE id = ?");
                $stmt->bind_param('idi', $package_id, $package_price, $user_id);
                
                if (!$stmt->execute()) {
                    throw new Exception("Failed to update VIP level: " . $conn->error);
                }
                
                // Record transaction
                $stmt = $conn->prepare("INSERT INTO transactions (user_id, type, amount, description, status, before_balance, after_balance) VALUES (?, 'vip', ?, ?, 'completed', ?, ?)");
                $package_name = isset($package['name']) ? $package['name'] : 'Unknown VIP Package';
                $description = "Purchase of VIP package: " . $package_name;
                $after_balance = $current_balance - $package_price;
                $stmt->bind_param('idsdd', $user_id, $package_price, $description, $current_balance, $after_balance);
                
                if (!$stmt->execute()) {
                    throw new Exception("Failed to record transaction: " . $conn->error);
                }
                
                $success_message = "Successfully purchased VIP package: " . $package_name;
                
            } elseif ($package_type === 'mining') {
                // Check if user has enough balance
                if ($current_balance < $package_price) {
                    throw new Exception("Insufficient balance. You need " . number_format($package_price, 2) . " USDT to purchase this package.");
                }
                
                // Check if user's VIP level allows mining purchase
                $vip_details = getVipDetails($current_vip_level);
                if (!isset($vip_details['miningbuy']) || $vip_details['miningbuy'] != 1) {
                    throw new Exception("Your VIP level does not allow mining package purchases.");
                }
                
                // Deduct user's balance
                $stmt = $conn->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
                $stmt->bind_param('di', $package_price, $user_id);
                
                if (!$stmt->execute()) {
                    throw new Exception("Failed to update balance: " . $conn->error);
                }
                
                // Create user mining package record
                $stmt = $conn->prepare("INSERT INTO user_mining_packages (user_id, package_id, status, purchase_date, expiry_date) VALUES (?, ?, 'active', NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY))");
                $stmt->bind_param('ii', $user_id, $package_id);
                
                if (!$stmt->execute()) {
                    throw new Exception("Failed to create mining package record: " . $conn->error);
                }
                
                // Record transaction
                $stmt = $conn->prepare("INSERT INTO transactions (user_id, type, amount, description, status, before_balance, after_balance) VALUES (?, 'mining', ?, ?, 'completed', ?, ?)");
                $package_name = isset($package['name']) ? $package['name'] : 'Unknown Mining Package';
                $description = "Purchase of Mining package: " . $package_name;
                $after_balance = $current_balance - $package_price;
                $stmt->bind_param('idsdd', $user_id, $package_price, $description, $current_balance, $after_balance);
                
                if (!$stmt->execute()) {
                    throw new Exception("Failed to record transaction: " . $conn->error);
                }
                
                $success_message = "Successfully purchased Mining package: " . $package_name;
            }
            
            // Commit transaction
            $conn->commit();
            
            // Log user action
            if (function_exists('logUserAction')) {
                $package_name = isset($package['name']) ? $package['name'] : 'Unknown Package';
                $description = "Purchase of " . ucfirst($package_type) . " package: " . $package_name;
                logUserAction($user_id, 'package_purchase', $description, $package_id, $package_type);
            }
            
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            $error_message = $e->getMessage();
        }
    } else {
        $error_message = "Invalid package information.";
    }
}

// Check if user's VIP level allows mining purchase (for mining packages)
if ($package_type === 'mining') {
    $vip_details = getVipDetails($current_vip_level);
    if (!isset($vip_details['miningbuy']) || $vip_details['miningbuy'] != 1) {
        $error_message = "Your VIP level does not allow mining package purchases. Please upgrade your VIP level first.";
    }
}

// Set page title
$page_title = 'Purchase ' . ucfirst($package_type) . ' Package';

include 'includes/mobile-header.php';
?>

<div class="purchase-page">
    <div class="page-header">
        <h1>Purchase <?= ucfirst($package_type) ?> Package</h1>
        <a href="packages.php?type=<?= $package_type ?>" class="back-link"><i class="fas fa-arrow-left"></i> Back to Packages</a>
    </div>
    
    <?php if ($error_message): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i>
        <?= $error_message ?>
    </div>
    <?php endif; ?>
    
    <?php if ($success_message): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <?= $success_message ?>
        <div class="alert-actions">
            <a href="index.php" class="btn btn-outline">Go to Dashboard</a>
            <a href="packages.php" class="btn btn-primary">View All Packages</a>
        </div>
    </div>
    <?php else: ?>
    
    <div class="purchase-card">
        <div class="package-details">
            <div class="package-header <?= $package_type ?>">
                <h2><?= isset($package['name']) ? htmlspecialchars($package['name']) : 'Package' ?></h2>
                <div class="package-price">
                    <?= number_format($package_price, 2) ?> USDT
                </div>
            </div>
            
            <div class="package-body">
                <?php if ($package_type === 'vip'): ?>
                <ul class="feature-list">
                <?php 
                                            // Günlük kazanç - stage1_base_reward from game_settings
                                            $dailyReward = isset($gameSettings[$package['id']]['stage1_base_reward']) ? 
                                                floatval($gameSettings[$package['id']]['stage1_base_reward']) : 
                                                (isset($gameSettings[0]['stage1_base_reward']) ? floatval($gameSettings[0]['stage1_base_reward']) : 5.0);
                                            ?>
                                            <li>
                                                <i class="fas fa-coins"></i>  
                                                <span><?= number_format($dailyReward, 2) ?> USDT Daily Earning</span>
                                            </li>
                                            <li>
                                                <i class="fas fa-percentage"></i>  
                                                <span><?= number_format($package['game_max_win_chance'] * 100, 1) ?>% Win Chance</span>
                                            </li>
                                            <?php 
                                            // Mining alınabilir durumu
                                            $canBuyMining = isset($package['miningbuy']) ? intval($package['miningbuy']) : 0;
                                            ?>
                                            <li class="<?= $canBuyMining ? 'available' : 'unavailable' ?>">
                                                <i class="fas fa-microchip"></i>  
                                                <span>Mining Available</span>
                                            </li>
                  
                    
                    <?php if (isset($package['tgsupport']) && $package['tgsupport'] == 1): ?>
                    <li>
                        <i class="fab fa-telegram"></i>
                        <span>Telegram Support</span>
                    </li>
                    <?php endif; ?>
                    
                    <?php if (isset($package['wpsupport']) && $package['wpsupport'] == 1): ?>
                    <li>
                        <i class="fab fa-whatsapp"></i>
                        <span>24/7 WhatsApp Support</span>
                    </li>
                    <?php endif; ?>
                    <li>
                        <i class="fas fa-calendar-check"></i>
                        <span>40-Day Validity</span>
                    </li>
                </ul>
                <?php elseif ($package_type === 'mining'): ?>
                <ul class="feature-list">
                    
                <li>
                                            <i class="fas fa-tachometer-alt"></i>  
                                            <span><?= $package['hash_rate'] ?> TH/s Hash Rate</span>
                                        </li>
                                        <li>
                                            <i class="fas fa-bolt"></i>  
                                            <span><?= number_format($package['hash_rate'] * $package['electricity_cost'], 2) ?>   USDT/day Electricity</span>
                                        </li>
                                        <li>
                                            <i class="fas fa-chart-line"></i>  
                                            <span><?= number_format($package['daily_revenue_rate'] * 100, 2) ?> Daily Rate</span>
                                        </li>
                                        <li>
                                            <i class="fas fa-coins"></i>  
                                            <span><?= number_format(($package['hash_rate'] * $package['daily_revenue_rate']) - ($package['hash_rate'] * $package['electricity_cost']), 2) ?> USDT Est. Daily</span>
                                        </li>
                                        <li>
                                            <i class="fas fa-coins"></i>  <span><?= number_format((($package['hash_rate'] * $package['daily_revenue_rate']) - ($package['hash_rate'] * $package['electricity_cost'])) * 30, 2) ?> USDT Est. Monthly</span>
                                        </li>
                    <li>
                        <i class="fas fa-calendar-alt"></i>
                        <span>30 Days Contract Period</span>
                    </li>
                </ul>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="payment-details">
            <h3>Payment Details</h3>
            
            <div class="payment-item">
                <span>Your Balance:</span>
                <span class="price"><?= number_format($current_balance, 2) ?> USDT</span>
            </div>
            
            <div class="payment-item">
                <span>Package Price:</span>
                <span class="price"><?= number_format($package_price, 2) ?> USDT</span>
            </div>
            
            <div class="payment-item total">
                <span>Remaining Balance:</span>
                <span class="price <?= $can_afford ? '' : 'insufficient' ?>"><?= number_format($current_balance - $package_price, 2) ?> USDT</span>
            </div>
            
            <?php if (!$can_afford): ?>
            <div class="insufficient-balance">
                <i class="fas fa-exclamation-triangle"></i>
                <p>Insufficient balance. Please deposit more funds.</p>
                <a href="deposit.php" class="btn btn-primary">Deposit Funds</a>
            </div>
            <?php endif; ?>
        </div>
        
        <?php if (!$confirm_action && $can_afford): ?>
        <form method="post" class="purchase-form">
            <input type="hidden" name="package_id" value="<?= $package_id ?>">
            <input type="hidden" name="package_type" value="<?= $package_type ?>">
            
            <p class="confirmation-text">
                Are you sure you want to purchase this package? The amount will be deducted from your balance.
            </p>
            
            <div class="form-actions">
                <a href="packages.php?type=<?= $package_type ?>" class="btn btn-outline">Cancel</a>
                <button type="submit" name="confirm_purchase" class="btn btn-primary">Confirm Purchase</button>
            </div>
        </form>
        <?php endif; ?>
    </div>
    
    <?php endif; ?>
</div>

<style>
/* Purchase Page Styles */
.purchase-page {
    padding: 15px;
    background-color: #f5f5f5;
    min-height: 100vh;
}

.page-header {
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.page-header h1 {
    font-size: 1.6rem;
    font-weight: 700;
    margin: 0;
    color: #3F88F6;
}

.back-link {
    color: #666;
    font-size: 0.9rem;
    text-decoration: none;
    display: flex;
    align-items: center;
}

.back-link i {
    margin-right: 5px;
}

.alert {
    padding: 15px;
    border-radius: 10px;
    margin-bottom: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.alert-danger {
    background-color: #fff0f0;
    border-left: 4px solid #dc3545;
    color: #dc3545;
}

.alert-success {
    background-color: #f0fff0;
    border-left: 4px solid #28a745;
    color: #28a745;
}

.alert i {
    margin-right: 10px;
}

.alert-actions {
    display: flex;
    gap: 10px;
    margin-top: 15px;
}

.purchase-card {
    background-color: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
}

.package-details {
    margin-bottom: 20px;
}

.package-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    color: white;
}

.package-header.vip {
    background-color: #3F88F6;
}

.package-header.mining {
    background-color: #20c997;
}

.package-header h2 {
    font-size: 1.3rem;
    font-weight: 600;
    margin: 0;
}

.package-price {
    font-size: 1.4rem;
    font-weight: 700;
}

.package-body {
    padding: 20px;
}

.feature-list {
    list-style-type: none;
    padding: 0;
    margin: 0;
}

.feature-list li {
    padding: 12px 0;
    border-bottom: 1px solid #eee;
    display: flex;
    align-items: center;
}

.feature-list li:last-child {
    border-bottom: none;
}

.feature-list li i {
    color: #3F88F6;
    margin-right: 10px;
    width: 20px;
    text-align: center;
}

.payment-details {
    padding: 20px;
    border-top: 1px solid #eee;
    background-color: #fafafa;
}

.payment-details h3 {
    font-size: 1.1rem;
    font-weight: 600;
    margin: 0 0 15px;
    color: #333;
}

.payment-item {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #eee;
}

.payment-item.total {
    font-weight: 600;
    border-bottom: none;
    padding-top: 15px;
}

.price.insufficient {
    color: #dc3545;
}

.insufficient-balance {
    margin-top: 15px;
    padding: 15px;
    background-color: #fff0f0;
    border-radius: 8px;
    text-align: center;
}

.insufficient-balance i {
    font-size: 1.5rem;
    color: #dc3545;
    margin-bottom: 10px;
}

.insufficient-balance p {
    margin: 0 0 15px;
    color: #666;
}

.purchase-form {
    padding: 20px;
    border-top: 1px solid #eee;
}

.confirmation-text {
    text-align: center;
    color: #666;
    margin: 0 0 20px;
}

.form-actions {
    display: flex;
    justify-content: space-between;
    gap: 10px;
}

.btn {
    display: inline-block;
    padding: 12px 20px;
    border-radius: 8px;
    font-weight: 500;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    border: none;
    outline: none;
    text-decoration: none;
    flex: 1;
}

.btn-primary {
    background-color: #3F88F6;
    color: white;
}

.btn-outline {
    background-color: transparent;
    border: 1px solid #3F88F6;
    color: #3F88F6;
}

@media (min-width: 768px) {
    .purchase-card {
        max-width: 600px;
        margin: 0 auto;
    }
}
</style>

<?php include 'includes/mobile-footer.php'; ?>