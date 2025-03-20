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
$type_filter = isset($_GET['type']) ? $_GET['type'] : 'all';
$user_filter = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';

// Sayfa numarası ve limit
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
$offset = ($page - 1) * $limit;

// İşlemleri getir
$transactions = getTransactions($start_date, $end_date, $type_filter, $user_filter, $status_filter, $limit, $offset);
$total_count = getTransactionCount($start_date, $end_date, $type_filter, $user_filter, $status_filter);

// Kullanıcı listesini getir
$users = getAllUsers();

// İşlem tipi isimleri
$transaction_types = [
    'deposit' => 'Para Yatırma',
    'withdraw' => 'Para Çekme',
    'referral' => 'Referans Kazancı',
    'referral_transfer' => 'Referans Transferi',
    'game' => 'Oyun Kazancı',
    'mining' => 'Mining Kazancı',
    'miningdeposit' => 'Mining Paketi Alımı',
    'vip' => 'VIP Paket Alımı',
    'bonus' => 'Bonus',
    'transfer' => 'Transfer',
    'other' => 'Diğer'
];

// İşlem tipi istatistikleri
$type_stats = getTransactionTypeStats($start_date, $end_date, $user_filter);

$page_title = 'İşlem Geçmişi';
include '../includes/header.php';
?>

