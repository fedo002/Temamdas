<?php
session_start();
require_once '../../includes/admin_functions.php';

// Admin oturum kontrolü
if(!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

// VIP paketlerini al
$packages = getAllVIPPackages();

// Sayfa başlığı
$page_title = 'VIP Paketleri';
include '../includes/header.php';
?>

<div class="container-fluid px-4 py-3">
    <div class="row align-items-center mb-4">
        <div class="col">
            <h1 class="h3">VIP Paketleri</h1>
        </div>
        <div class="col-auto">
            <a href="create.php" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> Yeni Paket Ekle
            </a>
        </div>
    </div>
    
    <!-- VIP Paketleri Listesi -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Paket Adı</th>
                            <th>Fiyat</th>
                            <th>Günlük Oyun Limiti</th>
                            <th>Kazanma Şansı</th>
                            <th>Referans Oranı</th>
                            <th>Mining Bonus</th>
                            <th>Durum</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($packages as $package): ?>
                        <tr>
                            <td><?= $package['id'] ?></td>
                            <td><?= htmlspecialchars($package['name']) ?></td>
                            <td><?= number_format($package['price'], 2) ?> USDT</td>
                            <td><?= $package['daily_game_limit'] ?></td>
                            <td><?= ($package['game_max_win_chance'] * 100) ?>%</td>
                            <td><?= ($package['referral_rate'] * 100) ?>%</td>
                            <td><?= ($package['mining_bonus_rate'] * 100) ?>%</td>
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
    
    <!-- VIP İstatistikleri -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">VIP Paket Dağılımı</h5>
                </div>
                <div class="card-body">
                    <canvas id="vipDistributionChart" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Aylık VIP Satışları</h5>
                </div>
                <div class="card-body">
                    <canvas id="vipSalesChart" height="300"></canvas>
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
                <p>Bu VIP paketini silmek istediğinize emin misiniz? Bu işlem, bu paketi satın almış kullanıcıları etkileyebilir.</p>
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
        fetch('../ajax/toggle_vip_package.php', {
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
        fetch('../ajax/delete_vip_package.php', {
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
    // VIP Dağılım Grafiği
    fetch('../ajax/vip_stats.php?type=distribution')
    .then(response => response.json())
    .then(data => {
        const ctx = document.getElementById('vipDistributionChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.package_names,
                datasets: [{
                    data: data.user_counts,
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
    
    // VIP Satış Grafiği
    fetch('../ajax/vip_stats.php?type=sales')
    .then(response => response.json())
    .then(data => {
        const ctx = document.getElementById('vipSalesChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.months,
                datasets: [{
                    label: 'Satış Adedi',
                    data: data.sales_count,
                    backgroundColor: 'rgba(115, 103, 240, 0.7)',
                    borderColor: '#7367f0',
                    borderWidth: 1
                }, {
                    label: 'Gelir (USDT)',
                    data: data.sales_amount,
                    backgroundColor: 'rgba(40, 199, 111, 0.7)',
                    borderColor: '#28c76f',
                    borderWidth: 1,
                    type: 'line',
                    yAxisID: 'y1'
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
                    },
                    y1: {
                        position: 'right',
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#28c76f'
                        }
                    }
                }
            }
        });
    });
});
</script>

<?php include '../includes/footer.php'; ?>