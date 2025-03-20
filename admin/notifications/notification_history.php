<?php
session_start();
require_once '../../includes/admin_userfunctions.php';
require_once '../../includes/admin_functions.php';
require_once '../../includes/notification_functions.php';

// Admin oturum kontrolü
if(!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

// Sayfa numarası
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 15; // Sayfa başına gösterilecek bildirim sayısı
$offset = ($page - 1) * $per_page;

// Filtreleme parametreleri
$filter_type = isset($_GET['type']) ? $_GET['type'] : '';
$filter_date = isset($_GET['date']) ? $_GET['date'] : '';
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';

// Bildirimleri getir
$notifications = getAdminNotifications($filter_type, $filter_date, $filter_status, $per_page, $offset);

// Toplam bildirim sayısını al
$total_notifications = getTotalNotificationCount($filter_type, $filter_date, $filter_status);
$total_pages = ceil($total_notifications / $per_page);

// Bildirim silme işlemi
if (isset($_POST['delete_notification']) && isset($_POST['notification_id'])) {
    $notification_id = (int)$_POST['notification_id'];
    
    if (deleteNotification($notification_id)) {
        $message = "Bildirim başarıyla silindi.";
        $message_type = "success";
    } else {
        $message = "Bildirim silinirken bir hata oluştu.";
        $message_type = "error";
    }
    
    // Sayfayı yeniden yükle
    header("Location: notification_history.php?page=$page" . 
           ($filter_type ? "&type=$filter_type" : "") . 
           ($filter_date ? "&date=$filter_date" : "") . 
           ($filter_status ? "&status=$filter_status" : "") . 
           ($message ? "&message=" . urlencode($message) . "&message_type=$message_type" : ""));
    exit;
}

// URL'den mesaj parametrelerini al
if (isset($_GET['message'])) {
    $message = $_GET['message'];
    $message_type = $_GET['message_type'] ?? 'info';
}

// Sayfa başlığı
$page_title = 'Bildirim Geçmişi';
include '../includes/header.php';
?>

<div class="container-fluid px-4 py-3">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h1 class="h3">Bildirim Geçmişi</h1>
            <a href="notifications.php" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> Yeni Bildirim Oluştur
            </a>
        </div>
    </div>
    
    <?php if(isset($message)): ?>
    <div class="alert alert-<?= $message_type == 'success' ? 'success' : ($message_type == 'error' ? 'danger' : 'info') ?> alert-dismissible fade show">
        <?= $message ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Filtreler</h5>
        </div>
        <div class="card-body">
            <form action="" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="type" class="form-label">Bildirim Türü</label>
                    <select class="form-select" id="type" name="type">
                        <option value="">Tümü</option>
                        <option value="general" <?= $filter_type == 'general' ? 'selected' : '' ?>>Genel</option>
                        <option value="vip" <?= $filter_type == 'vip' ? 'selected' : '' ?>>VIP</option>
                        <option value="mining" <?= $filter_type == 'mining' ? 'selected' : '' ?>>Mining</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="date" class="form-label">Tarih</label>
                    <select class="form-select" id="date" name="date">
                        <option value="">Tümü</option>
                        <option value="today" <?= $filter_date == 'today' ? 'selected' : '' ?>>Bugün</option>
                        <option value="yesterday" <?= $filter_date == 'yesterday' ? 'selected' : '' ?>>Dün</option>
                        <option value="last_week" <?= $filter_date == 'last_week' ? 'selected' : '' ?>>Son 7 Gün</option>
                        <option value="last_month" <?= $filter_date == 'last_month' ? 'selected' : '' ?>>Son 30 Gün</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Durum</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Tümü</option>
                        <option value="read" <?= $filter_status == 'read' ? 'selected' : '' ?>>Okunmuş</option>
                        <option value="unread" <?= $filter_status == 'unread' ? 'selected' : '' ?>>Okunmamış</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-1"></i> Filtrele
                    </button>
                    <a href="notification_history.php" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i> Temizle
                    </a>
                </div>
                
                <?php if (isset($_GET['page'])): ?>
                <input type="hidden" name="page" value="<?= $_GET['page'] ?>">
                <?php endif; ?>
            </form>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Gönderilen Bildirimler</h5>
            <span class="badge bg-primary"><?= $total_notifications ?> Sonuç</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th width="5%">#</th>
                            <th>Bildirim Bilgileri</th>
                            <th width="15%">Alıcı</th>
                            <th width="15%">Tür</th>
                            <th width="15%">Tarih</th>
                            <th width="10%">Durum</th>
                            <th width="10%">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($notifications)): ?>
                            <?php foreach ($notifications as $index => $notification): ?>
                                <tr>
                                    <td><?= $offset + $index + 1 ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($notification['title']) ?></strong>
                                        <p class="text-muted mb-0 small"><?= htmlspecialchars(mb_substr($notification['content'], 0, 100)) ?><?= strlen($notification['content']) > 100 ? '...' : '' ?></p>
                                    </td>
                                    <td>
                                        <span class="d-block"><?= htmlspecialchars($notification['username']) ?></span>
                                        <small class="text-muted"><?= htmlspecialchars($notification['email']) ?></small>
                                    </td>
                                    <td>
                                        <?php if ($notification['notification_type'] == 'vip'): ?>
                                            <span class="badge bg-warning text-dark">VIP</span>
                                        <?php elseif ($notification['notification_type'] == 'mining'): ?>
                                            <span class="badge bg-info text-dark">Mining</span>
                                        <?php else: ?>
                                            <span class="badge bg-primary">Genel</span>
                                        <?php endif; ?>
                                        <small class="d-block text-muted mt-1">Dil: <?= strtoupper($notification['language']) ?></small>
                                    </td>
                                    <td>
                                        <?= date('d.m.Y', strtotime($notification['created_at'])) ?>
                                        <small class="d-block text-muted"><?= date('H:i', strtotime($notification['created_at'])) ?></small>
                                    </td>
                                    <td>
                                        <?php if ($notification['read_status']): ?>
                                            <span class="badge bg-success">Okundu</span>
                                            <small class="d-block text-muted mt-1"><?= date('d.m.Y H:i', strtotime($notification['read_at'])) ?></small>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Okunmadı</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-icon" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#notificationDetailModal" 
                                                       data-title="<?= htmlspecialchars($notification['title']) ?>" 
                                                       data-content="<?= htmlspecialchars($notification['content']) ?>" 
                                                       data-type="<?= $notification['notification_type'] ?>" 
                                                       data-created="<?= date('d.m.Y H:i', strtotime($notification['created_at'])) ?>" 
                                                       data-user="<?= htmlspecialchars($notification['username']) ?>"
                                                       data-status="<?= $notification['read_status'] ? 'Okundu' : 'Okunmadı' ?>"
                                                       data-read-time="<?= $notification['read_status'] ? date('d.m.Y H:i', strtotime($notification['read_at'])) : '-' ?>">
                                                        <i class="fas fa-eye me-2"></i> Detaylar
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#deleteNotificationModal" data-id="<?= $notification['notification_id'] ?>">
                                                        <i class="fas fa-trash-alt me-2 text-danger"></i> Sil
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="fas fa-bell-slash fa-3x mb-3 text-muted"></i>
                                    <h5 class="text-muted">Bildirim bulunamadı</h5>
                                    <p class="text-muted">Filtreleri temizleyerek tüm bildirimleri görebilirsiniz</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <?php if ($total_pages > 1): ?>
            <div class="card-footer">
                <nav>
                    <ul class="pagination justify-content-center mb-0">
                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page - 1 ?><?= $filter_type ? "&type=$filter_type" : "" ?><?= $filter_date ? "&date=$filter_date" : "" ?><?= $filter_status ? "&status=$filter_status" : "" ?>">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>
                        
                        <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?><?= $filter_type ? "&type=$filter_type" : "" ?><?= $filter_date ? "&date=$filter_date" : "" ?><?= $filter_status ? "&status=$filter_status" : "" ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        
                        <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page + 1 ?><?= $filter_type ? "&type=$filter_type" : "" ?><?= $filter_date ? "&date=$filter_date" : "" ?><?= $filter_status ? "&status=$filter_status" : "" ?>">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Bildirim Detay Modal -->
