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
    <link rel="stylesheet" href="assets/css/translation-system.css">
    <script src="assets/js/translation-system.js"></script>
</head>
<body>
<!-- header.php dosyasına çeviri düğmesi ekleyelim -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <img src="assets/images/logo.png" alt="<?= APP_NAME ?>" height="30" class="me-2">
            <?= APP_NAME ?>
        </a>
        
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <!-- Mevcut menü öğeleri -->
                <!-- ... -->
                <li class="nav-item">
                        <a class="nav-link <?= $page_title == 'Ana Sayfa' ? 'active' : '' ?>" href="index.php">Ana Sayfa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $page_title == 'Hakkımızda' ? 'active' : '' ?>" href="about.php">Hakkımızda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($page_title == 'VIP Paketleri' || $page_title == 'Mining Paketleri') ? 'active' : '' ?>" href="packages.php">Paketler</a>
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
                        <li class="nav-item language-nav-item">
                            <div class="language-selector">
                                <button class="lang-toggle">
                                    <span class="current-lang">TR</span>
                                    <svg width="10" height="6" viewBox="0 0 10 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M1 1L5 5L9 1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                                <div class="lang-dropdown">
                                    <button class="lang-option" data-lang="tr">TR</button>
                                    <button class="lang-option" data-lang="en">EN</button>
                                    <button class="lang-option" data-lang="de">DE</button>
                                    <button class="lang-option" data-lang="fr">FR</button>
                                </div>
                            </div>
                        </li>
            </ul>
        </div>
    </div>
</nav>



    <!-- Padding for fixed navbar -->
    <div style="padding-top: 70px;"></div>