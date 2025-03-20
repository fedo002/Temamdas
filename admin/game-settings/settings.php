<?php
session_start();
require_once '../../includes/admin_userfunctions.php';
require_once '../../includes/admin_functions.php';

// Admin oturum kontrolü
if(!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

// Veritabanı bağlantısı
$conn = dbConnect();

// Oyun ayarlarını al (tüm VIP seviyeleri dahil)
function getAllGameSettings() {
    global $conn;
    
    $settings = [];
    $settings_by_vip = [];
    
    // Tüm game_settings tablosunu oku
    $query = "SELECT setting_key, setting_value, vip_level, category, description FROM game_settings ORDER BY vip_level ASC, setting_key ASC";
    $result = $conn->query($query);
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $key = $row['setting_key'];
            $value = $row['setting_value'];
            $level = $row['vip_level'];
            $category = $row['category'];
            
            // Genel ayarlar
            if ($level == 0) {
                $settings[$key] = $value;
            }
            
            // Her VIP seviyesi için ayrı dizi oluştur
            if (!isset($settings_by_vip[$level])) {
                $settings_by_vip[$level] = [];
            }
            
            // Ayarı VIP seviyesinin dizisine ekle
            $settings_by_vip[$level][$key] = [
                'value' => $value,
                'category' => $category,
                'description' => $row['description']
            ];
        }
    }
    
    return [
        'general' => $settings,
        'by_vip' => $settings_by_vip
    ];
}

// Site ayarlarını al
function getSiteSettingsForReferral() {
    global $conn;
    
    $settings = [];
    
    // site_settings tablosundan referans sistemi ayarlarını al
    $query = "SELECT setting_key, setting_value FROM site_settings 
              WHERE setting_key LIKE 'referral_%' OR setting_key = 'referral_active'";
    $result = $conn->query($query);
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    }
    
    // Varsayılan değerleri ayarla
    if (!isset($settings['referral_active'])) {
        $settings['referral_active'] = '1';
    }
    
    if (!isset($settings['referral_gametier1_rate'])) {
        $settings['referral_gametier1_rate'] = '0.02';
    }
    
    if (!isset($settings['referral_gametier2_rate'])) {
        $settings['referral_gametier2_rate'] = '0.01';
    }
    
    if (!isset($settings['referral_tier1_rate'])) {
        $settings['referral_tier1_rate'] = '0.02';
    }
    
    if (!isset($settings['referral_tier2_rate'])) {
        $settings['referral_tier2_rate'] = '0.01';
    }
    
    return $settings;
}
// Site ayarlarını güncelle
function updateReferralSiteSettings($updates) {
    global $conn;
    
    foreach ($updates as $key => $value) {
        // Önce ayarın mevcut olup olmadığını kontrol et
        $check_query = "SELECT setting_key FROM site_settings WHERE setting_key = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param('s', $key);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            // Ayar varsa güncelle
            $update_query = "UPDATE site_settings SET setting_value = ? WHERE setting_key = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param('ss', $value, $key);
            $update_stmt->execute();
        } else {
            // Ayar yoksa ekle
            $insert_query = "INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?)";
            $insert_stmt = $conn->prepare($insert_query);
            $insert_stmt->bind_param('ss', $key, $value);
            $insert_stmt->execute();
        }
    }
    
    return true;
}

// Oyun ayarlarını güncelle
function updateGameSettingsWithVIP($updates) {
    global $conn;
    
    foreach ($updates as $key => $data) {
        // Eğer VIP seviyesine özel bir ayar ise
        if (strpos($key, '_vip') !== false) {
            $parts = explode('_vip', $key);
            $settingKey = $parts[0];
            $vipLevel = intval($parts[1]);
            
            $stmt = $conn->prepare("UPDATE game_settings SET setting_value = ? WHERE setting_key = ? AND vip_level = ?");
            $stmt->bind_param('ssi', $data, $settingKey, $vipLevel);
        } else {
            // Genel ayar ise
            $stmt = $conn->prepare("UPDATE game_settings SET setting_value = ? WHERE setting_key = ? AND vip_level = 0");
            $stmt->bind_param('ss', $data, $key);
        }
        
        $stmt->execute();
    }
    
    return true;
}

// Oyun istatistiklerini al
$stats = getGameStats();

// VIP paketlerini al
$vip_packages = getVIPPackages();

// Tüm ayarları al (VIP seviyelerine göre)
$all_settings = getAllGameSettings();
$settings = $all_settings['general']; // Genel ayarlar
$settings_by_vip = $all_settings['by_vip']; // VIP seviyelerine göre ayarlar

// Site ayarlarını al (referans sistemi için)
$site_settings = getSiteSettingsForReferral();

// Aktif VIP seviyesini belirle (URL'den veya varsayılan 0)
$active_vip_level = isset($_GET['vip']) ? intval($_GET['vip']) : 0;

