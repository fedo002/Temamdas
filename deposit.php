<?php
// mobile/deposit.php - Deposit funds mobile page
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/class/NowPaymentsAPI.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=deposit.php');
    exit;
}

// Get user information
$user_id = $_SESSION['user_id'];
$user = getUserDetails($user_id);

// Get payment settings
$settings = getPaymentSettings();
$minDeposit = $settings['min_deposit_amount'];

// Get recent deposits
$deposits = getUserDeposits($user_id, 5);

// Create payment
$paymentCreated = false;
$paymentError = '';
$paymentData = null;
$cryptoPaymentAddress = '';
$cryptoAmount = 0;
$cryptoCurrency = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = (float)$_POST['amount'];
    
    // Amount validation
    if ($amount < $minDeposit) {
        $paymentError = "Minimum yatırma tutarı " . number_format($minDeposit, 2) . " USD.";
    } else {
        // Create payment with NOWPayments API
        $api = new NowPaymentsAPI($settings['nowpayments_api_key'], $settings['nowpayments_ipn_secret'], $settings['nowpayments_test_mode']);
        
        // Generate unique order ID
        $orderId = 'DEP-' . time() . '-' . $user_id;
        
        // Payment description
        $description = "Deposit for user: " . $user['username'];
        
        try {
            $apiAmount = $amount * 1.006;
            
            // Doğrudan ödeme adresi almak için createPayment kullanın
            // Ödeme adresi oluştur (site içi ödeme için)
            $payment = $api->createPayment($apiAmount, 'USD', 'usdttrc20', $orderId, $description);

            if ($payment && isset($payment['pay_address'])) {
                // Veritabanına kaydet
                $depositId = saveDeposit($user_id, $amount, 'pending', $payment['payment_id'], $orderId);
                
                // Ödeme bilgilerini göster
                $paymentCreated = true;
                $paymentData = $payment;
                $cryptoPaymentAddress = $payment['pay_address'];
                $cryptoAmount = $payment['pay_amount'];
                $cryptoCurrency = strtoupper($payment['pay_currency']);
            } else {
                $paymentError = "Ödeme oluşturulurken hata oluştu. Lütfen daha sonra tekrar deneyin.";
            }
        } catch (Exception $e) {
            $paymentError = "API Hatası: " . $e->getMessage();
        }
    }
}

$page_title = 'Deposit';
include 'includes/mobile-header.php';
?>

