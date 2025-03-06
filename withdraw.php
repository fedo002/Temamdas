<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

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
$minWithdraw = $settings['min_withdraw_amount'];
$withdrawFee = $settings['withdraw_fee'];

// Son çekimleri al
$withdrawals = getUserWithdrawals($user_id, 5);

// Çekim işlemi
$withdrawSuccess = false;
$withdrawError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = (float)$_POST['amount'];
    $address = trim($_POST['trc20_address']);
    
    // Validasyon
    $errors = [];
    
    if ($amount < $minWithdraw) {
        $errors[] = "Minimum çekim tutarı " . number_format($minWithdraw, 2) . " USDT olmalıdır.";
    }
    
    // Bakiye kontrolü
    $fee = ($amount * $withdrawFee) / 100;
    $totalAmount = $amount + $fee;
    
    if ($totalAmount > $user['balance']) {
        $errors[] = "Yetersiz bakiye. Toplam tutar (ücret dahil): " . number_format($totalAmount, 2) . " USDT";
    }
    
    // TRC20 adres kontrolü
    if (empty($address)) {
        $errors[] = "TRC20 cüzdan adresi gereklidir.";
    } elseif (!validateTRC20Address($address)) {
        $errors[] = "Geçersiz TRC20 cüzdan adresi.";
    }
    
    // Hata yoksa çekim işlemini yap
    if (empty($errors)) {
        $result = processWithdrawal($user_id, $amount, $address, $fee);
        
        if ($result['success']) {
            $withdrawSuccess = true;
        } else {
            $withdrawError = $result['message'];
        }
    } else {
        $withdrawError = implode('<br>', $errors);
    }
}

