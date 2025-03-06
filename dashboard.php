<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Kullanıcı oturum kontrolü
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Kullanıcı bilgilerini al
$user_id = $_SESSION['user_id'];
$user = getUserDetails($user_id);
$vip_details = getVipDetails($user['vip_level']);

// Son işlemleri al
$transactions = getUserTransactions($user_id, 5);

// Günlük mining gelirlerini al
$mining_earnings = getUserDailyMiningEarnings($user_id);

$page_title = 'Dashboard';
include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <!-- Kullanıcı Bilgileri -->
        <div class="col-lg-4 mb-4">
            <div class="card user-profile">
                <div class="card-body text-center">
                    <div class="position-relative mb-4">
                        <div class="profile-image-container">
                            <img src="assets/image/profile.png" class="rounded-circle" width="100" height="100" alt="Profil">
                        </div>
                        <span class="position-absolute bottom-0 end-0 badge rounded-pill bg-<?= $user['vip_level'] > 0 ? 'warning' : 'secondary' ?>">
                            <i class="fas <?= $user['vip_level'] > 0 ? 'fa-crown' : 'fa-user' ?>"></i>
                            <?= $vip_details['name'] ?>
                        </span>
                    </div>
                    
                    <h4 class="mb-1"><?= htmlspecialchars($user['username']) ?></h4>
                    <p class="text-muted small mb-3"><?= htmlspecialchars($user['email']) ?></p>
                    
                    <div class="d-flex justify-content-center mb-3">
                        <a href="profile.php" class="btn btn-sm btn-outline-primary me-2">
                            <i class="fas fa-user-edit me-1"></i> Profil
                        </a>
                        <a href="vip-packages.php" class="btn btn-sm btn-outline-warning">
                            <i class="fas fa-crown me-1"></i> VIP Paketleri
                        </a>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <div class="row text-center">
                        <div class="col-4 border-end">
                            <div class="h5 mb-0"><?= number_format($user['total_deposit'], 2) ?></div>
                            <div class="small text-muted">Toplam Yatırım</div>
                        </div>
                        <div class="col-4 border-end">
                            <div class="h5 mb-0"><?= number_format($user['total_withdraw'], 2) ?></div>
                            <div class="small text-muted">Toplam Çekim</div>
                        </div>
                        <div class="col-4">
                            <div class="h5 mb-0"><?= number_format($user['referral_balance'], 2) ?></div>
                            <div class="small text-muted">Referans Kazancı</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Referans Sistemi -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-users me-2"></i> Referans Sistemi</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted mb-2">Referans Kodunuz</label>
                        <div class="input-group">
                            <input type="text" class="form-control bg-dark border-0" value="<?= $user['referral_code'] ?>" id="referralCode" readonly>
                            <button class="btn btn-outline-primary" type="button" onclick="copyReferralCode()">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="text-muted mb-2">Referans Linkiniz</label>
                        <div class="input-group">
                            <input type="text" class="form-control bg-dark border-0" value="<?= SITE_URL ?>register.php?ref=<?= $user['referral_code'] ?>" id="referralLink" readonly>
                            <button class="btn btn-outline-primary" type="button" onclick="copyReferralLink()">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <div>Referans Oranı:</div>
                        <div class="fw-bold"><?= ($vip_details['referral_rate'] * 100) ?>%</div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>Toplam Referans:</div>
                        <div class="fw-bold"><?= $user['total_referrals'] ?? 0 ?></div>
                    </div>
                    
                    <a href="referrals.php" class="btn btn-sm btn-primary w-100 mt-3">
                        <i class="fas fa-users me-1"></i> Referanslarım
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Ana İçerik -->
        <div class="col-lg-8">
            <!-- Bakiye Kartları -->
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card bg-primary bg-gradient h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-white-50">Ana Bakiye</h6>
                                    <h2 class="text-white mb-0"><?= number_format($user['balance'], 2) ?> USDT</h2>
                                </div>
                                <div class="display-4 text-white-50">
                                    <i class="fas fa-wallet"></i>
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2 mt-3">
                                <a href="deposit.php" class="btn btn-sm btn-light flex-fill">
                                    <i class="fas fa-plus me-1"></i> Yatırım
                                </a>
                                <a href="withdraw.php" class="btn btn-sm btn-light flex-fill">
                                    <i class="fas fa-minus me-1"></i> Çekim
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-4">
                    <div class="card bg-success bg-gradient h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-white-50">Günlük Kazanç</h6>
                                    <h2 class="text-white mb-0"><?= number_format($mining_earnings['today'], 6) ?> USDT</h2>
                                </div>
                                <div class="display-4 text-white-50">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2 mt-3">
                                <a href="daily-game.php" class="btn btn-sm btn-light flex-fill">
                                    <i class="fas fa-gamepad me-1"></i> Günlük Oyun
                                </a>
                                <a href="mining.php" class="btn btn-sm btn-light flex-fill">
                                    <i class="fas fa-microchip me-1"></i> Mining
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Kazanç Grafiği -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Mining Kazançları (Son 7 Gün)</h5>
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-outline-primary active chart-period" data-period="weekly">Haftalık</button>
                        <button type="button" class="btn btn-sm btn-outline-primary chart-period" data-period="monthly">Aylık</button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="earningsChart" height="250"></canvas>
                </div>
            </div>
            
            <!-- Son İşlemler -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Son İşlemler</h5>
                    <a href="transactions.php" class="btn btn-sm btn-primary">Tümünü Gör</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Tarih</th>
                                    <th>İşlem</th>
                                    <th>Miktar</th>
                                    <th>Durum</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(count($transactions) > 0): ?>
                                    <?php foreach($transactions as $tx): ?>
                                    <tr>
                                        <td><?= date('d.m.Y H:i', strtotime($tx['created_at'])) ?></td>
                                        <td>
                                            <?php if($tx['type'] == 'deposit'): ?>
                                                <span class="badge bg-success">Yatırım</span>
                                            <?php elseif($tx['type'] == 'withdraw'): ?>
                                                <span class="badge bg-warning">Çekim</span>
                                            <?php elseif($tx['type'] == 'referral'): ?>
                                                <span class="badge bg-info">Referans</span>
                                            <?php elseif($tx['type'] == 'mining'): ?>
                                                <span class="badge bg-primary">Mining</span>
                                            <?php elseif($tx['type'] == 'game'): ?>
                                                <span class="badge bg-secondary">Oyun</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if(in_array($tx['type'], ['withdraw', 'purchase'])): ?>
                                                <span class="text-danger">-<?= number_format($tx['amount'], 2) ?> USDT</span>
                                            <?php else: ?>
                                                <span class="text-success">+<?= number_format($tx['amount'], 2) ?> USDT</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($tx['status'] == 'completed' || $tx['status'] == 'confirmed'): ?>
                                                <span class="badge bg-success">Tamamlandı</span>
                                            <?php elseif($tx['status'] == 'pending'): ?>
                                                <span class="badge bg-warning">Beklemede</span>
                                            <?php elseif($tx['status'] == 'failed'): ?>
                                                <span class="badge bg-danger">Başarısız</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center">Henüz işlem bulunmuyor</td>
                                    </tr>
                                <?php endif; ?>
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
// Referans kodu kopyalama
function copyReferralCode() {
    const code = document.getElementById('referralCode');
    code.select();
    document.execCommand('copy');
    alert('Referans kodu kopyalandı!');
}