// Form gönderildi ise
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updates = [];
    
    // Temel ayarlar
    if (isset($_POST['basic_settings'])) {
        $updates['daily_game_active'] = isset($_POST['daily_game_active']) ? '1' : '0';
        $updates['vip_chance_adjustment'] = $_POST['vip_chance_adjustment'];
    }
    
    // VIP seviyesine göre ödül ayarları
    elseif (isset($_POST['vip_level_settings'])) {
        $vip_level = intval($_POST['vip_level']);
        
        // Aşama 1 ödülü
        $updates["stage1_base_reward_vip{$vip_level}"] = $_POST['stage1_base_reward'];
        
        // Aşama 2 ödülleri
        $updates["stage2_low_reward_vip{$vip_level}"] = $_POST['stage2_low_reward'];
        $updates["stage2_medium_reward_vip{$vip_level}"] = $_POST['stage2_medium_reward'];
        $updates["stage2_high_reward_vip{$vip_level}"] = $_POST['stage2_high_reward'];
        
        // VIP 0 (genel) için ise şansları da güncelle
        if ($vip_level == 0) {
            $updates['stage2_low_chance'] = strval($_POST['stage2_low_chance'] / 100);
            $updates['stage2_medium_chance'] = strval($_POST['stage2_medium_chance'] / 100);
            $updates['stage2_high_chance'] = strval($_POST['stage2_high_chance'] / 100);
        }
    }
    
    // Referans sistemi ayarları
    elseif (isset($_POST['referral_settings'])) {
        $site_updates = [
            'referral_active' => isset($_POST['referral_active']) ? '1' : '0',
            'referral_gametier1_rate' => strval($_POST['referral_gametier1_rate'] / 100),
            'referral_gametier2_rate' => strval($_POST['referral_gametier2_rate'] / 100)
        ];
        
        $success = updateReferralSiteSettings($site_updates);
        if ($success) {
            $message = ['type' => 'success', 'text' => 'Referans sistemi ayarları başarıyla güncellendi.'];
        } else {
            $message = ['type' => 'error', 'text' => 'Referans ayarları güncellenirken bir hata oluştu.'];
        }
        
        // Güncel site ayarlarını yeniden al
        $site_settings = getSiteSettingsForReferral();
    }
    
    if (!empty($updates)) {
        $success = updateGameSettingsWithVIP($updates);
        if ($success) {
            $message = ['type' => 'success', 'text' => 'Oyun ayarları başarıyla güncellendi.'];
        } else {
            $message = ['type' => 'error', 'text' => 'Ayarlar güncellenirken bir hata oluştu.'];
        }
        
        // Güncel ayarları yeniden al
        $all_settings = getAllGameSettings();
        $settings = $all_settings['general']; // Genel ayarlar
        $settings_by_vip = $all_settings['by_vip']; // VIP seviyelerine göre ayarlar
    }
}

// Helper fonksiyon - belirli bir VIP seviyesi için ayar değerini al
function getSettingForVip($key, $vip, $settings_by_vip, $default = '0') {
    if (isset($settings_by_vip[$vip]) && isset($settings_by_vip[$vip][$key])) {
        return $settings_by_vip[$vip][$key]['value'];
    }
    
    // VIP 0 (genel) ayarını dene
    if (isset($settings_by_vip[0]) && isset($settings_by_vip[0][$key])) {
        return $settings_by_vip[0][$key]['value'];
    }
    
    return $default;
}

// Sayfa başlığı
$page_title = 'Oyun Ayarları';
include '../includes/header.php';
?>

