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
$status = isset($_GET['status']) ? trim($_GET['status']) : '';
$sort = isset($_GET['sort']) ? trim($_GET['sort']) : 'created_at';
$order = isset($_GET['order']) ? trim($_GET['order']) : 'desc';

// Kullanıcıları al
$users = getUsers($limit, $offset, $search, $status, $sort, $order);
$total_users = getUsersCount($search, $status);
$total_pages = ceil($total_users / $limit);

// Toplam istatistikler
$stats = getUsersStats();

// Mesaj işleme
if(isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// Sayfa başlığı
$page_title = 'Kullanıcı Listesi';
include '../includes/header.php';
?>

<div class="container-fluid px-4 py-3">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h1 class="h3">Kullanıcı Yönetimi</h1>
            <a href="add.php" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> Yeni Kullanıcı
            </a>
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
                            <h6 class="text-white-50">Toplam Kullanıcı</h6>
                            <h2 class="mb-0"><?= number_format($stats['total_users']) ?></h2>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-users fa-3x opacity-50"></i>
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
                            <h6 class="text-white-50">Aktif Kullanıcı</h6>
                            <h2 class="mb-0"><?= number_format($stats['active_users']) ?></h2>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-user-check fa-3x opacity-50"></i>
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
                            <h6 class="text-white-50">VIP Kullanıcı</h6>
                            <h2 class="mb-0"><?= number_format($stats['vip_users']) ?></h2>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-crown fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a href="vip-users.php" class="text-white-50 text-decoration-none">Detay</a>
                    <div class="small text-white">
                        <i class="fas fa-angle-right"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50">Bugün Kaydolan</h6>
                            <h2 class="mb-0"><?= number_format($stats['new_users_today']) ?></h2>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-user-plus fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Kullanıcı Arama</h5>
                </div>
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Arama Terimi:</label>
                            <input type="text" class="form-control" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Kullanıcı adı, e-posta veya ID">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Durum:</label>
                            <select class="form-select" name="status">
                                <option value="">Tümü</option>
                                <option value="active" <?= $status == 'active' ? 'selected' : '' ?>>Aktif</option>
                                <option value="pending" <?= $status == 'pending' ? 'selected' : '' ?>>Beklemede</option>
                                <option value="blocked" <?= $status == 'blocked' ? 'selected' : '' ?>>Bloklanmış</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Sıralama:</label>
                            <select class="form-select" name="sort">
                                <option value="created_at" <?= $sort == 'created_at' ? 'selected' : '' ?>>Kayıt Tarihi</option>
                                <option value="username" <?= $sort == 'username' ? 'selected' : '' ?>>Kullanıcı Adı</option>
                                <option value="balance" <?= $sort == 'balance' ? 'selected' : '' ?>>Bakiye</option>
                                <option value="last_login" <?= $sort == 'last_login' ? 'selected' : '' ?>>Son Giriş</option>
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
                            <a href="list.php" class="btn btn-secondary ms-2">
                                <i class="fas fa-sync-alt me-2"></i> Sıfırla
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Kullanıcı Listesi</h5>
                    <span class="text-muted">Toplam: <?= number_format($total_users) ?> kullanıcı</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Kullanıcı Adı</th>
                                    <th>E-posta</th>
                                    <th>Bakiye</th>
                                    <th>VIP</th>
                                    <th>Durum</th>
                                    <th>Kayıt Tarihi</th>
                                    <th>Son Giriş</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($users)): ?>
                                <tr>
                                    <td colspan="9" class="text-center py-4">Kullanıcı bulunamadı.</td>
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
                                        <td><?= number_format($user['balance'], 2) ?> USDT</td>
                                        <td>
                                            <?php if($user['vip_level'] > 0): ?>
                                                <span class="badge bg-primary">VIP <?= $user['vip_level'] ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Standart</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($user['status'] == 'active'): ?>
                                                <span class="badge bg-success">Aktif</span>
                                            <?php elseif($user['status'] == 'pending'): ?>
                                                <span class="badge bg-warning">Beklemede</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Bloklanmış</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= date('d.m.Y', strtotime($user['created_at'])) ?></td>
                                        <td><?= $user['last_login'] ? date('d.m.Y H:i', strtotime($user['last_login'])) : '-' ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="details.php?id=<?= $user['id'] ?>" class="btn btn-primary" title="Detay">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="edit.php?id=<?= $user['id'] ?>" class="btn btn-warning" title="Düzenle">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-danger" title="Blokla/Aktifleştir" data-bs-toggle="modal" data-bs-target="#statusModal" data-user-id="<?= $user['id'] ?>" data-username="<?= htmlspecialchars($user['username']) ?>" data-status="<?= $user['status'] ?>">
                                                    <?php if($user['status'] == 'blocked'): ?>
                                                        <i class="fas fa-unlock"></i>
                                                    <?php else: ?>
                                                        <i class="fas fa-ban"></i>
                                                    <?php endif; ?>
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
                                    <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&status=<?= $status ?>&sort=<?= $sort ?>&order=<?= $order ?>" aria-label="Önceki">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                                <?php endif; ?>
                                
                                <?php
                                $start_page = max(1, $page - 2);
                                $end_page = min($total_pages, $page + 2);
                                
                                if ($start_page > 1) {
                                    echo '<li class="page-item"><a class="page-link" href="?page=1&search=' . urlencode($search) . '&status=' . $status . '&sort=' . $sort . '&order=' . $order . '">1</a></li>';
                                    if ($start_page > 2) {
                                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                    }
                                }
                                
                                for ($i = $start_page; $i <= $end_page; $i++) {
                                    echo '<li class="page-item ' . ($i == $page ? 'active' : '') . '"><a class="page-link" href="?page=' . $i . '&search=' . urlencode($search) . '&status=' . $status . '&sort=' . $sort . '&order=' . $order . '">' . $i . '</a></li>';
                                }
                                
                                if ($end_page < $total_pages) {
                                    if ($end_page < $total_pages - 1) {
                                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                    }
                                    echo '<li class="page-item"><a class="page-link" href="?page=' . $total_pages . '&search=' . urlencode($search) . '&status=' . $status . '&sort=' . $sort . '&order=' . $order . '">' . $total_pages . '</a></li>';
                                }
                                ?>
                                
                                <?php if($page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&status=<?= $status ?>&sort=<?= $sort ?>&order=<?= $order ?>" aria-label="Sonraki">
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

<!-- Durum Değiştirme Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kullanıcı Durumunu Değiştir</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="statusForm" method="POST" action="update-status.php">
                <input type="hidden" name="user_id" id="statusUserId">
                <div class="modal-body">
                    <p id="statusMessage"></p>
                    <div class="mb-3">
                        <label class="form-label">Yeni Durum:</label>
                        <select class="form-select" name="status" id="statusSelect">
                            <option value="active">Aktif</option>
                            <option value="pending">Beklemede</option>
                            <option value="blocked">Bloklanmış</option>
                        </select>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Durum değiştirme modalı
    const statusModal = document.getElementById('statusModal');
    if (statusModal) {
        statusModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const userId = button.getAttribute('data-user-id');
            const username = button.getAttribute('data-username');
            const status = button.getAttribute('data-status');
            
            const statusUserId = document.getElementById('statusUserId');
            const statusMessage = document.getElementById('statusMessage');
            const statusSelect = document.getElementById('statusSelect');
            
            statusUserId.value = userId;
            statusMessage.textContent = `"${username}" kullanıcısının durumunu değiştirmek istediğinize emin misiniz?`;
            statusSelect.value = status;
        });
    }
});
</script>

<?php include '../includes/footer.php'; ?>