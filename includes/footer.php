<footer>
  <div class="main-footer py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4 mb-lg-0">
                <img src="assets/image/logo.png" alt="<?= APP_NAME ?>" height="40" class="mb-4">
                <p class="mb-4" data-i18n="profile.description">Platform bireylerin yenilikçi mining ve VIP çözümleri ile dijital ekonomiye katılmasını sağlayan bir platformdur.</p>
                <div class="social-links">
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" aria-label="Telegram"><i class="fab fa-telegram"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="Discord"><i class="fab fa-discord"></i></a>
                </div>
            </div>
            
            <div class="col-lg-2 col-md-4 mb-4 mb-md-0">
                <h5 class="text-white mb-4" data-i18n="footer.quick_links">Hızlı Linkler</h5>
                <ul class="footer-links">
                    <li><a href="index.php" data-i18n="buttons.home">Ana Sayfa</a></li>
                    <li><a href="packages.php?type=vip" data-i18n="buttons.vip">VIP Paketleri</a></li>
                    <li><a href="packages.php?type=mining" data-i18n="buttons.mining">Mining</a></li>
                    <li><a href="about.php" data-i18n="footer.about">Hakkımızda</a></li>
                    <li><a href="support.php" data-i18n="footer.support">Destek</a></li>
                </ul>
            </div>
            
            <div class="col-lg-2 col-md-4 mb-4 mb-md-0">
                <h5 class="text-white mb-4" data-i18n="footer.legal">Yasal</h5>
                <ul class="footer-links">
                    <li><a href="terms.php" data-i18n="footer.terms">Kullanım Şartları</a></li>
                    <li><a href="privacy.php" data-i18n="footer.privacy">Gizlilik Politikası</a></li>
                    <li><a href="faq.php" data-i18n="footer.faq">Sıkça Sorulan Sorular</a></li>
                </ul>
            </div>
            
            <div class="col-lg-4 col-md-4">
                <h5 class="text-white mb-4" data-i18n="footer.contact">İletişim</h5>
                <ul class="footer-contact">
                    <li><i class="fas fa-envelope"></i> support@example.com</li>
                    <li><i class="fab fa-telegram-plane"></i> @PNZA</li>
                    <li><i class="fas fa-map-marker-alt"></i> London, England</li>
                </ul>
                
                <div class="col-lg-4 col-md-4">
                    <h5 class="text-white mb-4" data-i18n="footer.payments">Para Birimi ve Ödeme Yöntemleri</h5>
                    
                            <div class="col-md-6 text-center text-md-end mt-3 mt-md-0">
                                <div class="payment-methods">
                                    <img src="assets/images/usdt-trc20.png" alt="USDT" height="30"> USDT 
                                </div>
                            </div>
                </div>
            </div>
        </div>
        
        
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <p class="mb-0 text-white-50">&copy; <?= date('Y') ?> <?= APP_NAME ?>. <span data-i18n="footer.copyright">Tüm hakları saklıdır.</span></p>
            </div>
        </div>
    </div>
  </div>
</footer>
    
    <!-- Custom Scripts -->
    <!-- <script src="assets/js/i18n.js"></script> -->
    
    <!-- Mobil menü için özel script -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mobil dropdown toggles
        const mobileDropdowns = document.querySelectorAll('.mobile-dropdown-toggle');
        
        // Mobil dropdown'lar için tıklama olayları
        mobileDropdowns.forEach(dropdown => {
            dropdown.addEventListener('click', function(e) {
                e.stopPropagation(); // Tıklama olayının yayılmasını durdur
                
                // Dropdown'un parent elementini bul
                const parent = this.closest('.mobile-dropdown');
                
                // Toggle aktif sınıfı
                parent.classList.toggle('active');
            });
        });
        
        // Menü dışına tıklama kontrolü
        document.addEventListener('click', function(e) {
            // Açık dropdown menüleri kapat
            const openDropdowns = document.querySelectorAll('.mobile-dropdown.active');
            openDropdowns.forEach(dropdown => {
                if (!dropdown.contains(e.target)) {
                    dropdown.classList.remove('active');
                }
            });
        });
    });
    </script>
    
</body>
</html>