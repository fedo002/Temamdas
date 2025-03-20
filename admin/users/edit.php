<?php
session_start();
require_once '../../includes/admin_userfunctions.php';
require_once '../../includes/admin_functions.php';

// Admin oturum kontrolü
if(!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

// Kullanıcı ID kontrolü
if(!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: list.php');
    exit;
}

$user_id = (int)$_GET['id'];
$user = getUserDetails($user_id);

// Kullanıcı bulunamadıysa listeme geri dön
if(!$user) {
    $_SESSION['message'] = ['type' => 'danger', 'text' => 'Kullanıcı bulunamadı.'];
    header('Location: list.php');
    exit;
}

// Form gönderildi ise
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Form verilerini al
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $trc20_address = trim($_POST['trc20_address'] ?? '');
    $status = $_POST['status'] ?? 'active';
    $vip_level = (int)$_POST['vip_level'] ?? 0;
    
    // Yeni şifre (opsiyonel)
    $password = trim($_POST['password'] ?? '');
    $update_password = !empty($password);
    
    // Verileri güncelle
    $result = updateUser($user_id, [
        'username' => $username,
        'email' => $email,
        'full_name' => $full_name,
        'trc20_address' => $trc20_address,
        'status' => $status,
        'vip_level' => $vip_level,
        'password' => $update_password ? $password : null
    ]);
    
    if($result === true) {
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Kullanıcı bilgileri başarıyla güncellendi.'];
    } else {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Hata: ' . $result];
    }
    
    // Kullanıcı detaylarını yeniden al
    $user = getUserDetails($user_id);
}

// VIP paketlerini al
$vip_packages = getVipPackages();

// Mesaj işleme
if(isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// Sayfa başlığı
$page_title = 'Kullanıcı Düzenle: ' . htmlspecialchars($user['username']);
include '../includes/header.php';
?>

<div class="container-fluid px-4 py-3">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h1 class="h3">Kullanıcı Düzenle: <?= htmlspecialchars($user['username']) ?></h1>
            <div>
                <a href="details.php?id=<?= $user_id ?>" class="btn btn-info me-2">
                    <i class="fas fa-eye me-2"></i> Kullanıcı Detayları
                </a>
                <a href="list.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Kullanıcı Listesi
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
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Kullanıcı Bilgileri</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Kullanıcı ID:</label>
                                <input type="text" class="form-control" value="<?= $user['id'] ?>" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kayıt Tarihi:</label>
                                <input type="text" class="form-control" value="<?= date('d.m.Y H:i', strtotime($user['created_at'])) ?>" readonly>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Kullanıcı Adı:</label>
                                <input type="text" class="form-control" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                                <div class="form-text">Kullanıcı adını değiştirmek kullanıcının giriş bilgilerini etkileyecektir.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">E-posta:</label>
                                <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Ad Soyad:</label>
                                <input type="text" class="form-control" name="full_name" value="<?= htmlspecialchars($user['full_name'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">TRC20 Adresi:</label>
                                <input type="text" class="form-control" name="trc20_address" value="<?= htmlspecialchars($user['trc20_address'] ?? '') ?>">
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Durum:</label>
                                <select class="form-select" name="status">
                                    <option value="active" <?= $user['status'] == 'active' ? 'selected' : '' ?>>Aktif</option>
                                    <option value="pending" <?= $user['status'] == 'pending' ? 'selected' : '' ?>>Beklemede</option>
                                    <option value="blocked" <?= $user['status'] == 'blocked' ? 'selected' : '' ?>>Bloklanmış</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">VIP Seviyesi:</label>
                                <select class="form-select" name="vip_level">
                                    <?php foreach($vip_packages as $package): ?>
                                        <option value="<?= $package['id'] ?>" <?= $user['vip_level'] == $package['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($package['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Yeni Şifre (değiştirmek istiyorsanız):</label>
                            <input type="password" class="form-control" name="password" placeholder="Şifreyi değiştirmek için doldurun">
                            <div class="form-text">Boş bırakılırsa şifre değiştirilmeyecektir.</div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="details.php?id=<?= $user_id ?>" class="btn btn-secondary">İptal</a>
                            <button type="submit" class="btn btn-primary">Kullanıcıyı Güncelle</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Bakiye Bilgileri</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Ana Bakiye:</span>
                        <h5 class="mb-0"><?= number_format($user['balance'], 2) ?> USDT</h5>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Referans Bakiyesi:</span>
                        <h5 class="mb-0"><?= number_format($user['referral_balance'], 2) ?> USDT</h5>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Toplam Yatırım:</span>
                        <h5 class="mb-0"><?= number_format($user['total_deposit'], 2) ?> USDT</h5>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Toplam Çekim:</span>
                        <h5 class="mb-0"><?= number_format($user['total_withdraw'], 2) ?> USDT</h5>
                    </div>
                    
                    <div class="d-grid gap-2 mt-4">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#updateBalanceModal">
                            <i class="fas fa-coins me-2"></i> Bakiye İşlemi
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Referans Bilgileri</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="d-block">Referans Kodu:</label>
                        <div class="input-group">
                            <input type="text" class="form-control" value="<?= $user['referral_code'] ?>" readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('<?= $user['referral_code'] ?>')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    
                    <?php if($user['referrer_id']): ?>
                    <div class="mb-3">
                        <label class="d-block">Referans Eden:</label>
                        <a href="details.php?id=<?= $user['referrer_id'] ?>" class="btn btn-outline-primary btn-sm">
                            <?= getUsernameById($user['referrer_id']) ?> (#<?= $user['referrer_id'] ?>)
                        </a>
                    </div>
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label class="d-block">Toplam Referans:</label>
                        <h5><?= getUserReferralsCount($user['id']) ?> kullanıcı</h5>
                    </div>
                    
                    <div class="d-grid">
                        <a href="details.php?id=<?= $user_id ?>#referrals" class="btn btn-outline-secondary">
                            <i class="fas fa-users me-2"></i> Referansları Görüntüle
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Update Balance Modal -->
<div class="modal fade" id="updateBalanceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bakiye İşlemi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="update-balance.php">
                <input type="hidden" name="user_id" value="<?= $user_id ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">İşlem Türü:</label>
                        <select class="form-select" name="type" id="balanceType">
                            <option value="add">Bakiye Ekle</option>
                            <option value="subtract">Bakiye Çıkar</option>
                            <option value="set">Bakiye Ayarla</option>
                            <option value="add_referral">Referans Bakiyesi Ekle</option>
                            <option value="subtract_referral">Referans Bakiyesi Çıkar</option>
                            <option value="set_referral">Referans Bakiyesi Ayarla</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Miktar (USDT):</label>
                        <input type="number" class="form-control" name="amount" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Not:</label>
                        <textarea class="form-control" name="note" rows="3" placeholder="İşlem hakkında not ekleyin"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">İşlemi Gerçekleştir</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Referans kodu kopyalama
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('Referans kodu panoya kopyalandı!');
    }, function(err) {
        console.error('Kopyalama işlemi başarısız oldu: ', err);
    });
}
</script>

<?php include '../includes/footer.php'; ?>