<?php
session_start();
require_once '../../includes/admin_functions.php';

// Admin oturum kontrolü
if(!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

// Referans sistemi ayarlarını al
$settings = getReferralSettings();

// Form gönderildi ise
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updates = [
        'referral_active' => isset($_POST['referral_active']) ? 1 : 0,
        'tier1_rate' => $_POST['tier1_rate'] / 100,
        'tier2_rate' => $_POST['tier2_rate'] / 100,
        'min_deposit_required' => isset($_POST['min_deposit_required']) ? 1 : 0,
        'min_deposit_amount' => $_POST['min_deposit_amount'],
        'bonus_for_referrer' => isset($_POST['bonus_for_referrer']) ? 1 : 0,
        'bonus_amount' => $_POST['bonus_amount']
    ];
    
    $success = updateReferralSettings($updates);
    if ($success) {
        $message = ['type' => 'success', 'text' => 'Referans sistemi ayarları başarıyla güncellendi.'];
    } else {
        $message = ['type' => 'error', 'text' => 'Ayarlar güncellenirken bir hata oluştu.'];
    }
    
    // Güncel ayarları yeniden al
    $settings = getReferralSettings();
}

// Referans istatistikleri
$stats = getReferralStats();

// Sayfa başlığı
$page_title = 'Referans Sistemi';
include '../includes/header.php';
?>

<div class="container-fluid px-4 py-3">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3">Referans Sistemi Ayarları</h1>
        </div>
    </div>
    
    <?php if(isset($message)): ?>
    <div class="alert alert-<?= $message['type'] == 'success' ? 'success' : 'danger' ?> alert-dismissible fade show">
        <?= $message['text'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-lg-7">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Referans Sistemi Ayarları</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="referral_active" name="referral_active" <?= $settings['referral_active'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="referral_active">Referans Sistemi Aktif</label>
                        </div>
                        
                        <h5 class="mb-3">Referans Komisyon Oranları</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">1. Seviye Referans Oranı (%):</label>
                                    <input type="number" class="form-control" name="tier1_rate" value="<?= $settings['tier1_rate'] * 100 ?>" min="0" max="100" step="0.1">
                                    <small class="text-muted">Kullanıcının doğrudan davet ettiği kişilerden alacağı komisyon oranı.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">2. Seviye Referans Oranı (%):</label>
                                    <input type="number" class="form-control" name="tier2_rate" value="<?= $settings['tier2_rate'] * 100 ?>" min="0" max="100" step="0.1">
                                    <small class="text-muted">Dolaylı referanslardan alınacak komisyon oranı.</small>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <h5 class="mb-3">Minimum Yatırım Gereksinimleri</h5>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="min_deposit_required" name="min_deposit_required" <?= $settings['min_deposit_required'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="min_deposit_required">Minimum Yatırım Şartı</label>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Minimum Yatırım Miktarı (USDT):</label>
                            <input type="number" class="form-control" name="min_deposit_amount" value="<?= $settings['min_deposit_amount'] ?>" min="0" step="1">
                            <small class="text-muted">Referans komisyonu kazanmak için gerekli minimum yatırım miktarı.</small>
                        </div>
                        
                        <hr>
                        
                        <h5 class="mb-3">Referans Bonus Sistemi</h5>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="bonus_for_referrer" name="bonus_for_referrer" <?= $settings['bonus_for_referrer'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="bonus_for_referrer">Referans Bonus Sistemi Aktif</label>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Bonus Miktarı (USDT):</label>
                            <input type="number" class="form-control" name="bonus_amount" value="<?= $settings['bonus_amount'] ?>" min="0" step="0.1">
                            <small class="text-muted">Davet edilen her kullanıcı için verilecek bonus miktarı.</small>
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
        
        <div class="col-lg-5">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Referans İstatistikleri</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <label class="fw-bold d-block mb-2">Toplam Referans Sayısı:</label>
                        <h3 class="counter-value" data-target="<?= $stats['total_referrals'] ?>"><?= number_format($stats['total_referrals']) ?></h3>
                    </div>
                    
                    <div class="mb-4">
                        <label class="fw-bold d-block mb-2">Toplam Ödenen Komisyon:</label>
                        <h3 class="counter-value" data-target="<?= $stats['total_commission'] ?>"><?= number_format($stats['total_commission'], 2) ?> USDT</h3>
                    </div>
                    
                    <div class="mb-4">
                        <label class="fw-bold d-block mb-2">Bu Ay Ödenen Komisyon:</label>
                        <h3 class="counter-value" data-target="<?= $stats['monthly_commission'] ?>"><?= number_format($stats['monthly_commission'], 2) ?> USDT</h3>
                    </div>
                    
                    <div class="mb-4">
                        <label class="fw-bold d-block mb-2">En Çok Komisyon Kazanan Kullanıcı:</label>
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h5 class="mb-0"><?= htmlspecialchars($stats['top_referrer']['username']) ?></h5>
                                <span class="text-muted"><?= number_format($stats['top_referrer']['total_commission'], 2) ?> USDT</span>
                            </div>
                            <a href="../users/details.php?id=<?= $stats['top_referrer']['id'] ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Referans Ağacı</h5>
                </div>
                <div class="card-body">
                    <canvas id="referralTreeChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">En Çok Referans Yapan Kullanıcılar</h5>
                    <a href="earnings.php" class="btn btn-sm btn-primary">Tüm Referans Kazançları</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Kullanıcı</th>
                                    <th>Toplam Referans</th>
                                    <th>Aktif Referans</th>
                                    <th>Toplam Kazanç</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stats['top_referrers'] as $user): ?>
                                <tr>
                                    <td><?= htmlspecialchars($user['username']) ?></td>
                                    <td><?= number_format($user['total_referrals']) ?></td>
                                    <td><?= number_format($user['active_referrals']) ?></td>
                                    <td><?= number_format($user['total_commission'], 2) ?> USDT</td>
                                    <td>
                                        <a href="../users/details.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Referans Ağacı Grafiği
    fetch('../ajax/referral_tree.php')
    .then(response => response.json())
    .then(data => {
        const ctx = document.getElementById('referralTreeChart').getContext('2d');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Direkt Referanslar', 'İkinci Seviye Referanslar'],
                datasets: [{
                    data: [data.direct_count, data.indirect_count],
                    backgroundColor: [
                        'rgba(115, 103, 240, 0.7)',
                        'rgba(40, 199, 111, 0.7)'
                    ],
                    borderColor: [
                        '#7367f0',
                        '#28c76f'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#d0d2d6'
                        }
                    }
                }
            }
        });
    });
});
</script>

<?php include '../includes/footer.php'; ?>