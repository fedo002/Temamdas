<?php
session_start();
require_once '../../includes/admin_userfunctions.php';
require_once '../../includes/admin_functions.php';

// Admin oturum kontrolü
if(!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

// Sayfa parametreleri
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

// Arama parametreleri
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$vip_level = isset($_GET['vip_level']) ? intval($_GET['vip_level']) : 0;
$sort = isset($_GET['sort']) ? trim($_GET['sort']) : 'created_at';
$order = isset($_GET['order']) ? trim($_GET['order']) : 'desc';

// VIP kullanıcıları al
$users = getVipUsers($limit, $offset, $search, $vip_level, $sort, $order);
$total_users = getVipUsersCount($search, $vip_level);
$total_pages = ceil($total_users / $limit);

// VIP paketlerini al
$vip_packages = getVipPackages();

// VIP istatistikleri
$stats = getVipStats();

// Mesaj işleme
if(isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// Sayfa başlığı
$page_title = 'VIP Kullanıcılar';
include '../includes/header.php';
?>

<div class="container-fluid px-4 py-3">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h1 class="h3">VIP Kullanıcı Yönetimi</h1>
            <div>
                <a href="list.php" class="btn btn-secondary me-2">
                    <i class="fas fa-list me-2"></i> Tüm Kullanıcılar
                </a>
                <a href="../vip-packages/list.php" class="btn btn-primary">
                    <i class="fas fa-crown me-2"></i> VIP Paketleri
                </a>
            </div>
        </div>
    </div>
    
    <?php if(isset($message)): ?>
    <div class="alert alert-<?= $message['type'] ?> alert-dismissible fade show">
        <?= $message['text'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    
    <div class="row mb-4">
        <!-- İstatistik Kutuları -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50">Toplam VIP Kullanıcı</h6>
                            <h2 class="mb-0"><?= number_format($stats['total_vip_users']) ?></h2>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-crown fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50">Toplam VIP Geliri</h6>
                            <h2 class="mb-0"><?= number_format($stats['total_vip_revenue'], 2) ?> USDT</h2>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-dollar-sign fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50">Bu Ay VIP Satın Alan</h6>
                            <h2 class="mb-0"><?= number_format($stats['monthly_vip_users']) ?></h2>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-chart-line fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50">Bu Ay VIP Geliri</h6>
                            <h2 class="mb-0"><?= number_format($stats['monthly_vip_revenue'], 2) ?> USDT</h2>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-money-bill-wave fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">VIP Kullanıcı Arama</h5>
                </div>
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Arama Terimi:</label>
                            <input type="text" class="form-control" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Kullanıcı adı, e-posta veya ID">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">VIP Seviyesi:</label>
                            <select class="form-select" name="vip_level">
                                <option value="0">Tümü</option>
                                <?php foreach($vip_packages as $package): ?>
                                    <option value="<?= $package['id'] ?>" <?= $vip_level == $package['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($package['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Sıralama:</label>
                            <select class="form-select" name="sort">
                                <option value="created_at" <?= $sort == 'created_at' ? 'selected' : '' ?>>Kayıt Tarihi</option>
                                <option value="username" <?= $sort == 'username' ? 'selected' : '' ?>>Kullanıcı Adı</option>
                                <option value="vip_level" <?= $sort == 'vip_level' ? 'selected' : '' ?>>VIP Seviyesi</option>
                                <option value="balance" <?= $sort == 'balance' ? 'selected' : '' ?>>Bakiye</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Yön:</label>
                            <select class="form-select" name="order">
                                <option value="desc" <?= $order == 'desc' ? 'selected' : '' ?>>Azalan</option>
                                <option value="asc" <?= $order == 'asc' ? 'selected' : '' ?>>Artan</option>
                            </select>
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i> Ara
                            </button>
                            <a href="vip-users.php" class="btn btn-secondary ms-2">
                                <i class="fas fa-sync-alt me-2"></i> Sıfırla
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">VIP Seviye Dağılımı</h5>
                </div>
                <div class="card-body">
                    <canvas id="vipDistributionChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">VIP Kullanıcı Listesi</h5>
                    <span class="text-muted">Toplam: <?= number_format($total_users) ?> VIP kullanıcı</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Kullanıcı Adı</th>
                                    <th>E-posta</th>
                                    <th>VIP Paketi</th>
                                    <th>Bakiye</th>
                                    <th>Toplam Yatırım</th>
                                    <th>Kayıt Tarihi</th>
                                    <th>Son Giriş</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($users)): ?>
                                <tr>
                                    <td colspan="9" class="text-center py-4">VIP kullanıcı bulunamadı.</td>
                                </tr>
                                <?php else: ?>
                                    <?php foreach($users as $user): ?>
                                    <tr>
                                        <td><?= $user['id'] ?></td>
                                        <td>
                                            <a href="details.php?id=<?= $user['id'] ?>" class="text-decoration-none">
                                                <?= htmlspecialchars($user['username']) ?>
                                            </a>
                                        </td>
                                        <td><?= $user['email'] ?></td>
                                        <td>
                                            <span class="badge bg-primary"><?= htmlspecialchars(getVipPackageName($user['vip_level'])) ?></span>
                                        </td>
                                        <td><?= number_format($user['balance'], 2) ?> USDT</td>
                                        <td><?= number_format($user['total_deposit'], 2) ?> USDT</td>
                                        <td><?= date('d.m.Y', strtotime($user['created_at'])) ?></td>
                                        <td><?= $user['last_login'] ? date('d.m.Y H:i', strtotime($user['last_login'])) : '-' ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="details.php?id=<?= $user['id'] ?>" class="btn btn-primary" title="Detay">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button type="button" class="btn btn-warning" title="VIP Seviyesi Değiştir" data-bs-toggle="modal" data-bs-target="#changeVipModal" data-user-id="<?= $user['id'] ?>" data-username="<?= htmlspecialchars($user['username']) ?>" data-vip-level="<?= $user['vip_level'] ?>">
                                                    <i class="fas fa-crown"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <?php if($total_pages > 1): ?>
                    <div class="d-flex justify-content-center mt-4">
                        <nav aria-label="Sayfalama">
                            <ul class="pagination">
                                <?php if($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&vip_level=<?= $vip_level ?>&sort=<?= $sort ?>&order=<?= $order ?>" aria-label="Önceki">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                                <?php endif; ?>
                                
                                <?php
                                $start_page = max(1, $page - 2);
                                $end_page = min($total_pages, $page + 2);
                                
                                if ($start_page > 1) {
                                    echo '<li class="page-item"><a class="page-link" href="?page=1&search=' . urlencode($search) . '&vip_level=' . $vip_level . '&sort=' . $sort . '&order=' . $order . '">1</a></li>';
                                    if ($start_page > 2) {
                                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                    }
                                }
                                
                                for ($i = $start_page; $i <= $end_page; $i++) {
                                    echo '<li class="page-item ' . ($i == $page ? 'active' : '') . '"><a class="page-link" href="?page=' . $i . '&search=' . urlencode($search) . '&vip_level=' . $vip_level . '&sort=' . $sort . '&order=' . $order . '">' . $i . '</a></li>';
                                }
                                
                                if ($end_page < $total_pages) {
                                    if ($end_page < $total_pages - 1) {
                                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                    }
                                    echo '<li class="page-item"><a class="page-link" href="?page=' . $total_pages . '&search=' . urlencode($search) . '&vip_level=' . $vip_level . '&sort=' . $sort . '&order=' . $order . '">' . $total_pages . '</a></li>';
                                }
                                ?>
                                
                                <?php if($page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&vip_level=<?= $vip_level ?>&sort=<?= $sort ?>&order=<?= $order ?>" aria-label="Sonraki">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- VIP Seviyesi Değiştirme Modal -->
<div class="modal fade" id="changeVipModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">VIP Seviyesi Değiştir</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="vipForm" method="POST" action="update-vip.php">
                <input type="hidden" name="user_id" id="vipUserId">
                <div class="modal-body">
                    <p id="vipMessage"></p>
                    <div class="mb-3">
                        <label class="form-label">VIP Seviyesi:</label>
                        <select class="form-select" name="vip_level" id="vipLevelSelect">
                            <?php foreach($vip_packages as $package): ?>
                                <option value="<?= $package['id'] ?>">
                                    <?= htmlspecialchars($package['name']) ?> (<?= number_format($package['price'], 2) ?> USDT)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Not:</label>
                        <textarea class="form-control" name="note" rows="3" placeholder="VIP seviyesi değişikliği hakkında not ekleyin"></textarea>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="charge_user" id="chargeUserCheck">
                        <label class="form-check-label" for="chargeUserCheck">
                            Kullanıcıdan ücret al (Bakiyesinden düşülecek)
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // VIP seviyesi değiştirme modalı
    const vipModal = document.getElementById('changeVipModal');
    if (vipModal) {
        vipModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const userId = button.getAttribute('data-user-id');
            const username = button.getAttribute('data-username');
            const vipLevel = button.getAttribute('data-vip-level');
            
            const vipUserId = document.getElementById('vipUserId');
            const vipMessage = document.getElementById('vipMessage');
            const vipLevelSelect = document.getElementById('vipLevelSelect');
            
            vipUserId.value = userId;
            vipMessage.textContent = `"${username}" kullanıcısının VIP seviyesini değiştirmek istediğinize emin misiniz?`;
            vipLevelSelect.value = vipLevel;
        });
    }
    
    // VIP seviye dağılımı grafiği
    const ctx = document.getElementById('vipDistributionChart').getContext('2d');
    
    fetch('../ajax/vip_distribution.php')
    .then(response => response.json())
    .then(data => {
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: data.labels,
                datasets: [{
                    data: data.data,
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
    })
    .catch(error => {
        console.error('Error fetching VIP distribution data:', error);
    });
});
</script>

<?php include '../includes/footer.php'; ?>