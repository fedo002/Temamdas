<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4 mb-lg-0">
                <div class="footer-about">
                    <div class="footer-logo mb-3">
                        <h3>Kazanç Platformu</h3>
                    </div>
                    <p>
                        Birlikte Kazan, Birlikte Büyü! Sistemimize katılarak hem basit görevler yaparak hem de yatırımlarınızla gelir elde edebilirsiniz.
                    </p>
                    <ul class="social-links">
                        <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                        <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                        <li><a href="#"><i class="fab fa-instagram"></i></a></li>
                        <li><a href="#"><i class="fab fa-telegram-plane"></i></a></li>
                    </ul>
                </div>
            </div>
            
            <div class="col-lg-2 col-md-6 mb-4 mb-lg-0">
                <h5 class="footer-title">Hızlı Linkler</h5>
                <ul class="footer-links">
                    <li><a href="index.php">Ana Sayfa</a></li>
                    <li><a href="about.php">Hakkımızda</a></li>
                    <li><a href="vip-packages.php">VIP Paketleri</a></li>
                    <li><a href="mining.php">Mining Paketleri</a></li>
                </ul>
            </div>
            
            <div class="col-lg-2 col-md-6 mb-4 mb-lg-0">
                <h5 class="footer-title">Kullanıcı</h5>
                <ul class="footer-links">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li><a href="dashboard.php">Panel</a></li>
                        <li><a href="deposit.php">Para Yatırma</a></li>
                        <li><a href="withdraw.php">Para Çekme</a></li>
                        <li><a href="referrals.php">Referanslarım</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Giriş Yap</a></li>
                        <li><a href="register.php">Kayıt Ol</a></li>
                        <li><a href="forgot-password.php">Şifremi Unuttum</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            
            <div class="col-lg-4 col-md-12">
                <h5 class="footer-title">İletişim</h5>
                <ul class="footer-links">
                    <li>
                        <i class="fas fa-envelope me-2"></i>
                        <a href="mailto:<?= $settings['support_email'] ?? 'support@example.com' ?>"><?= $settings['support_email'] ?? 'support@example.com' ?></a>
                    </li>
                    <li>
                        <i class="fas fa-headset me-2"></i>
                        <a href="support.php">Destek Talebi Oluştur</a>
                    </li>
                </ul>
                
                <div class="mt-4">
                    <h5 class="footer-title">Ödeme Yöntemi</h5>
                    <div class="payment-methods">
                        <img src="assets/images/usdt-trc20.png" alt="USDT TRC-20" height="40">USDT TRC-20
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="copyright">
        <div class="container">
            <p class="mb-0">&copy; <?= date('Y') ?> <?= APP_NAME ?>. Tüm hakları saklıdır.</p>
        </div>
    </div>
</footer>

<!-- Back to Top Button -->
<a href="#" class="back-to-top" id="backToTop">
    <i class="fas fa-chevron-up"></i>
</a>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Back to Top Button
    var backToTopBtn = document.getElementById('backToTop');
    
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            backToTopBtn.classList.add('show');
        } else {
            backToTopBtn.classList.remove('show');
        }
    });
    
    backToTopBtn.addEventListener('click', function(e) {
        e.preventDefault();
        window.scrollTo({top: 0, behavior: 'smooth'});
    });
    
    // Tooltip Initialization
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Custom File Input
    var customFileInputs = document.querySelectorAll('.custom-file-input');
    customFileInputs.forEach(function(input) {
        input.addEventListener('change', function(e) {
            var fileName = this.files[0].name;
            var nextSibling = this.nextElementSibling;
            nextSibling.innerText = fileName;
        });
    });
});
</script>
</body>
</html>