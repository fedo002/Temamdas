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

// Manuel para yatırma işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_deposit'])) {
    $user_id = (int)$_POST['user_id'];
    $amount = (float)$_POST['amount'];
    $status = $_POST['status'];
    $payment_id = $_POST['payment_id'] ?? null;
    $notes = $_POST['notes'] ?? null;
    
    // Validasyon
    $errors = [];
    
    if ($user_id <= 0) {
        $errors[] = "Geçerli bir kullanıcı seçmelisiniz.";
    }
    
    if ($amount <= 0) {
        $errors[] = "Yatırım tutarı sıfırdan büyük olmalıdır.";
    }
    
    // Hata yoksa yatırım ekle
    if (empty($errors)) {
        $result = addManualDeposit($user_id, $amount, $status, $payment_id, $notes);
        
        if ($result['success']) {
            $success_message = "Para yatırma işlemi başarıyla eklendi. İşlem ID: " . $result['deposit_id'];
        } else {
            $error_message = "İşlem eklenirken bir hata oluştu: " . $result['message'];
        }
    } else {
        $error_message = implode("<br>", $errors);
    }
}

// Tarih filtresi
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-30 days'));
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$user_filter = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;

// Yatırım kayıtlarını getir
$deposits = getDepositsi($start_date, $end_date, $status_filter, $user_filter);

// Toplam yatırım tutarını hesapla
$total_amount = 0;
$confirmed_amount = 0;
$pending_amount = 0;

foreach ($deposits as $deposit) {
    $total_amount += $deposit['amount'];
    
    if ($deposit['status'] === 'confirmed') {
        $confirmed_amount += $deposit['amount'];
    } elseif ($deposit['status'] === 'pending') {
        $pending_amount += $deposit['amount'];
    }
}

// Kullanıcı listesini getir
$users = getAllUsers();

$page_title = 'Para Yatırma İşlemleri';
include '../includes/header.php';
?>

