<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/admin_functions.php';


// Admin oturum kontrolü
if(!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}


// Admin bilgilerini al
$admin_id = $_SESSION['admin_id'];
$admin = getAdminDetailsi($admin_id);

// İşlem mesajları
$success_message = '';
$error_message = '';

// Profil güncelleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $full_name = trim($_POST['full_name']);
        $email = trim($_POST['email']);
        
        // E-posta değişimi kontrolü
        $is_email_changed = ($email !== $admin['email']);
        
        // Validasyon
        $errors = [];
        
        // E-posta kontrolü
        if (empty($email)) {
            $errors[] = "E-posta adresi gereklidir.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Geçerli bir e-posta adresi giriniz.";
        } elseif ($is_email_changed && !isAdminEmailAvailable($email)) {
            $errors[] = "Bu e-posta adresi zaten kullanılmaktadır.";
        }
        
        // Hata yoksa güncelleme yap
        if (empty($errors)) {
            $result = updateAdminProfile($admin_id, $full_name, $email);
            
            if ($result['success']) {
                // İşlem logu ekle
                addAdminLog($admin_id, 'profile_update', 'Profil bilgileri güncellendi.');
                
                $success_message = "Profil bilgileriniz başarıyla güncellendi.";
                // Admin bilgilerini yeniden al
                $admin = getAdminDetailsi($admin_id);
            } else {
                $error_message = $result['message'];
            }
        } else {
            $error_message = implode('<br>', $errors);
        }
    } elseif (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Validasyon
        $errors = [];
        
        // Mevcut şifre kontrolü
        if (empty($current_password)) {
            $errors[] = "Mevcut şifrenizi girmelisiniz.";
        } elseif (!password_verify($current_password, $admin['password'])) {
            $errors[] = "Mevcut şifreniz hatalı.";
        }
        
        // Yeni şifre kontrolü
        if (empty($new_password)) {
            $errors[] = "Yeni şifre gereklidir.";
        } elseif (strlen($new_password) < 8) {
            $errors[] = "Yeni şifre en az 8 karakter olmalıdır.";
        } elseif ($new_password !== $confirm_password) {
            $errors[] = "Yeni şifre ve onay şifresi eşleşmiyor.";
        }
        
        // Hata yoksa şifre değiştir
        if (empty($errors)) {
            $result = changeAdminPassword($admin_id, $new_password);
            
            if ($result['success']) {
                // İşlem logu ekle
                addAdminLog($admin_id, 'password_change', 'Şifre değiştirildi.');
                
                $success_message = "Şifreniz başarıyla değiştirildi.";
            } else {
                $error_message = $result['message'];
            }
        } else {
            $error_message = implode('<br>', $errors);
        }
    }
}

// Son giriş logları
$login_logs = getAdminLoginLogs($admin_id, 5);

