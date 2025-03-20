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

// Tarih filtresi
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-30 days'));
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
$package_filter = isset($_GET['package_id']) ? (int)$_GET['package_id'] : 0;
$user_filter = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;

// Mining istatistiklerini getir
$stats = getMiningStats($start_date, $end_date, $package_filter, $user_filter);

// Özet istatistikler
$total_revenue = 0;
$total_electricity_cost = 0;
$total_net_revenue = 0;
$total_hash_rate = 0;
$active_packages = 0;
$paused_packages = 0;

// Günlük istatistikler
$daily_stats = [];
$chart_dates = [];
$chart_revenues = [];
$chart_costs = [];
$chart_net_revenues = [];

// Mining paketlerini getir
$packages = getAllMiningPackages();

// Kullanıcı listesini getir
$users = getAllUsers();

// Özet istatistikleri hesapla
$summary = getMiningStatsSummary();
$active_packages = $summary['active_packages'];
$paused_packages = $summary['paused_packages'];
$total_hash_rate = $summary['total_hash_rate'];
$total_daily_revenue = $summary['total_daily_revenue'];
$daily_electricity_cost = $summary['daily_electricity_cost'];
$daily_net_revenue = $summary['daily_net_revenue'];

// Son 30 günlük istatistikleri hesapla
for ($i = 0; $i < 30; $i++) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $daily = getDailyMiningStats($date, $package_filter, $user_filter);
    
    $daily_stats[$date] = $daily;
    
    if ($date >= $start_date && $date <= $end_date) {
        $total_revenue += $daily['total_revenue'];
        $total_electricity_cost += $daily['total_electricity_cost'];
        $total_net_revenue += $daily['total_net_revenue'];
    }
    
    // Grafik verileri
    if (count($chart_dates) < 14 && $date >= $start_date && $date <= $end_date) {
        $chart_dates[] = date('d M', strtotime($date));
        $chart_revenues[] = number_format($daily['total_revenue'], 6, '.', '');
        $chart_costs[] = number_format($daily['total_electricity_cost'], 6, '.', '');
        $chart_net_revenues[] = number_format($daily['total_net_revenue'], 6, '.', '');
    }
}

// Verileri tarihe göre sırala
$chart_dates = array_reverse($chart_dates);
$chart_revenues = array_reverse($chart_revenues);
$chart_costs = array_reverse($chart_costs);
$chart_net_revenues = array_reverse($chart_net_revenues);

// Mining paketlerine göre istatistikler
$package_stats = getPackageMiningStats($start_date, $end_date);

// En çok kazanç sağlayan kullanıcılar
$top_users = getTopMiningUsers($start_date, $end_date, 10);

$page_title = 'Mining İstatistikleri';
include '../includes/header.php';
?>

