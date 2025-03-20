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
    header('Location: list.php?error=user_not_found');
    exit;
}

// Sayfa parametreleri
$transaction_page = isset($_GET['tx_page']) ? max(1, intval($_GET['tx_page'])) : 1;
$tx_limit = 10;
$tx_offset = ($transaction_page - 1) * $tx_limit;

// Kullanıcı işlemlerini al
$transactions = getUserTransactions($user_id, $tx_limit, $tx_offset);
$total_transactions = getUserTransactionsCount($user_id);
$tx_pages = ceil($total_transactions / $tx_limit);

// Kullanıcının referanslarını al
$referrals_page = isset($_GET['ref_page']) ? max(1, intval($_GET['ref_page'])) : 1;
$ref_limit = 10;
$ref_offset = ($referrals_page - 1) * $ref_limit;

$referrals = getUserReferrals($user_id, $ref_limit, $ref_offset);
$total_referrals = getUserReferralsCount($user_id);
$ref_pages = ceil($total_referrals / $ref_limit);

// Kullanıcının referans kazançlarını al
$earnings = getUserReferralEarnings($user_id, 5, 0);

// Kullanıcının mining paketlerini al
$mining_packages = getUserMiningPackages($user_id);

// Kullanıcının VIP seviyesini al
$vip_package = getUserVipPackage($user_id);