<div class="container-fluid px-4 py-4">
    <div class="row">
        <div class="col-12 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">İşlem Geçmişi</h1>
                <a href="export_transactions.php?start_date=<?= $start_date ?>&end_date=<?= $end_date ?>&type=<?= $type_filter ?>&user_id=<?= $user_filter ?>&status=<?= $status_filter ?>" class="btn btn-primary">
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
                        <div class="col-md-2">
                            <label for="start_date" class="form-label">Başlangıç Tarihi</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="<?= $start_date ?>">
                        </div>
                        <div class="col-md-2">
                            <label for="end_date" class="form-label">Bitiş Tarihi</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="<?= $end_date ?>">
                        </div>
                        <div class="col-md-2">
                            <label for="type" class="form-label">İşlem Tipi</label>
                            <select class="form-select" id="type" name="type">
                                <option value="all" <?= $type_filter === 'all' ? 'selected' : '' ?>>Tümü</option>
                                <?php foreach ($transaction_types as $key => $value): ?>
                                    <option value="<?= $key ?>" <?= $type_filter === $key ? 'selected' : '' ?>><?= $value ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="status" class="form-label">Durum</label>
                            <select class="form-select" id="status" name="status">
                                <option value="all" <?= $status_filter === 'all' ? 'selected' : '' ?>>Tümü</option>
                                <option value="completed" <?= $status_filter === 'completed' ? 'selected' : '' ?>>Tamamlandı</option>
                                <option value="pending" <?= $status_filter === 'pending' ? 'selected' : '' ?>>Beklemede</option>
                                <option value="failed" <?= $status_filter === 'failed' ? 'selected' : '' ?>>Başarısız</option>
                                <option value="cancelled" <?= $status_filter === 'cancelled' ? 'selected' : '' ?>>İptal</option>
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
                        <div class="col-md-2">
                            <label for="limit" class="form-label">Sayfa Başına</label>
                            <select class="form-select" id="limit" name="limit" onchange="this.form.submit()">
                                <option value="20" <?= $limit === 20 ? 'selected' : '' ?>>20</option>
                                <option value="50" <?= $limit === 50 ? 'selected' : '' ?>>50</option>
                                <option value="100" <?= $limit === 100 ? 'selected' : '' ?>>100</option>
                                <option value="200" <?= $limit === 200 ? 'selected' : '' ?>>200</option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- İşlem Tipi İstatistikleri -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">İşlem Tipi İstatistikleri</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($type_stats as $type => $data): ?>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title"><?= $transaction_types[$type] ?? $type ?></h6>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-primary"><?= number_format($data['count']) ?> işlem</span>
                                            <span class="<?= $data['amount'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                                <?= number_format($data['amount'], 2) ?> USDT
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- İşlem Tablosu -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">İşlem Listesi</h5>
                    <span class="text-muted">Toplam: <?= number_format($total_count) ?> işlem</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Kullanıcı</th>
                                    <th>İşlem Tipi</th>
                                    <th>Tutar</th>
                                    <th>Bakiye Öncesi</th>
                                    <th>Bakiye Sonrası</th>
                                    <th>Durum</th>
                                    <th>Referans ID</th>
                                    <th>Açıklama</th>
                                    <th>Tarih</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($transactions) > 0): ?>
                                    <?php foreach ($transactions as $transaction): ?>
                                    <tr>
                                        <td><?= $transaction['id'] ?></td>
                                        <td><?= htmlspecialchars($transaction['username']) ?></td>
                                        <td>
                                            <span class="badge 
                                                <?= getTypeBadgeClass($transaction['type']) ?>">
                                                <?= $transaction_types[$transaction['type']] ?? $transaction['type'] ?>
                                            </span>
                                        </td>
                                        <td class="<?= $transaction['amount'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                            <?= number_format($transaction['amount'], 2) ?> USDT
                                        </td>
                                        <td><?= number_format($transaction['before_balance'] ?? 0, 2) ?> USDT</td>
                                        <td><?= number_format($transaction['after_balance'] ?? 0, 2) ?> USDT</td>
                                        <td>
                                            <?php if ($transaction['status'] === 'completed'): ?>
                                                <span class="badge bg-success">Tamamlandı</span>
                                            <?php elseif ($transaction['status'] === 'pending'): ?>
                                                <span class="badge bg-warning">Beklemede</span>
                                            <?php elseif ($transaction['status'] === 'cancelled'): ?>
                                                <span class="badge bg-secondary">İptal</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Başarısız</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $transaction['related_id'] ?? '-' ?></td>
                                        <td>
                                            <span class="text-truncate d-inline-block" style="max-width: 200px;" title="<?= htmlspecialchars($transaction['description'] ?? '') ?>">
                                                <?= htmlspecialchars($transaction['description'] ?? '') ?>
                                            </span>
                                        </td>
                                        <td><?= date('d.m.Y H:i', strtotime($transaction['created_at'])) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="10" class="text-center py-4">
                                            <p class="text-muted mb-0">Belirtilen kriterlere uygun işlem bulunamadı.</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <!-- Sayfalama -->
                    <?php if ($total_count > $limit): ?>
                    <?php $total_pages = ceil($total_count / $limit); ?>
                    <nav>
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=1&start_date=<?= $start_date ?>&end_date=<?= $end_date ?>&type=<?= $type_filter ?>&user_id=<?= $user_filter ?>&status=<?= $status_filter ?>&limit=<?= $limit ?>">İlk</a>
                            </li>
                            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= max(1, $page - 1) ?>&start_date=<?= $start_date ?>&end_date=<?= $end_date ?>&type=<?= $type_filter ?>&user_id=<?= $user_filter ?>&status=<?= $status_filter ?>&limit=<?= $limit ?>">Önceki</a>
                            </li>
                            
                            <?php 
                            $start_page = max(1, $page - 2);
                            $end_page = min($total_pages, $page + 2);
                            
                            if ($start_page > 1) {
                                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                            }
                            
                            for ($i = $start_page; $i <= $end_page; $i++): 
                            ?>
                                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>&start_date=<?= $start_date ?>&end_date=<?= $end_date ?>&type=<?= $type_filter ?>&user_id=<?= $user_filter ?>&status=<?= $status_filter ?>&limit=<?= $limit ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php 
                            if ($end_page < $total_pages) {
                                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                            }
                            ?>
                            
                            <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= min($total_pages, $page + 1) ?>&start_date=<?= $start_date ?>&end_date=<?= $end_date ?>&type=<?= $type_filter ?>&user_id=<?= $user_filter ?>&status=<?= $status_filter ?>&limit=<?= $limit ?>">Sonraki</a>
                            </li>
                            <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $total_pages ?>&start_date=<?= $start_date ?>&end_date=<?= $end_date ?>&type=<?= $type_filter ?>&user_id=<?= $user_filter ?>&status=<?= $status_filter ?>&limit=<?= $limit ?>">Son</a>
                            </li>
                        </ul>
                    </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Datatables
    if (typeof $.fn.DataTable !== 'undefined') {
        $('.datatable').DataTable({
            paging: false,
            searching: false,
            info: false
        });
    }
});

<?php
/**
 * İşlem tipine göre badge sınıfını döndürür
 * 
 * @param string $type İşlem tipi
 * @return string Badge sınıfı
 */
function getTypeBadgeClass($type) {
    switch ($type) {
        case 'deposit':
            return 'bg-success';
        case 'withdraw':
            return 'bg-danger';
        case 'referral':
            return 'bg-info';
        case 'referral_transfer':
            return 'bg-primary';
        case 'game':
            return 'bg-warning';
        case 'mining':
            return 'bg-info';
        case 'miningdeposit':
            return 'bg-secondary';
        case 'vip':
            return 'bg-primary';
        case 'bonus':
            return 'bg-success';
        case 'transfer':
            return 'bg-secondary';
        default:
            return 'bg-secondary';
    }
}
?>
</script>

<?php include '../includes/footer.php'; ?>