<div class="deposit-page">
    <h1 class="page-title">Deposit</h1>
    
    <!-- Main Content -->
    <div class="deposit-container">
        <?php if($paymentCreated && $paymentData): ?>
        <!-- Payment Information (Simplified) -->
        <div class="payment-created-card">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h2>Payment Request Created!</h2>
            
            <!-- Essential Payment Information -->
            <div class="essential-payment-info">
                <!-- Timer -->
                <div class="payment-timer">
                    <div class="timer-icon">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div class="timer-content">
                        <p>Time remaining:</p>
                        <div id="countdown">30:00</div>
                    </div>
                </div>
                
                <!-- QR Code -->
                <div class="qr-container">
                    <div id="qrcode"></div>
                </div>
                
                <!-- Amount -->
                <div class="detail-row crypto-amount">
                    <span>Amount:</span>
                    <strong><?= number_format($cryptoAmount, 8) ?> <?= $cryptoCurrency ?></strong>
                </div>
                
                <!-- Address -->
                <div class="address-container">
                    <input type="text" id="wallet-address" value="<?= $cryptoPaymentAddress ?>" readonly>
                    <button id="copy-address" class="copy-btn">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
                
                <!-- Status -->
                <div class="payment-status-section">
                    <div class="status-container">
                        <div class="status-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="status-content">
                            <div class="status-label" id="status-label">Waiting for Payment</div>
                            <div class="progress-bar">
                                <div class="progress-fill" id="progress-fill" style="width: 0%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Collapsible Payment Instructions -->
            <div class="collapsible-section">
                <button class="collapsible-button">
                    <i class="fas fa-question-circle"></i> How to Pay
                    <i class="fas fa-chevron-down arrow-icon"></i>
                </button>
                <div class="collapsible-content">
                    <div class="payment-steps">
                        <ol>
                            <li>Open your crypto wallet</li>
                            <li>Select <?= $cryptoCurrency ?> currency (use TRC-20 network)</li>
                            <li>Scan the QR code or copy the wallet address above</li>
                            <li>Enter the amount: <strong><?= number_format($cryptoAmount, 8) ?> <?= $cryptoCurrency ?></strong></li>
                            <li>Confirm and send the payment</li>
                        </ol>
                    </div>
                    
                    <div class="warning-note">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>Please make sure you use the correct network (TRC-20). We are not responsible for losses caused by using the wrong network.</span>
                    </div>
                    
                    <div class="payment-details">
                        <div class="detail-row">
                            <span>Payment ID:</span>
                            <strong><?= $paymentData['payment_id'] ?></strong>
                        </div>
                    </div>
                </div>
            </div>
            
            <a href="transactions.php" class="btn btn-outline btn-block mt-4">
                <i class="fas fa-history"></i> View Transaction History
            </a>
        </div>
        <?php else: ?>
        <!-- Deposit Form - Keep this part the same but translate to English -->
        <div class="card deposit-form-card">
            <h2><i class="fas fa-wallet"></i> Deposit Funds</h2>
            
            <?php if($paymentError): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <?= $paymentError ?>
            </div>
            <?php endif; ?>
            
            <form method="POST" id="depositForm">
                <div class="form-group">
                    <label for="amount">Amount to Deposit (USD)</label>
                    <div class="input-group">
                        <span class="input-prefix">$</span>
                        <input type="number" id="amount" name="amount" min="<?= $minDeposit ?>" step="1" placeholder="" required>
                    </div>
                    <div class="form-hint">Minimum deposit amount: <?= number_format($minDeposit, 2) ?> USD</div>
                </div>
                
                <div class="amount-shortcuts">
                    <button type="button" class="amount-btn" data-amount="10">+10</button>
                    <button type="button" class="amount-btn" data-amount="50">+50</button>
                    <button type="button" class="amount-btn" data-amount="100">+100</button>
                    <button type="button" class="amount-btn" data-amount="500">+500</button>
                </div>
                
                <div class="alert alert-info">
                    <div class="alert-icon">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div class="alert-content">
                        <h4>Payment Information</h4>
                        <p>You can pay with cryptocurrency. Your balance will be updated automatically after the payment is completed.</p>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-wallet"></i> Deposit
                </button>
            </form>
        </div>
        
        <!-- Information Card -->
        <div class="card info-card">
            <h3><i class="fas fa-info-circle"></i> Information</h3>
            
            <ul class="info-list">
                <li>
                    <i class="fas fa-check-circle"></i>
                    <div>
                        <strong>Minimum Deposit:</strong>
                        <span><?= number_format($minDeposit, 2) ?> USD</span>
                    </div>
                </li>
                <li>
                    <i class="fas fa-check-circle"></i>
                    <div>
                        <strong>Payment Method:</strong>
                        <span>Cryptocurrency (USDT)</span>
                    </div>
                </li>
                <li>
                    <i class="fas fa-check-circle"></i>
                    <div>
                        <strong>Processing Time:</strong>
                        <span>Usually 5-30 minutes</span>
                    </div>
                </li>
                <li>
                    <i class="fas fa-check-circle"></i>
                    <div>
                        <strong>Transaction Fee:</strong>
                        <span>None</span>
                    </div>
                </li>
            </ul>
            
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <span>Your payment will be processed securely. If you encounter any issues, please contact our support team.</span>
            </div>
        </div>
        
        <!-- Recent Deposits -->
        <?php if(count($deposits) > 0): ?>
        <div class="card recent-deposits-card">
            <h3><i class="fas fa-history"></i> Recent Deposits</h3>
            
            <div class="deposits-list">
                <?php foreach($deposits as $deposit): ?>
                <div class="deposit-item">
                    <div class="deposit-details">
                        <div class="deposit-amount"><?= number_format($deposit['amount'], 2) ?> USD</div>
                        <div class="deposit-date"><?= date('d.m.Y H:i', strtotime($deposit['created_at'])) ?></div>
                    </div>
                    <div class="deposit-status">
                        <?php if($deposit['status'] == 'confirmed'): ?>
                            <span class="status-badge status-completed">Completed</span>
                        <?php elseif($deposit['status'] == 'pending'): ?>
                            <span class="status-badge status-pending">Pending</span>
                        <?php else: ?>
                            <span class="status-badge status-failed">Failed</span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <a href="transactions.php?filter=deposit" class="btn btn-outline btn-sm btn-block">
                <i class="fas fa-list"></i> View All Deposits
            </a>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<style>
/* Original styles plus new styles for collapsible section */
.deposit-page {
    padding: 15px;
}

.page-title {
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 20px;
    color: #000;
}

