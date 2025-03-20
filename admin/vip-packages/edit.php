<?php
session_start();
require_once '../../includes/admin_userfunctions.php';
require_once '../../includes/admin_functions.php';

// Admin oturum kontrolü
if(!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

// Paket ID kontrolü
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error_message'] = "Geçersiz paket ID'si.";
    header('Location: list.php');
    exit;
}

$package_id = (int)$_GET['id'];

// Paket bilgilerini getir
$package = getVipPackageById($package_id);

if (!$package) {
    $_SESSION['error_message'] = "Paket bulunamadı.";
    header('Location: list.php');
    exit;
}

// Form gönderildiyse
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Form verilerini al
    $name = trim($_POST['name'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $duration_days = (int)($_POST['duration_days'] ?? 30);
    $daily_game_limit = (int)($_POST['daily_game_limit'] ?? 0);
    $game_max_win_chance = (float)($_POST['game_max_win_chance'] ?? 0) / 100; // Yüzde değeri kesire çevir
    $referral_rate = (float)($_POST['referral_rate'] ?? 0) / 100; // Yüzde değeri kesire çevir
    $mining_bonus_rate = (float)($_POST['mining_bonus_rate'] ?? 0) / 100; // Yüzde değeri kesire çevir
    $features = $_POST['features'] ?? [];
    $description = trim($_POST['description'] ?? '');
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // Hata kontrolü
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Paket adı boş bırakılamaz.";
    }
    
    if ($price <= 0) {
        $errors[] = "Paket fiyatı sıfırdan büyük olmalıdır.";
    }
    
    if ($duration_days <= 0) {
        $errors[] = "Paket süresi sıfırdan büyük olmalıdır.";
    }
    
    // Hata yoksa güncelle
    if (empty($errors)) {
        // Özellikleri JSON'a dönüştür
        $features_json = json_encode($features, JSON_UNESCAPED_UNICODE);
        
        // VIP paketini güncelle
        $result = updateVipPackage(
            $package_id,
            $name, 
            $price, 
            $duration_days, 
            $daily_game_limit, 
            $game_max_win_chance, 
            $referral_rate, 
            $mining_bonus_rate, 
            $features_json, 
            $description, 
            $is_active
        );
        
        if ($result) {
            // Admin log kaydı
            addAdminLog($_SESSION['admin_id'], 'update_vip_package', "VIP paketi güncellendi: {$name}", $package_id, 'vip_package');
            
            // Başarı mesajı ve yönlendirme
            $_SESSION['success_message'] = "VIP paketi başarıyla güncellendi.";
            header('Location: list.php');
            exit;
        } else {
            $errors[] = "Paket güncellenirken bir hata oluştu.";
        }
    }
} else {
    // İlk form yüklemesi için değerleri paket verilerinden al
    $name = $package['name'];
    $price = $package['price'];
    // Sütun yoksa varsayılan değer kullan
    $duration_days = $package['duration_days'] ?? 30;
    $daily_game_limit = $package['daily_game_limit'];
    $game_max_win_chance = $package['game_max_win_chance'] * 100; // Kesirli değeri yüzdeye çevir
    $referral_rate = $package['referral_rate'] * 100; // Kesirli değeri yüzdeye çevir
    $mining_bonus_rate = $package['mining_bonus_rate'] * 100; // Kesirli değeri yüzdeye çevir
    
    // features sütunu yoksa boş dizi kullan
    $features = [];
    if (isset($package['features']) && !empty($package['features'])) {
        $decoded_features = json_decode($package['features'], true);
        if (is_array($decoded_features)) {
            $features = $decoded_features;
        }
    }
    
    $description = $package['description'] ?? '';
    $is_active = $package['is_active'];
}

// Sayfa başlığı
$page_title = 'VIP Paketi Düzenle';
include '../includes/header.php';
?>

