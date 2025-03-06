<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Referans kodu kontrolü
if (isset($_GET['ref'])) {
    $ref_code = trim($_GET['ref']);
    if (!empty($ref_code)) {
        // Çerezi 30 gün için ayarla
        setcookie('ref_code', $ref_code, time() + (86400 * 30), '/');
    }
}

// Ödeme ayarlarını al
$settings = getSiteSettings();

$page_title = 'Ana Sayfa';
include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="hero-content">
                    <h1 class="mb-4 display-4">💰 Birlikte Kazan, Birlikte Büyü! 💰</h1>
                    <p class="lead mb-4">
                        Sistemimize katılarak hem basit görevler yaparak hem de yatırımlarınızla gelir elde edebilirsiniz!
                    </p>
                    <div class="hero-cta">
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <a href="dashboard.php" class="btn btn-lg btn-primary me-3">
                                <i class="fas fa-tachometer-alt me-2"></i> Panele Git
                            </a>
                        <?php else: ?>
                            <a href="register.php" class="btn btn-lg btn-primary me-3">
                                <i class="fas fa-user-plus me-2"></i> Hemen Başla
                            </a>
                            <a href="login.php" class="btn btn-lg btn-outline-light">
                                <i class="fas fa-sign-in-alt me-2"></i> Giriş Yap
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 d-none d-lg-block">
                <div class="hero-image text-center">
                    <img src="assets/images/hero-image.png" alt="Kazanç Platformu" class="img-fluid">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features-section py-5">
    <div class="container">
        <div class="section-title text-center mb-5">
            <h2>Platformumuzun Özellikleri</h2>
            <p class="lead">Kazanç sağlamak için birçok farklı yöntem sunuyoruz</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <h3>Görev Yap, Gelir Elde Et!</h3>
                    <p>Basit görevler yaparak anında kazanç sağlamaya başlayabilirsiniz.</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Referans Sistemi ile Daha Fazla Kazanç!</h3>
                    <p>Kullanıcı sayısı arttıkça gelirler de artar, referans sistemimiz sayesinde ek kazanç elde edebilirsiniz.</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Topluluk Gücü ile Yatırım!</h3>
                    <p>Kullanıcılarımızın yatırımları, profesyonel finansörler tarafından yönetilerek en iyi fırsatlar değerlendirilir.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How it Works Section -->
<section class="how-it-works-section py-5 bg-light">
    <div class="container">
        <div class="section-title text-center mb-5">
            <h2>Nasıl Çalışır?</h2>
            <p class="lead">Platformumuzda kazanmaya başlamak çok kolay</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-3">
                <div class="step-card">
                    <div class="step-number">1</div>
                    <h4>Ücretsiz Üye Ol</h4>
                    <p>Hızlı ve ücretsiz bir şekilde platformumuza kayıt olun.</p>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="step-card">
                    <div class="step-number">2</div>
                    <h4>USDT Yatır</h4>
                    <p>TRC-20 ağını kullanarak USDT yatırın ve hesabınızı aktifleştirin.</p>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="step-card">
                    <div class="step-number">3</div>
                    <h4>Görevleri Tamamla</h4>
                    <p>Günlük görevleri tamamlayarak ekstra kazanç elde edin.</p>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="step-card">
                    <div class="step-number">4</div>
                    <h4>Mining Paketleri Al</h4>
                    <p>Mining paketleri alarak pasif gelir elde edin.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- VIP Packages Section -->
