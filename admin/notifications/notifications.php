<?php
session_start();
require_once '../../includes/admin_userfunctions.php';
require_once '../../includes/admin_functions.php';
require_once '../../includes/notification_functions.php';
require_once '../includes/admin_notification_functions.php';

// Admin oturum kontrolü
if(!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

// Tüm kullanıcıları al
$users = getAllUsers();

// VIP kullanıcıları al
$vip_users = getVIPUsers();

// Mining paketi olan kullanıcıları al
$mining_users = getMiningUsers();

// Mesaj sonucu için değişkenler
$message = null;
$message_type = null;

// Form gönderildi ise
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $notification_type = $_POST['notification_type'] ?? 'general';
    $target_type = $_POST['target_type'] ?? 'all';
    $languages = $_POST['languages'] ?? ['all'];
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    
    // Hedef kullanıcıları belirle
    $target_users = [];
    
    if ($target_type === 'all') {
        $target_users = array_column($users, 'id');
    } else if ($target_type === 'vip') {
        $target_users = array_column($vip_users, 'id');
    } else if ($target_type === 'mining') {
        $target_users = array_column($mining_users, 'id');
    } else if ($target_type === 'specific' && isset($_POST['specific_users'])) {
        $target_users = $_POST['specific_users'];
    }
    
    // Her dil için bildirim içeriği oluştur
    $notification_contents = [];
    foreach ($languages as $lang) {
        $notification_contents[] = [
            'language' => $lang,
            'notification_type' => $notification_type,
            'title' => $title,
            'content' => $content
        ];
    }
    
    // Bildirimleri gönder
    $success = true;
    $sent_count = 0;
    
    foreach ($target_users as $user_id) {
        // Her kullanıcı için bildirim oluştur
        $notification_id = createNotification($user_id);
        
        if ($notification_id) {
            // Bildirim içeriklerini ekle
            foreach ($notification_contents as $content_data) {
                $content_success = addNotificationContent(
                    $notification_id, 
                    $content_data['language'], 
                    $content_data['notification_type'], 
                    $content_data['title'], 
                    $content_data['content']
                );
                
                if (!$content_success) {
                    $success = false;
                    break;
                }
            }
            
            $sent_count++;
        } else {
            $success = false;
        }
    }
    
    if ($success) {
        $message = "Bildirimler başarıyla gönderildi. Toplam $sent_count kullanıcıya bildirim gönderildi.";
        $message_type = "success";
    } else {
        $message = "Bildirimler gönderilirken bir hata oluştu. Lütfen tekrar deneyin.";
        $message_type = "error";
    }
}

// Sayfa başlığı
$page_title = 'Bildirim Yönetimi';
include '../includes/header.php';
?>

