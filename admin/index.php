<?php
session_start();
require_once '../includes/admin_functions.php';

// Admin oturum kontrolü
if(!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// İstatistikleri al
$stats = getAdminDashboardStats();

// Sayfa başlığı
$page_title = 'Admin Panel';
include 'includes/header.php';
?>

<div class="container-fluid px-4 py-3">
    <div class="row">
        <div class="col-12 mb-4">
            <h1 class="h3">Admin Dashboard</h1>
            <p class="text-muted">Platform genel bakış ve istatistikler</p>
        </div>
    </div>
    
    <div class="row mb-4">
        <!-- Ana İstatistikler -->
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50">Toplam Kullanıcı</h6>
                            <h2 class="mb-0"><?= number_format($stats['total_users']) ?></h2>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a href="users/list.php" class="text-white-50 text-decoration-none">Detay</a>
                    <div class="small text-white">
                        <i class="fas fa-angle-right"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50">Toplam Deposit</h6>
                            <h2 class="mb-0"><?= number_format($stats['total_deposits'], 2) ?> USDT</h2>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-wallet"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a href="reports/deposits.php" class="text-white-50 text-decoration-none">Detay</a>
                    <div class="small text-white">
                        <i class="fas fa-angle-right"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50">Toplam Withdraw</h6>
                            <h2 class="mb-0"><?= number_format($stats['total_withdrawals'], 2) ?> USDT</h2>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-hand-holding-usd"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a href="reports/withdrawals.php" class="text-white-50 text-decoration-none">Detay</a>
                    <div class="small text-white">
                        <i class="fas fa-angle-right"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card bg-danger text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50">Aktif Ticket</h6>
                            <h2 class="mb-0"><?= number_format($stats['active_tickets']) ?></h2>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-ticket-alt"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a href="support/tickets.php" class="text-white-50 text-decoration-none">Detay</a>
                    <div class="small text-white">
                        <i class="fas fa-angle-right"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mb-4">
        <!-- Son Kullanıcılar -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Son Kaydolan Kullanıcılar</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Kullanıcı</th>
                                    <th>Kayıt Tarihi</th>
                                    <th>Bakiye</th>
                                    <th>İşlem</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($stats['recent_users'] as $user): ?>
                                <tr>
                                    <td><?= htmlspecialchars($user['username']) ?></td>
                                    <td><?= date('d.m.Y H:i', strtotime($user['created_at'])) ?></td>
                                    <td><?= number_format($user['balance'], 2) ?> USDT</td>
                                    <td>
                                        <a href="users/details.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="users/list.php" class="btn btn-sm btn-primary">Tüm Kullanıcılar</a>
                </div>
            </div>
        </div>
        
        <!-- Son İşlemler -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Son İşlemler</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>İşlem</th>
                                    <th>Kullanıcı</th>
                                    <th>Tutar</th>
                                    <th>Tarih</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($stats['recent_transactions'] as $tx): ?>
                                <tr>
                                    <td>
                                        <?php if($tx['type'] == 'deposit'): ?>
                                            <span class="badge bg-success">Deposit</span>
                                        <?php elseif($tx['type'] == 'withdraw'): ?>
                                            <span class="badge bg-warning">Withdraw</span>
                                        <?php else: ?>
                                            <span class="badge bg-info"><?= $tx['type'] ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($tx['username']) ?></td>
                                    <td><?= number_format($tx['amount'], 2) ?> USDT</td>
                                    <td><?= date('d.m.Y H:i', strtotime($tx['created_at'])) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="reports/transactions.php" class="btn btn-sm btn-primary">Tüm İşlemler</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Haftalık İstatistikler -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Haftalık İstatistikler</h5>
                </div>
                <div class="card-body">
                    <canvas id="weeklyStatsChart" height="300"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Hızlı Linkler -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Hızlı İşlemler</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="vip-packages/list.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center bg-dark">
                            <span><i class="fas fa-crown me-2"></i> VIP Paketleri Yönet</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        <a href="mining-packages/list.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center bg-dark">
                            <span><i class="fas fa-microchip me-2"></i> Mining Paketleri Yönet</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        <a href="game-settings/settings.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center bg-dark">
                            <span><i class="fas fa-gamepad me-2"></i> Oyun Ayarları</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        <a href="referral-system/settings.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center bg-dark">
                            <span><i class="fas fa-users me-2"></i> Referans Sistemi</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        <a href="support/tickets.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center bg-dark">
                            <span><i class="fas fa-headset me-2"></i> Destek Talepleri</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        <a href="settings/general.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center bg-dark">
                            <span><i class="fas fa-cog me-2"></i> Genel Ayarlar</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Haftalık istatistikler grafiği
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('weeklyStatsChart').getContext('2d');
    
    fetch('ajax/weekly_stats.php')
    .then(response => response.json())
    .then(data => {
        const weeklyStatsChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.dates,
                datasets: [
                    {
                        label: 'Yeni Kullanıcılar',
                        data: data.new_users,
                        backgroundColor: 'rgba(115, 103, 240, 0.7)',
                        borderColor: '#7367f0',
                        borderWidth: 1
                    },
                    {
                        label: 'Depositler',
                        data: data.deposits,
                        backgroundColor: 'rgba(40, 199, 111, 0.7)',
                        borderColor: '#28c76f',
                        borderWidth: 1
                    },
                    {
                        label: 'Withdrawlar',
                        data: data.withdrawals,
                        backgroundColor: 'rgba(255, 159, 67, 0.7)',
                        borderColor: '#ff9f43',
                        borderWidth: 1
                    }
                ]
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
});
</script>

<?php include 'includes/footer.php'; ?>