// Mesaj işleme
if(isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// Kullanıcı işlemleri
if(isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if($action == 'update_status') {
        $new_status = $_POST['status'];
        $result = updateUserStatus($user_id, $new_status);
        
        if($result === true) {
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Kullanıcı durumu başarıyla güncellendi.'];
        } else {
            $_SESSION['message'] = ['type' => 'danger', 'text' => 'Hata: ' . $result];
        }
        
        header('Location: details.php?id=' . $user_id);
        exit;
    }
    
    if($action == 'update_balance') {
        $amount = (float)$_POST['amount'];
        $type = $_POST['type'];
        $note = $_POST['note'];
        
        $result = updateUserBalance($user_id, $amount, $type, $note);
        
        if($result === true) {
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Kullanıcı bakiyesi başarıyla güncellendi.'];
        } else {
            $_SESSION['message'] = ['type' => 'danger', 'text' => 'Hata: ' . $result];
        }
        
        header('Location: details.php?id=' . $user_id);
        exit;
    }
}

// Sayfa başlığı
$page_title = 'Kullanıcı Detayları: ' . htmlspecialchars($user['username']);
include '../includes/header.php';
?>

<div class="container-fluid px-4 py-3">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h1 class="h3">Kullanıcı Detayları: <?= htmlspecialchars($user['username']) ?></h1>
            <div>
                <a href="list.php" class="btn btn-secondary me-2">
                    <i class="fas fa-arrow-left me-2"></i> Kullanıcı Listesi
                </a>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#updateBalanceModal">
                    <i class="fas fa-coins me-2"></i> Bakiye İşlemi
                </button>
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
        <!-- Kullanıcı Bilgileri -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Temel Bilgiler</h5>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="userActionsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-cog"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userActionsDropdown">
                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#updateStatusModal">Durum Değiştir</a></li>
                            <li><a class="dropdown-item" href="edit.php?id=<?= $user_id ?>">Düzenle</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="login-as.php?id=<?= $user_id ?>" target="_blank">Kullanıcı Olarak Giriş Yap</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="avatar-circle mx-auto mb-3">
                            <span class="initials"><?= strtoupper(substr($user['username'], 0, 2)) ?></span>
                        </div>
                        <h4 class="mb-0"><?= htmlspecialchars($user['username']) ?></h4>
                        <p class="text-muted"><?= $user['email'] ?></p>
                        <div class="badge bg-<?= $user['status'] == 'active' ? 'success' : ($user['status'] == 'pending' ? 'warning' : 'danger') ?> mb-3">
                            <?= ucfirst($user['status']) ?>
                        </div>
                    </div>
                    
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Kullanıcı ID:</dt>
                        <dd class="col-sm-7"><?= $user['id'] ?></dd>
                        
                        <?php if($user['full_name']): ?>
                        <dt class="col-sm-5">Ad Soyad:</dt>
                        <dd class="col-sm-7"><?= htmlspecialchars($user['full_name']) ?></dd>
                        <?php endif; ?>
                        
                        <dt class="col-sm-5">Referans Kodu:</dt>
                        <dd class="col-sm-7"><?= $user['referral_code'] ?></dd>
                        
                        <?php if($user['referrer_id']): ?>
                        <dt class="col-sm-5">Referans Eden:</dt>
                        <dd class="col-sm-7">
                            <a href="details.php?id=<?= $user['referrer_id'] ?>"><?= getUsernameById($user['referrer_id']) ?></a>
                        </dd>
                        <?php endif; ?>
                        
                        <dt class="col-sm-5">Kayıt Tarihi:</dt>
                        <dd class="col-sm-7"><?= date('d.m.Y H:i', strtotime($user['created_at'])) ?></dd>
                        
                        <dt class="col-sm-5">Son Giriş:</dt>
                        <dd class="col-sm-7"><?= $user['last_login'] ? date('d.m.Y H:i', strtotime($user['last_login'])) : 'Henüz Giriş Yapılmadı' ?></dd>
                    </dl>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Bakiye Bilgileri</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Ana Bakiye:</span>
                        <h5 class="mb-0"><?= number_format($user['balance'], 2) ?> USDT</h5>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Referans Bakiyesi:</span>
                        <h5 class="mb-0"><?= number_format($user['referral_balance'], 2) ?> USDT</h5>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Toplam Yatırım:</span>
                        <h5 class="mb-0"><?= number_format($user['total_deposit'], 2) ?> USDT</h5>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Toplam Çekim:</span>
                        <h5 class="mb-0"><?= number_format($user['total_withdraw'], 2) ?> USDT</h5>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Toplam Kazanç:</span>
                        <h5 class="mb-0"><?= number_format($user['total_earnings'], 2) ?> USDT</h5>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">VIP & Mining</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">VIP Seviyesi:</span>
                        <h5 class="mb-0">
                            <?php if($vip_package): ?>
                                <?= htmlspecialchars($vip_package['name']) ?>
                            <?php else: ?>
                                Standart
                            <?php endif; ?>
                        </h5>
                    </div>
                    
                    <div class="mb-4">
                        <h6 class="mb-2">Mining Paketleri:</h6>
                        <?php if(empty($mining_packages)): ?>
                            <p class="text-muted">Aktif mining paketi bulunmuyor.</p>
                        <?php else: ?>
                            <?php foreach($mining_packages as $package): ?>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span><?= htmlspecialchars($package['name']) ?></span>
                                    <span class="badge bg-<?= $package['status'] == 'active' ? 'success' : 'warning' ?>">
                                        <?= $package['status'] == 'active' ? 'Aktif' : 'Pasif' ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    
                    <?php if($user['trc20_address']): ?>
                    <div class="mb-3">
                        <h6 class="mb-2">TRC20 Adresi:</h6>
                        <div class="input-group">
                            <input type="text" class="form-control form-control-sm" value="<?= $user['trc20_address'] ?>" readonly>
                            <button class="btn btn-sm btn-outline-secondary" type="button" onclick="copyToClipboard('<?= $user['trc20_address'] ?>')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- İşlem Geçmişi -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">İşlem Geçmişi</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tür</th>
                                    <th>Miktar</th>
                                    <th>Bakiye Değişimi</th>
                                    <th>Durum</th>
                                    <th>Tarih</th>
                                    <th>İşlem</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($transactions)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4">İşlem kaydı bulunamadı.</td>
                                </tr>
                                <?php else: ?>
                                    <?php foreach($transactions as $tx): ?>
                                    <tr>
                                        <td><?= $tx['id'] ?></td>
                                        <td>
                                            <?php if($tx['type'] == 'deposit'): ?>
                                                <span class="badge bg-success">Deposit</span>
                                            <?php elseif($tx['type'] == 'withdraw'): ?>
                                                <span class="badge bg-warning">Withdraw</span>
                                            <?php elseif($tx['type'] == 'game'): ?>
                                                <span class="badge bg-info">Oyun</span>
                                            <?php elseif($tx['type'] == 'referral'): ?>
                                                <span class="badge bg-primary">Referans</span>
                                            <?php elseif($tx['type'] == 'mining'): ?>
                                                <span class="badge bg-secondary">Mining</span>
                                            <?php elseif($tx['type'] == 'vip'): ?>
                                                <span class="badge bg-dark">VIP</span>
                                            <?php else: ?>
                                                <span class="badge bg-light text-dark"><?= ucfirst($tx['type']) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= number_format($tx['amount'], 2) ?> USDT</td>
                                        <td>
                                            <?php if($tx['before_balance'] !== null && $tx['after_balance'] !== null): ?>
                                                <?= number_format($tx['before_balance'], 2) ?> → <?= number_format($tx['after_balance'], 2) ?>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($tx['status'] == 'completed'): ?>
                                                <span class="badge bg-success">Tamamlandı</span>
                                            <?php elseif($tx['status'] == 'pending'): ?>
                                                <span class="badge bg-warning">Beklemede</span>
                                            <?php elseif($tx['status'] == 'failed'): ?>
                                                <span class="badge bg-danger">Başarısız</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary"><?= ucfirst($tx['status']) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= date('d.m.Y H:i', strtotime($tx['created_at'])) ?></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#transactionDetailsModal<?= $tx['id'] ?>">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            
                                            <!-- Transaction Details Modal -->
                                            <div class="modal fade" id="transactionDetailsModal<?= $tx['id'] ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">İşlem Detayı #<?= $tx['id'] ?></h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <dl class="row mb-0">
                                                                <dt class="col-sm-4">İşlem ID:</dt>
                                                                <dd class="col-sm-8"><?= $tx['id'] ?></dd>
                                                                
                                                                <dt class="col-sm-4">Tür:</dt>
                                                                <dd class="col-sm-8"><?= ucfirst($tx['type']) ?></dd>
                                                                
                                                                <dt class="col-sm-4">Miktar:</dt>
                                                                <dd class="col-sm-8"><?= number_format($tx['amount'], 2) ?> USDT</dd>
                                                                
                                                                <dt class="col-sm-4">Durum:</dt>
                                                                <dd class="col-sm-8"><?= ucfirst($tx['status']) ?></dd>
                                                                
                                                                <dt class="col-sm-4">Tarih:</dt>
                                                                <dd class="col-sm-8"><?= date('d.m.Y H:i:s', strtotime($tx['created_at'])) ?></dd>
                                                                
                                                                <?php if($tx['related_id']): ?>
                                                                <dt class="col-sm-4">İlişkili ID:</dt>
                                                                <dd class="col-sm-8"><?= $tx['related_id'] ?></dd>
                                                                <?php endif; ?>
                                                                
                                                                <?php if($tx['description']): ?>
                                                                <dt class="col-sm-4">Açıklama:</dt>
                                                                <dd class="col-sm-8"><?= $tx['description'] ?></dd>
                                                                <?php endif; ?>
                                                                
                                                                <?php if($tx['before_balance'] !== null && $tx['after_balance'] !== null): ?>
                                                                <dt class="col-sm-4">Önceki Bakiye:</dt>
                                                                <dd class="col-sm-8"><?= number_format($tx['before_balance'], 2) ?> USDT</dd>
                                                                
                                                                <dt class="col-sm-4">Sonraki Bakiye:</dt>
                                                                <dd class="col-sm-8"><?= number_format($tx['after_balance'], 2) ?> USDT</dd>
                                                                <?php endif; ?>
                                                            </dl>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <?php if($tx_pages > 1): ?>
                    <div class="d-flex justify-content-center mt-4">
                        <nav aria-label="Page navigation">
                            <ul class="pagination">
                                <?php if($transaction_page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?id=<?= $user_id ?>&tx_page=<?= $transaction_page - 1 ?>" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                                <?php endif; ?>
                                
                                <?php for($i = 1; $i <= $tx_pages; $i++): ?>
                                <li class="page-item <?= $i == $transaction_page ? 'active' : '' ?>">
                                    <a class="page-link" href="?id=<?= $user_id ?>&tx_page=<?= $i ?>"><?= $i ?></a>
                                </li>
                                <?php endfor; ?>
                                
                                <?php if($transaction_page < $tx_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?id=<?= $user_id ?>&tx_page=<?= $transaction_page + 1 ?>" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Referans Ağacı</h5>
                        </div>
                        <div class="card-body">
                            <?php if(empty($referrals)): ?>
                                <p class="text-center py-4 mb-0">Bu kullanıcının referansı bulunmuyor.</p>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Kullanıcı</th>
                                                <th>Kayıt Tarihi</th>
                                                <th>Durum</th>
                                                <th>Toplam Yatırım</th>
                                                <th>Referans Sayısı</th>
                                                <th>İşlem</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($referrals as $ref): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($ref['username']) ?></td>
                                                <td><?= date('d.m.Y', strtotime($ref['created_at'])) ?></td>
                                                <td>
                                                    <span class="badge bg-<?= $ref['status'] == 'active' ? 'success' : ($ref['status'] == 'pending' ? 'warning' : 'danger') ?>">
                                                        <?= ucfirst($ref['status']) ?>
                                                    </span>
                                                </td>
                                                <td><?= number_format($ref['total_deposits'], 2) ?> USDT</td>
                                                <td><?= $ref['referral_count'] ?></td>
                                                <td>
                                                    <a href="details.php?id=<?= $ref['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                
                                <?php if($ref_pages > 1): ?>
                                <div class="d-flex justify-content-center mt-4">
                                    <nav aria-label="Page navigation">
                                        <ul class="pagination">
                                            <?php if($referrals_page > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?id=<?= $user_id ?>&ref_page=<?= $referrals_page - 1 ?>" aria-label="Previous">
                                                    <span aria-hidden="true">&laquo;</span>
                                                </a>
                                            </li>
                                            <?php endif; ?>
                                            
                                            <?php for($i = 1; $i <= $ref_pages; $i++): ?>
                                            <li class="page-item <?= $i == $referrals_page ? 'active' : '' ?>">
                                                <a class="page-link" href="?id=<?= $user_id ?>&ref_page=<?= $i ?>"><?= $i ?></a>
                                            </li>
                                            <?php endfor; ?>
                                            
                                            <?php if($referrals_page < $ref_pages): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?id=<?= $user_id ?>&ref_page=<?= $referrals_page + 1 ?>" aria-label="Next">
                                                    <span aria-hidden="true">&raquo;</span>
                                                </a>
                                            </li>
                                            <?php endif; ?>
                                        </ul>
                                    </nav>
                                </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Referans Kazançları</h5>
                        </div>
                        <div class="card-body">
                            <?php if(empty($earnings)): ?>
                                <p class="text-center py-4 mb-0">Bu kullanıcının referans kazancı bulunmuyor.</p>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Davet Edilen</th>
                                                <th>Miktar</th>
                                                <th>Durum</th>
                                                <th>Tarih</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($earnings as $earning): ?>
                                            <tr>
                                                <td><?= $earning['id'] ?></td>
                                                <td>
                                                    <a href="details.php?id=<?= $earning['referred_user_id'] ?>">
                                                        <?= getUsernameById($earning['referred_user_id']) ?>
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
                                                <td><?= date('d.m.Y', strtotime($earning['created_at'])) ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-end mt-3">
                                    <a href="../referral-system/earnings.php?user_id=<?= $user_id ?>" class="btn btn-sm btn-primary">Tüm Referans Kazançları</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kullanıcı Durumunu Güncelle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="update_status">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Kullanıcı Durumu:</label>
                        <select class="form-select" name="status">
                            <option value="active" <?= $user['status'] == 'active' ? 'selected' : '' ?>>Aktif</option>
                            <option value="pending" <?= $user['status'] == 'pending' ? 'selected' : '' ?>>Beklemede</option>
                            <option value="blocked" <?= $user['status'] == 'blocked' ? 'selected' : '' ?>>Bloklanmış</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Kaydet</button>
                </div>