// Referans linki kopyalama
function copyReferralLink() {
    const link = document.getElementById('referralLink');
    link.select();
    document.execCommand('copy');
    alert('Referans linki kopyalandı!');
}

// Kazanç grafiği
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('earningsChart').getContext('2d');
    let earningsChart;
    
    // İlk grafik yüklemesi
    loadChartData('weekly');
    
    // Grafik periyot butonları
    document.querySelectorAll('.chart-period').forEach(button => {
        button.addEventListener('click', function() {
            document.querySelectorAll('.chart-period').forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            loadChartData(this.dataset.period);
        });
    });
    
    function loadChartData(period) {
        fetch('ajax/earnings_chart.php?period=' + period)
        .then(response => response.json())
        .then(data => {
            if (earningsChart) {
                earningsChart.destroy();
            }
            
            earningsChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.dates,
                    datasets: [{
                        label: 'Mining Kazancı',
                        data: data.earnings,
                        borderColor: '#28c76f',
                        backgroundColor: 'rgba(40, 199, 111, 0.1)',
                        fill: true,
                        tension: 0.4
                    }, {
                        label: 'Toplam Kazanç',
                        data: data.total_earnings,
                        borderColor: '#7367f0',
                        backgroundColor: 'rgba(115, 103, 240, 0.1)',
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
                                usePointStyle: true,
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
    }
});
</script>

<?php include 'includes/footer.php'; ?>
