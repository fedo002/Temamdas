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

// Paket ID kontrolü
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: list.php');
    exit;
}

$package_id = (int)$_GET['id'];

// Paket bilgilerini getir
$package = getMiningPackage($package_id);

if (!$package) {
    header('Location: list.php');
    exit;
}

// Form gönderildi mi kontrolü
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Form verilerini al
    $name = trim($_POST['name']);
    $hash_rate = (float)$_POST['hash_rate'];
    $electricity_cost = (float)$_POST['electricity_cost'];
    $daily_revenue_rate = (float)$_POST['daily_revenue_rate'] / 100; // Yüzde değerini oran olarak çevir
    $package_price = (float)$_POST['package_price'];
    $description = trim($_POST['description']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // Validasyon
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Paket adı boş olamaz.";
    }
    
    if ($hash_rate <= 0) {
        $errors[] = "Hash rate pozitif bir değer olmalıdır.";
    }
    
    if ($electricity_cost < 0) {
        $errors[] = "Elektrik maliyeti negatif olamaz.";
    }
    
    if ($daily_revenue_rate <= 0) {
        $errors[] = "Günlük kazanç oranı pozitif bir değer olmalıdır.";
    }
    
    if ($package_price <= 0) {
        $errors[] = "Paket fiyatı pozitif bir değer olmalıdır.";
    }
    
    // Hata yoksa güncelle
    if (empty($errors)) {
        $result = updateMiningPackage(
            $package_id,
            $name,
            $hash_rate,
            $electricity_cost,
            $daily_revenue_rate,
            $package_price,
            $description,
            $is_active
        );
        
        if ($result) {
            $success_message = "Mining paketi başarıyla güncellendi.";
            // Paket bilgilerini tekrar getir
            $package = getMiningPackage($package_id);
        } else {
            $error_message = "Paket güncellenirken bir hata oluştu.";
        }
    } else {
        $error_message = implode("<br>", $errors);
    }
}

// Sayfa başlığı
$page_title = 'Mining Paketi Düzenle';
include '../includes/header.php';
?>

<div class="container-fluid px-4 py-4">
    <div class="row">
        <div class="col-12 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Mining Paketi Düzenle</h1>
                <a href="list.php" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i> Listeye Dön
                </a>
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
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Paket Bilgileri</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="row mb-3">
                            <label for="name" class="col-md-3 col-form-label">Paket Adı:</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($package['name']) ?>" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <label for="hash_rate" class="col-md-3 col-form-label">Hash Rate (MH/s):</label>
                            <div class="col-md-9">
                                <input type="number" class="form-control" id="hash_rate" name="hash_rate" value="<?= $package['hash_rate'] ?>" step="0.01" min="0.01" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <label for="electricity_cost" class="col-md-3 col-form-label">Elektrik Maliyeti (kw/h):</label>
                            <div class="col-md-9">
                                <input type="number" class="form-control" id="electricity_cost" name="electricity_cost" value="<?= $package['electricity_cost'] ?>" step="0.0001" min="0" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <label for="daily_revenue_rate" class="col-md-3 col-form-label">Günlük Kazanç Oranı (%):</label>
                            <div class="col-md-9">
                                <input type="number" class="form-control" id="daily_revenue_rate" name="daily_revenue_rate" value="<?= $package['daily_revenue_rate'] * 100 ?>" step="0.01" min="0.01" required>
                                <div class="form-text">Günlük kazanç oranı, hash rate başına kazanç yüzdesidir.</div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <label for="package_price" class="col-md-3 col-form-label">Paket Fiyatı (USDT):</label>
                            <div class="col-md-9">
                                <input type="number" class="form-control" id="package_price" name="package_price" value="<?= $package['package_price'] ?>" step="0.01" min="0.01" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <label for="description" class="col-md-3 col-form-label">Açıklama:</label>
                            <div class="col-md-9">
                                <textarea class="form-control" id="description" name="description" rows="4"><?= htmlspecialchars($package['description']) ?></textarea>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-9 offset-md-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" <?= $package['is_active'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="is_active">Paket Aktif</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-9 offset-md-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i> Değişiklikleri Kaydet
                                </button>
                                <a href="list.php" class="btn btn-outline-secondary ms-2">
                                    <i class="fas fa-times me-2"></i> İptal
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Paket İstatistikleri</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <label class="fw-bold d-block mb-2">Günlük Tahmini Kazanç:</label>
                        <h3 class="text-success">
                            <span id="daily_revenue"><?= number_format($package['hash_rate'] * $package['daily_revenue_rate'], 6) ?></span> USDT
                        </h3>
                    </div>
                    
                    <div class="mb-4">
                        <label class="fw-bold d-block mb-2">Aylık Tahmini Kazanç:</label>
                        <h3 class="text-primary">
                            <span id="monthly_revenue"><?= number_format($package['hash_rate'] * $package['daily_revenue_rate'] * 30, 6) ?></span> USDT
                        </h3>
                    </div>
                    
                    <div class="mb-4">
                        <label class="fw-bold d-block mb-2">Yatırım Geri Dönüş Süresi:</label>
                        <h3>
                            <span id="roi_days"><?= number_format($package['package_price'] / ($package['hash_rate'] * $package['daily_revenue_rate']), 0) ?></span> gün
                        </h3>
                    </div>
                    
                    <hr>
                    
                    <div class="mb-4">
                        <label class="fw-bold d-block mb-2">Aktif Kullanıcı Sayısı:</label>
                        <h4>
                            <?= getPackageUserCount($package_id) ?> kullanıcı
                        </h4>
                    </div>
                    
                    <div class="mb-4">
                        <label class="fw-bold d-block mb-2">Toplam Satış:</label>
                        <h4>
                            <?= getPackageTotalSales($package_id) ?> adet
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Hesaplamaları güncelle
    function updateCalculations() {
        const hashRate = parseFloat(document.getElementById('hash_rate').value) || 0;
        const dailyRate = parseFloat(document.getElementById('daily_revenue_rate').value) / 100 || 0;
        const packagePrice = parseFloat(document.getElementById('package_price').value) || 0;
        
        // Günlük kazanç
        const dailyRevenue = hashRate * dailyRate;
        document.getElementById('daily_revenue').textContent = dailyRevenue.toFixed(6);
        
        // Aylık kazanç
        const monthlyRevenue = dailyRevenue * 30;
        document.getElementById('monthly_revenue').textContent = monthlyRevenue.toFixed(6);
        
        // Geri dönüş süresi
        const roiDays = dailyRevenue > 0 ? Math.round(packagePrice / dailyRevenue) : 0;
        document.getElementById('roi_days').textContent = roiDays;
    }
    
    // Input değişimlerini izle
    document.getElementById('hash_rate').addEventListener('input', updateCalculations);
    document.getElementById('daily_revenue_rate').addEventListener('input', updateCalculations);
    document.getElementById('package_price').addEventListener('input', updateCalculations);
});
</script>

<?php include '../includes/footer.php'; ?>