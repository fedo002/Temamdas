<?php
session_start();
require_once '../includes/admin_functions.php';

// Admin oturum kontrolü
if(!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Ödeme ayarlarını al
$settings = getPaymentSettings();

// Form gönderildi ise
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updates = [
        'nowpayments_api_key' => trim($_POST['nowpayments_api_key']),
        'nowpayments_ipn_secret' => trim($_POST['nowpayments_ipn_secret']),
        'nowpayments_test_mode' => isset($_POST['nowpayments_test_mode']) ? 1 : 0,
        'min_deposit_amount' => (float)$_POST['min_deposit_amount'],
        'min_withdraw_amount' => (float)$_POST['min_withdraw_amount'],
        'withdraw_fee' => (float)$_POST['withdraw_fee'],
        'trc20_address' => trim($_POST['trc20_address'])
    ];
    
    $success = updatePaymentSettings($updates);
    if ($success) {
        $message = ['type' => 'success', 'text' => 'Ödeme ayarları başarıyla güncellendi.'];
    } else {
        $message = ['type' => 'error', 'text' => 'Ayarlar güncellenirken bir hata oluştu.'];
    }
    
    // Güncel ayarları yeniden al
    $settings = getPaymentSettings();
}

// Sayfa başlığı
$page_title = 'Ödeme Ayarları';
include 'includes/header.php';
?>

<div class="container-fluid px-4 py-3">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3">Ödeme Ayarları</h1>
        </div>
    </div>
    
    <?php if(isset($message)): ?>
    <div class="alert alert-<?= $message['type'] == 'success' ? 'success' : 'danger' ?> alert-dismissible fade show">
        <?= $message['text'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">NOWPayments.io Ayarları</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            NOWPayments.io API entegrasyonu için gerekli bilgileri girmeniz gerekmektedir. API anahtarı ve IPN Secret bilgilerinizi <a href="https://nowpayments.io/merchant-tools" target="_blank" class="alert-link">NOWPayments Merchant Dashboard</a> üzerinden edinebilirsiniz.
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">API Anahtarı</label>
                            <input type="text" class="form-control" name="nowpayments_api_key" value="<?= htmlspecialchars($settings['nowpayments_api_key']) ?>" required>
                            <div class="form-text">NOWPayments Merchant Dashboard'dan edindiğiniz API anahtarını giriniz.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">IPN Secret Anahtarı</label>
                            <input type="text" class="form-control" name="nowpayments_ipn_secret" value="<?= htmlspecialchars($settings['nowpayments_ipn_secret']) ?>">
                            <div class="form-text">Ödeme bildirimlerini (IPN) doğrulamak için gereklidir.</div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="nowpayments_test_mode" name="nowpayments_test_mode" <?= $settings['nowpayments_test_mode'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="nowpayments_test_mode">Test Modu</label>
                            </div>
                            <div class="form-text">Test modunda gerçek ödemeler alınmaz, sadece test amaçlıdır.</div>
                        </div>
                        
                        <hr>
                        
                        <h5 class="mb-3">TRC-20 USDT Ayarları</h5>
                        
                        <div class="mb-3">
                            <label class="form-label">Platform TRC-20 USDT Cüzdan Adresi</label>
                            <input type="text" class="form-control" name="trc20_address" value="<?= htmlspecialchars($settings['trc20_address']) ?>">
                            <div class="form-text">Manuel ödemeler ve çekimler için kullanılacak ana cüzdan adresi.</div>
                        </div>
                        
                        <hr>
                        
                        <h5 class="mb-3">Ödeme Limitleri</h5>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Minimum Yatırım Tutarı (USDT)</label>
                                    <input type="number" class="form-control" name="min_deposit_amount" value="<?= $settings['min_deposit_amount'] ?>" min="0" step="0.1">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Minimum Çekim Tutarı (USDT)</label>
                                    <input type="number" class="form-control" name="min_withdraw_amount" value="<?= $settings['min_withdraw_amount'] ?>" min="0" step="0.1">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Çekim Ücreti (%)</label>
                                    <input type="number" class="form-control" name="withdraw_fee" value="<?= $settings['withdraw_fee'] ?>" min="0" max="100" step="0.01">
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-end mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Ayarları Kaydet
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">NOWPayments IPN Ayarları</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Önemli:</strong> Ödemelerin otomatik olarak işlenebilmesi için aşağıdaki IPN URL'sini NOWPayments hesabınıza kaydetmelisiniz.
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">IPN Callback URL</label>
                        <div class="input-group">
                            <input type="text" class="form-control bg-dark" value="<?= SITE_URL ?>ipn/nowpayments.php" id="ipn_url" readonly>
                            <button class="btn btn-outline-primary" type="button" onclick="copyIpnUrl()">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    
                    <p class="mb-2"><strong>Adımlar:</strong></p>
                    <ol class="small">
                        <li>NOWPayments Merchant Dashboard'a giriş yapın</li>
                        <li>API Settings bölümüne gidin</li>
                        <li>IPN Callbacks kısmından "Add New URL" butonuna tıklayın</li>
                        <li>Yukarıdaki URL'yi ekleyin</li>
                        <li>Status kısmını "Active" olarak ayarlayın</li>
                    </ol>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Ödeme İstatistikleri</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="fw-bold d-block mb-2">Toplam Yatırım:</label>
                        <h3><?= number_format($stats['total_deposits'], 2) ?> USDT</h3>
                    </div>
                    
                    <div class="mb-3">
                        <label class="fw-bold d-block mb-2">Toplam Çekim:</label>
                        <h3><?= number_format($stats['total_withdrawals'], 2) ?> USDT</h3>
                    </div>
                    
                    <div class="mb-3">
                        <label class="fw-bold d-block mb-2">Bu Ayki Yatırımlar:</label>
                        <h3><?= number_format($stats['monthly_deposits'], 2) ?> USDT</h3>
                    </div>
                    
                    <div class="mb-3">
                        <label class="fw-bold d-block mb-2">Bekleyen Çekimler:</label>
                        <h3><?= number_format($stats['pending_withdrawals'], 2) ?> USDT</h3>
                    </div>
                    
                    <div class="text-center mt-4">
                        <a href="reports/payments.php" class="btn btn-primary">
                            <i class="fas fa-chart-bar me-2"></i> Detaylı Rapor
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyIpnUrl() {
    const ipnUrl = document.getElementById('ipn_url');
    ipnUrl.select();
    document.execCommand('copy');
    alert('IPN URL kopyalandı!');
}
</script>

<?php include 'includes/footer.php'; ?>