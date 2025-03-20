</main>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sidebar Toggle
        const sidebar = document.getElementById('sidebar');
        const content = document.getElementById('content');
        const sidebarToggle = document.getElementById('sidebarToggle');
        
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('sidebar-show');
        });
        
        // Ekran genişliğine göre sidebar durumunu ayarla
        function handleResize() {
            if (window.innerWidth < 992) {
                sidebar.classList.remove('sidebar-show');
            } else {
                sidebar.classList.remove('sidebar-collapsed');
            }
        }
        
        window.addEventListener('resize', handleResize);
        handleResizehandleResize();
        
        // Tooltip'leri etkinleştir
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Animasyonlu sayaçlar için
        const counterElements = document.querySelectorAll('.counter-value');
        counterElements.forEach(function(element) {
            const target = parseInt(element.getAttribute('data-target'));
            let count = 0;
            const duration = 2000; // 2 saniye
            const frameDuration = 1000 / 60; // 60 FPS
            const totalFrames = Math.round(duration / frameDuration);
            const increment = target / totalFrames;
            
            const timer = setInterval(() => {
                count += increment;
                if (count >= target) {
                    element.textContent = target.toLocaleString();
                    clearInterval(timer);
                } else {
                    element.textContent = Math.floor(count).toLocaleString();
                }
            }, frameDuration);
        });
    });
    </script>
</body>
</html>