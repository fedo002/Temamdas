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

// Filtreler
$filters = [];
if (isset($_GET['user_id']) && !empty($_GET['user_id'])) {
    $filters['user_id'] = intval($_GET['user_id']);
}
if (isset($_GET['status']) && !empty($_GET['status'])) {
    $filters['status'] = $_GET['status'];
}
if (isset($_GET['date_from']) && !empty($_GET['date_from'])) {
    $filters['date_from'] = $_GET['date_from'];
}
if (isset($_GET['date_to']) && !empty($_GET['date_to'])) {
    $filters['date_to'] = $_GET['date_to'];
}

// Kazançları al
$earnings = getReferralEarnings($limit, $offset, $filters);

// Sayfa başlığı
$page_title = 'Referans Kazançları';
include '../includes/header.php';
?>

<div class="container-fluid px-4 py-3">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h1 class="h3">Referans Kazançları</h1>
            <a href="settings.php" class="btn btn-primary">
                <i class="fas fa-cog me-2"></i> Referans Ayarları
            </a>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Filtreler</h5>
                </div>
                <div class="card-body">
                    <form method="GET" class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Kullanıcı ID:</label>
                            <input type="number" class="form-control" name="user_id" value="<?= isset($_GET['user_id']) ? htmlspecialchars($_GET['user_id']) : '' ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Durum:</label>
                            <select class="form-select" name="status">
                                <option value="">Tümü</option>
                                <option value="pending" <?= isset($_GET['status']) && $_GET['status'] == 'pending' ? 'selected' : '' ?>>Beklemede</option>
                                <option value="approved" <?= isset($_GET['status']) && $_GET['status'] == 'approved' ? 'selected' : '' ?>>Onaylandı</option>
                                <option value="rejected" <?= isset($_GET['status']) && $_GET['status'] == 'rejected' ? 'selected' : '' ?>>Reddedildi</option>
                                <option value="paid" <?= isset($_GET['status']) && $_GET['status'] == 'paid' ? 'selected' : '' ?>>Ödendi</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Başlangıç Tarihi:</label>
                            <input type="date" class="form-control" name="date_from" value="<?= isset($_GET['date_from']) ? htmlspecialchars($_GET['date_from']) : '' ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Bitiş Tarihi:</label>
                            <input type="date" class="form-control" name="date_to" value="<?= isset($_GET['date_to']) ? htmlspecialchars($_GET['date_to']) : '' ?>">
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-2"></i> Filtrele
                            </button>
                            <a href="earnings.php" class="btn btn-secondary ms-2">
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
                <div class="card-header">
                    <h5 class="mb-0">Referans Kazançları Listesi</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Kazanan Kullanıcı</th>
                                    <th>Davet Edilen Kullanıcı</th>
                                    <th>Miktar</th>
                                    <th>Durum</th>
                                    <th>Sipariş ID</th>
                                    <th>Tarih</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($earnings)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4">Kayıt bulunamadı.</td>
                                </tr>
                                <?php else: ?>
                                    <?php foreach ($earnings as $earning): ?>
                                    <tr>
                                        <td><?= $earning['id'] ?></td>
                                        <td>
                                            <a href="../users/details.php?id=<?= $earning['user_id'] ?>" class="text-decoration-none">
                                                <?= htmlspecialchars($earning['referrer_username']) ?>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="../users/details.php?id=<?= $earning['referred_user_id'] ?>" class="text-decoration-none">
                                                <?= htmlspecialchars($earning['referred_username']) ?>
                                            </a>
                                        </td>
                                        <td><?= number_format($earning['amount'], 2) ?> USDT</td>
                                        <td>
                                            <?php if($earning['status'] == 'pending'): ?>
                                                <span class="badge bg-warning">Beklemede</span>
                                            <?php elseif($earning['status'] == 'approved'): ?>
                                                <span class="badge bg-success">Onaylandı</span>
                                            <?php elseif($earning['status'] == 'rejected'): ?>
                                                <span class="badge bg-danger">Reddedildi</span>
                                            <?php elseif($earning['status'] == 'paid'): ?>
                                                <span class="badge bg-info">Ödendi</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($earning['order_id']): ?>
                                                <a href="../orders/details.php?id=<?= $earning['order_id'] ?>" class="text-decoration-none">
                                                    #<?= $earning['order_id'] ?>
                                                </a>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td><?= date('d.m.Y H:i', strtotime($earning['created_at'])) ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <?php if($earning['status'] == 'pending'): ?>
                                                <button type="button" class="btn btn-success update-status" data-id="<?= $earning['id'] ?>" data-status="approved">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger update-status" data-id="<?= $earning['id'] ?>" data-status="rejected">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                                <?php elseif($earning['status'] == 'approved' && !$earning['is_paid']): ?>
                                                <button type="button" class="btn btn-info update-status" data-id="<?= $earning['id'] ?>" data-status="paid">
                                                    <i class="fas fa-dollar-sign"></i>
                                                </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Sayfalama buraya eklenebilir -->
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Durum güncelleme işlemleri
    const updateButtons = document.querySelectorAll('.update-status');
    
    updateButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const status = this.getAttribute('data-status');
            
            if (confirm(`Bu kaydın durumunu "${status}" olarak güncellemek istediğinize emin misiniz?`)) {
                fetch('../ajax/update_referral_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        id: id,
                        status: status
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Durum başarıyla güncellendi.');
                        location.reload();
                    } else {
                        alert('Hata oluştu: ' + data.error);
                    }
                })
                .catch(error => {
                    alert('İşlem sırasında bir hata oluştu.');
                    console.error('Error:', error);
                });
            }
        });
    });
});
</script>

<?php include '../includes/footer.php'; ?>