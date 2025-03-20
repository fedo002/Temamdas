<?php
session_start();
require_once '../../includes/admin_userfunctions.php';
require_once '../../includes/admin_functions.php';

// Admin oturum kontrolü
if(!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

// Form gönderildi ise
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Form verilerini al
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $trc20_address = trim($_POST['trc20_address'] ?? '');
    $status = $_POST['status'] ?? 'active';
    $vip_level = (int)$_POST['vip_level'] ?? 0;
    $balance = (float)($_POST['balance'] ?? 0);
    
    // Kullanıcı oluştur
    $result = createUser([
        'username' => $username,
        'email' => $email,
        'password' => $password,
        'full_name' => $full_name,
        'trc20_address' => $trc20_address,
        'status' => $status,
        'vip_level' => $vip_level,
        'balance' => $balance
    ]);
    
    if(is_numeric($result)) {
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Kullanıcı başarıyla oluşturuldu.'];
        header('Location: details.php?id=' . $result);
        exit;
    } else {
        $message = ['type' => 'danger', 'text' => 'Hata: ' . $result];
    }
}

// VIP paketlerini al
$vip_packages = getVipPackages();

// Sayfa başlığı
$page_title = 'Yeni Kullanıcı Ekle';
include '../includes/header.php';
?>

<div class="container-fluid px-4 py-3">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h1 class="h3">Yeni Kullanıcı Ekle</h1>
            <a href="list.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i> Kullanıcı Listesi
            </a>
        </div>
    </div>
    
    <?php if(isset($message)): ?>
    <div class="alert alert-<?= $message['type'] ?> alert-dismissible fade show">
        <?= $message['text'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Kullanıcı Bilgileri</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Kullanıcı Adı:</label>
                                <input type="text" class="form-control" name="username" value="<?= isset($username) ? htmlspecialchars($username) : '' ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">E-posta:</label>
                                <input type="email" class="form-control" name="email" value="<?= isset($email) ? htmlspecialchars($email) : '' ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Şifre:</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Ad Soyad:</label>
                                <input type="text" class="form-control" name="full_name" value="<?= isset($full_name) ? htmlspecialchars($full_name) : '' ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">TRC20 Adresi:</label>
                                <input type="text" class="form-control" name="trc20_address" value="<?= isset($trc20_address) ? htmlspecialchars($trc20_address) : '' ?>">
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Durum:</label>
                                <select class="form-select" name="status">
                                    <option value="active" <?= isset($status) && $status == 'active' ? 'selected' : '' ?>>Aktif</option>
                                    <option value="pending" <?= isset($status) && $status == 'pending' ? 'selected' : '' ?>>Beklemede</option>
                                    <option value="blocked" <?= isset($status) && $status == 'blocked' ? 'selected' : '' ?>>Bloklanmış</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">VIP Seviyesi:</label>
                                <select class="form-select" name="vip_level">
                                    <?php foreach($vip_packages as $package): ?>
                                        <option value="<?= $package['id'] ?>" <?= isset($vip_level) && $vip_level == $package['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($package['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Başlangıç Bakiyesi:</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="balance" step="0.01" min="0" value="<?= isset($balance) ? $balance : '0' ?>">
                                    <span class="input-group-text">USDT</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="list.php" class="btn btn-secondary">İptal</a>
                            <button type="submit" class="btn btn-primary">Kullanıcı Oluştur</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>