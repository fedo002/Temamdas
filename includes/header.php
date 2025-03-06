<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? APP_NAME ?> | <?= APP_NAME ?></title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico" type="image/x-icon">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/custom.css">
    
    <style>
        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 40px;
            height: 40px;
            background-color: var(--primary-color);
            color: white;
            text-align: center;
            line-height: 40px;
            border-radius: 50%;
            z-index: 99;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        
        .back-to-top.show {
            opacity: 1;
            visibility: visible;
        }
        
        .back-to-top:hover {
            background-color: #655bd3;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="assets/images/logo.png" alt="<?= APP_NAME ?>" height="30" class="me-2">
                <?= APP_NAME ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= $page_title == 'Ana Sayfa' ? 'active' : '' ?>" href="index.php">Ana Sayfa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $page_title == 'Hakkımızda' ? 'active' : '' ?>" href="about.php">Hakkımızda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($page_title == 'VIP Paketleri' || $page_title == 'Mining Paketleri') ? 'active' : '' ?>" href="packages.php">Paketler</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $page_title == 'İletişim' ? 'active' : '' ?>" href="contact.php">İletişim</a>
                    </li>
                    
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-1"></i> Hesabım
                            </a>
                            <ul class="dropdown-menu dropdown-menu-dark">
                                <li><a class="dropdown-item" href="dashboard.php">Panel</a></li>
                                <li><a class="dropdown-item" href="deposit.php">Para Yatırma</a></li>
                                <li><a class="dropdown-item" href="withdraw.php">Para Çekme</a></li>
                                <li><a class="dropdown-item" href="referrals.php">Referanslarım</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="support.php">Destek</a></li>
                                <li><a class="dropdown-item" href="logout.php">Çıkış Yap</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link btn btn-sm btn-primary ms-2 px-3" href="login.php">Giriş Yap</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn btn-sm btn-outline-light ms-2 px-3" href="register.php">Kayıt Ol</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Padding for fixed navbar -->
    <div style="padding-top: 70px;"></div>