<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Admin Panel' ?> | Kazanç Platformu</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    
        <!-- Ana CSS Dosyası -->
    <link rel="stylesheet" href="../../assets/css/admin-style.css">
    
    <!-- Ana JS Dosyası --> 
    <script src="../../assets/js/admin.js"></script>
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="/admin" class="sidebar-brand">
                    <i class="fas fa-chart-line me-2"></i>
                    Admin Panel
                </a>
            </div>
            
            <div class="sidebar-nav">
                <div class="sidebar-item">
                    <a href="/admin" class="sidebar-link <?= $page_title == 'Admin Panel' ? 'active' : '' ?>">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </div>
                
                <!-- Kullanıcılar -->
                <div class="sidebar-heading">Kullanıcılar</div>
                <div class="sidebar-item">
                    <a href="/admin/users/list.php" class="sidebar-link <?= strpos($page_title, 'Kullanıcılar') !== false ? 'active' : '' ?>">
                        <i class="fas fa-users"></i> Kullanıcılar
                    </a>
                </div>
                <div class="sidebar-item">
                    <a href="/admin/users/vip-users.php" class="sidebar-link <?= $page_title == 'VIP Kullanıcılar' ? 'active' : '' ?>">
                        <i class="fas fa-crown"></i> VIP Kullanıcılar
                    </a>
                </div>
                
                <!-- Paketler ve Modüller -->
                <div class="sidebar-heading">Paketler ve Modüller</div>
                <div class="sidebar-item">
                    <a href="/admin/vip-packages/list.php" class="sidebar-link <?= $page_title == 'VIP Paketleri' ? 'active' : '' ?>">
                        <i class="fas fa-shopping-cart"></i> VIP Paketleri
                    </a>
                </div>
                <div class="sidebar-item">
                    <a href="/admin/mining-packages/list.php" class="sidebar-link <?= $page_title == 'Mining Paketleri' ? 'active' : '' ?>">
                        <i class="fas fa-microchip"></i> Mining Paketleri
                    </a>
                </div>
                <div class="sidebar-item">
                    <a href="/admin/game-settings/settings.php" class="sidebar-link <?= $page_title == 'Oyun Ayarları' ? 'active' : '' ?>">
                        <i class="fas fa-gamepad"></i> Oyun Ayarları
                    </a>
                </div>
                
                <!-- Finansal İşlemler -->
                <div class="sidebar-heading">Finansal</div>
                <div class="sidebar-item">
                    <a href="/admin/reports/deposits.php" class="sidebar-link <?= $page_title == 'Depositler' ? 'active' : '' ?>">
                        <i class="fas fa-wallet"></i> Depositler
                    </a>
                </div>
                <div class="sidebar-item">
                    <a href="/admin/reports/withdrawals.php" class="sidebar-link <?= $page_title == 'Çekimler' ? 'active' : '' ?>">
                        <i class="fas fa-hand-holding-usd"></i> Çekimler
                    </a>
                </div>
                <div class="sidebar-item">
                    <a href="/admin/referral-system/settings.php" class="sidebar-link <?= $page_title == 'Referans Sistemi' ? 'active' : '' ?>">
                        <i class="fas fa-users-cog"></i> Referans Sistemi
                    </a>
                </div>
                
                
                <!-- Destek ve Ayarlar -->
                <div class="sidebar-heading">Destek ve Ayarlar</div>
                <div class="sidebar-item">
                    <a href="/admin/support/tickets.php" class="sidebar-link <?= $page_title == 'Destek Talepleri' ? 'active' : '' ?>">
                        <i class="fas fa-headset"></i> Destek Talepleri
                    </a>
                </div>
                <div class="sidebar-item">
                    <a href="/admin/settings/general.php" class="sidebar-link <?= $page_title == 'Genel Ayarlar' ? 'active' : '' ?>">
                        <i class="fas fa-cog"></i> Genel Ayarlar
                    </a>
                </div>
                <div class="sidebar-item">
                    <a href="/admin/notifications/notifications.php" class="sidebar-link <?= $page_title == 'Genel Ayarlar' ? 'active' : '' ?>">
                    <i class="fas fa-bell me-1 nav-icon"></i> Bildirim Gönder
                    </a>
                </div>
            </div>
        </nav>
        
        <!-- İçerik Alanı -->
        <div class="content" id="content">
            <!-- Navbar -->
            <nav class="navbar navbar-admin">
                <div class="container-fluid">
                    <button class="navbar-toggler" type="button" id="sidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    
                    <div class="d-flex align-items-center">
                        <div class="dropdown">
                            <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-2 fa-lg"></i>
                                <span>Admin</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end">
                                <li><a class="dropdown-item" href="/admin/settings/profile.php">Profil</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="/admin/logout.php">Çıkış</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>
            
            <!-- İçerik -->
            <main>