<section class="vip-packages-section py-5">
    <div class="container">
        <div class="section-title text-center mb-5">
            <h2>VIP Paketleri</h2>
            <p class="lead">VIP üyelik ile daha fazla avantaj elde edin</p>
        </div>
        
        <div class="row g-4">
            <?php
            // VIP paketlerini getir
            $vip_packages = getVIPPackages();
            foreach($vip_packages as $package):
            ?>
            <div class="col-md-3">
                <div class="pricing-card <?= $package['id'] == 3 ? 'popular' : '' ?>">
                    <?php if($package['id'] == 3): ?>
                    <div class="popular-badge">En Popüler</div>
                    <?php endif; ?>
                    <h3><?= $package['name'] ?></h3>
                    <div class="price">
                        <?= $package['price'] == 0 ? 'Ücretsiz' : number_format($package['price'], 2) . ' USDT' ?>
                    </div>
                    <ul class="features-list">
                        <li>Günlük <?= $package['daily_game_limit'] ?> oyun hakkı</li>
                        <li><?= ($package['game_max_win_chance'] * 100) ?>% kazanma şansı</li>
                        <li><?= ($package['referral_rate'] * 100) ?>% referans komisyonu</li>
                        <?php if($package['mining_bonus_rate'] > 0): ?>
                        <li><?= ($package['mining_bonus_rate'] * 100) ?>% mining bonus</li>
                        <?php else: ?>
                        <li class="disabled">Mining bonus yok</li>
                        <?php endif; ?>
                    </ul>
                    <a href="<?= isset($_SESSION['user_id']) ? 'vip-packages.php' : 'register.php' ?>" class="btn btn-primary">
                        <?= $package['price'] == 0 ? 'Başla' : 'Satın Al' ?>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="testimonials-section py-5 bg-light">
    <div class="container">
        <div class="section-title text-center mb-5">
            <h2>Kullanıcı Yorumları</h2>
            <p class="lead">Platformumuzdan memnun kalan kullanıcılarımız ne diyor?</p>
        </div>
        
        <div class="row">
            <div class="col-12">
                <div class="testimonials-slider">
                    <div class="testimonial-item">
                        <div class="testimonial-content">
                            <p>"Bu platform sayesinde aylık pasif gelirim %30 arttı. Mining paketleri gerçekten çok verimli çalışıyor!"</p>
                            <div class="user-info">
                                <h5>Ahmet Y.</h5>
                                <span>Gold VIP Üye</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="testimonial-item">
                        <div class="testimonial-content">
                            <p>"Referans sistemi ile 10 arkadaşımı davet ettim ve şimdiye kadar 500 USDT komisyon kazandım. Harika bir sistem!"</p>
                            <div class="user-info">
                                <h5>Mehmet K.</h5>
                                <span>Platinum VIP Üye</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="testimonial-item">
                        <div class="testimonial-content">
                            <p>"Günlük görevleri yapmak çok kolay ve eğlenceli. Her gün düzenli olarak USDT kazanıyorum."</p>
                            <div class="user-info">
                                <h5>Ayşe M.</h5>
                                <span>Silver VIP Üye</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="faq-section py-5">
    <div class="container">
        <div class="section-title text-center mb-5">
            <h2>Sıkça Sorulan Sorular</h2>
            <p class="lead">Platform hakkında merak edilenler</p>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                Platformda nasıl para kazanabilirim?
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Platformumuzda günlük görevleri tamamlayarak, mining paketleri satın alarak, referans sistemi ile yeni kullanıcılar getirerek para kazanabilirsiniz. Her bir kazanç yöntemi farklı avantajlar sunar.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                Minimum yatırım tutarı nedir?
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Minimum yatırım tutarı 10 USDT'dir. Bu tutarı TRC-20 ağı üzerinden yatırabilirsiniz. Platformumuzda işlem ücretleri yoktur.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                Mining paketleri nasıl çalışır?
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Mining paketleri, bakiyenizden satın alabileceğiniz pasif gelir ürünleridir. Satın aldığınız paketin hash gücü ve özelliklerine göre günlük kazanç elde edersiniz. Bu kazançlar otomatik olarak hesabınıza eklenir.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingFour">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                Referans sistemi nasıl çalışır?
                            </button>
                        </h2>
                        <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Her kullanıcıya özel bir referans kodu verilir. Bu kodu paylaşarak arkadaşlarınızı platforma davet edebilirsiniz. Davet ettiğiniz kişiler yatırım yaptığında, yatırım tutarının belirli bir yüzdesini komisyon olarak kazanırsınız. VIP seviyeniz yükseldikçe, referans komisyon oranınız da artar.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingFive">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                Para çekme işlemleri ne kadar sürer?
                            </button>
                        </h2>
                        <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Para çekme işlemleri genellikle 24 saat içinde tamamlanır. Minimum para çekme tutarı 20 USDT'dir ve çekim işlemlerinde %2 işlem ücreti alınır. Tüm ödemeler TRC-20 USDT olarak yapılır.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h2 class="mb-4">🚀 Siz de bu kazançlı ekosisteme katılın ve birlikte büyüyelim!</h2>
                <p class="lead mb-4">Hemen üye olun ve kazanmaya başlayın!</p>
                <?php if(!isset($_SESSION['user_id'])): ?>
                <div class="cta-buttons">
                    <a href="register.php" class="btn btn-lg btn-primary me-3">
                        <i class="fas fa-user-plus me-2"></i> Ücretsiz Kaydol
                    </a>
                    <a href="login.php" class="btn btn-lg btn-outline-light">
                        <i class="fas fa-sign-in-alt me-2"></i> Giriş Yap
                    </a>
                </div>
                <?php else: ?>
                <div class="cta-buttons">
                    <a href="dashboard.php" class="btn btn-lg btn-primary me-3">
                        <i class="fas fa-tachometer-alt me-2"></i> Panele Git
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>