<?php
session_start();
require_once '../includes/admin_functions.php';

// Admin oturum kontrolü
if(!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Para çekme isteklerini getir
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'pending';
$withdrawals = getAllWithdrawals($status_filter, 50);

// İşlem yapılacak çekim
$withdrawal = null;
$action_message = '';

if(isset($_GET['id'])) {
    $withdrawal_id = $_GET['id'];
    $withdrawal = getWithdrawalDetails($withdrawal_id);
}

// İşlem yapılıyorsa
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $withdrawal_id = $_POST['withdrawal_id'];
    $action = $_POST['action'];
    $admin_id = $_SESSION['admin_id'];
    $tx_hash = isset($_POST['tx_hash']) ? $_POST['tx_hash'] : null;
    $note = isset($_POST['note']) ? $_POST['note'] : null;
    
    if ($action === 'approve') {
        $result = approveWithdrawal($withdrawal_id, $admin_id, $tx_hash, $note);
        if ($result['success']) {
            $action_message = '<div class="alert alert-success">İşlem başarıyla onaylandı.</div>';
        } else {
            $action_message = '<div class="alert alert-danger">' . $result['message'] . '</div>';
        }
    } elseif ($action === 'reject') {
        $result = rejectWithdrawal($withdrawal_id, $admin_id, $note);
        if ($result['success']) {
            $action_message = '<div class="alert alert-success">İşlem başarıyla reddedildi.</div>';
        } else {
            $action_message = '<div class="alert alert-danger">' . $result['message'] . '</div>';
        }
    }
    
    // İşlem sonrası redirect
    if (empty($action_message)) {
        header('Location: withdrawals.php');
        exit;
    }
}

$page_title = 'Para Çekme İşlemleri';
include 'includes/header.php';
?>