<div class="container-fluid px-4 py-3">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3">Oyun Ayarları</h1>
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
            <!-- Temel Ayarlar Kartı -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Temel Oyun Ayarları</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="basic_settings" value="1">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="daily_game_active" name="daily_game_active" <?= $settings['daily_game_active'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="daily_game_active">Günlük Ödül Oyunu Aktif</label>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">VIP Şans Ayarlaması (%):</label>
                            <input type="number" class="form-control" name="vip_chance_adjustment" value="<?= $settings['vip_chance_adjustment'] ?? 5 ?>" min="0" max="20">
                            <small class="text-muted">Her VIP seviyesi için yüksek ödül şansını artıracak ve düşük ödül şansını azaltacak oran.</small>
                        </div>
                        
                        <div class="text-end mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Temel Ayarları Kaydet
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Referans Sistemi Ayarları -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Referans Sistemi Ayarları</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="referral_settings" value="1">
                        
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="referral_active" name="referral_active" <?= $site_settings['referral_active'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="referral_active">Referans Sistemi Aktif</label>
                        </div>
                        
                        <h5 class="mb-3">Oyun Kazançları için Referans Oranları</h5>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">1. Seviye Referans Oranı (%):</label>
                                    <input type="number" class="form-control" name="referral_gametier1_rate" 
                                           value="<?= floatval($site_settings['referral_gametier1_rate']) * 100 ?>" 
                                           step="0.1" min="0" max="50">
                                    <small class="text-muted">Oyundan kazanılan ödülden 1. seviye referansa verilecek yüzde.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">2. Seviye Referans Oranı (%):</label>
                                    <input type="number" class="form-control" name="referral_gametier2_rate" 
                                           value="<?= floatval($site_settings['referral_gametier2_rate']) * 100 ?>" 
                                           step="0.1" min="0" max="50">
                                    <small class="text-muted">Oyundan kazanılan ödülden 2. seviye referansa verilecek yüzde.</small>
                                </div>
                            </div>
                        </div>
                        
                        
                        <div class="text-end mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Referans Ayarlarını Kaydet
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- VIP Seviyelerine Göre Ödül Ayarları -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">VIP Seviyesine Göre Ödül Ayarları</h5>
                    <div class="btn-group">
                        <a href="?vip=0" class="btn btn-sm btn-outline-secondary <?= $active_vip_level == 0 ? 'active' : '' ?>">Genel</a>
                        <?php for($i = 1; $i <= 5; $i++): ?>
                        <a href="?vip=<?= $i ?>" class="btn btn-sm btn-outline-secondary <?= $active_vip_level == $i ? 'active' : '' ?>">VIP <?= $i ?></a>
                        <?php endfor; ?>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="vip_level_settings" value="1">
                        <input type="hidden" name="vip_level" value="<?= $active_vip_level ?>">
                        
                        <h5 class="mb-3">
                            <?= $active_vip_level == 0 ? 'Genel Ayarlar (VIP Yok)' : 'VIP ' . $active_vip_level . ' Ayarları' ?>
                        </h5>
                        
                        <div class="mb-3">
                            <label class="form-label">Aşama 1 Ödülü (USDT):</label>
                            <input type="number" class="form-control" name="stage1_base_reward" 
                                value="<?= getSettingForVip('stage1_base_reward', $active_vip_level, $settings_by_vip, 5) ?>" 
                                step="0.1" min="0">
                            <small class="text-muted">Kullanıcının ilk aşamada kazandığında alacağı ödül miktarı.</small>
                        </div>
                        
                        <hr>
                        
                        <h5 class="mb-3">Aşama 2 Ödülleri</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Düşük Ödül (USDT):</label>
                                    <input type="number" class="form-control" name="stage2_low_reward" 
                                        value="<?= getSettingForVip('stage2_low_reward', $active_vip_level, $settings_by_vip, 3) ?>" 
                                        step="0.1" min="0">
                                </div>
                                
                                <?php if($active_vip_level == 0): ?>
                                <div class="mb-3">
                                    <label class="form-label">Düşük Ödül Şansı (%):</label>
                                    <input type="number" class="form-control" name="stage2_low_chance" 
                                        value="<?= floatval($settings['stage2_low_chance'] ?? 0.75) * 100 ?>" 
                                        min="0" max="100">
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Orta Ödül (USDT):</label>
                                    <input type="number" class="form-control" name="stage2_medium_reward" 
                                        value="<?= getSettingForVip('stage2_medium_reward', $active_vip_level, $settings_by_vip, 7) ?>" 
                                        step="0.1" min="0">
                                </div>
                                
                                <?php if($active_vip_level == 0): ?>
                                <div class="mb-3">
                                    <label class="form-label">Orta Ödül Şansı (%):</label>
                                    <input type="number" class="form-control" name="stage2_medium_chance" 
                                        value="<?= floatval($settings['stage2_medium_chance'] ?? 0.20) * 100 ?>" 
                                        min="0" max="100">
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Yüksek Ödül (USDT):</label>
                                    <input type="number" class="form-control" name="stage2_high_reward" 
                                        value="<?= getSettingForVip('stage2_high_reward', $active_vip_level, $settings_by_vip, 10) ?>" 
                                        step="0.1" min="0">
                                </div>
                                
                                <?php if($active_vip_level == 0): ?>
                                <div class="mb-3">
                                    <label class="form-label">Yüksek Ödül Şansı (%):</label>
                                    <input type="number" class="form-control" name="stage2_high_chance" 
                                        value="<?= floatval($settings['stage2_high_chance'] ?? 0.05) * 100 ?>" 
                                        min="0" max="100">
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <?php if($active_vip_level == 0): ?>
                        <div class="total-percentage-warning alert alert-warning mt-3" style="display: none;">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <span>Uyarı: Ödül şansları toplamı %100 olmalıdır.</span>
                        </div>
                        <?php endif; ?>
                        
                        <div class="text-end mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> 
                                <?= $active_vip_level == 0 ? 'Genel Ayarları Kaydet' : 'VIP ' . $active_vip_level . ' Ayarlarını Kaydet' ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Oyun İstatistikleri</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <label class="fw-bold d-block mb-2">Bugünkü Oyun Sayısı:</label>
                        <h3 class="counter-value" data-target="<?= $stats['today_games'] ?? 0 ?>"><?= $stats['today_games'] ?? 0 ?></h3>
                    </div>
                    
                    <div class="mb-4">
                        <label class="fw-bold d-block mb-2">Bugün Dağıtılan Ödül:</label>
                        <h3 class="counter-value" data-target="<?= $stats['today_rewards'] ?? 0 ?>"><?= number_format($stats['today_rewards'] ?? 0, 2) ?> USDT</h3>
                    </div>
                    
                    <div class="mb-4">
                        <label class="fw-bold d-block mb-2">Toplam Oyun Sayısı:</label>
                        <h3 class="counter-value" data-target="<?= $stats['total_games'] ?? 0 ?>"><?= $stats['total_games'] ?? 0 ?></h3>
                    </div>
                    
                    <div class="mb-4">
                        <label class="fw-bold d-block mb-2">Toplam Dağıtılan Ödül:</label>
                        <h3 class="counter-value" data-target="<?= $stats['total_rewards'] ?? 0 ?>"><?= number_format($stats['total_rewards'] ?? 0, 2) ?> USDT</h3>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Referans Sistemi Özeti</h5>
                </div>
                <div class="card-body">
                    <p><strong>Durum:</strong> <?= $site_settings['referral_active'] ? '<span class="text-success">Aktif</span>' : '<span class="text-danger">Pasif</span>' ?></p>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mt-3">Oyun Kazançları</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td>1. Seviye:</td>
                                    <td><?= number_format(floatval($site_settings['referral_gametier1_rate']) * 100, 2) ?>%</td>
                                </tr>
                                <tr>
                                    <td>2. Seviye:</td>
                                    <td><?= number_format(floatval($site_settings['referral_gametier2_rate']) * 100, 2) ?>%</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">VIP Oyun Limitleri</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Her VIP seviyesi için günlük oyun limitleri ve maksimum kazanma şansları:</p>
                    
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>VIP Paketi</th>
                                <th>Günlük Limit</th>
                                <th>Max Kazanma Şansı</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(is_array($vip_packages) && count($vip_packages) > 0): ?>
                                <?php foreach($vip_packages as $package): ?>
                                <tr>
                                    <td><?= $package['name'] ?></td>
                                    <td><?= $package['daily_game_limit'] ?></td>
                                    <td><?= ($package['game_max_win_chance'] * 100) ?>%</td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center">VIP paketi bulunamadı.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    
                    <div class="text-center mt-3">
                        <a href="../vip-packages/list.php" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-cog me-1"></i> VIP Paketlerini Düzenle
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Ödül Tablosu Özeti</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>VIP Seviyesi</th>
                                <th>Aşama 1</th>
                                <th>Düşük</th>
                                <th>Orta</th>
                                <th>Yüksek</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php for($i = 0; $i <= 5; $i++): ?>
                            <tr <?= $i == $active_vip_level ? 'class="table-primary"' : '' ?>>
                                <td><?= $i == 0 ? 'Genel' : 'VIP ' . $i ?></td>
                                <td><?= getSettingForVip('stage1_base_reward', $i, $settings_by_vip, 5) ?></td>
                                <td><?= getSettingForVip('stage2_low_reward', $i, $settings_by_vip, 3) ?></td>
                                <td><?= getSettingForVip('stage2_medium_reward', $i, $settings_by_vip, 7) ?></td>
                                <td><?= getSettingForVip('stage2_high_reward', $i, $settings_by_vip, 10) ?></td>
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if($active_vip_level == 0): ?>
    // Yüzde toplamı kontrolü (sadece VIP 0 için)
    const lowRateInput = document.querySelector('input[name="stage2_low_chance"]');
    const mediumRateInput = document.querySelector('input[name="stage2_medium_chance"]');
    const highRateInput = document.querySelector('input[name="stage2_high_chance"]');
    const warningDiv = document.querySelector('.total-percentage-warning');
    
    if (lowRateInput && mediumRateInput && highRateInput) {
        [lowRateInput, mediumRateInput, highRateInput].forEach(input => {
            input.addEventListener('input', checkTotalPercentage);
        });
        
        function checkTotalPercentage() {
            const total = parseFloat(lowRateInput.value) + parseFloat(mediumRateInput.value) + parseFloat(highRateInput.value);
            
            if (Math.abs(total - 100) > 0.1) {
                warningDiv.style.display = 'block';
            } else {
                warningDiv.style.display = 'none';
            }
        }
        
        // İlk kontrolü yap
        checkTotalPercentage();
    }
    <?php endif; ?>
});
</script>

<?php include '../includes/footer.php'; ?>