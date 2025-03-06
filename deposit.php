<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/class/NowPaymentsAPI.php';

// Kullanıcı oturum kontrolü
if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Kullanıcı bilgilerini al
$user_id = $_SESSION['user_id'];
$user = getUserDetails($user_id);

// Ödeme ayarlarını al
$settings = getPaymentSettings();
$minDeposit = $settings['min_deposit_amount'];

// Son yatırımları al
$deposits = getUserDeposits($user_id, 5);

// Ödeme oluşturma işlemi
$paymentCreated = false;
$paymentError = '';
$paymentData = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = (float)$_POST['amount'];
    
    // Miktar kontrolü
    if ($amount < $minDeposit) {
        $paymentError = "Minimum yatırım tutarı " . number_format($minDeposit, 2) . " USDT olmalıdır.";
    } else {
        // NOWPayments API ile ödeme oluştur
        $api = new NowPaymentsAPI($settings['nowpayments_api_key'], $settings['nowpayments_ipn_secret'], $settings['nowpayments_test_mode']);
        
        // Benzersiz sipariş ID oluştur
        $orderId = 'DEP-' . time() . '-' . $user_id;
        
        // Ödeme açıklaması
        $description = "Deposit for user: " . $user['username'];
        
        // IPN callback URL
        $ipnUrl = SITE_URL . 'ipn/nowpayments.php';
        
        // Başarılı ve iptal URL'leri
        $successUrl = SITE_URL . 'deposit-success.php';
        $cancelUrl = SITE_URL . 'deposit.php';
        
        // Ödeme oluştur
        $payment = $api->createInvoice($amount, 'usd', $orderId, $description, $successUrl, $cancelUrl);
        
        if ($payment && isset($payment['id'])) {
            // Ödemeyi veritabanına kaydet
            $depositId = saveDeposit($user_id, $amount, 'pending', $payment['id'], $orderId);
            
            if ($depositId) {
                $paymentCreated = true;
                $paymentData = $payment;
            } else {
                $paymentError = "Yatırım kaydedilirken bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
            }
        } else {
            $paymentError = "Ödeme oluşturulurken bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
        }
    }
}

$page_title = 'Yatırım Yap';
include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-wallet me-2"></i> USDT TRC-20 Yatırım</h5>
                </div>
                <div class="card-body">
                    <?php if($paymentCreated && $paymentData): ?>
                    <div class="text-center mb-4">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <h4>Ödeme Talebi Oluşturuldu!</h4>
                        <p>Lütfen ödeme işlemini tamamlamak için aşağıdaki bağlantıya tıklayın.</p>
                    </div>
                    
                    <div class="alert alert-info">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <strong>Ödeme Tutarı:</strong> <?= number_format($amount, 2) ?> USDT<br>
                                <strong>Ödeme ID:</strong> <?= $paymentData['id'] ?>
                            </div>
                            <a href="<?= $paymentData['invoice_url'] ?>" target="_blank" class="btn btn-primary">
                                <i class="fas fa-external-link-alt me-2"></i> Ödeme Sayfasına Git
                            </a>
                        </div>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Not:</strong> Ödeme sayfası 1 saat boyunca geçerlidir. Bu süre içinde ödeme yapmazsanız, yeni bir ödeme talebi oluşturmanız gerekir.
                    </div>
                    <?php else: ?>
                    <?php if($paymentError): ?>
                    <div class="alert alert-danger mb-4">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?= $paymentError ?>
                    </div>
                    <?php endif; ?>
                    
                    <form method="POST" id="depositForm">
                        <div class="mb-4">
                            <label class="form-label">Yatırım Tutarı (USDT)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                <input type="number" class="form-control" name="amount" id="amount" placeholder="Minimum: <?= $minDeposit ?> USDT" min="<?= $minDeposit ?>" step="1" required>
                            </div>
                            <div class="form-text">Minimum yatırım tutarı: <?= number_format($minDeposit, 2) ?> USDT</div>
                        </div>
                        
                        <div class="deposit-amount-buttons mb-4">
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-outline-primary deposit-amount-btn" data-amount="10">+10 USDT</button>
                                <button type="button" class="btn btn-outline-primary deposit-amount-btn" data-amount="50">+50 USDT</button>
                                <button type="button" class="btn btn-outline-primary deposit-amount-btn" data-amount="100">+100 USDT</button>
                                <button type="button" class="btn btn-outline-primary deposit-amount-btn" data-amount="500">+500 USDT</button>
                                <button type="button" class="btn btn-outline-primary deposit-amount-btn" data-amount="1000">+1000 USDT</button>
                            </div>
                        </div>
                        
                        <div class="alert alert-info mb-4">
                            <div class="d-flex">
                                <div class="me-3">
                                    <i class="fas fa-info-circle fa-2x"></i>
                                </div>
                                <div>
                                    <h5 class="alert-heading">Ödeme Bilgileri</h5>
                                    <p class="mb-0">
                                        USDT TRC-20 cüzdanınızdan ödeme yapabilirsiniz. Ödeme işlemi tamamlandıktan sonra, bakiyeniz otomatik olarak güncellenecektir.
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-wallet me-2"></i> Yatırım Yap
                            </button>
                        </div>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i> Bilgi</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-3">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <strong>Minimum Yatırım:</strong> <?= number_format($minDeposit, 2) ?> USDT
                        </li>
                        <li class="mb-3">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <strong>Ödeme Yöntemi:</strong> USDT (TRC-20)
                        </li>
                        <li class="mb-3">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <strong>İşlem Süresi:</strong> Genellikle 5-30 dakika
                        </li>
                        <li class="mb-3">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <strong>İşlem Ücreti:</strong> Yok
                        </li>
                    </ul>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Önemli:</strong> Lütfen sadece TRC-20 ağını kullanın. Diğer ağlar üzerinden yapılan ödemeler işlenemeyebilir.
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i> Son Yatırımlar</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <?php if(count($deposits) > 0): ?>
                            <?php foreach($deposits as $deposit): ?>
                            <div class="list-group-item bg-transparent">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="d-block"><?= number_format($deposit['amount'], 2) ?> USDT</span>
                                        <small class="text-muted"><?= date('d.m.Y H:i', strtotime($deposit['created_at'])) ?></small>
                                    </div>
                                    <?php if($deposit['status'] == 'confirmed'): ?>
                                        <span class="badge bg-success">Tamamlandı</span>
                                    <?php elseif($deposit['status'] == 'pending'): ?>
                                        <span class="badge bg-warning">Beklemede</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Başarısız</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="list-group-item bg-transparent text-center py-5">
                                <p class="mb-0 text-muted">Henüz yatırım işlemi bulunmuyor</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <a href="transactions.php?type=deposit" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-list me-1"></i> Tüm Yatırımları Görüntüle
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Miktar butonları
    const amountBtns = document.querySelectorAll('.deposit-amount-btn');
    const amountInput = document.getElementById('amount');
    
    amountBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const amount = parseFloat(this.dataset.amount);
            const currentAmount = parseFloat(amountInput.value) || 0;
            amountInput.value = (currentAmount + amount).toFixed(2);
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>