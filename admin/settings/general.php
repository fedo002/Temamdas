<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/admin_functions.php';
require_once '../../includes/admin_userfunctions.php';

// Admin oturum kontrolü
if(!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

// Bağlantı değişkenini al
$conn = $GLOBALS['db']->getConnection();

// Mevcut ayarları getir
$settings = getSiteSettingsi();

// Form gönderildi mi kontrol et
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updates = [];
    $success = true;
    
    // Site genel ayarları
    if (isset($_POST['general_settings'])) {
        $updates = [
            'site_name' => trim($_POST['site_name']),
            'site_description' => trim($_POST['site_description']),
            'support_email' => trim($_POST['support_email']),
            'contact_email' => trim($_POST['contact_email'])
        ];
        
        $success = updateSiteSettings($updates);
        if ($success) {
            $general_message = ['type' => 'success', 'text' => 'Genel ayarlar başarıyla güncellendi.'];
        } else {
            $general_message = ['type' => 'error', 'text' => 'Ayarlar güncellenirken bir hata oluştu.'];
        }
        
        // Ayarları yeniden yükle
        $settings = getSiteSettings();
    }
    
    // Referans sistemi ayarları
    if (isset($_POST['referral_settings'])) {
        $updates = [
            'referral_active' => isset($_POST['referral_active']) ? 1 : 0,
            'referral_tier1_rate' => floatval($_POST['referral_tier1_rate']) / 100,
            'referral_tier2_rate' => floatval($_POST['referral_tier2_rate']) / 100
        ];
        
        $success = updateSiteSettings($updates);
        if ($success) {
            $referral_message = ['type' => 'success', 'text' => 'Referans sistemi ayarları başarıyla güncellendi.'];
        } else {
            $referral_message = ['type' => 'error', 'text' => 'Ayarlar güncellenirken bir hata oluştu.'];
        }
        
        // Ayarları yeniden yükle
        $settings = getSiteSettings();
    }
    
    // Ödeme ayarları
    if (isset($_POST['payment_settings'])) {
        $updates = [
            'nowpayments_api_key' => trim($_POST['nowpayments_api_key']),
            'nowpayments_ipn_secret' => trim($_POST['nowpayments_ipn_secret']),
            'nowpayments_test_mode' => isset($_POST['nowpayments_test_mode']) ? 1 : 0,
            'min_deposit_amount' => floatval($_POST['min_deposit_amount']),
            'min_withdraw_amount' => floatval($_POST['min_withdraw_amount']),
            'withdraw_fee' => floatval($_POST['withdraw_fee']),
            'trc20_address' => trim($_POST['trc20_address'])
        ];
        
        $success = updatePaymentSettings($updates);
        if ($success) {
            $payment_message = ['type' => 'success', 'text' => 'Ödeme ayarları başarıyla güncellendi.'];
        } else {
            $payment_message = ['type' => 'error', 'text' => 'Ödeme ayarları güncellenirken bir hata oluştu.'];
        }
        
        // Ödeme ayarlarını yeniden yükle
        $payment_settings = getPaymentSettings();
    }
}

// Ödeme ayarlarını al
$payment_settings = getPaymentSettings();

// Sayfa başlığı
$page_title = 'Genel Ayarlar';
include '../includes/header.php';
?>

