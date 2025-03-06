<?php
session_start();
require_once '../../includes/admin_functions.php';

// Admin oturum kontrolü
if(!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

// Mining paketlerini al
$packages = getAllMiningPackages();

// Sayfa başlığı
$page_title = 'Mining Paketleri';
include '../includes/header.php';
?>

<div class="container-fluid px-4 py-3">
    <div class="row align-items-center mb-4">
        <div class="col">
            <h1 class="h3">Mining Paketleri</h1>
        </div>
        <div class="col-auto">
            <a href="create.php" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> Yeni Paket Ekle
            </a>
        </div>
    </div>
    
    <!-- Filtreleme -->
    <div class="card mb-4">
        <div class="card-body">
            <form class="row g-3" method="GET">
                <div class="col-md-3">
                    <label class="form-label">Paket Adı</label>
                    <input type="text" class="form-control" name="name" value="<?= $_GET['name'] ?? '' ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Durumu</label>
                    <select class="form-select" name="status">
                        <option value="">Tümü</option>
                        <option value="active" <?= (isset($_GET['status']) && $_GET['status'] == 'active') ? 'selected' : '' ?>>Aktif</option>
                        <option value="inactive" <?= (isset($_GET['status']) && $_GET['status'] == 'inactive') ? 'selected' : '' ?>>Pasif</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Sıralama</label>
                    <select class="form-select" name="sort">
                        <option value="price_asc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'price_asc') ? 'selected' : '' ?>>Fiyat (Artan)</option>
                        <option value="price_desc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'price_desc') ? 'selected' : '' ?>>Fiyat (Azalan)</option>
                        <option value="hash_asc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'hash_asc') ? 'selected' : '' ?>>Hash Rate (Artan)</option>
                        <option value="hash_desc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'hash_desc') ? 'selected' : '' ?>>Hash Rate (Azalan)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label d-block">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-2"></i> Filtrele
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Mining Paketleri Listesi -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Paket Adı</th>
                            <th>Hash Rate</th>
                            <th>Elektrik Maliyeti</th>
                            <th>Günlük Getiri</th>
                            <th>Fiyat</th>
                            <th>Durum</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($packages as $package): ?>
                        <tr>
                            <td><?= $package['id'] ?></td>
                            <td><?= htmlspecialchars($package['name']) ?></td>
                            <td><?= number_format($package['hash_rate'], 2) ?> MH/s</td>
                            <td><?= number_format($package['electricity_cost'], 4) ?> kw/h</td>
                            <td><?= ($package['daily_revenue_rate'] * 100) ?>%</td>
                            <td><?= number_format($package['package_price'], 2) ?> USDT</td>
                            <td>
                                <?php if($package['is_active']): ?>
                                    <span class="badge bg-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Pasif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="edit.php?id=<?= $package['id'] ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-<?= $package['is_active'] ? 'warning' : 'success' ?>" 
                                            onclick="toggleStatus(<?= $package['id'] ?>, <?= $package['is_active'] ? 0 : 1 ?>)">
                                        <i class="fas fa-<?= $package['is_active'] ? 'pause' : 'play' ?>"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger" 
                                            onclick="deletePackage(<?= $package['id'] ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Mining İstatistikleri -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Mining Kullanım İstatistikleri</h5>
                </div>
                <div class="card-body">
                    <canvas id="miningUsageChart" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Günlük Mining Kazançları</h5>
                </div>
                <div class="card-body">
                    <canvas id="miningEarningsChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Durum Değiştirme Modal -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark">
            <div class="modal-header border-0">
                <h5 class="modal-title">Paket Durumu Değiştir</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="statusMessage">Paket durumunu değiştirmek istediğinize emin misiniz?</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-primary" id="confirmStatusBtn">Onayla</button>
            </div>
        </div>
    </div>
</div>

<!-- Silme Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark">
            <div class="modal-header border-0">
                <h5 class="modal-title">Paketi Sil</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Uyarı!</strong> Bu işlem geri alınamaz!
                </div>
                <p>Bu paketi silmek istediğinize emin misiniz? Bu işlem, aktif kullanıcıların paketlerini etkilemeyecektir.</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Sil</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Durum değiştirme işlevi
function toggleStatus(packageId, newStatus) {
    const statusModal = new bootstrap.Modal(document.getElementById('statusModal'));
    const message = newStatus ? 
        'Bu paketi aktif duruma getirmek istediğinize emin misiniz?' : 
        'Bu paketi pasif duruma getirmek istediğinize emin misiniz?';
    
    document.getElementById('statusMessage').textContent = message;
    document.getElementById('confirmStatusBtn').onclick = function() {
        fetch('../ajax/toggle_mining_package.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'package_id=' + packageId + '&status=' + newStatus
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                statusModal.hide();
                window.location.reload();
            } else {
                alert('Hata: ' + data.message);
            }
        });
    };
    
    statusModal.show();
}

// Silme işlevi
function deletePackage(packageId) {
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    document.getElementById('confirmDeleteBtn').onclick = function() {
        fetch('../ajax/delete_mining_package.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'package_id=' + packageId
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                deleteModal.hide();
                window.location.reload();
            } else {
                alert('Hata: ' + data.message);
            }
        });
    };
    
    deleteModal.show();
}

// Grafikler
document.addEventListener('DOMContentLoaded', function() {
    // Mining Kullanım Grafiği
    fetch('../ajax/mining_stats.php?type=usage')
    .then(response => response.json())
    .then(data => {
        const ctx = document.getElementById('miningUsageChart').getContext('2d');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: data.package_names,
                datasets: [{
                    data: data.usage_counts,
                    backgroundColor: [
                        'rgba(115, 103, 240, 0.7)',
                        'rgba(40, 199, 111, 0.7)',
                        'rgba(255, 159, 67, 0.7)',
                        'rgba(234, 84, 85, 0.7)',
                        'rgba(0, 207, 232, 0.7)'
                    ],
                    borderColor: [
                        '#7367f0',
                        '#28c76f',
                        '#ff9f43',
                        '#ea5455',
                        '#00cfe8'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            color: '#d0d2d6'
                        }
                    }
                }
            }
        });
    });
    
    // Mining Kazanç Grafiği
    fetch('../ajax/mining_stats.php?type=earnings')
    .then(response => response.json())
    .then(data => {
        const ctx = document.getElementById('miningEarningsChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.dates,
                datasets: [{
                    label: 'Toplam Kazanç',
                    data: data.earnings,
                    borderColor: '#28c76f',
                    backgroundColor: 'rgba(40, 199, 111, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            color: '#d0d2d6'
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: '#d0d2d6'
                        }
                    },
                    y: {
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
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