<div class="modal fade" id="notificationDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bildirim Detayı</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="notification-preview p-3 rounded mb-3 bg-dark text-light">
                    <div class="d-flex mb-3">
                        <div class="notification-icon me-3 p-2 rounded-circle bg-opacity-10" id="modal-icon-container">
                            <i class="fas fa-bell" id="modal-icon"></i>
                        </div>
                        <div>
                            <h5 id="modal-title"></h5>
                            <small class="text-muted" id="modal-date"></small>
                        </div>
                    </div>
                    <p id="modal-content" class="mb-0"></p>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Alıcı:</label>
                            <p id="modal-user" class="mb-1"></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Durum:</label>
                            <p id="modal-status" class="mb-1"></p>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Okunma Zamanı:</label>
                    <p id="modal-read-time" class="mb-0"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
            </div>
        </div>
    </div>
</div>

<!-- Bildirim Silme Modal -->
<div class="modal fade" id="deleteNotificationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bildirim Sil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Bu bildirimi silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <form method="POST">
                    <input type="hidden" name="notification_id" id="delete-notification-id">
                    <button type="submit" name="delete_notification" class="btn btn-danger">Sil</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Bildirim detay modalı
    const notificationDetailModal = document.getElementById('notificationDetailModal');
    if (notificationDetailModal) {
        notificationDetailModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const title = button.getAttribute('data-title');
            const content = button.getAttribute('data-content');
            const type = button.getAttribute('data-type');
            const created = button.getAttribute('data-created');
            const user = button.getAttribute('data-user');
            const status = button.getAttribute('data-status');
            const readTime = button.getAttribute('data-read-time');
            
            // Modal içeriğini güncelle
            document.getElementById('modal-title').textContent = title;
            document.getElementById('modal-content').textContent = content;
            document.getElementById('modal-date').textContent = created;
            document.getElementById('modal-user').textContent = user;
            document.getElementById('modal-status').textContent = status;
            document.getElementById('modal-read-time').textContent = readTime;
            
            // İkon ve rengini ayarla
            const iconContainer = document.getElementById('modal-icon-container');
            const icon = document.getElementById('modal-icon');
            
            if (type === 'vip') {
                icon.className = 'fas fa-crown text-warning';
                iconContainer.className = 'notification-icon me-3 p-2 rounded-circle bg-warning bg-opacity-10';
            } else if (type === 'mining') {
                icon.className = 'fas fa-hammer text-info';
                iconContainer.className = 'notification-icon me-3 p-2 rounded-circle bg-info bg-opacity-10';
            } else {
                icon.className = 'fas fa-bell text-primary';
                iconContainer.className = 'notification-icon me-3 p-2 rounded-circle bg-primary bg-opacity-10';
            }
        });
    }
    
    // Bildirim silme modalı
    const deleteNotificationModal = document.getElementById('deleteNotificationModal');
    if (deleteNotificationModal) {
        deleteNotificationModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            document.getElementById('delete-notification-id').value = id;
        });
    }
});
</script>