<div class="container-fluid px-4 py-4">
    <div class="row">
        <div class="col-12 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Site Ayarları</h1>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header p-0">
                    <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab" aria-controls="general" aria-selected="true">
                                <i class="fas fa-cog me-2"></i> Genel Ayarlar
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="referral-tab" data-bs-toggle="tab" data-bs-target="#referral" type="button" role="tab" aria-controls="referral" aria-selected="false">
                                <i class="fas fa-users me-2"></i> Referans Sistemi
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="payment-tab" data-bs-toggle="tab" data-bs-target="#payment" type="button" role="tab" aria-controls="payment" aria-selected="false">
                                <i class="fas fa-credit-card me-2"></i> Ödeme Ayarları
                            </button>
                        </li>
                    </ul>
                </div>
                
                <div class="card-body">
                    <div class="tab-content" id="settingsTabsContent">
                        <!-- Genel Ayarlar -->
                        <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                            <?php if (isset($general_message)): ?>
                            <div class="alert alert-<?= $general_message['type'] == 'success' ? 'success' : 'danger' ?> alert-dismissible fade show">
                                <?= $general_message['text'] ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php endif; ?>
                            
                            <form method="POST" action="">
                                <div class="row mb-3">
                                    <label for="site_name" class="col-md-3 col-form-label">Site Adı:</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" id="site_name" name="site_name" value="<?= htmlspecialchars($settings['site_name'] ?? '') ?>" required>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <label for="site_description" class="col-md-3 col-form-label">Site Açıklaması:</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" id="site_description" name="site_description" value="<?= htmlspecialchars($settings['site_description'] ?? '') ?>">
                                        <div class="form-text">Site başlık altında görünen kısa açıklama.</div>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <label for="support_email" class="col-md-3 col-form-label">Destek E-posta:</label>
                                    <div class="col-md-9">
                                        <input type="email" class="form-control" id="support_email" name="support_email" value="<?= htmlspecialchars($settings['support_email'] ?? '') ?>">
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <label for="contact_email" class="col-md-3 col-form-label">İletişim E-posta:</label>
                                    <div class="col-md-9">
                                        <input type="email" class="form-control" id="contact_email" name="contact_email" value="<?= htmlspecialchars($settings['contact_email'] ?? '') ?>">
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-9 offset-md-3">
                                        <button type="submit" name="general_settings" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i> Ayarları Kaydet
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Referans Sistemi -->
                        <div class="tab-pane fade" id="referral" role="tabpanel" aria-labelledby="referral-tab">
                            <?php if (isset($referral_message)): ?>
                            <div class="alert alert-<?= $referral_message['type'] == 'success' ? 'success' : 'danger' ?> alert-dismissible fade show">
                                <?= $referral_message['text'] ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php endif; ?>
                            
                            <form method="POST" action="">
                                <div class="row mb-3">
                                    <div class="col-md-9 offset-md-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="referral_active" name="referral_active" <?= ($settings['referral_active'] ?? 0) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="referral_active">Referans Sistemi Aktif</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <label for="referral_tier1_rate" class="col-md-3 col-form-label">Tier 1 Komisyon Oranı (%):</label>
                                    <div class="col-md-9">
                                        <input type="number" class="form-control" id="referral_tier1_rate" name="referral_tier1_rate" value="<?= ($settings['referral_tier1_rate'] ?? 0) * 100 ?>" step="0.01" min="0" max="100">
                                        <div class="form-text">Direkt referanslardan alınacak komisyon oranı.</div>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <label for="referral_tier2_rate" class="col-md-3 col-form-label">Tier 2 Komisyon Oranı (%):</label>
                                    <div class="col-md-9">
                                        <input type="number" class="form-control" id="referral_tier2_rate" name="referral_tier2_rate" value="<?= ($settings['referral_tier2_rate'] ?? 0) * 100 ?>" step="0.01" min="0" max="100">
                                        <div class="form-text">İkinci seviye referanslardan alınacak komisyon oranı.</div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-9 offset-md-3">
                                        <button type="submit" name="referral_settings" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i> Ayarları Kaydet
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Ödeme Ayarları -->
                        <div class="tab-pane fade" id="payment" role="tabpanel" aria-labelledby="payment-tab">
                            <?php if (isset($payment_message)): ?>
                            <div class="alert alert-<?= $payment_message['type'] == 'success' ? 'success' : 'danger' ?> alert-dismissible fade show">
                                <?= $payment_message['text'] ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php endif; ?>
                            
                            <form method="POST" action="">
                                <h5 class="mb-3">NOWPayments Ayarları</h5>
                                
                                <div class="row mb-3">
                                    <label for="nowpayments_api_key" class="col-md-3 col-form-label">API Anahtarı:</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" id="nowpayments_api_key" name="nowpayments_api_key" value="<?= htmlspecialchars($payment_settings['nowpayments_api_key'] ?? '') ?>">
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <label for="nowpayments_ipn_secret" class="col-md-3 col-form-label">IPN Secret:</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" id="nowpayments_ipn_secret" name="nowpayments_ipn_secret" value="<?= htmlspecialchars($payment_settings['nowpayments_ipn_secret'] ?? '') ?>">
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-9 offset-md-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="nowpayments_test_mode" name="nowpayments_test_mode" <?= ($payment_settings['nowpayments_test_mode'] ?? 0) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="nowpayments_test_mode">Test Modu</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <hr>
                                
                                <h5 class="mb-3">Genel Ödeme Ayarları</h5>
                                
                                <div class="row mb-3">
                                    <label for="min_deposit_amount" class="col-md-3 col-form-label">Min. Yatırım Tutarı (USDT):</label>
                                    <div class="col-md-9">
                                        <input type="number" class="form-control" id="min_deposit_amount" name="min_deposit_amount" value="<?= $payment_settings['min_deposit_amount'] ?? 10 ?>" step="0.01" min="1">
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <label for="min_withdraw_amount" class="col-md-3 col-form-label">Min. Çekim Tutarı (USDT):</label>
                                    <div class="col-md-9">
                                        <input type="number" class="form-control" id="min_withdraw_amount" name="min_withdraw_amount" value="<?= $payment_settings['min_withdraw_amount'] ?? 20 ?>" step="0.01" min="1">
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <label for="withdraw_fee" class="col-md-3 col-form-label">Çekim Ücreti (%):</label>
                                    <div class="col-md-9">
                                        <input type="number" class="form-control" id="withdraw_fee" name="withdraw_fee" value="<?= $payment_settings['withdraw_fee'] ?? 2 ?>" step="0.1" min="0">
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <label for="trc20_address" class="col-md-3 col-form-label">TRC20 Cüzdan Adresi:</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" id="trc20_address" name="trc20_address" value="<?= htmlspecialchars($payment_settings['trc20_address'] ?? '') ?>">
                                        <div class="form-text">Ödemeleri alacağınız platform cüzdan adresi.</div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-9 offset-md-3">
                                        <button type="submit" name="payment_settings" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i> Ayarları Kaydet
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>