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
    
    // Hata yoksa oluştur
    if (empty($errors)) {
        $package_id = createMiningPackage(
            $name,
            $hash_rate,
            $electricity_cost,
            $daily_revenue_rate,
            $package_price,
            $description,
            $is_active
        );
        
        if ($package_id) {
            // Başarılı mesajı ile listeye yönlendir
            header("Location: list.php?created=1&id=$package_id");
            exit;
        } else {
            $error_message = "Paket oluşturulurken bir hata oluştu.";
        }
    } else {
        $error_message = implode("<br>", $errors);
    }
}

// Sayfa başlığı
$page_title = 'Yeni Mining Paketi Ekle';
include '../includes/header.php';
?>

<div class="container-fluid px-4 py-4">
    <div class="row">
        <div class="col-12 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Yeni Mining Paketi Ekle</h1>
                <a href="list.php" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i> Listeye Dön
                </a>
            </div>
        </div>
    </div>
    
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
                                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <label for="hash_rate" class="col-md-3 col-form-label">Hash Rate (MH/s):</label>
                            <div class="col-md-9">
                                <input type="number" class="form-control" id="hash_rate" name="hash_rate" value="<?= $_POST['hash_rate'] ?? '10' ?>" step="0.01" min="0.01" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <label for="electricity_cost" class="col-md-3 col-form-label">Elektrik Maliyeti (kw/h):</label>
                            <div class="col-md-9">
                                <input type="number" class="form-control" id="electricity_cost" name="electricity_cost" value="<?= $_POST['electricity_cost'] ?? '0.1' ?>" step="0.0001" min="0" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <label for="daily_revenue_rate" class="col-md-3 col-form-label">Günlük Kazanç Oranı (%):</label>
                            <div class="col-md-9">
                                <input type="number" class="form-control" id="daily_revenue_rate" name="daily_revenue_rate" value="<?= $_POST['daily_revenue_rate'] ?? '2' ?>" step="0.01" min="0.01" required>
                                <div class="form-text">Günlük kazanç oranı, hash rate başına kazanç yüzdesidir.</div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <label for="package_price" class="col-md-3 col-form-label">Paket Fiyatı (USDT):</label>
                            <div class="col-md-9">
                                <input type="number" class="form-control" id="package_price" name="package_price" value="<?= $_POST['package_price'] ?? '100' ?>" step="0.01" min="0.01" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <label for="description" class="col-md-3 col-form-label">Açıklama:</label>
                            <div class="col-md-9">
                                <textarea class="form-control" id="description" name="description" rows="4"><?= htmlspecialchars($_POST['description'] ?? 'Başlangıç seviyesi mining paketi') ?></textarea>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-9 offset-md-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" <?= isset($_POST['is_active']) ? 'checked' : 'checked' ?>>
                                    <label class="form-check-label" for="is_active">Paket Aktif</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-9 offset-md-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i> Paketi Oluştur
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
                    <h5 class="card-title mb-0">Paket Önizleme</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <label class="fw-bold d-block mb-2">Günlük Tahmini Kazanç:</label>
                        <h3 class="text-success">
                            <span id="daily_revenue">0.20</span> USDT
                        </h3>
                    </div>
                    
                    <div class="mb-4">
                        <label class="fw-bold d-block mb-2">Aylık Tahmini Kazanç:</label>
                        <h3 class="text-primary">
                            <span id="monthly_revenue">6.00</span> USDT
                        </h3>
                    </div>
                    
                    <div class="mb-4">
                        <label class="fw-bold d-block mb-2">Yatırım Geri Dönüş Süresi:</label>
                        <h3>
                            <span id="roi_days">500</span> gün
                        </h3>
                    </div>
                    
                    <hr>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Bilgi:</strong> Yeni paket oluşturulduktan sonra düzenleme sayfasında daha fazla istatistik görüntüleyebilirsiniz.
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
    
    // Başlangıçta hesapla
    updateCalculations();
});
</script>

<?php include '../includes/footer.php'; ?>