.deposit-container {
    display: flex;
    flex-direction: column;
    gap: 20px;
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

.card h2 i, .card h3 i {
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

/* Essential Payment Info */
.essential-payment-info {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 15px;
    margin-bottom: 20px;
}

.payment-created-card {
    background-color: white;
    border-radius: 12px;
    padding: 25px 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    text-align: center;
}

.success-icon {
    font-size: 3rem;
    color: #28c76f;
    margin-bottom: 15px;
}

.payment-created-card h2 {
    font-size: 1.4rem;
    font-weight: 600;
    margin-bottom: 20px;
    display: block;
}

/* QR Code */
.qr-container {
    display: inline-block;
    background-color: white;
    padding: 10px;
    border-radius: 8px;
    border: 1px solid #ddd;
    margin: 0 auto;
}

#qrcode {
    width: 180px;
    height: 180px;
    margin: 0 auto;
}

/* Amount and Address */
.crypto-amount {
    background-color: #fffbeb;
    padding: 10px;
    border-radius: 6px;
    border: 1px dashed #ffc107;
    margin: 10px 0;
    width: 100%;
    max-width: 300px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.address-container {
    display: flex;
    background-color: #f8f9fa;
    border-radius: 8px;
    overflow: hidden;
    width: 100%;
    max-width: 300px;
    border: 1px solid #ddd;
}

#wallet-address {
    flex: 1;
    padding: 12px 15px;
    border: none;
    font-family: monospace;
    font-size: 14px;
    color: #333;
    background-color: #f8f9fa;
}

.copy-btn {
    background-color: var(--primary-color);
    color: white;
    border: none;
    padding: 0 15px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 45px;
}

/* Payment Timer */
.payment-timer {
    display: flex;
    align-items: center;
    background-color: #f8f9fa;
    border-radius: 8px;
    padding: 10px 15px;
    width: 100%;
    max-width: 300px;
}

.timer-icon {
    font-size: 1.5rem;
    color: var(--primary-color);
    margin-right: 15px;
}

.timer-content {
    flex: 1;
}

.timer-content p {
    margin: 0 0 5px;
    color: #6e6b7b;
}

#countdown {
    font-size: 1.3rem;
    font-weight: 600;
    color: #333;
}

/* Payment Status */
.payment-status-section {
    width: 100%;
    max-width: 300px;
}

.status-container {
    display: flex;
    align-items: center;
}

.status-icon {
    font-size: 1.5rem;
    color: #ffc107;
    margin-right: 15px;
}

.status-content {
    flex: 1;
}

.status-label {
    font-weight: 600;
    margin-bottom: 8px;
}

.progress-bar {
    height: 8px;
    background-color: #eee;
    border-radius: 10px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background-color: var(--primary-color);
    border-radius: 10px;
    transition: width 0.5s;
}

/* Collapsible Section */
.collapsible-section {
    width: 100%;
    margin: 15px 0;
    border: 1px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
}

.collapsible-button {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    padding: 15px;
    background-color: #f8f9fa;
    border: none;
    text-align: left;
    font-weight: 600;
    cursor: pointer;
}

.collapsible-button i:first-child {
    margin-right: 10px;
    color: var(--primary-color);
}

.arrow-icon {
    transition: transform 0.3s;
}

.collapsible-button.active .arrow-icon {
    transform: rotate(180deg);
}

.collapsible-content {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease-out;
    background-color: white;
}

.payment-steps {
    padding: 15px;
}

.payment-steps ol {
    margin: 0;
    padding-left: 25px;
}

.payment-steps li {
    margin-bottom: 10px;
    color: #555;
}

.warning-note {
    margin: 0 15px 15px;
    padding: 10px;
    background-color: #fff3cd;
    color: #856404;
    border-radius: 6px;
    display: flex;
    align-items: flex-start;
}

.warning-note i {
    margin-right: 10px;
    margin-top: 2px;
}

.payment-details {
    background-color: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    margin: 0 15px 15px;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
}

.detail-row:last-child {
    margin-bottom: 0;
}

.detail-row span {
    color: #6e6b7b;
}

/* Form Styles - Keep the rest of the styles the same */
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

.form-hint {
    font-size: 0.8rem;
    color: #6e6b7b;
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
    background-color: #f8f9fa;
    border: 1px solid #ddd;
    border-radius: 20px;
    padding: 8px 15px;
    font-size: 0.9rem;
    color: #333;
    cursor: pointer;
}

/* Alerts */
.alert {
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 0.9rem;
}

.alert-info {
    background-color: #d1ecf1;
    color: #0c5460;
    display: flex;
    align-items: flex-start;
}

.alert-warning {
    background-color: #fff3cd;
    color: #856404;
}

.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
    display: flex;
    align-items: flex-start;
}

.alert i {
    margin-right: 10px;
    font-size: 1.1rem;
    margin-top: 2px;
}

.alert-icon {
    font-size: 1.5rem;
    margin-right: 15px;
}

.alert-content {
    flex: 1;
}

.alert-content h4 {
    font-size: 1rem;
    font-weight: 600;
    margin: 0 0 5px;
}

