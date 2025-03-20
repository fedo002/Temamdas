/**
 * Vanilla Dropdown - Bootstrap dropdown'lar çalışmadığında kullanılabilecek saf JavaScript alternatifi
 * 
 * Bu dosya, bootstrap.js'in düzgün çalışmadığı durumlarda dropdown'ları manuel
 * olarak çalıştırmak için kullanılabilir.
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Vanilla Dropdown başlatılıyor...');
    
    // Önce Bootstrap'in yüklenip yüklenmediğini kontrol et
    if (typeof bootstrap !== 'undefined' && typeof bootstrap.Dropdown !== 'undefined') {
        console.log('Bootstrap yüklü, native dropdown kullanılıyor.');
        
        try {
            // Tüm dropdown'ları başlat
            var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
            dropdownElementList.map(function (dropdownToggleEl) {
                new bootstrap.Dropdown(dropdownToggleEl);
                console.log('Dropdown başlatıldı:', dropdownToggleEl.textContent.trim());
            });
        } catch (err) {
            console.error('Bootstrap dropdown başlatma hatası:', err);
            initVanillaDropdowns();
        }
    } else {
        console.warn('Bootstrap yüklü değil, vanilla dropdown kullanılıyor.');
        initVanillaDropdowns();
    }
    
    // Bootstrap yoksa veya çalışmıyorsa manuel dropdown'ları başlat
    function initVanillaDropdowns() {
        // Dropdown CSS ekle
        addDropdownCSS();
        
        // Tüm dropdown toggle butonlarını seç
        const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
        
        // Her dropdown toggle için event listener ekle
        dropdownToggles.forEach(toggle => {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Mevcut açık dropdown'ları kapat
                document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                    if (menu !== this.nextElementSibling) {
                        menu.classList.remove('show');
                    }
                });
                
                // Toggle dropdown menu
                const menu = this.nextElementSibling;
                menu.classList.toggle('show');
                
                // Popper.js benzeri doğru konumlandırma
                if (menu.classList.contains('show')) {
                    positionDropdown(toggle, menu);
                }
            });
        });
        
        // Sayfa tıklamasında açık dropdown'ları kapat
        document.addEventListener('click', function(e) {
            const openMenus = document.querySelectorAll('.dropdown-menu.show');
            openMenus.forEach(menu => {
                const dropdown = menu.closest('.dropdown');
                if (dropdown && !dropdown.contains(e.target)) {
                    menu.classList.remove('show');
                }
            });
        });
        
        // Navbar dropdown'ları için özel işlem
        const navbarDropdowns = document.querySelectorAll('.navbar .nav-item.dropdown');
        navbarDropdowns.forEach(dropdown => {
            const toggle = dropdown.querySelector('.dropdown-toggle');
            const menu = dropdown.querySelector('.dropdown-menu');
            
            if (toggle && menu) {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Diğer açık navbar dropdown'larını kapat
                    document.querySelectorAll('.navbar .dropdown-menu.show').forEach(m => {
                        if (m !== menu) {
                            m.classList.remove('show');
                        }
                    });
                    
                    // Toggle dropdown
                    menu.classList.toggle('show');
                    
                    // Navbar dropdown için özel konum
                    if (menu.classList.contains('show')) {
                        const rect = toggle.getBoundingClientRect();
                        menu.style.position = 'absolute';
                        menu.style.top = rect.bottom + 'px';
                        menu.style.left = rect.left + 'px';
                    }
                });
            }
        });
        
        console.log('Vanilla dropdown başlatıldı.');
    }
    
    // Dropdown menüyü doğru konumlandır
    function positionDropdown(toggle, menu) {
        const rect = toggle.getBoundingClientRect();
        const isDropup = toggle.closest('.dropup') !== null;
        const isDropend = toggle.closest('.dropend') !== null;
        const isDropstart = toggle.closest('.dropstart') !== null;
        
        menu.style.position = 'absolute';
        menu.style.zIndex = '1000';
        
        if (isDropup) {
            menu.style.top = (rect.top - menu.offsetHeight) + 'px';
            menu.style.left = rect.left + 'px';
        } else if (isDropend) {
            menu.style.top = rect.top + 'px';
            menu.style.left = rect.right + 'px';
        } else if (isDropstart) {
            menu.style.top = rect.top + 'px';
            menu.style.left = (rect.left - menu.offsetWidth) + 'px';
        } else {
            // Normal dropdown
            menu.style.top = rect.bottom + 'px';
            menu.style.left = rect.left + 'px';
        }
        
        // Check if dropdown is going off the screen
        const menuRect = menu.getBoundingClientRect();
        const viewportWidth = window.innerWidth;
        const viewportHeight = window.innerHeight;
        
        if (menuRect.right > viewportWidth) {
            menu.style.left = (viewportWidth - menuRect.width - 5) + 'px';
        }
        
        if (menuRect.bottom > viewportHeight) {
            menu.style.top = (rect.top - menuRect.height) + 'px';
        }
    }
    
    // Dropdown için gerekli CSS ekleme
    function addDropdownCSS() {
        if (!document.getElementById('vanilla-dropdown-styles')) {
            const style = document.createElement('style');
            style.id = 'vanilla-dropdown-styles';
            style.textContent = `
                .dropdown-menu {
                    display: none;
                }
                .dropdown-menu.show {
                    display: block;
                }
                .dropdown-toggle::after {
                    display: inline-block;
                    margin-left: 0.255em;
                    vertical-align: 0.255em;
                    content: "";
                    border-top: 0.3em solid;
                    border-right: 0.3em solid transparent;
                    border-bottom: 0;
                    border-left: 0.3em solid transparent;
                }
            `;
            document.head.appendChild(style);
        }
    }
});