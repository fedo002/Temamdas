<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/admin_functions.php';

// Admin oturum kontrolü
if(!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

// Bağlantı değişkenini al
$conn = $GLOBALS['db']->getConnection();

// Silme işlemi
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $package_id = (int)$_GET['id'];
    
    if (deleteMiningPackage($package_id)) {
        $success_message = "Mining paketi başarıyla silindi.";
    } else {
        $error_message = "Paket silinemedi. Paket kullanımda olabilir.";
    }
}

// Durum değiştirme işlemi
if (isset($_GET['action']) && $_GET['action'] === 'toggle' && isset($_GET['id'])) {
    $package_id = (int)$_GET['id'];
    $package = getMiningPackage($package_id);
    
    if ($package) {
        $new_status = $package['is_active'] ? 0 : 1;
        
        $stmt = $conn->prepare("UPDATE mining_packages SET is_active = ? WHERE id = ?");
        
        if ($stmt) {
            $stmt->bind_param("ii", $new_status, $package_id);
            if ($stmt->execute()) {
                $status_message = $new_status ? "Mining paketi aktifleştirildi." : "Mining paketi devre dışı bırakıldı.";
            }
        }
    }
}

// Mining paketlerini getir
$packages = getAllMiningPackages();

// Sayfa başlığı
$page_title = 'Mining Paketleri';
include '../includes/header.php';
?>

<style>
    .stats-table-container {
  max-height: 400px;
  overflow-y: auto;
}

.stats-table-container .col-md-6{
  float: left;
}
</style>


<div class="container-fluid px-4 py-4">
    <div class="row">
        <div class="col-12 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Mining Paketleri</h1>
                <a href="create.php" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i> Yeni Paket Ekle
                </a>
            </div>
        </div>
    </div>
    
    <?php if (isset($success_message) || isset($status_message)): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i> <?= $success_message ?? $status_message ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>
    
    <?php if (isset($error_message)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i> <?= $error_message ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Tüm Mining Paketleri</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Paket Adı</th>
                                    <th>Hash Rate</th>
                                    <th>Elektrik Maliyeti</th>
                                    <th>Günlük Kazanç</th>
                                    <th>Fiyat</th>
                                    <th>Geri Dönüş</th>
                                    <th>Durum</th>
                                    <th>Kullanıcı</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($packages) > 0): ?>
                                    <?php foreach ($packages as $package): ?>
                                    <tr>
                                        <td><?= $package['id'] ?></td>
                                        <td><?= htmlspecialchars($package['name']) ?></td>
                                        <td><?= number_format($package['hash_rate'], 2) ?> MH/s</td>
                                        <td><?= number_format($package['electricity_cost'], 4) ?> kw/h</td>
                                        <td>
                                            <?php
                                            $daily_revenue = $package['hash_rate'] * $package['daily_revenue_rate'];
                                            echo number_format($daily_revenue, 6) . ' USDT';
                                            ?>
                                        </td>
                                        <td><?= number_format($package['package_price'], 2) ?> USDT</td>
                                        <td>
                                            <?php
                                            $roi_days = $daily_revenue > 0 ? ceil($package['package_price'] / $daily_revenue) : 0;
                                            echo $roi_days . ' gün';
                                            ?>
                                        </td>
                                        <td>
                                            <?php if ($package['is_active']): ?>
                                                <span class="badge bg-success">Aktif</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Pasif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= getPackageUserCount($package['id']) ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="edit.php?id=<?= $package['id'] ?>" class="btn btn-primary">
                                                    <i class="fas fa-edit"></i> Düzenle
                                                </a>
                                                <a href="list.php?action=toggle&id=<?= $package['id'] ?>" class="btn btn-<?= $package['is_active'] ? 'warning' : 'success' ?>">
                                                    <i class="fas fa-<?= $package['is_active'] ? 'power-off' : 'check' ?>"></i> <?= $package['is_active'] ? 'Devre Dışı' : 'Aktifleştir' ?>
                                                </a>
                                                <?php if (getPackageUserCount($package['id']) === 0): ?>
                                                <a href="list.php?action=delete&id=<?= $package['id'] ?>" class="btn btn-danger" onclick="return confirm('Bu paketi silmek istediğinize emin misiniz?')">
                                                    <i class="fas fa-trash"></i> Sil
                                                </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="10" class="text-center py-4">
                                            <p class="text-muted mb-0">Henüz mining paketi bulunmuyor.</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Mining İstatistikleri Özeti -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Mining Sistemi İstatistikleri</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-4">
                            <div class="card bg-primary text-white h-100">
                                <div class="card-body text-center">
                                    <h6 class="card-title mb-2">Toplam Aktif Paket</h6>
                                    <h2><?= array_reduce($packages, function($count, $package) { return $count + ($package['is_active'] ? 1 : 0); }, 0) ?></h2>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-4">
                            <div class="card bg-success text-white h-100">
                                <div class="card-body text-center">
                                    <h6 class="card-title mb-2">Toplam Kullanıcı</h6>
                                    <h2><?= getTotalMiningUsers() ?></h2>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-4">
                            <div class="card bg-info text-white h-100">
                                <div class="card-body text-center">
                                    <h6 class="card-title mb-2">Toplam Mining Paketi</h6>
                                    <h2><?= getTotalActivePackages() ?></h2>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-4">
                            <div class="card bg-warning text-white h-100">
                                <div class="card-body text-center">
                                    <h6 class="card-title mb-2">Bugünkü Kazanç</h6>
                                    <h2><?= number_format(getTodayMiningEarnings(), 2) ?> USDT</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                     <div class="stats-table-container">
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">En Popüler Paketler</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="packagesChart" height="250"></canvas>
                                </div>
                            </div>
                            </div>
                        
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Son 7 Gün Kazanç</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="earningsChart" height="250"></canvas>
                                </div>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Paketler Grafiği
    const packagesCtx = document.getElementById('packagesChart').getContext('2d');
    
    // AJAX ile paket verilerini al
    fetch('../ajax/mining_stats.php?type=packages')
    .then(response => response.json())
    .then(data => {
        new Chart(packagesCtx, {
            type: 'pie',
            data: {
                labels: data.names,
                datasets: [{
                    data: data.counts,
                    backgroundColor: [
                        '#7367f0',
                        '#28c76f',
                        '#ea5455',
                        '#ff9f43',
                        '#00cfe8',
                        '#a66efa',
                        '#71dd37',
                        '#fa8b33'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                    }
                }
            }
        });
    })
    .catch(error => {
        console.error('Paket verileri alınırken hata oluştu:', error);
    });
    
    // Kazanç Grafiği
    const earningsCtx = document.getElementById('earningsChart').getContext('2d');
    
    // AJAX ile kazanç verilerini al
    fetch('../ajax/mining_stats.php?type=earnings')
    .then(response => response.json())
    .then(data => {
        new Chart(earningsCtx, {
            type: 'line',
            data: {
                labels: data.dates,
                datasets: [{
                    label: 'Günlük Kazanç (USDT)',
                    data: data.earnings,
                    borderColor: '#28c76f',
                    backgroundColor: 'rgba(40, 199, 111, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    })
    .catch(error => {
        console.error('Kazanç verileri alınırken hata oluştu:', error);
    });
});
</script>

<?php include '../includes/footer.php'; ?>