<div class="container-fluid px-4 py-3">
    <div class="row align-items-center mb-4">
        <div class="col">
            <h1 class="h3">VIP Paketi Düzenle</h1>
        </div>
        <div class="col-auto">
            <a href="list.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i> Geri Dön
            </a>
        </div>
    </div>
    
    <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
                <li><?= $error ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>
    
    <div class="card">
        <div class="card-body">
            <form method="POST" action="">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Paket Adı <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?= htmlspecialchars($name) ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label for="price" class="form-label">Fiyat (USDT) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="price" name="price" 
                               value="<?= htmlspecialchars($price) ?>" step="0.01" min="0" required>
                    </div>
                    <div class="col-md-3">
                        <label for="duration_days" class="form-label">Süre (Gün) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="duration_days" name="duration_days" 
                               value="<?= htmlspecialchars($duration_days) ?>" min="1" required>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="daily_game_limit" class="form-label">Günlük Oyun Limiti</label>
                        <input type="number" class="form-control" id="daily_game_limit" name="daily_game_limit" 
                               value="<?= htmlspecialchars($daily_game_limit) ?>" min="0">
                        <div class="form-text">0 = Limitsiz</div>
                    </div>
                    <div class="col-md-3">
                        <label for="game_max_win_chance" class="form-label">Kazanma Şansı (%)</label>
                        <input type="number" class="form-control" id="game_max_win_chance" name="game_max_win_chance" 
                               value="<?= htmlspecialchars($game_max_win_chance) ?>" step="0.01" min="0" max="100">
                    </div>
                    <div class="col-md-3">
                        <label for="referral_rate" class="form-label">Referans Oranı (%)</label>
                        <input type="number" class="form-control" id="referral_rate" name="referral_rate" 
                               value="<?= htmlspecialchars($referral_rate) ?>" step="0.01" min="0" max="100">
                    </div>
                    <div class="col-md-3">
                        <label for="mining_bonus_rate" class="form-label">Mining Bonus Oranı (%)</label>
                        <input type="number" class="form-control" id="mining_bonus_rate" name="mining_bonus_rate" 
                               value="<?= htmlspecialchars($mining_bonus_rate) ?>" step="0.01" min="0" max="100">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Açıklama</label>
                    <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($description) ?></textarea>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Paket Özellikleri</label>
                    <div class="row">
                        <div class="col-md-6">
                            <div id="featuresContainer">
                                <?php if (empty($features)): ?>
                                <div class="input-group mb-2 feature-row">
                                    <input type="text" class="form-control" name="features[]" placeholder="Özellik girin">
                                    <button type="button" class="btn btn-outline-danger remove-feature">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <?php else: ?>
                                <?php foreach ($features as $feature): ?>
                                <div class="input-group mb-2 feature-row">
                                    <input type="text" class="form-control" name="features[]" 
                                           value="<?= htmlspecialchars($feature) ?>" placeholder="Özellik girin">
                                    <button type="button" class="btn btn-outline-danger remove-feature">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="addFeature">
                                <i class="fas fa-plus me-1"></i> Özellik Ekle
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                               <?= $is_active ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_active">
                            Aktif
                        </label>
                        <div class="form-text">İşaretliyse paket kullanıcılara gösterilir.</div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> Paketi Güncelle
                    </button>
                    <a href="list.php" class="btn btn-secondary ms-2">
                        <i class="fas fa-times me-2"></i> İptal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Özellik ekle/kaldır işlemleri
    const featuresContainer = document.getElementById('featuresContainer');
    const addFeatureBtn = document.getElementById('addFeature');
    
    // Özellik ekleme
    addFeatureBtn.addEventListener('click', function() {
        const newRow = document.createElement('div');
        newRow.className = 'input-group mb-2 feature-row';
        newRow.innerHTML = `
            <input type="text" class="form-control" name="features[]" placeholder="Özellik girin">
            <button type="button" class="btn btn-outline-danger remove-feature">
                <i class="fas fa-times"></i>
            </button>
        `;
        featuresContainer.appendChild(newRow);
        
        // Yeni eklenen silme butonuna event listener ekle
        newRow.querySelector('.remove-feature').addEventListener('click', removeFeature);
    });
    
    // Özellik silme
    function removeFeature() {
        this.closest('.feature-row').remove();
    }
    
    // Mevcut silme butonlarına event listener ekle
    document.querySelectorAll('.remove-feature').forEach(button => {
        button.addEventListener('click', removeFeature);
    });
});
</script>

<?php include '../includes/footer.php'; ?>