$page_title = 'Para Çekme';
include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-hand-holding-usd me-2"></i> USDT TRC-20 Para Çekme</h5>
                </div>
                <div class="card-body">
                    <?php if ($withdrawSuccess): ?>
                    <div class="text-center mb-4">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <h4>Para Çekme Talebi Alındı!</h4>
                        <p>Talebiniz incelendikten sonra işleme alınacaktır. İşlem durumunu "İşlemlerim" sayfasından takip edebilirsiniz.</p>
                    </div>
                    
                    <div class="text-center mt-4">
                        <a href="transactions.php?type=withdraw" class="btn btn-primary">
                            <i class="fas fa-list me-2"></i> İşlemlerim
                        </a>
                        <a href="withdraw.php" class="btn btn-outline-primary ms-2">
                            <i class="fas fa-plus me-2"></i> Yeni Talep
                        </a>
                    </div>
                    <?php else: ?>
                    <?php if ($withdrawError): ?>
                    <div class="alert alert-danger mb-4">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?= $withdrawError ?>
                    </div>
                    <?php endif; ?>
                    
                    <form method="POST" id="withdrawForm">
                        <div class="mb-4">
                            <label class="form-label">Kullanılabilir Bakiye</label>
                            <div class="input-group">
                                <span class="input-group-text bg-success text-white"><i class="fas fa-wallet"></i></span>
                                <input type="text" class="form-control bg-dark" value="<?= number_format($user['balance'], 2) ?> USDT" readonly>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">Çekim Tutarı (USDT)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                <input type="number" class="form-control" name="amount" id="amount" placeholder="Minimum: <?= $minWithdraw ?> USDT" min="<?= $minWithdraw ?>" step="1" required>
                            </div>
                            <div class="form-text">Minimum çekim tutarı: <?= number_format($minWithdraw, 2) ?> USDT</div>
                        </div>
                        
                        <div class="withdraw-amount-buttons mb-4">
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-outline-primary withdraw-amount-btn" data-amount="<?= $minWithdraw ?>"><?= $minWithdraw ?> USDT</button>
                                <button type="button" class="btn btn-outline-primary withdraw-amount-btn" data-amount="50">50 USDT</button>
                                <button type="button" class="btn btn-outline-primary withdraw-amount-btn" data-amount="100">100 USDT</button>
                                <button type="button" class="btn btn-outline-primary withdraw-amount-btn" data-amount="max">Maksimum</button>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">TRC20 USDT Cüzdan Adresi</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-wallet"></i></span>
                                <input type="text" class="form-control" name="trc20_address" placeholder="TLkh4TXUvV6PxdC..." required>
                            </div>
                            <div class="form-text">Lütfen doğru TRC20 ağındaki USDT cüzdan adresinizi girin.</div>
                        </div>
                        
                        <div class="fee-calculator alert alert-info mb-4">
                            <div class="d-flex justify-content-between">
                                <span>Çekim Tutarı:</span>
                                <span id="withdrawAmount">0.00 USDT</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>İşlem Ücreti (<?= $withdrawFee ?>%):</span>
                                <span id="withdrawFee">0.00 USDT</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between fw-bold">
                                <span>Toplam Çekilecek:</span>
                                <span id="totalWithdraw">0.00 USDT</span>
                            </div>
                        </div>
                        
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Önemli:</strong> Lütfen doğru TRC20 cüzdan adresini girdiğinizden emin olun. Yanlış adrese gönderilen ödemeler geri alınamaz.
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane me-2"></i> Para Çekme Talebi Oluştur
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
                            <strong>Minimum Çekim:</strong> <?= number_format($minWithdraw, 2) ?> USDT
                        </li>
                        <li class="mb-3">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <strong>Ödeme Yöntemi:</strong> USDT (TRC-20)
                        </li>
                        <li class="mb-3">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <strong>İşlem Süresi:</strong> Genellikle 24 saat içinde
                        </li>
                        <li class="mb-3">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <strong>İşlem Ücreti:</strong> %<?= $withdrawFee ?>
                        </li>
                    </ul>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Önemli:</strong> Para çekme işlemleri, güvenlik kontrollerinden sonra işleme alınır ve genellikle 24 saat içinde tamamlanır.
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i> Son Çekimler</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <?php if(count($withdrawals) > 0): ?>
                            <?php foreach($withdrawals as $withdrawal): ?>
                            <div class="list-group-item bg-transparent">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="d-block"><?= number_format($withdrawal['amount'], 2) ?> USDT</span>
                                        <small class="text-muted"><?= date('d.m.Y H:i', strtotime($withdrawal['created_at'])) ?></small>
                                    </div>
                                    <?php if($withdrawal['status'] == 'completed'): ?>
                                        <span class="badge bg-success">Tamamlandı</span>
                                    <?php elseif($withdrawal['status'] == 'pending'): ?>
                                        <span class="badge bg-warning">Beklemede</span>
                                    <?php elseif($withdrawal['status'] == 'processing'): ?>
                                        <span class="badge bg-info">İşleniyor</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Başarısız</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="list-group-item bg-transparent text-center py-5">
                                <p class="mb-0 text-muted">Henüz çekim işlemi bulunmuyor</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <a href="transactions.php?type=withdraw" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-list me-1"></i> Tüm Çekimleri Görüntüle
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Miktar butonları
    const amountBtns = document.querySelectorAll('.withdraw-amount-btn');
    const amountInput = document.getElementById('amount');
    const maxBalance = <?= $user['balance'] ?>;
    const withdrawFee = <?= $withdrawFee / 100 ?>;
    const minWithdraw = <?= $minWithdraw ?>;
    
    // Ücret hesaplayıcı
    const withdrawAmountText = document.getElementById('withdrawAmount');
    const withdrawFeeText = document.getElementById('withdrawFee');
    const totalWithdrawText = document.getElementById('totalWithdraw');
    
    // Ücret hesaplama fonksiyonu
    function calculateFee() {
        const amount = parseFloat(amountInput.value) || 0;
        const fee = amount * withdrawFee;
        const total = amount + fee;
        
        withdrawAmountText.textContent = amount.toFixed(2) + ' USDT';
        withdrawFeeText.textContent = fee.toFixed(2) + ' USDT';
        totalWithdrawText.textContent = total.toFixed(2) + ' USDT';
    }
    
    // Miktar butonları için olay dinleyici
    amountBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            if (this.dataset.amount === 'max') {
                // Maksimum çekilebilir tutarı hesapla (ücreti düşerek)
                const maxWithdrawAmount = maxBalance / (1 + withdrawFee);
                amountInput.value = Math.floor(maxWithdrawAmount * 100) / 100;
            } else {
                const amount = parseFloat(this.dataset.amount);
                amountInput.value = amount.toFixed(2);
            }
            
            calculateFee();
        });
    });
    
    // Miktar değiştiğinde ücret hesapla
    amountInput.addEventListener('input', calculateFee);
    
    // Sayfa yüklendiğinde ücret hesapla
    calculateFee();
});
</script>

<?php include 'includes/footer.php'; ?>