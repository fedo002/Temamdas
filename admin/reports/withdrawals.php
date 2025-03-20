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

// Manuel para çekme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_withdrawal'])) {
    $user_id = (int)$_POST['user_id'];
    $amount = (float)$_POST['amount'];
    $fee = (float)$_POST['fee'];
    $status = $_POST['status'];
    $trc20_address = $_POST['trc20_address'];
    $transaction_hash = $_POST['transaction_hash'] ?? null;
    $admin_note = $_POST['admin_note'] ?? null;
    
    // Validasyon
    $errors = [];
    
    if ($user_id <= 0) {
        $errors[] = "Geçerli bir kullanıcı seçmelisiniz.";
    }
    
    if ($amount <= 0) {
        $errors[] = "Çekim tutarı sıfırdan büyük olmalıdır.";
    }
    
    if ($fee < 0) {
        $errors[] = "İşlem ücreti negatif olamaz.";
    }
    
    if (empty($trc20_address)) {
        $errors[] = "TRC20 adresi gereklidir.";
    } elseif (!validateTRC20Address($trc20_address)) {
        $errors[] = "Geçersiz TRC20 adresi.";
    }
    
    // Kullanıcı bakiyesi kontrolü
    $user = getUserDetailsi($user_id);
    $total_amount = $amount + $fee;
    
    if ($user && $total_amount > $user['balance']) {
        $errors[] = "Kullanıcının bakiyesi yetersiz. Bakiye: " . number_format($user['balance'], 2) . " USDT";
    }
    
    // Hata yoksa çekim ekle
    if (empty($errors)) {
        $result = addManualWithdrawal($user_id, $amount, $fee, $status, $trc20_address, $transaction_hash, $admin_note);
        
        if ($result['success']) {
            $success_message = "Para çekme işlemi başarıyla eklendi. İşlem ID: " . $result['withdrawal_id'];
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

// Çekim kayıtlarını getir
$withdrawals = getWithdrawals($start_date, $end_date, $status_filter, $user_filter);

// Toplam çekim tutarını hesapla
$total_amount = 0;
$completed_amount = 0;
$pending_amount = 0;

foreach ($withdrawals as $withdrawal) {
    $total_amount += $withdrawal['amount'];
    
    if ($withdrawal['status'] === 'completed') {
        $completed_amount += $withdrawal['amount'];
    } elseif ($withdrawal['status'] === 'pending' || $withdrawal['status'] === 'processing') {
        $pending_amount += $withdrawal['amount'];
    }
}

// Kullanıcı listesini getir
$users = getAllUsers();

$page_title = 'Para Çekme İşlemleri';
include '../includes/header.php';
?>

<div class="container-fluid px-4 py-4">
    <div class="row">
        <div class="col-12 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Para Çekme İşlemleri</h1>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addWithdrawalModal">
                    <i class="fas fa-plus me-2"></i> Manuel Para Çekme
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
                                <option value="processing" <?= $status_filter === 'processing' ? 'selected' : '' ?>>İşleniyor</option>
                                <option value="completed" <?= $status_filter === 'completed' ? 'selected' : '' ?>>Tamamlandı</option>
                                <option value="failed" <?= $status_filter === 'failed' ? 'selected' : '' ?>>Başarısız</option>
                                <option value="cancelled" <?= $status_filter === 'cancelled' ? 'selected' : '' ?>>İptal Edildi</option>
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
                            <h6 class="card-title mb-0">Toplam Çekim</h6>
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
                            <h6 class="card-title mb-0">Tamamlanan Çekim</h6>
                            <h2 class="mt-2 mb-0"><?= number_format($completed_amount, 2) ?> USDT</h2>
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
                            <h6 class="card-title mb-0">Bekleyen Çekim</h6>
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
                    <h5 class="card-title mb-0">Para Çekme İşlemleri</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Kullanıcı</th>
                                    <th>Tutar</th>
                                    <th>Ücret</th>
                                    <th>Durum</th>
                                    <th>TRC20 Adresi</th>
                                    <th>İşlem Hash</th>
                                    <th>Tarih</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($withdrawals) > 0): ?>
                                    <?php foreach ($withdrawals as $withdrawal): ?>
                                    <tr>
                                        <td><?= $withdrawal['id'] ?></td>
                                        <td><?= htmlspecialchars($withdrawal['username']) ?></td>
                                        <td><?= number_format($withdrawal['amount'], 2) ?> USDT</td>
                                        <td><?= number_format($withdrawal['fee'], 2) ?> USDT</td>
                                        <td>
                                            <?php if ($withdrawal['status'] === 'completed'): ?>
                                                <span class="badge bg-success">Tamamlandı</span>
                                            <?php elseif ($withdrawal['status'] === 'pending'): ?>
                                                <span class="badge bg-warning">Beklemede</span>
                                            <?php elseif ($withdrawal['status'] === 'processing'): ?>
                                                <span class="badge bg-info">İşleniyor</span>
                                            <?php elseif ($withdrawal['status'] === 'cancelled'): ?>
                                                <span class="badge bg-secondary">İptal</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Başarısız</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="text-truncate" style="max-width: 120px;" title="<?= htmlspecialchars($withdrawal['trc20_address']) ?>">
                                                    <?= htmlspecialchars($withdrawal['trc20_address']) ?>
                                                </span>
                                                <button class="btn btn-sm btn-link copy-address" data-address="<?= htmlspecialchars($withdrawal['trc20_address']) ?>">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if (!empty($withdrawal['transaction_hash'])): ?>
                                                <div class="d-flex align-items-center">
                                                    <span class="text-truncate" style="max-width: 120px;" title="<?= htmlspecialchars($withdrawal['transaction_hash']) ?>">
                                                        <?= htmlspecialchars($withdrawal['transaction_hash']) ?>
                                                    </span>
                                                    <button class="btn btn-sm btn-link copy-hash" data-hash="<?= htmlspecialchars($withdrawal['transaction_hash']) ?>">
                                                        <i class="fas fa-copy"></i>
                                                    </button>
                                                </div>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td><?= date('d.m.Y H:i', strtotime($withdrawal['created_at'])) ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-info view-withdrawal" data-id="<?= $withdrawal['id'] ?>">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <?php if ($withdrawal['status'] === 'pending'): ?>
                                                <button type="button" class="btn btn-primary process-withdrawal" data-id="<?= $withdrawal['id'] ?>">
                                                    <i class="fas fa-sync-alt"></i>
                                                </button>
                                                <button type="button" class="btn btn-success complete-withdrawal" data-id="<?= $withdrawal['id'] ?>">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger cancel-withdrawal" data-id="<?= $withdrawal['id'] ?>">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                                <?php elseif ($withdrawal['status'] === 'processing'): ?>
                                                <button type="button" class="btn btn-success complete-withdrawal" data-id="<?= $withdrawal['id'] ?>" data-bs-toggle="modal" data-bs-target="#completeWithdrawalModal" data-withdrawal-id="<?= $withdrawal['id'] ?>">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger cancel-withdrawal" data-id="<?= $withdrawal['id'] ?>">
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
                                            <p class="text-muted mb-0">Belirtilen kriterlere uygun para çekme işlemi bulunamadı.</p>
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

<!-- Para Çekme Ekleme Modalı -->
<div class="modal fade" id="addWithdrawalModal" tabindex="-1" aria-labelledby="addWithdrawalModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addWithdrawalModalLabel">Manuel Para Çekme</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addWithdrawalForm" method="POST" action="">
                    <div class="mb-3">
                        <label for="modal_user_id" class="form-label">Kullanıcı</label>
                        <select class="form-select" id="modal_user_id" name="user_id" required>
                            <option value="">Kullanıcı Seçin</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= $user['id'] ?>" data-balance="<?= $user['balance'] ?>">
                                    <?= htmlspecialchars($user['username']) ?> (<?= number_format($user['balance'], 2) ?> USDT)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">Kullanıcı bakiyesi: <span id="userBalance">0.00</span> USDT</div>
                    </div>
                    <div class="mb-3">
                        <label for="modal_amount" class="form-label">Çekim Tutarı (USDT)</label>
                        <input type="number" class="form-control" id="modal_amount" name="amount" step="0.01" min="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="modal_fee" class="form-label">İşlem Ücreti (USDT)</label>
                        <input type="number" class="form-control" id="modal_fee" name="fee" step="0.01" min="0" value="0">
                    </div>
                    <div class="mb-3">
                        <label for="modal_trc20_address" class="form-label">TRC20 Cüzdan Adresi</label>
                        <input type="text" class="form-control" id="modal_trc20_address" name="trc20_address" required>
                    </div>
                    <div class="mb-3">
                        <label for="modal_transaction_hash" class="form-label">İşlem Hash (İsteğe Bağlı)</label>
                        <input type="text" class="form-control" id="modal_transaction_hash" name="transaction_hash">
                    </div>
                    <div class="mb-3">
                        <label for="modal_status" class="form-label">Durum</label>
                        <select class="form-select" id="modal_status" name="status" required>
                            <option value="completed">Tamamlandı</option>
                            <option value="pending">Beklemede</option>
                            <option value="processing">İşleniyor</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="modal_admin_note" class="form-label">Admin Notu (İsteğe Bağlı)</label>
                        <textarea class="form-control" id="modal_admin_note" name="admin_note" rows="3"></textarea>
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Uyarı:</strong> Bu işlem kullanıcı bakiyesini doğrudan etkileyecektir. Lütfen bilgileri dikkatle kontrol edin.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <button type="submit" form="addWithdrawalForm" name="add_withdrawal" class="btn btn-primary">İşlemi Kaydet</button>
            </div>
        </div>
    </div>
</div>

<!-- İşlem Tamamlama Modalı -->
<div class="modal fade" id="completeWithdrawalModal" tabindex="-1" aria-labelledby="completeWithdrawalModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="completeWithdrawalModalLabel">Para Çekme İşlemini Tamamla</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="completeWithdrawalForm" method="POST" action="process_withdrawal.php">
                    <input type="hidden" id="complete_withdrawal_id" name="withdrawal_id">
                    <input type="hidden" name="action" value="complete">
                    
                    <div class="mb-3">
                        <label for="complete_transaction_hash" class="form-label">İşlem Hash</label>
                        <input type="text" class="form-control" id="complete_transaction_hash" name="transaction_hash" required>
                        <div class="form-text">Bu işlemi tamamlamak için TRC20 işlem hash'i gereklidir.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="complete_admin_note" class="form-label">Admin Notu (İsteğe Bağlı)</label>
                        <textarea class="form-control" id="complete_admin_note" name="admin_note" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <button type="submit" form="completeWithdrawalForm" class="btn btn-success">İşlemi Tamamla</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Kullanıcı seçildiğinde bakiyeyi göster
    const userSelect = document.getElementById('modal_user_id');
    const userBalanceSpan = document.getElementById('userBalance');
    
    userSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const balance = selectedOption.getAttribute('data-balance') || 0;
        userBalanceSpan.textContent = parseFloat(balance).toFixed(2);
    });
    
    // Adres kopyalama
    const copyAddressButtons = document.querySelectorAll('.copy-address');
    copyAddressButtons.forEach(button => {
        button.addEventListener('click', function() {
            const address = this.getAttribute('data-address');
            navigator.clipboard.writeText(address)
                .then(() => {
                    this.innerHTML = '<i class="fas fa-check"></i>';
                    setTimeout(() => {
                        this.innerHTML = '<i class="fas fa-copy"></i>';
                    }, 2000);
                });
        });
    });
    
    // Hash kopyalama
    const copyHashButtons = document.querySelectorAll('.copy-hash');
    copyHashButtons.forEach(button => {
        button.addEventListener('click', function() {
            const hash = this.getAttribute('data-hash');
            navigator.clipboard.writeText(hash)
                .then(() => {
                    this.innerHTML = '<i class="fas fa-check"></i>';
                    setTimeout(() => {
                        this.innerHTML = '<i class="fas fa-copy"></i>';
                    }, 2000);
                });
        });
    });
    
    // İşlem durumunu değiştirme
    document.querySelectorAll('.process-withdrawal').forEach(button => {
        button.addEventListener('click', function() {
            const withdrawalId = this.getAttribute('data-id');
            if (confirm('Bu çekim işlemini "İşleniyor" durumuna almak istediğinize emin misiniz?')) {
                window.location.href = `process_withdrawal.php?action=process&id=${withdrawalId}`;
            }
        });
    });
    
    document.querySelectorAll('.complete-withdrawal').forEach(button => {
        if (!button.hasAttribute('data-bs-toggle')) {
            button.addEventListener('click', function() {
                const withdrawalId = this.getAttribute('data-id');
                if (confirm('Bu çekim işlemini tamamlamak istediğinize emin misiniz?')) {
                    window.location.href = `process_withdrawal.php?action=complete&id=${withdrawalId}`;
                }
            });
        }
    });
    
    document.querySelectorAll('.cancel-withdrawal').forEach(button => {
        button.addEventListener('click', function() {
            const withdrawalId = this.getAttribute('data-id');
            if (confirm('Bu çekim işlemini iptal etmek istediğinize emin misiniz? Bu işlem kullanıcının bakiyesine iade edilecektir.')) {
                window.location.href = `process_withdrawal.php?action=cancel&id=${withdrawalId}`;
            }
        });
    });
    
    // İşlem tamamlama modalı
    const completeWithdrawalModal = document.getElementById('completeWithdrawalModal');
    if (completeWithdrawalModal) {
        completeWithdrawalModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const withdrawalId = button.getAttribute('data-withdrawal-id');
            document.getElementById('complete_withdrawal_id').value = withdrawalId;
        });
    }
    
    // Çekim detayları görüntüleme
    const viewButtons = document.querySelectorAll('.view-withdrawal');
    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            const withdrawalId = this.getAttribute('data-id');
            // AJAX ile detayları getir veya modal göster
            alert('Detay görüntüleme fonksiyonu henüz eklenmedi. ID: ' + withdrawalId);
        });
    });
});
</script>

<?php include '../includes/footer.php'; ?>