<div class="container-fluid px-4 py-4">
    <div class="row">
        <div class="col-12 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Mining İstatistikleri</h1>
                <a href="export_mining_stats.php?start_date=<?= $start_date ?>&end_date=<?= $end_date ?>&package_id=<?= $package_filter ?>&user_id=<?= $user_filter ?>" class="btn btn-primary">
                    <i class="fas fa-file-export me-2"></i> Dışa Aktar
                </a>
            </div>
        </div>
    </div>
    
    <!-- Filtreler -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="" class="row g-3">
                        <div class="col-md-3">
                            <label for="start_date" class="form-label">Başlangıç Tarihi</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="<?= $start_date ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="end_date" class="form-label">Bitiş Tarihi</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="<?= $end_date ?>">
                        </div>
                        <div class="col-md-2">
                            <label for="package_id" class="form-label">Mining Paketi</label>
                            <select class="form-select" id="package_id" name="package_id">
                                <option value="0">Tüm Paketler</option>
                                <?php foreach ($packages as $package): ?>
                                    <option value="<?= $package['id'] ?>" <?= $package_filter === $package['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($package['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="user_id" class="form-label">Kullanıcı</label>
                            <select class="form-select" id="user_id" name="user_id">
                                <option value="0">Tüm Kullanıcılar</option>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?= $user['id'] ?>" <?= $user_filter === $user['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($user['username']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-2"></i> Filtrele
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Özet Kartları -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Toplam Hash Rate</h6>
                            <h2 class="mt-2 mb-0"><?= number_format($total_hash_rate, 2) ?> MH/s</h2>
                        </div>
                        <div class="fs-1">
                            <i class="fas fa-microchip"></i>
                        </div>
                    </div>
                    <div class="mt-2 text-white-50">
                        <small>Aktif paket sayısı: <?= $active_packages ?></small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Günlük Net Kazanç</h6>
                            <h2 class="mt-2 mb-0"><?= number_format($daily_net_revenue, 6) ?> USDT</h2>
                        </div>
                        <div class="fs-1">
                            <i class="fas fa-coins"></i>
                        </div>
                    </div>
                    <div class="mt-2 text-white-50">
                        <small>Aylık tahmini: <?= number_format($daily_net_revenue * 30, 6) ?> USDT</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0"><?= date('d.m.Y', strtotime($start_date)) ?> - <?= date('d.m.Y', strtotime($end_date)) ?></h6>
                            <h2 class="mt-2 mb-0"><?= number_format($total_net_revenue, 6) ?> USDT</h2>
                        </div>
                        <div class="fs-1">
                            <i class="fas fa-wallet"></i>
                        </div>
                    </div>
                    <div class="mt-2 text-white-50">
                        <small>Brüt kazanç: <?= number_format($total_revenue, 6) ?> USDT</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Elektrik Maliyeti</h6>
                            <h2 class="mt-2 mb-0"><?= number_format($total_electricity_cost, 6) ?> USDT</h2>
                        </div>
                        <div class="fs-1">
                            <i class="fas fa-bolt"></i>
                        </div>
                    </div>
                    <div class="mt-2 text-white-50">
                        <small>Günlük maliyet: <?= number_format($daily_electricity_cost, 6) ?> USDT</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Grafik ve Tablolar -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <!-- Grafik -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Günlük Mining Kazançları</h5>
                </div>
                <div class="card-body">
                    <canvas id="miningChart" height="300"></canvas>
                </div>
            </div>
            
            <!-- Paket Bazlı İstatistikler -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Paket Bazlı İstatistikler</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Paket</th>
                                    <th>Aktif Kullanıcı</th>
                                    <th>Toplam Hash Rate</th>
                                    <th>Günlük Kazanç</th>
                                    <th>Elektrik Maliyeti</th>
                                    <th>Net Kazanç</th>
                                    <th>Toplam Dönem</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($package_stats as $stat): ?>
                                <tr>
                                    <td><?= htmlspecialchars($stat['name']) ?></td>
                                    <td><?= $stat['user_count'] ?></td>
                                    <td><?= number_format($stat['total_hash_rate'], 2) ?> MH/s</td>
                                    <td><?= number_format($stat['daily_revenue'], 6) ?> USDT</td>
                                    <td><?= number_format($stat['daily_electricity_cost'], 6) ?> USDT</td>
                                    <td><?= number_format($stat['daily_net_revenue'], 6) ?> USDT</td>
                                    <td><?= number_format($stat['period_net_revenue'], 6) ?> USDT</td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Günlük Veriler -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Günlük Veriler</h5>
                    <span class="text-muted small"><?= date('d.m.Y', strtotime($start_date)) ?> - <?= date('d.m.Y', strtotime($end_date)) ?></span>
                </div>
                <div class="card-body">
                    <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                        <table class="table table-sm table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Tarih</th>
                                    <th>Brüt</th>
                                    <th>Maliyet</th>
                                    <th>Net</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                // Tarihleri yeni tarihten eskiye sırala
                                $dates = array_keys($daily_stats);
                                rsort($dates);
                                
                                foreach ($dates as $date):
                                    if ($date >= $start_date && $date <= $end_date):
                                        $day_stats = $daily_stats[$date];
                                ?>
                                <tr>
                                    <td><?= date('d.m.Y', strtotime($date)) ?></td>
                                    <td><?= number_format($day_stats['total_revenue'], 6) ?></td>
                                    <td><?= number_format($day_stats['total_electricity_cost'], 6) ?></td>
                                    <td><?= number_format($day_stats['total_net_revenue'], 6) ?></td>
                                </tr>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- En Çok Kazanç Sağlayan Kullanıcılar -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">En Çok Kazananlar</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Kullanıcı</th>
                                    <th>Hash Rate</th>
                                    <th>Net Kazanç</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($top_users as $user): ?>
                                <tr>
                                    <td><?= htmlspecialchars($user['username']) ?></td>
                                    <td><?= number_format($user['hash_rate'], 2) ?> MH/s</td>
                                    <td><?= number_format($user['net_revenue'], 6) ?> USDT</td>
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
    // Mining Grafiği
    const ctx = document.getElementById('miningChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($chart_dates) ?>,
            datasets: [
                {
                    label: 'Brüt Kazanç',
                    data: <?= json_encode($chart_revenues) ?>,
                    borderColor: '#28c76f',
                    backgroundColor: 'rgba(40, 199, 111, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Elektrik Maliyeti',
                    data: <?= json_encode($chart_costs) ?>,
                    borderColor: '#ea5455',
                    backgroundColor: 'rgba(234, 84, 85, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Net Kazanç',
                    data: <?= json_encode($chart_net_revenues) ?>,
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
                },
                tooltip: {
                    mode: 'index',
                    intersect: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>

<?php include '../includes/footer.php'; ?>