<div class="container-fluid px-4 py-3">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h1 class="h3">Bildirim Yönetimi</h1>
            <a href="notification_history.php" class="btn btn-outline-primary">
                <i class="fas fa-history me-2"></i> Bildirim Geçmişi
            </a>
        </div>
    </div>
    
    <?php if($message): ?>
    <div class="alert alert-<?= $message_type == 'success' ? 'success' : 'danger' ?> alert-dismissible fade show">
        <?= $message ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Yeni Bildirim Oluştur</h5>
                    <span class="badge bg-primary">Toplam Kullanıcı: <?= count($users) ?></span>
                </div>
                <div class="card-body">
                    <form method="POST" id="notification-form">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="notification_type" class="form-label">Bildirim Türü:</label>
                                    <select class="form-select" id="notification_type" name="notification_type" required>
                                        <option value="general">Genel Bildirim</option>
                                        <option value="vip">VIP Bildirim</option>
                                        <option value="mining">Mining Bildirim</option>
                                    </select>
                                    <small class="text-muted">
                                        Bildirim türüne göre kullanıcılara filtreleme yapılır. Örneğin, VIP bildirimleri 
                                        sadece VIP kullanıcılara gösterilir.
                                    </small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="target_type" class="form-label">Hedef Kullanıcılar:</label>
                                    <select class="form-select" id="target_type" name="target_type" required>
                                        <option value="all">Tüm Kullanıcılar (<?= count($users) ?>)</option>
                                        <option value="vip">Sadece VIP Kullanıcılar (<?= count($vip_users) ?>)</option>
                                        <option value="mining">Sadece Mining Kullanıcılar (<?= count($mining_users) ?>)</option>
                                        <option value="specific">Belirli Kullanıcılar</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3" id="specific_users_container" style="display: none;">
                            <label for="specific_users" class="form-label">Kullanıcıları Seçin:</label>
                            <select class="form-select" id="specific_users" name="specific_users[]" multiple size="6">
                                <?php foreach ($users as $user): ?>
                                <option value="<?= $user['id'] ?>"><?= $user['username'] ?> (<?= $user['email'] ?>)</option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Ctrl tuşuna basılı tutarak birden fazla kullanıcı seçebilirsiniz.</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Diller:</label>
                            <div class="language-options">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="lang_all" name="languages[]" value="all" checked>
                                    <label class="form-check-label" for="lang_all">Tüm Diller</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input language-option" type="checkbox" id="lang_tr" name="languages[]" value="tr">
                                    <label class="form-check-label" for="lang_tr">Türkçe</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input language-option" type="checkbox" id="lang_en" name="languages[]" value="en">
                                    <label class="form-check-label" for="lang_en">İngilizce</label>
                                </div>
                                <!-- Diğer dilleri buraya ekleyebilirsiniz -->
                            </div>
                            <small class="text-muted">
                                "Tüm Diller" seçilirse, bildirim kullanıcının dil tercihine bakılmaksızın herkese gösterilir.
                                Belirli diller seçilirse, bildirim sadece o dili kullanan kullanıcılara gösterilir.
                            </small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Bildirim Başlığı:</label>
                            <input type="text" class="form-control" id="title" name="title" required maxlength="100">
                        </div>
                        
                        <div class="mb-3">
                            <label for="content" class="form-label">Bildirim İçeriği:</label>
                            <textarea class="form-control" id="content" name="content" rows="5" required maxlength="500"></textarea>
                            <div class="text-end mt-1">
                                <small class="text-muted"><span id="content-counter">0</span>/500 karakter</small>
                            </div>
                        </div>
                        
                        <div class="preview-section p-3 rounded mb-4" style="background-color: #f8f9fa; display: none;">
                            <h6 class="mb-3">Bildirim Önizleme:</h6>
                            <div class="notification-preview d-flex align-items-start bg-dark text-light p-3 rounded">
                                <div class="notification-icon me-3 p-2 rounded-circle" style="background-color: rgba(255,255,255,0.1);">
                                    <i class="fas fa-bell" id="preview-icon"></i>
                                </div>
                                <div class="notification-content">
                                    <h6 id="preview-title">Bildirim Başlığı</h6>
                                    <p class="mb-1" id="preview-content">Bildirim içeriği burada görünecek...</p>
                                    <small class="text-muted"><?= date('d.m.Y H:i') ?></small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-end">
                            <button type="button" class="btn btn-outline-secondary me-2" id="preview-btn">
                                <i class="fas fa-eye me-1"></i> Önizle
                            </button>
                            <button type="submit" class="btn btn-primary" id="send-notification-btn">
                                <i class="fas fa-paper-plane me-1"></i> Bildirimi Gönder
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Yan bilgi panelleri, dokümantasyon için yer kazanmak adına kısaltıldı -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Kullanıcı İstatistikleri</h5>
                </div>
                <div class="card-body">
                    <!-- İstatistikler... -->
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Bildirim Türleri</h5>
                </div>
                <div class="card-body">
                    <!-- Bildirim türleri açıklamaları... -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form elementlerini seç
    const notificationForm = document.getElementById('notification-form');
    const targetTypeSelect = document.getElementById('target_type');
    const specificUsersContainer = document.getElementById('specific_users_container');
    const notificationTypeSelect = document.getElementById('notification_type');
    const titleInput = document.getElementById('title');
    const contentInput = document.getElementById('content');
    const contentCounter = document.getElementById('content-counter');
    const previewBtn = document.getElementById('preview-btn');
    const previewSection = document.querySelector('.preview-section');
    const previewTitle = document.getElementById('preview-title');
    const previewContent = document.getElementById('preview-content');
    const previewIcon = document.getElementById('preview-icon');
    const langAllCheckbox = document.getElementById('lang_all');
    const languageOptions = document.querySelectorAll('.language-option');
    const sendBtn = document.getElementById('send-notification-btn');
    
    // Karakter sayacı
    contentInput.addEventListener('input', function() {
        const length = this.value.length;
        contentCounter.textContent = length;
        
        if (length > 450) {
            contentCounter.classList.add('text-danger');
        } else {
            contentCounter.classList.remove('text-danger');
        }
    });
    
    // Hedef kullanıcı tipi değiştiğinde
    targetTypeSelect.addEventListener('change', function() {
        if (this.value === 'specific') {
            specificUsersContainer.style.display = 'block';
        } else {
            specificUsersContainer.style.display = 'none';
        }
        
        // Bildirim türü ve hedef kullanıcı tipini senkronize et
        if (this.value === 'vip') {
            notificationTypeSelect.value = 'vip';
        } else if (this.value === 'mining') {
            notificationTypeSelect.value = 'mining';
        }
    });
    
    // Bildirim türü değiştiğinde
    notificationTypeSelect.addEventListener('change', function() {
        updatePreviewIcon();
    });
    
    // Dil seçenekleri kontrolü
    langAllCheckbox.addEventListener('change', function() {
        if (this.checked) {
            languageOptions.forEach(option => {
                option.checked = false;
                option.disabled = true;
            });
        } else {
            languageOptions.forEach(option => {
                option.disabled = false;
            });
        }
    });
    
    // Önizleme butonu
    previewBtn.addEventListener('click', function() {
        previewTitle.textContent = titleInput.value || 'Bildirim Başlığı';
        previewContent.textContent = contentInput.value || 'Bildirim içeriği burada görünecek...';
        updatePreviewIcon();
        previewSection.style.display = 'block';
    });
    
    // Önizleme ikonunu güncelle
    function updatePreviewIcon() {
        const notificationType = notificationTypeSelect.value;
        
        if (notificationType === 'vip') {
            previewIcon.className = 'fas fa-crown text-warning';
        } else if (notificationType === 'mining') {
            previewIcon.className = 'fas fa-hammer text-info';
        } else {
            previewIcon.className = 'fas fa-bell text-primary';
        }
    }
    
    // Form doğrulama
    notificationForm.addEventListener('submit', function(e) {
        // Gerekli doğrulama kontrolleri...
        
        // Onay kutucuğu
        if (!confirm('Bu bildirimi göndermek istediğinizden emin misiniz?')) {
            e.preventDefault();
            return false;
        }
        
        // Gönderme butonunu devre dışı bırak
        sendBtn.disabled = true;
        sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Gönderiliyor...';
    });
});
</script>

<?php include '../includes/footer.php'; ?>