<?php
// withdraw-referral.php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/header.php';

// Redirect to login if not logged in
if (!isLoggedIn()) {
    header('Location: login.php?redirect=withdraw-referral.php');
    exit;
}

// Get user ID from session
$userId = $_SESSION['user_id'];

// Get available balance
$stmt = $conn->prepare("SELECT SUM(amount) as available_balance FROM referral_earnings 
                       WHERE user_id = ? AND status = 'approved' AND is_paid = 0");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$availableBalance = $row['available_balance'] ?: 0;

// Get user's payment methods
$stmt = $conn->prepare("SELECT * FROM user_payment_methods WHERE user_id = ? AND status = 'active'");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$paymentMethods = $result->fetch_all(MYSQLI_ASSOC);

// Get withdrawal history
$stmt = $conn->prepare("SELECT * FROM referral_withdrawals WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$withdrawalHistory = $result->fetch_all(MYSQLI_ASSOC);

// Process withdrawal request
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_withdrawal'])) {
    $amount = floatval($_POST['amount'] ?? 0);
    $paymentMethodId = intval($_POST['payment_method'] ?? 0);
    
    // Basic validation
    if ($amount <= 0) {
        $message = 'Please enter a valid amount.';
        $messageType = 'error';
    } elseif ($amount < 10) {
        $message = 'Minimum withdrawal amount is $10.';
        $messageType = 'error';
    } elseif ($amount > $availableBalance) {
        $message = 'Withdrawal amount exceeds your available balance.';
        $messageType = 'error';
    } elseif ($paymentMethodId <= 0) {
        $message = 'Please select a payment method.';
        $messageType = 'error';
    } else {
        // Verify payment method belongs to user
        $stmt = $conn->prepare("SELECT id FROM user_payment_methods WHERE id = ? AND user_id = ? AND status = 'active'");
        $stmt->bind_param("ii", $paymentMethodId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $message = 'Invalid payment method selected.';
            $messageType = 'error';
        } else {
            // Start transaction
            $conn->begin_transaction();
            
            try {
                // Create withdrawal request
                $stmt = $conn->prepare("INSERT INTO referral_withdrawals (user_id, amount, payment_method_id, status, created_at) 
                                      VALUES (?, ?, ?, 'pending', NOW())");
                $stmt->bind_param("idi", $userId, $amount, $paymentMethodId);
                $stmt->execute();
                
                // Commit the transaction
                $conn->commit();
                
                $message = 'Withdrawal request submitted successfully. Your request is now pending approval.';
                $messageType = 'success';
                
                // Refresh available balance
                $stmt = $conn->prepare("SELECT SUM(amount) as available_balance FROM referral_earnings 
                                       WHERE user_id = ? AND status = 'approved' AND is_paid = 0");
                $stmt->bind_param("i", $userId);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                $availableBalance = $row['available_balance'] ?: 0;
                
                // Refresh withdrawal history
                $stmt = $conn->prepare("SELECT * FROM referral_withdrawals WHERE user_id = ? ORDER BY created_at DESC");
                $stmt->bind_param("i", $userId);
                $stmt->execute();
                $result = $stmt->get_result();
                $withdrawalHistory = $result->fetch_all(MYSQLI_ASSOC);
                
            } catch (Exception $e) {
                // Rollback on error
                $conn->rollback();
                $message = 'An error occurred while processing your withdrawal request. Please try again.';
                $messageType = 'error';
            }
        }
    }
}
?>

<div class="withdraw-page">
    <div class="container">
        <div class="withdraw-container">
            <div class="page-header">
                <h1 data-i18n="withdraw_earnings">Withdraw Referral Earnings</h1>
                <a href="referrals.php" class="btn btn-outline" data-i18n="back_to_referrals">Back to Referrals</a>
            </div>
            
            <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $messageType === 'success' ? 'success' : 'danger'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
            <?php endif; ?>
            
            <div class="row">
                <!-- Withdrawal Form -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h2 data-i18n="request_withdrawal">Request Withdrawal</h2>
                        </div>
                        <div class="card-body">
                            <div class="balance-info">
                                <div class="balance-label" data-i18n="available_balance">Available Balance:</div>
                                <div class="balance-value">$<?php echo number_format($availableBalance, 2); ?> USD</div>
                            </div>
                            
                            <?php if ($availableBalance >= 10): ?>
                            <form method="POST" action="" class="withdrawal-form">
                                <div class="form-group">
                                    <label for="amount" data-i18n="withdrawal_amount">Withdrawal Amount</label>
                                    <div class="input-group">
                                        <div class="input-prefix">$</div>
                                        <input type="number" id="amount" name="amount" min="10" max="<?php echo $availableBalance; ?>" step="0.01" required>
                                        <div class="input-suffix">USD</div>
                                    </div>
                                    <div class="form-hint" data-i18n="min_withdrawal">Minimum withdrawal: $10.00</div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="payment_method" data-i18n="payment_method">Payment Method</label>
                                    <?php if (empty($paymentMethods)): ?>
                                    <div class="no-payment-methods">
                                        <p data-i18n="no_payment_methods">You haven't added any payment methods yet.</p>
                                        <a href="payment-methods.php" class="btn btn-outline btn-sm" data-i18n="add_payment_method">Add Payment Method</a>
                                    </div>
                                    <?php else: ?>
                                    <select id="payment_method" name="payment_method" required>
                                        <option value="" data-i18n="select_payment_method">Select Payment Method</option>
                                        <?php foreach ($paymentMethods as $method): ?>
                                        <option value="<?php echo $method['id']; ?>">
                                            <?php 
                                            $typeLabel = ucfirst($method['type']);
                                            $accountLabel = '';
                                            
                                            if ($method['type'] === 'bank') {
                                                $accountLabel = "Bank Account - " . substr($method['account_number'], -4);
                                            } elseif ($method['type'] === 'paypal') {
                                                $accountLabel = "PayPal - " . $method['email'];
                                            } elseif ($method['type'] === 'crypto') {
                                                $accountLabel = $method['currency'] . " Wallet";
                                            }
                                            
                                            echo htmlspecialchars($typeLabel . ': ' . $accountLabel);
                                            ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-hint">
                                        <a href="payment-methods.php" data-i18n="manage_payment_methods">Manage Payment Methods</a>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if (!empty($paymentMethods)): ?>
                                <button type="submit" name="submit_withdrawal" class="btn btn-primary" data-i18n="request_withdrawal">Request Withdrawal</button>
                                <?php endif; ?>
                            </form>
                            <?php else: ?>
                            <div class="insufficient-balance">
                                <p data-i18n="insufficient_balance">You need a minimum balance of $10.00 to request a withdrawal.</p>
                                <a href="referrals.php" class="btn btn-outline" data-i18n="earn_more">Earn More Referrals</a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Withdrawal Info -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h2 data-i18n="withdrawal_info">Withdrawal Information</h2>
                        </div>
                        <div class="card-body">
                            <div class="info-box">
                                <h3 data-i18n="processing_time">Processing Time</h3>
                                <p data-i18n="processing_time_text">Withdrawal requests are typically processed within 1-3 business days.</p>
                            </div>
                            
                            <div class="info-box">
                                <h3 data-i18n="fees">Fees</h3>
                                <p data-i18n="fees_text">There are no fees for withdrawals. However, your payment provider may charge transaction fees.</p>
                            </div>
                            
                            <div class="info-box">
                                <h3 data-i18n="min_max">Minimum & Maximum</h3>
                                <p data-i18n="min_max_text">The minimum withdrawal amount is $10.00. The maximum depends on your available balance.</p>
                            </div>
                            
                            <div class="info-box">
                                <h3 data-i18n="need_help">Need Help?</h3>
                                <p data-i18n="need_help_text">If you have any questions or issues with withdrawals, please <a href="support.php">contact support</a>.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Withdrawal History -->
            <div class="card withdrawal-history">
                <div class="card-header">
                    <h2 data-i18n="withdrawal_history">Withdrawal History</h2>
                </div>
                <div class="card-body">
                    <?php if (empty($withdrawalHistory)): ?>
                    <div class="no-history" data-i18n="no_withdrawal_history">You haven't made any withdrawal requests yet.</div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="history-table">
                            <thead>
                                <tr>
                                    <th data-i18n="date">Date</th>
                                    <th data-i18n="amount">Amount</th>
                                    <th data-i18n="payment_method">Payment Method</th>
                                    <th data-i18n="status">Status</th>
                                    <th data-i18n="completed_date">Completed Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($withdrawalHistory as $withdrawal): ?>
                                <tr>
                                    <td><?php echo date('M d, Y', strtotime($withdrawal['created_at'])); ?></td>
                                    <td>$<?php echo number_format($withdrawal['amount'], 2); ?> USD</td>
                                    <td>
                                        <?php 
                                        // Get payment method details
                                        $methodStmt = $conn->prepare("SELECT * FROM user_payment_methods WHERE id = ?");
                                        $methodStmt->bind_param("i", $withdrawal['payment_method_id']);
                                        $methodStmt->execute();
                                        $methodResult = $methodStmt->get_result();
                                        $method = $methodResult->fetch_assoc();
                                        
                                        if ($method) {
                                            $typeLabel = ucfirst($method['type']);
                                            $accountLabel = '';
                                            
                                            if ($method['type'] === 'bank') {
                                                $accountLabel = "Bank Account - " . substr($method['account_number'], -4);
                                            } elseif ($method['type'] === 'paypal') {
                                                $accountLabel = "PayPal - " . $method['email'];
                                            } elseif ($method['type'] === 'crypto') {
                                                $accountLabel = $method['currency'] . " Wallet";
                                            }
                                            
                                            echo htmlspecialchars($typeLabel . ': ' . $accountLabel);
                                        } else {
                                            echo 'Payment method deleted';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?php echo strtolower($withdrawal['status']); ?>">
                                            <?php echo ucfirst($withdrawal['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php echo $withdrawal['processed_at'] ? date('M d, Y', strtotime($withdrawal['processed_at'])) : '-'; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
