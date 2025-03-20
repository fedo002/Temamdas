<?php
session_start();
require_once '../../includes/admin_userfunctions.php';
require_once '../../includes/admin_functions.php';

// Admin oturum kontrolü
if(!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

// Talep ID kontrolü
if(!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: tickets.php');
    exit;
}

$ticket_id = (int)$_GET['id'];
$ticket = getTicketDetails($ticket_id);

// Talep bulunamadıysa listeye geri dön
if(!$ticket) {
    $_SESSION['message'] = ['type' => 'danger', 'text' => 'Destek talebi bulunamadı.'];
    header('Location: tickets.php');
    exit;
}

// Cevap formu gönderildi ise
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply'])) {
    $message = trim($_POST['message'] ?? '');
    
    if(!empty($message)) {
        // Cevap ekle
        $result = addTicketReply($ticket_id, $message, $_SESSION['admin_id'], false);
        
        if($result === true) {
            // Yönlendir ve sayfayı yenile
            header('Location: view-ticket.php?id=' . $ticket_id . '&success=1');
            exit;
        } else {
            $error = $result;
        }
    } else {
        $error = 'Mesaj alanı boş olamaz.';
    }
}

// Durum değiştirme formu gönderildi ise
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $status = $_POST['status'];
    
    // Durumu güncelle
    $result = updateTicketStatus($ticket_id, $status);
    
    if($result === true) {
        // Yönlendir ve sayfayı yenile
        header('Location: view-ticket.php?id=' . $ticket_id . '&status_updated=1');
        exit;
    } else {
        $error = $result;
    }
}

// Başarı mesajları
if(isset($_GET['success'])) {
    $message = ['type' => 'success', 'text' => 'Cevabınız başarıyla gönderildi.'];
}

if(isset($_GET['status_updated'])) {
    $message = ['type' => 'success', 'text' => 'Talep durumu başarıyla güncellendi.'];
}

// Talebi güncel bilgilerle yeniden al
$ticket = getTicketDetails($ticket_id);

// Sayfa başlığı
$page_title = 'Destek Talebi #' . $ticket_id;
include '../includes/header.php';
?>