<?php
/**
 * Admin için bildirimleri getirir.
 *
 * @param string $filter_type Bildirim türü filtresi
 * @param string $filter_date Tarih filtresi
 * @param string $filter_status Durum filtresi
 * @param int $limit Limit
 * @param int $offset Offset
 * @return array Bildirim listesi
 */
function getAdminNotifications($filter_type = '', $filter_date = '', $filter_status = '', $limit = 10, $offset = 0) {
    $conn = $GLOBALS['db']->getConnection();
    
    $query = "SELECT n.notification_id, n.user_id, n.created_at, 
                     nc.title, nc.content, nc.notification_type, nc.language,
                     u.username, u.email,
                     nrs.read_status, nrs.read_at
              FROM notifications n
              JOIN notification_contents nc ON n.notification_id = nc.notification_id
              JOIN users u ON n.user_id = u.id
              LEFT JOIN notification_read_status nrs ON n.notification_id = nrs.notification_id AND nrs.user_id = n.user_id
              WHERE 1=1";
    
    // Filtreler
    $params = [];
    $types = "";
    
    if ($filter_type) {
        $query .= " AND nc.notification_type = ?";
        $params[] = $filter_type;
        $types .= "s";
    }
    
    if ($filter_date) {
        switch ($filter_date) {
            case 'today':
                $query .= " AND DATE(n.created_at) = CURDATE()";
                break;
            case 'yesterday':
                $query .= " AND DATE(n.created_at) = CURDATE() - INTERVAL 1 DAY";
                break;
            case 'last_week':
                $query .= " AND n.created_at >= CURDATE() - INTERVAL 7 DAY";
                break;
            case 'last_month':
                $query .= " AND n.created_at >= CURDATE() - INTERVAL 30 DAY";
                break;
        }
    }
    
    if ($filter_status) {
        if ($filter_status === 'read') {
            $query .= " AND nrs.read_status = 1";
        } else if ($filter_status === 'unread') {
            $query .= " AND (nrs.read_status IS NULL OR nrs.read_status = 0)";
        }
    }
    
    // Sıralama ve limit
    $query .= " ORDER BY n.created_at DESC";
    $query .= " LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    $types .= "ii";
    
    $stmt = $conn->prepare($query);
    
    if ($types) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $notifications = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $notifications[] = $row;
        }
    }
    
    return $notifications;
}

