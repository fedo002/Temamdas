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
$user_mining = getUserMiningPackages($user_id);

// Mining paketlerini al
$mining_packages = getMiningPackages();

$page_title = 'Mining Sistemi';
include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center">
                    <h1 class="mb-3">Mining Platformu</h1>
                    <p class="lead">Profesyonel mining paketlerimiz ile pasif gelir elde edin!</p>
                </div>
            </div>
        </div>
    </div>

    <?php if (count($user_mining) > 0): ?>
    <!-- Aktif Mining Paketleri -->
    <div class="row mb-5">
        <div class="col-12 mb-4">
            <h3><i class="fas fa-microchip me-2"></i> Aktif Mining Paketleriniz</h3>
        </div>
        
        <?php foreach($user_mining as $package): ?>
        <div class="col-md-6">
            <div class="card mining-active-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <h4><?= $package['name'] ?></h4>
                        <span class="badge bg-success p-2"><?= $package['status'] ?></span>
                    </div>
                    
                    <div class="mining-stats d-flex flex-wrap mb-3">
                        <div class="stat-badge me-2 mb-2">
                            <i class="fas fa-tachometer-alt me-1"></i> 
                            <span><?= number_format($package['hash_rate'], 2) ?> MH/s</span>
                        </div>
                        <div class="stat-badge me-2 mb-2">
                            <i class="fas fa-bolt me-1"></i> 
                            <span><?= number_format($package['electricity_cost'], 4) ?> kw/h</span>
                        </div>
                        <div class="stat-badge me-2 mb-2">
                            <i class="fas fa-calendar-alt me-1"></i> 
                            <span><?= date('d.m.Y', strtotime($package['purchase_date'])) ?></span>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <div class="small text-muted mb-1">Günlük Kazanç (Tahmini)</div>
                            <h5 class="mb-0 text-success"><?= number_format($package['daily_revenue'], 6) ?> USDT</h5>
                        </div>
                        <div>
                            <div class="small text-muted mb-1">Toplam Kazanç</div>
                            <h5 class="mb-0"><?= number_format($package['total_earned'], 6) ?> USDT</h5>
                        </div>
                    </div>
                    
                    <div class="progress mb-3" style="height: 10px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" 
                             role="progressbar" 
                             style="width: <?= min(($package['total_earned'] / $package['package_price']) * 100, 100) ?>%">
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <span class="small text-muted">Yatırım Geri Dönüşü: <?= number_format(($package['total_earned'] / $package['package_price']) * 100, 1) ?>%</span>
                        <span class="small text-muted">Hedef: <?= number_format($package['package_price'], 2) ?> USDT</span>
                    </div>
                </div>
                <div class="card-footer bg-transparent d-flex justify-content-between">
                    <button class="btn btn-sm btn-outline-primary" onclick="showMiningStats('<?= $package['id'] ?>')">
                        <i class="fas fa-chart-line me-1"></i> İstatistikler
                    </button>
                    
                    <?php if($package['status'] == 'active'): ?>
                    <button class="btn btn-sm btn-outline-warning" onclick="pauseMining('<?= $package['id'] ?>')">
                        <i class="fas fa-pause me-1"></i> Duraklat
                    </button>
                    <?php else: ?>
                    <button class="btn btn-sm btn-outline-success" onclick="resumeMining('<?= $package['id'] ?>')">
                        <i class="fas fa-play me-1"></i> Devam Et
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Mining İstatistikler Grafiği -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0"><i class="fas fa-chart-line me-2"></i> Mining Performansı</h4>
                </div>
                <div class="card-body">
                    <canvas id="miningStatsChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Mining Paketleri -->
    <div class="row">
        <div class="col-12 mb-4">
            <h3><i class="fas fa-store me-2"></i> Mining Paketleri</h3>
        </div>
        
        <?php foreach($mining_packages as $package): ?>
        <div class="col-md-4 mb-4">
            <div class="card mining-package">
                <div class="card-body text-center p-4">
                    <h4 class="mb-3"><?= $package['name'] ?></h4>
                    
                    <div class="hash-rate mb-3">
                        <span class="display-6"><?= number_format($package['hash_rate'], 0) ?></span>
                        <span class="text-muted">MH/s</span>
                    </div>
                    
                    <ul class="list-unstyled text-start mb-4">
                        <li class="mb-2">
                            <i class="fas fa-bolt me-2 text-warning"></i> 
                            Elektrik: <?= number_format($package['electricity_cost'], 4) ?> kw/h
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-chart-line me-2 text-success"></i> 
                            Günlük Getiri: <?= ($package['daily_revenue_rate'] * 100) ?>%
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-coins me-2 text-primary"></i> 
                            Tahmini Günlük: <?= number_format($package['hash_rate'] * $package['daily_revenue_rate'], 6) ?> USDT
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-calendar-day me-2 text-info"></i> 
                            Tahmini Geri Dönüş: <?= ceil($package['package_price'] / ($package['hash_rate'] * $package['daily_revenue_rate'])) ?> gün
                        </li>
                    </ul>
                    
                    <div class="package-price mb-4">
                        <span class="display-6 text-primary"><?= number_format($package['package_price'], 2) ?></span>
                        <span class="text-muted">USDT</span>
                    </div>
                    
                    <button class="btn btn-primary w-100" onclick="buyMiningPackage('<?= $package['id'] ?>')">
                        <i class="fas fa-shopping-cart me-2"></i> Satın Al
                    </button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Mining Satın Alma Modal -->