<div class="container-fluid px-4 py-4">
    <div class="row">
        <div class="col-12 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Para Yatırma İşlemleri</h1>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDepositModal">
                    <i class="fas fa-plus me-2"></i> Manuel Para Yatırma
                </button>
            </div>
        </div>
    </div>
    
    <?php if (isset($success_message)): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i> <?= $success_message ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>
    
    <?php if (isset($error_message)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i> <?= $error_message ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>
    
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
                        <div class="col-md-3">
                            <label for="status" class="form-label">Durum</label>
                            <select class="form-select" id="status" name="status">
                                <option value="all" <?= $status_filter === 'all' ? 'selected' : '' ?>>Tümü</option>
                                <option value="pending" <?= $status_filter === 'pending' ? 'selected' : '' ?>>Beklemede</option>
                                <option value="confirmed" <?= $status_filter === 'confirmed' ? 'selected' : '' ?>>Onaylandı</option>
                                <option value="failed" <?= $status_filter === 'failed' ? 'selected' : '' ?>>Başarısız</option>
                            </select>
                        </div>
                        <div class="col-md-3">
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
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Toplam Yatırım</h6>
                            <h2 class="mt-2 mb-0"><?= number_format($total_amount, 2) ?> USDT</h2>
                        </div>
                        <div class="fs-1">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Onaylanmış Yatırım</h6>
                            <h2 class="mt-2 mb-0"><?= number_format($confirmed_amount, 2) ?> USDT</h2>
                        </div>
                        <div class="fs-1">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Bekleyen Yatırım</h6>
                            <h2 class="mt-2 mb-0"><?= number_format($pending_amount, 2) ?> USDT</h2>
                        </div>
                        <div class="fs-1">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- İşlem Tablosu -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Para Yatırma İşlemleri</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Kullanıcı</th>
                                    <th>Tutar</th>
                                    <th>Durum</th>
                                    <th>Ödeme ID</th>
                                    <th>Sipariş ID</th>
                                    <th>Kaynak</th>
                                    <th>Tarih</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($deposits) > 0): ?>
                                    <?php foreach ($deposits as $deposit): ?>
                                    <tr>
                                        <td><?= $deposit['id'] ?></td>
                                        <td><?= htmlspecialchars($deposit['username']) ?></td>
                                        <td><?= number_format($deposit['amount'], 2) ?> USDT</td>
                                        <td>
                                            <?php if ($deposit['status'] === 'confirmed'): ?>
                                                <span class="badge bg-success">Onaylandı</span>
                                            <?php elseif ($deposit['status'] === 'pending'): ?>
                                                <span class="badge bg-warning">Beklemede</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Başarısız</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($deposit['payment_id'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($deposit['order_id'] ?? '-') ?></td>
                                        <td>
                                            <?php if (isset($deposit['is_manual']) && $deposit['is_manual']): ?>
                                                <span class="badge bg-info">Manuel</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary"><?= htmlspecialchars($deposit['payment_method'] ?? 'Sistem') ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= date('d.m.Y H:i', strtotime($deposit['created_at'])) ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-info view-deposit" data-id="<?= $deposit['id'] ?>">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <?php if ($deposit['status'] === 'pending'): ?>
                                                <button type="button" class="btn btn-success confirm-deposit" data-id="<?= $deposit['id'] ?>">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger cancel-deposit" data-id="<?= $deposit['id'] ?>">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <p class="text-muted mb-0">Belirtilen kriterlere uygun para yatırma işlemi bulunamadı.</p>
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
</div>

<!-- Para Yatırma Modalı -->
<div class="modal fade" id="addDepositModal" tabindex="-1" aria-labelledby="addDepositModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDepositModalLabel">Manuel Para Yatırma</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addDepositForm" method="POST" action="">
                    <div class="mb-3">
                        <label for="modal_user_id" class="form-label">Kullanıcı</label>
                        <select class="form-select" id="modal_user_id" name="user_id" required>
                            <option value="">Kullanıcı Seçin</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['username']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="modal_amount" class="form-label">Tutar (USDT)</label>
                        <input type="number" class="form-control" id="modal_amount" name="amount" step="0.01" min="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="modal_status" class="form-label">Durum</label>
                        <select class="form-select" id="modal_status" name="status" required>
                            <option value="confirmed">Onaylandı</option>
                            <option value="pending">Beklemede</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="modal_payment_id" class="form-label">Ödeme ID (İsteğe Bağlı)</label>
                        <input type="text" class="form-control" id="modal_payment_id" name="payment_id">
                    </div>
                    <div class="mb-3">
                        <label for="modal_notes" class="form-label">Notlar (İsteğe Bağlı)</label>
                        <textarea class="form-control" id="modal_notes" name="notes" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <button type="submit" form="addDepositForm" name="add_deposit" class="btn btn-primary">Yatırım Ekle</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Yatırım onaylama işlemi
    const confirmButtons = document.querySelectorAll('.confirm-deposit');
    confirmButtons.forEach(button => {
        button.addEventListener('click', function() {
            const depositId = this.getAttribute('data-id');
            if (confirm('Bu yatırımı onaylamak istediğinize emin misiniz?')) {
                window.location.href = `process_deposit.php?action=confirm&id=${depositId}`;
            }
        });
    });
    
    // Yatırım iptal etme işlemi
    const cancelButtons = document.querySelectorAll('.cancel-deposit');
    cancelButtons.forEach(button => {
        button.addEventListener('click', function() {
            const depositId = this.getAttribute('data-id');
            if (confirm('Bu yatırımı iptal etmek istediğinize emin misiniz?')) {
                window.location.href = `process_deposit.php?action=cancel&id=${depositId}`;
            }
        });
    });
    
    // Yatırım detayları görüntüleme
    const viewButtons = document.querySelectorAll('.view-deposit');
    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            const depositId = this.getAttribute('data-id');
            // AJAX ile detayları getir veya modal göster
            alert('Detay görüntüleme fonksiyonu henüz eklenmedi. ID: ' + depositId);
        });
    });
});
</script>

<?php include '../includes/footer.php'; ?>