<div class="container-fluid px-4 py-3">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h1 class="h3">Destek Talebi #<?= $ticket_id ?></h1>
            <div>
                <a href="tickets.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Tüm Talepler
                </a>
                <?php if($ticket['status'] != 'closed'): ?>
                <button type="button" class="btn btn-success ms-2" data-bs-toggle="modal" data-bs-target="#statusModal">
                    <i class="fas fa-check-circle me-2"></i> Talebi Kapat
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php if(isset($message)): ?>
    <div class="alert alert-<?= $message['type'] ?> alert-dismissible fade show">
        <?= $message['text'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    
    <?php if(isset($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= $error ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-md-4 order-md-2 mb-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Talep Bilgileri</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Durum:</span>
                            <?php if($ticket['status'] == 'open'): ?>
                                <span class="badge bg-info">Açık</span>
                            <?php elseif($ticket['status'] == 'in_progress'): ?>
                                <span class="badge bg-warning">İşlemde</span>
                            <?php else: ?>
                                <span class="badge bg-success">Kapalı</span>
                            <?php endif; ?>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Öncelik:</span>
                            <?php if($ticket['priority'] == 'low'): ?>
                                <span class="badge bg-secondary">Düşük</span>
                            <?php elseif($ticket['priority'] == 'medium'): ?>
                                <span class="badge bg-primary">Orta</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Yüksek</span>
                            <?php endif; ?>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Oluşturulma:</span>
                            <span><?= date('d.m.Y H:i', strtotime($ticket['created_at'])) ?></span>
                        </li>
                        <?php if($ticket['last_updated']): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Son Güncelleme:</span>
                            <span><?= date('d.m.Y H:i', strtotime($ticket['last_updated'])) ?></span>
                        </li>
                        <?php endif; ?>
                    </ul>
                    
                    <?php if($ticket['status'] != 'closed'): ?>
                    <form method="POST" class="mt-3">
                        <div class="mb-3">
                            <label class="form-label">Durum Değiştir:</label>
                            <select class="form-select" name="status">
                                <option value="open" <?= $ticket['status'] == 'open' ? 'selected' : '' ?>>Açık</option>
                                <option value="in_progress" <?= $ticket['status'] == 'in_progress' ? 'selected' : '' ?>>İşlemde</option>
                                <option value="closed">Kapalı</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Durumu Güncelle</button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Kullanıcı Bilgileri</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar avatar-md">
                                <span class="avatar-initial rounded-circle bg-primary"><?= strtoupper(substr($ticket['username'], 0, 1)) ?></span>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0"><?= htmlspecialchars($ticket['username']) ?></h6>
                            <small class="text-muted"><?= $ticket['email'] ?></small>
                        </div>
                    </div>
                    
                    <a href="../users/details.php?id=<?= $ticket['user_id'] ?>" class="btn btn-outline-primary btn-sm d-block mb-2">
                        <i class="fas fa-user me-2"></i> Kullanıcı Detayları
                    </a>
                    
                    <a href="tickets.php?user_id=<?= $ticket['user_id'] ?>" class="btn btn-outline-info btn-sm d-block">
                        <i class="fas fa-ticket-alt me-2"></i> Kullanıcının Tüm Talepleri
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-8 order-md-1">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><?= htmlspecialchars($ticket['subject']) ?></h5>
                </div>
                <div class="card-body">
                    <!-- Mesajlar -->
                    <div class="chat-history bg-dark p-3 rounded mb-4" style="max-height: 500px; overflow-y: auto;">
                        <!-- İlk Mesaj (Talep) -->
                        <div class="chat-message customer">
                            <div class="d-flex mb-4">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-md">
                                        <span class="avatar-initial rounded-circle bg-primary"><?= strtoupper(substr($ticket['username'], 0, 1)) ?></span>
                                    </div>
                                </div>
                                <div class="flex-grow-1 bg-light-dark p-3 rounded">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <h6 class="mb-0"><?= htmlspecialchars($ticket['username']) ?> <span class="badge bg-secondary">Kullanıcı</span></h6>
                                        <small class="text-muted"><?= date('d.m.Y H:i', strtotime($ticket['created_at'])) ?></small>
                                    </div>
                                    <div class="mt-2">
                                        <?= nl2br(htmlspecialchars($ticket['messages'][0]['message'] ?? '')) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Diğer Mesajlar -->
                        <?php foreach($ticket['messages'] as $index => $message): ?>
                            <?php if($index === 0) continue; // İlk mesajı atla ?>
                            
                            <div class="chat-message <?= $message['is_user_message'] ? 'customer' : 'admin' ?>">
                                <div class="d-flex mb-4">
                                    <?php if($message['is_user_message']): ?>
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar avatar-md">
                                                <span class="avatar-initial rounded-circle bg-primary"><?= strtoupper(substr($message['username'], 0, 1)) ?></span>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 bg-light-dark p-3 rounded">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <h6 class="mb-0"><?= htmlspecialchars($message['username']) ?> <span class="badge bg-secondary">Kullanıcı</span></h6>
                                                <small class="text-muted"><?= date('d.m.Y H:i', strtotime($message['created_at'])) ?></small>
                                            </div>
                                    <?php else: ?>
                                        <div class="flex-grow-1 bg-dark-light p-3 rounded">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <h6 class="mb-0"><span class="badge bg-success">Admin</span> <?= htmlspecialchars($message['username']) ?></h6>
                                                <small class="text-muted"><?= date('d.m.Y H:i', strtotime($message['created_at'])) ?></small>
                                            </div>
                                    <?php endif; ?>
                                        <div class="mt-2">
                                            <?= nl2br(htmlspecialchars($message['message'])) ?>
                                        </div>
                                    </div>
                                    <?php if(!$message['is_user_message']): ?>
                                        <div class="flex-shrink-0 ms-3">
                                            <div class="avatar avatar-md">
                                                <span class="avatar-initial rounded-circle bg-success">A</span>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Cevap Formu -->
                    <?php if($ticket['status'] != 'closed'): ?>
                    <form method="POST" action="">
                        <input type="hidden" name="reply" value="1">
                        <div class="mb-3">
                            <label class="form-label">Cevabınız:</label>
                            <textarea class="form-control" name="message" rows="5" required></textarea>
                        </div>
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#templateModal">
                                <i class="fas fa-comment-dots me-2"></i> Hazır Cevaplar
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i> Cevap Gönder
                            </button>
                        </div>
                    </form>
                    <?php else: ?>
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i> Bu destek talebi kapatılmıştır. Tekrar açmak için durumu değiştirin.
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Talep Kapatma Modal -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark">
            <div class="modal-header border-0">
                <h5 class="modal-title">Talebi Kapat</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bu destek talebini kapatmak istediğinize emin misiniz?</p>
                <p class="text-muted">Bu işlem geri alınabilir.</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <form method="POST">
                    <input type="hidden" name="status" value="closed">
                    <button type="submit" class="btn btn-success">Talebi Kapat</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Hazır Cevaplar Modal -->
<div class="modal fade" id="templateModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark">
            <div class="modal-header border-0">
                <h5 class="modal-title">Hazır Cevaplar</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="list-group">
                    <button type="button" class="list-group-item list-group-item-action" onclick="insertTemplate('Merhaba, talebiniz için teşekkürler. İncelememiz devam ediyor, en kısa sürede dönüş yapacağız.')">
                        <strong>İnceleme Devam Ediyor</strong>
                        <p class="mb-0 text-muted small">Merhaba, talebiniz için teşekkürler. İncelememiz devam ediyor, en kısa sürede dönüş yapacağız.</p>
                    </button>
                    <button type="button" class="list-group-item list-group-item-action" onclick="insertTemplate('Merhaba, talebinizi çözdük. Herhangi bir sorunuz olursa bize tekrar yazabilirsiniz. İyi günler.')">
                        <strong>Sorun Çözüldü</strong>
                        <p class="mb-0 text-muted small">Merhaba, talebinizi çözdük. Herhangi bir sorunuz olursa bize tekrar yazabilirsiniz. İyi günler.</p>
                    </button>
                    <button type="button" class="list-group-item list-group-item-action" onclick="insertTemplate('Merhaba, talebiniz için teşekkürler. Daha fazla bilgiye ihtiyacımız var. Lütfen aşağıdaki bilgileri paylaşır mısınız:\n\n- İşletim sisteminiz\n- Tarayıcınız\n- Hatayı ne zaman aldınız?\n\nBu bilgiler sorununuzu çözmemize yardımcı olacaktır.')">
                        <strong>Daha Fazla Bilgi İsteği</strong>
                        <p class="mb-0 text-muted small">Kullanıcıdan ek bilgi isteme.</p>
                    </button>
                    <button type="button" class="list-group-item list-group-item-action" onclick="insertTemplate('Merhaba, sorunuzu çözmemize yardımcı olmak için ekran görüntüsü gönderebilir misiniz? Bu, sorununuzu anlamamızı kolaylaştıracaktır. Teşekkürler.')">
                        <strong>Ekran Görüntüsü İsteği</strong>
                        <p class="mb-0 text-muted small">Kullanıcıdan ekran görüntüsü isteme.</p>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Hazır cevapları eklemek için
function insertTemplate(text) {
    const textarea = document.querySelector('textarea[name="message"]');
    textarea.value = text;
    document.getElementById('templateModal').querySelector('.btn-close').click();
    textarea.focus();
}
</script>

<?php include '../includes/footer.php'; ?>