$page_title = 'Profil Ayarları';
include '../includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>Profil Ayarları</h6>
                </div>
                <div class="card-body">
                    <?php if ($success_message): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i> <?= $success_message ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($error_message): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i> <?= $error_message ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php endif; ?>
                    
                    <ul class="nav nav-pills mb-4" id="profileTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="profile-info-tab" data-bs-toggle="tab" data-bs-target="#profile-info" type="button" role="tab" aria-controls="profile-info" aria-selected="true">
                                <i class="fas fa-user-edit me-2"></i> Profil Bilgileri
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="password-change-tab" data-bs-toggle="tab" data-bs-target="#password-change" type="button" role="tab" aria-controls="password-change" aria-selected="false">
                                <i class="fas fa-key me-2"></i> Şifre Değiştir
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="login-history-tab" data-bs-toggle="tab" data-bs-target="#login-history" type="button" role="tab" aria-controls="login-history" aria-selected="false">
                                <i class="fas fa-history me-2"></i> Giriş Geçmişi
                            </button>
                        </li>
                    </ul>
                    
                    <div class="tab-content" id="profileTabsContent">
                        <!-- Profil Bilgileri -->
                        <div class="tab-pane fade show active" id="profile-info" role="tabpanel" aria-labelledby="profile-info-tab">
                            <form method="POST" action="">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="username" class="form-control-label">Kullanıcı Adı</label>
                                            <input type="text" class="form-control" id="username" value="<?= htmlspecialchars($admin['username']) ?>" readonly>
                                            <small class="form-text text-muted">Kullanıcı adı değiştirilemez.</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="role" class="form-control-label">Yetki</label>
                                            <input type="text" class="form-control" id="role" value="<?= getAdminRoleName($admin['role']) ?>" readonly>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="full_name" class="form-control-label">Tam İsim</label>
                                            <input type="text" class="form-control" id="full_name" name="full_name" value="<?= htmlspecialchars($admin['full_name']) ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email" class="form-control-label">E-posta Adresi</label>
                                            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($admin['email']) ?>" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="created_at" class="form-control-label">Oluşturulma Tarihi</label>
                                            <input type="text" class="form-control" id="created_at" value="<?= date('d.m.Y H:i', strtotime($admin['created_at'])) ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="last_login" class="form-control-label">Son Giriş</label>
                                            <input type="text" class="form-control" id="last_login" value="<?= $admin['last_login'] ? date('d.m.Y H:i', strtotime($admin['last_login'])) : 'Henüz giriş yapılmadı' ?>" readonly>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-end mt-4">
                                    <button type="submit" name="update_profile" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i> Değişiklikleri Kaydet
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Şifre Değiştir -->
                        <div class="tab-pane fade" id="password-change" role="tabpanel" aria-labelledby="password-change-tab">
                            <form method="POST" action="">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="current_password" class="form-control-label">Mevcut Şifre</label>
                                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="new_password" class="form-control-label">Yeni Şifre</label>
                                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                                            <small class="form-text text-muted">En az 8 karakter uzunluğunda olmalıdır.</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="confirm_password" class="form-control-label">Yeni Şifre Tekrar</label>
                                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="alert alert-info mt-3">
                                    <i class="fas fa-info-circle me-2"></i> Güçlü bir şifre için, büyük/küçük harfler, rakamlar ve özel karakterler kullanın.
                                </div>
                                
                                <div class="d-flex justify-content-end mt-4">
                                    <button type="submit" name="change_password" class="btn btn-primary">
                                        <i class="fas fa-key me-2"></i> Şifreyi Değiştir
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Giriş Geçmişi -->
                        <div class="tab-pane fade" id="login-history" role="tabpanel" aria-labelledby="login-history-tab">
                            <div class="table-responsive">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tarih ve Saat</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">IP Adresi</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tarayıcı</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Durum</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (count($login_logs) > 0): ?>
                                            <?php foreach($login_logs as $log): ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex px-2 py-1">
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <h6 class="mb-0 text-sm"><?= date('d.m.Y', strtotime($log['created_at'])) ?></h6>
                                                            <p class="text-xs text-secondary mb-0"><?= date('H:i:s', strtotime($log['created_at'])) ?></p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <p class="text-xs font-weight-bold mb-0"><?= $log['ip_address'] ?></p>
                                                </td>
                                                <td>
                                                    <p class="text-xs text-secondary mb-0" style="max-width:300px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="<?= htmlspecialchars($log['user_agent']) ?>"><?= htmlspecialchars($log['user_agent']) ?></p>
                                                </td>
                                                <td>
                                                    <?php if ($log['status'] === 'success'): ?>
                                                        <span class="badge badge-sm bg-gradient-success">Başarılı</span>
                                                    <?php elseif ($log['status'] === 'failed'): ?>
                                                        <span class="badge badge-sm bg-gradient-danger">Başarısız</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-sm bg-gradient-warning">Geçersiz</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center py-4">
                                                    <p class="text-secondary mb-0">Henüz giriş kaydı bulunmuyor</p>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <?php if (count($login_logs) > 0): ?>
                            <div class="d-flex justify-content-end mt-4">
                                <a href="../logs/login_logs.php" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-list me-2"></i> Tüm Giriş Kayıtlarını Görüntüle
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>