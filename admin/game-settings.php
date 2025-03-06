<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Admin oturum kontrolü
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Form gönderilmiş mi?
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = dbConnect();
    
    // Güncellenecek ayarlar
    $settings = [
        'daily_game_active' => isset($_POST['daily_game_active']) ? '1' : '0',
        'stage1_base_reward' => floatval($_POST['stage1_base_reward']),
        'stage2_low_reward' => floatval($_POST['stage2_low_reward']),
        'stage2_medium_reward' => floatval($_POST['stage2_medium_reward']),
        'stage2_high_reward' => floatval($_POST['stage2_high_reward']),
        'stage2_low_chance' => floatval($_POST['stage2_low_chance']) / 100, // Yüzde formatından ondalık formata çevir
        'stage2_medium_chance' => floatval($_POST['stage2_medium_chance']) / 100,
        'stage2_high_chance' => floatval($_POST['stage2_high_chance']) / 100,
        'vip_bonus_multiplier' => floatval($_POST['vip_bonus_multiplier'])
    ];
    
    // Şansların toplamının 100% olduğunu kontrol et
    $total_chance = $settings['stage2_low_chance'] + $settings['stage2_medium_chance'] + $settings['stage2_high_chance'];
    if (abs($total_chance - 1.0) > 0.01) {
        $error = "İkinci aşama şanslarının toplamı 100% olmalıdır. Şu anda: " . number_format($total_chance * 100, 1) . "%";
    } else {
        // Ayarları güncelle
        foreach ($settings as $key => $value) {
            $stmt = $conn->prepare("UPDATE game_settings SET setting_value = ? WHERE setting_key = ?");
            $stmt->bind_param('ss', $value, $key);
            $stmt->execute();
        }
        
        $success = "Oyun ayarları başarıyla güncellendi.";
    }
}

// Mevcut ayarları al
$game_settings = getGameSettings();

$page_title = 'Oyun Ayarları';
include 'includes/header.php';
?>

<div class="container-fluid px-4 py-3">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3">Günlük Ödül Oyunu Ayarları</h1>
        </div>
    </div>
    
    <?php if (isset($error)): ?>
    <div class="alert alert-danger" role="alert">
        <?= $error ?>
    </div>
    <?php endif; ?>
    
    <?php if (isset($success)): ?>
    <div class="alert alert-success" role="alert">
        <?= $success ?>
    </div>
    <?php endif; ?>
    
    <div class="card">
        <div class="card-body">
            <form method="POST">
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="daily_game_active" name="daily_game_active" <?= $game_settings['daily_game_active'] == '1' ? 'checked' : '' ?>>
                    <label class="form-check-label" for="daily_game_active">Günlük Ödül Oyunu Aktif</label>
                </div>
                
                <h5 class="mt-4">Birinci Aşama Ayarları</h5>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Temel Ödül Miktarı (USDT)</label>
                        <input type="number" class="form-control" name="stage1_base_reward" value="<?= $game_settings['stage1_base_reward'] ?>" step="0.1" min="0">
                        <div class="form-text">Birinci aşamayı kazananlara verilecek temel ödül miktarı.</div>
                    </div>
                </div>
                
                <h5 class="mt-4">İkinci Aşama Ödülleri</h5>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Düşük Ödül (USDT)</label>
                        <input type="number" class="form-control" name="stage2_low_reward" value="<?= $game_settings['stage2_low_reward'] ?>" step="0.1" min="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Orta Ödül (USDT)</label>
                        <input type="number" class="form-control" name="stage2_medium_reward" value="<?= $game_settings['stage2_medium_reward'] ?>" step="0.1" min="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Yüksek Ödül (USDT)</label>
                        <input type="number" class="form-control" name="stage2_high_reward" value="<?= $game_settings['stage2_high_reward'] ?>" step="0.1" min="0">
                    </div>
                </div>
                
                <h5 class="mt-4">İkinci Aşama Şansları</h5>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Düşük Ödül Şansı (%)</label>
                        <input type="number" class="form-control" name="stage2_low_chance" value="<?= $game_settings['stage2_low_chance'] * 100 ?>" step="1" min="0" max="100">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Orta Ödül Şansı (%)</label>
                        <input type="number" class="form-control" name="stage2_medium_chance" value="<?= $game_settings['stage2_medium_chance'] * 100 ?>" step="1" min="0" max="100">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Yüksek Ödül Şansı (%)</label>
                        <input type="number" class="form-control" name="stage2_high_chance" value="<?= $game_settings['stage2_high_chance'] * 100 ?>" step="1" min="0" max="100">
                    </div>
                </div>
                <div class="alert alert-info">
                    <strong>Not:</strong> İkinci aşama şanslarının toplamı 100% olmalıdır.
                </div>
                
                <h5 class="mt-4">VIP Bonus Ayarları</h5>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">VIP Bonus Çarpanı</label>
                        <input type="number" class="form-control" name="vip_bonus_multiplier" value="<?= $game_settings['vip_bonus_multiplier'] ?>" step="0.1" min="0">
                        <div class="form-text">VIP seviyesi başına eklenen bonus miktarı.</div>
                    </div>
                </div>
                
                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> Ayarları Kaydet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>