.alert-content p {
    margin: 0;
}

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
    transition: background-color 0.3s;
    border: none;
}

.btn i {
    margin-right: 8px;
}

.btn-block {
    display: flex;
    width: 100%;
}

.btn-lg {
    padding: 14px 24px;
    font-size: 1rem;
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

.btn-sm {
    padding: 8px 15px;
    font-size: 0.9rem;
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
    color: #28c76f;
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
    color: #6e6b7b;
}

/* Deposits List */
.deposits-list {
    margin-bottom: 15px;
}

.deposit-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 0;
    border-bottom: 1px solid #eee;
}

.deposit-item:last-child {
    border-bottom: none;
}

.deposit-amount {
    font-weight: 600;
    margin-bottom: 5px;
}

.deposit-date {
    font-size: 0.8rem;
    color: #6e6b7b;
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

.status-failed {
    background-color: #f8d7da;
    color: #721c24;
}

.mt-4 {
    margin-top: 20px;
}
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Amount buttons
    const amountBtns = document.querySelectorAll('.amount-btn');
    const amountInput = document.getElementById('amount');
    
    amountBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const amount = parseFloat(this.dataset.amount);
            const currentAmount = parseFloat(amountInput.value) || 0;
            amountInput.value = (currentAmount + amount).toFixed(2);
        });
    });
    
    <?php if($paymentCreated && $paymentData): ?>
    // Generate QR Code
    if (document.getElementById('qrcode')) {
        const qrData = "<?= $cryptoPaymentAddress ?>";
        new QRCode(document.getElementById("qrcode"), {
            text: qrData,
            width: 180,
            height: 180,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
    }
    
    // Address copy functionality
    const copyAddressBtn = document.getElementById('copy-address');
    if (copyAddressBtn) {
        copyAddressBtn.addEventListener('click', function() {
            const walletAddress = document.getElementById('wallet-address');
            walletAddress.select();
            document.execCommand('copy');
            
            // Visual feedback when copied
            this.innerHTML = '<i class="fas fa-check"></i>';
            setTimeout(() => {
                this.innerHTML = '<i class="fas fa-copy"></i>';
            }, 2000);
        });
    }
    
    // Collapsible section functionality
    const collapsibleBtn = document.querySelector('.collapsible-button');
    if (collapsibleBtn) {
        collapsibleBtn.addEventListener('click', function() {
            this.classList.toggle('active');
            const content = this.nextElementSibling;
            
            if (content.style.maxHeight) {
                content.style.maxHeight = null;
            } else {
                content.style.maxHeight = content.scrollHeight + "px";
            }
        });
    }
    
    // Countdown timer
    const countdownEl = document.getElementById('countdown');
    if (countdownEl) {
        let timeLeft = 30 * 60; // 30 minutes = 1800 seconds
        
        const updateTimer = () => {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            
            countdownEl.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            
            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                countdownEl.textContent = "00:00";
                // Update status message when time runs out
                const statusLabel = document.getElementById('status-label');
                if (statusLabel) {
                    statusLabel.textContent = "Payment time expired";
                }
            }
            
            timeLeft--;
        };
        
        updateTimer();
        const timerInterval = setInterval(updateTimer, 1000);
    }
    
    // Payment status check
    const checkPaymentStatus = async () => {
        try {
            const response = await fetch('check_payment_status.php?payment_id=<?= $paymentData['payment_id'] ?>');
            const data = await response.json();
            
            if (data.status) {
                const statusLabel = document.getElementById('status-label');
                const progressFill = document.getElementById('progress-fill');
                const statusIcon = document.querySelector('.status-icon i');
                
                // Update UI based on payment status
                if (data.status === 'confirming' || data.status === 'partially_paid') {
                    statusLabel.textContent = "Payment Confirming";
                    progressFill.style.width = "50%";
                    statusIcon.className = "fas fa-spinner fa-spin";
                    statusIcon.style.color = "#3498db";
                } else if (data.status === 'confirmed' || data.status === 'finished') {
                    statusLabel.textContent = "Payment Completed";
                    progressFill.style.width = "100%";
                    statusIcon.className = "fas fa-check-circle";
                    statusIcon.style.color = "#28c76f";
                    
                    // Redirect to transaction history after successful payment
                    setTimeout(() => {
                        window.location.href = 'transactions.php?filter=deposit&status=success';
                    }, 5000);
                }
            }
        } catch (error) {
            console.error('Payment status check error:', error);
        }
    };
    
    // Check initial status
    checkPaymentStatus();
    
    // Check at regular intervals (every 30 seconds)
    const statusInterval = setInterval(checkPaymentStatus, 30000);
    <?php endif; ?>
});
</script>

<?php include 'includes/mobile-footer.php'; ?>