<div class="modal fade" id="buyMiningModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark">
            <div class="modal-header border-0">
                <h5 class="modal-title">Mining Paketi Satın Al</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="purchaseDetails"></div>
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle me-2"></i> 
                    Mining paketi satın aldıktan sonra, günlük bakiyenize otomatik olarak gelir eklenecektir.
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-primary" id="confirmPurchaseBtn">
                    <i class="fas fa-check-circle me-2"></i> Satın Al
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Mining Stats Chart
document.addEventListener('DOMContentLoaded', function() {
    <?php if (count($user_mining) > 0): ?>
    const ctx = document.getElementById('miningStatsChart').getContext('2d');
    
    // Son 30 günlük verileri al
    fetch('ajax/mining_stats.php')
    .then(response => response.json())
    .then(data => {
        const miningChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.dates,
                datasets: [
                    {
                        label: 'Günlük Kazanç (USDT)',
                        data: data.earnings,
                        borderColor: '#28c76f',
                        backgroundColor: 'rgba(40, 199, 111, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Elektrik Maliyeti (USDT)',
                        data: data.costs,
                        borderColor: '#ea5455',
                        backgroundColor: 'rgba(234, 84, 85, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Net Kazanç (USDT)',
                        data: data.net_earnings,
                        borderColor: '#7367f0',
                        backgroundColor: 'rgba(115, 103, 240, 0.1)',
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            color: '#d0d2d6'
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
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
                },
                elements: {
                    point: {
                        radius: 0
                    }
                }
            }
        });
    });
    <?php endif; ?>
});

// Mining paket satın alma
function buyMiningPackage(packageId) {
    fetch('ajax/get_mining_package.php?id=' + packageId)
    .then(response => response.json())
    .then(data => {
        document.getElementById('purchaseDetails').innerHTML = `
            <h4 class="text-center mb-4">${data.name}</h4>
            <div class="d-flex justify-content-between mb-3">
                <span>Hash Rate:</span>
                <strong>${data.hash_rate} MH/s</strong>
            </div>
            <div class="d-flex justify-content-between mb-3">
                <span>Günlük Getiri:</span>
                <strong>${(data.daily_revenue_rate * 100)}%</strong>
            </div>
            <div class="d-flex justify-content-between mb-3">
                <span>Elektrik Maliyeti:</span>
                <strong>${data.electricity_cost} kw/h</strong>
            </div>
            <div class="d-flex justify-content-between mb-3">
                <span>Paket Fiyatı:</span>
                <strong class="text-primary">${data.package_price} USDT</strong>
            </div>
            <div class="d-flex justify-content-between mb-3">
                <span>Mevcut Bakiyeniz:</span>
                <strong class="text-warning">${data.user_balance} USDT</strong>
            </div>
        `;
        
        const confirmBtn = document.getElementById('confirmPurchaseBtn');
        confirmBtn.setAttribute('data-package-id', packageId);
        confirmBtn.addEventListener('click', confirmPurchase);
        
        new bootstrap.Modal(document.getElementById('buyMiningModal')).show();
    });
}

function confirmPurchase() {
    const packageId = this.getAttribute('data-package-id');
    
    fetch('ajax/buy_mining_package.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'package_id=' + packageId
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert('Mining paketi başarıyla satın alındı!');
            window.location.reload();
        } else {
            alert('Hata: ' + data.message);
        }
    });
}

function pauseMining(packageId) {
    fetch('ajax/toggle_mining.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'package_id=' + packageId + '&action=pause'
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert('Mining durduruldu!');
            window.location.reload();
        } else {
            alert('Hata: ' + data.message);
        }
    });
}

function resumeMining(packageId) {
    fetch('ajax/toggle_mining.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'package_id=' + packageId + '&action=resume'
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert('Mining yeniden başlatıldı!');
            window.location.reload();
        } else {
            alert('Hata: ' + data.message);
        }
    });
}

function showMiningStats(packageId) {
    window.location.href = 'mining-details.php?id=' + packageId;
}
</script>

<?php include 'includes/footer.php'; ?>