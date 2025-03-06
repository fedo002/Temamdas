<?php
session_start();
require_once '../../includes/admin_functions.php';

// Admin oturum kontrolü
if(!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

// Oyun ayarlarını al
$settings = getGameSettings();

// Form gönderildi ise
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updates = [
        'daily_game_active' => isset($_POST['daily_game_active']) ? 1 : 0,
        'default_daily_limit' => $_POST['default_daily_limit'],
        'stage1_win_rate' => $_POST['stage1_win_rate'] / 100,
        'stage2_low_reward' => $_POST['stage2_low_reward'],
        'stage2_medium_reward' => $_POST['stage2_medium_reward'],
        'stage2_high_reward' => $_POST['stage2_high_reward'],
        'stage2_low_rate' => $_POST['stage2_low_rate'] / 100,
        'stage2_medium_rate' => $_POST['stage2_medium_rate'] / 100,
        'stage2_high_rate' => $_POST['stage2_high_rate'] / 100
    ];
    
    $success = updateGameSettings($updates);
    if ($success) {
        $message = ['type' => 'success', 'text' => 'Oyun ayarları başarıyla güncellendi.'];
    } else {
        $message = ['type' => 'error', 'text' => 'Ayarlar güncellenirken bir hata oluştu.'];
    }
    
    // Güncel ayarları yeniden al
    $settings = getGameSettings();
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
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Günlük Ödül Oyunu Ayarları</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="daily_game_active" name="daily_game_active" <?= $settings['daily_game_active'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="daily_game_active">Günlük Ödül Oyunu Aktif</label>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Standart Kullanıcılar için Günlük Oyun Limiti:</label>
                            <input type="number" class="form-control" name="default_daily_limit" value="<?= $settings['default_daily_limit'] ?>" min="1" max="10">
                            <small class="text-muted">VIP kullanıcılar için limitler, VIP paket ayarlarından düzenlenir.</small>
                        </div>
                        
                        <hr>
                        
                        <h5 class="mb-3">Aşama 1 Ayarları</h5>
                        <div class="mb-3">
                            <label class="form-label">Kazanma Şansı (%):</label>
                            <input type="number" class="form-control" name="stage1_win_rate" value="<?= $settings['stage1_win_rate'] * 100 ?>" min="1" max="100">
                            <small class="text-muted">Kullanıcının ilk aşamada doğru kartı bulma şansı.</small>
                        </div>
                        
                        <hr>
                        
                        <h5 class="mb-3">Aşama 2 Ayarları</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Düşük Ödül (USDT):</label>
                                    <input type="number" class="form-control" name="stage2_low_reward" value="<?= $settings['stage2_low_reward'] ?>" step="0.1" min="0">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Düşük Ödül Şansı (%):</label>
                                    <input type="number" class="form-control" name="stage2_low_rate" value="<?= $settings['stage2_low_rate'] * 100 ?>" min="0" max="100">
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Orta Ödül (USDT):</label>
                                    <input type="number" class="form-control" name="stage2_medium_reward" value="<?= $settings['stage2_medium_reward'] ?>" step="0.1" min="0">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Orta Ödül Şansı (%):</label>
                                    <input type="number" class="form-control" name="stage2_medium_rate" value="<?= $settings['stage2_medium_rate'] * 100 ?>" min="0" max="100">
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Yüksek Ödül (USDT):</label>
                                    <input type="number" class="form-control" name="stage2_high_reward" value="<?= $settings['stage2_high_reward'] ?>" step="0.1" min="0">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Yüksek Ödül Şansı (%):</label>
                                    <input type="number" class="form-control" name="stage2_high_rate" value="<?= $settings['stage2_high_rate'] * 100 ?>" min="0" max="100">
                                </div>
                            </div>
                        </div>
                        
                        <div class="total-percentage-warning alert alert-warning mt-3" style="display: none;">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <span>Uyarı: Ödül şansları toplamı %100 olmalıdır.</span>
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
                    <h5 class="mb-0">Oyun İstatistikleri</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <label class="fw-bold d-block mb-2">Bugünkü Oyun Sayısı:</label>
                        <h3 class="counter-value" data-target="<?= $stats['today_games'] ?>"><?= $stats['today_games'] ?></h3>
                    </div>
                    
                    <div class="mb-4">
                        <label class="fw-bold d-block mb-2">Bugün Dağıtılan Ödül:</label>
                        <h3 class="counter-value" data-target="<?= $stats['today_rewards'] ?>"><?= number_format($stats['today_rewards'], 2) ?> USDT</h3>
                    </div>
                    
                    <div class="mb-4">
                        <label class="fw-bold d-block mb-2">Toplam Oyun Sayısı:</label>
                        <h3 class="counter-value" data-target="<?= $stats['total_games'] ?>"><?= $stats['total_games'] ?></h3>
                    </div>
                    
                    <div class="mb-4">
                        <label class="fw-bold d-block mb-2">Toplam Dağıtılan Ödül:</label>
                        <h3 class="counter-value" data-target="<?= $stats['total_rewards'] ?>"><?= number_format($stats['total_rewards'], 2) ?> USDT</h3>
                    </div>
                </div>
            </div>
            
            <div class="card">
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
                            <?php foreach($vip_packages as $package): ?>
                            <tr>
                                <td><?= $package['name'] ?></td>
                                <td><?= $package['daily_game_limit'] ?></td>
                                <td><?= ($package['game_max_win_chance'] * 100) ?>%</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <div class="text-center mt-3">
                        <a href="../vip-packages/list.php" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-cog me-1"></i> VIP Paketlerini Düzenle
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Yüzde toplamı kontrolü
    const lowRateInput = document.querySelector('input[name="stage2_low_rate"]');
    const mediumRateInput = document.querySelector('input[name="stage2_medium_rate"]');
    const highRateInput = document.querySelector('input[name="stage2_high_rate"]');
    const warningDiv = document.querySelector('.total-percentage-warning');
    
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
});
</script>

<?php include '../includes/footer.php'; ?>