/**
 * Toplam bildirim sayısını getirir.
 *
 * @param string $filter_type Bildirim türü filtresi
 * @param string $filter_date Tarih filtresi
 * @param string $filter_status Durum filtresi
 * @return int Bildirim sayısı
 */
function getTotalNotificationCount($filter_type = '', $filter_date = '', $filter_status = '') {
    $conn = $GLOBALS['db']->getConnection();
    
    $query = "SELECT COUNT(DISTINCT n.notification_id) as total
              FROM notifications n
              JOIN notification_contents nc ON n.notification_id = nc.notification_id
              JOIN users u ON n.user_id = u.id
              LEFT JOIN notification_read_status nrs ON n.notification_id = nrs.notification_id AND nrs.user_id = n.user_id
              WHERE 1=1";
    
    // Filtreler
    $params = [];
    $types = "";
    
    if ($filter_type) {
        $query .= " AND nc.notification_type = ?";
        $params[] = $filter_type;
        $types .= "s";
    }
    
    if ($filter_date) {
        switch ($filter_date) {
            case 'today':
                $query .= " AND DATE(n.created_at) = CURDATE()";
                break;
            case 'yesterday':
                $query .= " AND DATE(n.created_at) = CURDATE() - INTERVAL 1 DAY";
                break;
            case 'last_week':
                $query .= " AND n.created_at >= CURDATE() - INTERVAL 7 DAY";
                break;
            case 'last_month':
                $query .= " AND n.created_at >= CURDATE() - INTERVAL 30 DAY";
                break;
        }
    }
    
    if ($filter_status) {
        if ($filter_status === 'read') {
            $query .= " AND nrs.read_status = 1";
        } else if ($filter_status === 'unread') {
            $query .= " AND (nrs.read_status IS NULL OR nrs.read_status = 0)";
        }
    }
    
    $stmt = $conn->prepare($query);
    
    if ($types) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return (int)$row['total'];
}

/**
 * Bildirimi siler.
 *
 * @param int $notification_id Bildirim ID
 * @return bool Başarılı mı
 */
function deleteNotification($notification_id) {
    $conn = $GLOBALS['db']->getConnection();
    
    // Önce bildirim içeriğini sil
    $query1 = "DELETE FROM notification_contents WHERE notification_id = ?";
    $stmt1 = $conn->prepare($query1);
    $stmt1->bind_param("i", $notification_id);
    $result1 = $stmt1->execute();
    
    // Sonra okuma durumunu sil
    $query2 = "DELETE FROM notification_read_status WHERE notification_id = ?";
    $stmt2 = $conn->prepare($query2);
    $stmt2->bind_param("i", $notification_id);
    $result2 = $stmt2->execute();
    
    // Son olarak bildirimi sil
    $query3 = "DELETE FROM notifications WHERE notification_id = ?";
    $stmt3 = $conn->prepare($query3);
    $stmt3->bind_param("i", $notification_id);
    $result3 = $stmt3->execute();
    
    return $result1 && $result2 && $result3;
}

include '../includes/footer.php';
?>