<div class="container-fluid px-4 py-3">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3">Para Çekme İşlemleri</h1>
        </div>
    </div>
    
    <?= $action_message ?>
    
    <!-- Filtreleme -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex">
                <div class="nav nav-pills">
                    <a href="?status=pending" class="nav-link <?= $status_filter == 'pending' ? 'active' : '' ?>">Bekleyen</a>
                    <a href="?status=processing" class="nav-link <?= $status_filter == 'processing' ? 'active' : '' ?>">İşleniyor</a>
                    <a href="?status=completed" class="nav-link <?= $status_filter == 'completed' ? 'active' : '' ?>">Tamamlanan</a>
                    <a href="?status=cancelled" class="nav-link <?= $status_filter == 'cancelled' ? 'active' : '' ?>">İptal Edilen</a>
                </div>
                
                <?php if ($status_filter == 'pending' && count($withdrawals) > 0): ?>
                <div class="ms-auto">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bulkApproveModal">
                        <i class="fas fa-check-circle me-2"></i> Toplu Onay
                    </button>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php if ($withdrawal): ?>
    <!-- İşlem Detayları Kartı -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">İşlem Detayları</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th>ID:</th>
                            <td>#<?= $withdrawal['id'] ?></td>
                        </tr>
                        <tr>
                            <th>Kullanıcı:</th>
                            <td><?= htmlspecialchars($withdrawal['username']) ?> (#<?= $withdrawal['user_id'] ?>)</td>
                        </tr>
                        <tr>
                            <th>Miktar:</th>
                            <td><?= number_format($withdrawal['amount'], 6) ?> USDT</td>
                        </tr>
                        <tr>
                            <th>İşlem Ücreti:</th>
                            <td><?= number_format($withdrawal['fee'], 6) ?> USDT</td>
                        </tr>
                        <tr>
                            <th>Toplam:</th>
                            <td><?= number_format($withdrawal['amount'] + $withdrawal['fee'], 6) ?> USDT</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th>TRC20 Adresi:</th>
                            <td>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm" value="<?= $withdrawal['trc20_address'] ?>" id="trc20_address" readonly>
                                    <button class="btn btn-outline-primary btn-sm" type="button" onclick="copyAddress()">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>Durum:</th>
                            <td>
                                <?php if($withdrawal['status'] == 'pending'): ?>
                                    <span class="badge bg-warning">Beklemede</span>
                                <?php elseif($withdrawal['status'] == 'processing'): ?>
                                    <span class="badge bg-info">İşleniyor</span>
                                <?php elseif($withdrawal['status'] == 'completed'): ?>
                                    <span class="badge bg-success">Tamamlandı</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">İptal Edildi</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Talep Tarihi:</th>
                            <td><?= date('d.m.Y H:i', strtotime($withdrawal['created_at'])) ?></td>
                        </tr>
                        <?php if($withdrawal['processed_at']): ?>
                        <tr>
                            <th>İşlem Tarihi:</th>
                            <td><?= date('d.m.Y H:i', strtotime($withdrawal['processed_at'])) ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if($withdrawal['transaction_hash']): ?>
                        <tr>
                            <th>İşlem Hash:</th>
                            <td><?= htmlspecialchars($withdrawal['transaction_hash']) ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
            
            <?php if($withdrawal['status'] == 'pending'): ?>
            <div class="mt-4">
                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">
                        <i class="fas fa-check-circle me-2"></i> İşlemi Onayla
                    </button>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                        <i class="fas fa-times-circle me-2"></i> İşlemi Reddet
                    </button>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Para Çekme Listesi -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <?php if ($status_filter == 'pending'): ?>
                            <th width="30">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="selectAll">
                                </div>
                            </th>
                            <?php endif; ?>
                            <th>ID</th>
                            <th>Kullanıcı</th>
                            <th>Miktar</th>
                            <th>TRC20 Adresi</th>
                            <th>Tarih</th>
                            <th>Durum</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($withdrawals) > 0): ?>
                            <?php foreach($withdrawals as $w): ?>
                            <tr>
                                <?php if ($status_filter == 'pending'): ?>
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input withdrawal-check" type="checkbox" value="<?= $w['id'] ?>">
                                    </div>
                                </td>
                                <?php endif; ?>
                                <td>#<?= $w['id'] ?></td>
                                <td><?= htmlspecialchars($w['username']) ?></td>
                                <td><?= number_format($w['amount'], 6) ?> USDT</td>
                                <td>
                                    <span class="text-truncate d-inline-block" style="max-width: 150px;" title="<?= $w['trc20_address'] ?>">
                                        <?= substr($w['trc20_address'], 0, 10) ?>...
                                    </span>
                                </td>
                                <td><?= date('d.m.Y H:i', strtotime($w['created_at'])) ?></td>
                                <td>
                                    <?php if($w['status'] == 'pending'): ?>
                                        <span class="badge bg-warning">Beklemede</span>
                                    <?php elseif($w['status'] == 'processing'): ?>
                                        <span class="badge bg-info">İşleniyor</span>
                                    <?php elseif($w['status'] == 'completed'): ?>
                                        <span class="badge bg-success">Tamamlandı</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">İptal Edildi</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="?id=<?= $w['id'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if($w['status'] == 'pending'): ?>
                                    <button type="button" class="btn btn-sm btn-outline-success quick-approve" data-id="<?= $w['id'] ?>">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="<?= ($status_filter == 'pending') ? 8 : 7 ?>" class="text-center">İşlem bulunamadı.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Onaylama Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">İşlemi Onayla</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="withdrawal_id" value="<?= $withdrawal ? $withdrawal['id'] : '' ?>">
                    <input type="hidden" name="action" value="approve">
                    
                    <div class="mb-3">
                        <label class="form-label">İşlem Hash (Opsiyonel)</label>
                        <input type="text" class="form-control" name="tx_hash" placeholder="İşlem hash değeri...">
                        <div class="form-text">Blockchain üzerindeki işlem hash değerini girebilirsiniz.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Not (Opsiyonel)</label>
                        <textarea class="form-control" name="note" rows="3" placeholder="İşlemle ilgili not..."></textarea>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Bu işlemi onayladığınızda, kullanıcının hesabından çekilen <?= $withdrawal ? number_format($withdrawal['amount'], 6) : '0' ?> USDT tutarın transfer edildiğini onaylamış olacaksınız.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-success">Onayla</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reddetme Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">İşlemi Reddet</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="withdrawal_id" value="<?= $withdrawal ? $withdrawal['id'] : '' ?>">
                    <input type="hidden" name="action" value="reject">
                    
                    <div class="mb-3">
                        <label class="form-label">Ret Nedeni</label>
                        <textarea class="form-control" name="note" rows="3" placeholder="Ret nedeni..." required></textarea>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Bu işlemi reddettiğinizde, çekilen tutar kullanıcının hesabına iade edilecektir. Bu işlem geri alınamaz.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-danger">Reddet</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Toplu Onaylama Modal -->
<div class="modal fade" id="bulkApproveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark">
            <div class="modal-header">
                <h5 class="modal-title">Toplu İşlem Onaylama</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Seçilen tüm işlemleri onaylamak üzeresiniz.
                </div>
                
                <div id="selectedWithdrawals" class="mb-3">
                    <p>Seçili işlem yok. Lütfen onaylamak istediğiniz işlemleri seçin.</p>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Not (Opsiyonel)</label>
                    <textarea class="form-control" id="bulkNote" rows="3" placeholder="İşlemlerle ilgili not..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-success" id="bulkApproveBtn" disabled>Seçili İşlemleri Onayla</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Adres kopyalama
    window.copyAddress = function() {
        const addressInput = document.getElementById('trc20_address');
        addressInput.select();
        document.execCommand('copy');
        alert('Adres kopyalandı!');
    };
    
    // Toplu seçim
    const selectAllCheckbox = document.getElementById('selectAll');
    const withdrawalCheckboxes = document.querySelectorAll('.withdrawal-check');
    
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            withdrawalCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectedWithdrawals();
        });
    }
    
    if (withdrawalCheckboxes.length > 0) {
        withdrawalCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedWithdrawals);
        });
    }
    
    // Seçili işlemleri güncelle
    function updateSelectedWithdrawals() {
        const selectedIds = [];
        withdrawalCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                selectedIds.push(checkbox.value);
            }
        });
        
        const selectedWithdrawalsDiv = document.getElementById('selectedWithdrawals');
        const bulkApproveBtn = document.getElementById('bulkApproveBtn');
        
        if (selectedIds.length > 0) {
            selectedWithdrawalsDiv.innerHTML = `
                <p><strong>${selectedIds.length} işlem seçildi:</strong></p>
                <ul class="list-group list-group-flush">
                    ${selectedIds.map(id => `<li class="list-group-item bg-transparent">#${id}</li>`).join('')}
                </ul>
            `;
            bulkApproveBtn.disabled = false;
        } else {
            selectedWithdrawalsDiv.innerHTML = `<p>Seçili işlem yok. Lütfen onaylamak istediğiniz işlemleri seçin.</p>`;
            bulkApproveBtn.disabled = true;
        }
    }
    
    // Toplu onaylama işlemi
    const bulkApproveBtn = document.getElementById('bulkApproveBtn');
    if (bulkApproveBtn) {
        bulkApproveBtn.addEventListener('click', function() {
            const selectedIds = [];
            withdrawalCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    selectedIds.push(checkbox.value);
                }
            });
            
            if (selectedIds.length === 0) {
                alert('Lütfen en az bir işlem seçin.');
                return;
            }
            
            const note = document.getElementById('bulkNote').value;
            
            if (confirm(`${selectedIds.length} işlemi onaylamak istediğinize emin misiniz?`)) {
                // AJAX ile toplu onaylama işlemi
                fetch('ajax/bulk_approve_withdrawals.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `ids=${JSON.stringify(selectedIds)}&note=${note}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('İşlemler başarıyla onaylandı!');
                        location.reload();
                    } else {
                        alert('Hata: ' + data.message);
                    }
                });
            }
        });
    }
    
    // Hızlı onaylama
    const quickApproveButtons = document.querySelectorAll('.quick-approve');
    quickApproveButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            if (confirm(`#${id} numaralı işlemi onaylamak istediğinize emin misiniz?`)) {
                // Quick approve form oluştur ve submit et
                const form = document.createElement('form');
                form.method = 'POST';
                form.style.display = 'none';
                
                const idInput = document.createElement('input');
                idInput.name = 'withdrawal_id';
                idInput.value = id;
                
                const actionInput = document.createElement('input');
                actionInput.name = 'action';
                actionInput.value = 'approve';
                
                form.appendChild(idInput);
                form.appendChild(actionInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>