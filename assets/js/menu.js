/**
 * menu.js - Site menüleri için JavaScript kodları
 * Masaüstü ve mobil menü davranışlarını yönetir
 */

document.addEventListener('DOMContentLoaded', function() {
    // Masaüstü menü davranışları
    initDesktopMenu();
    
    // Mobil menü davranışları
    initMobileMenu();
    
    // Sayfa yükleme ve kaydırma olaylarını dinle
    handleScrollEvents();
});

/**
 * Masaüstü menü davranışlarını başlatır
 */
function initDesktopMenu() {
    // Bildirim öğelerine tıklandığında sayfaya yönlendir
    const notificationItems = document.querySelectorAll('.notification-item');
    notificationItems.forEach(item => {
        item.addEventListener('click', function(e) {
            window.location.href = "notifications.php";
        });
    });
    
    // "Tümünü Okundu İşaretle" bağlantısı
    const markAllReadLink = document.querySelector('.mark-all-read');
    if (markAllReadLink) {
        markAllReadLink.addEventListener('click', function(e) {
            e.preventDefault();
            
            // AJAX ile tüm bildirimleri okundu olarak işaretle
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'notifications.php?mark_all=1', true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    // Bildirim sayacını sıfırla ve başarı mesajı göster
                    const notificationBadges = document.querySelectorAll('.notification-badge');
                    notificationBadges.forEach(badge => {
                        badge.style.display = 'none';
                    });
                    
                    // Bildirim panellerini güncelle
                    const notificationPanels = document.querySelectorAll('.notifications-body, .notification-body');
                    notificationPanels.forEach(panel => {
                        panel.innerHTML = `
                            <div class="dropdown-item no-notifications">
                                <i class="fas fa-bell-slash"></i>
                                <p data-i18n="notifications.noNotifications">Bildiriminiz bulunmuyor</p>
                            </div>
                        `;
                    });
                    
                    // Kullanıcıya geri bildirim
                    showToast('Bildirimler', 'Tüm bildirimler okundu olarak işaretlendi', 'success');
                }
            };
            xhr.send();
        });
    }
}

/**
 * Mobil menü davranışlarını başlatır
 */
function initMobileMenu() {
    // DOM elemanlarını sınıf ile seç
    const notificationToggles = document.querySelectorAll('.js-notification-toggle');
    const notificationPanel = document.getElementById('notificationPanel');
    const accountToggles = document.querySelectorAll('.js-account-toggle');
    const accountPanel = document.getElementById('accountPanel');
    const mobileOverlay = document.getElementById('mobileOverlay');
    
    // Bildirim paneli toggle
    if (notificationToggles.length > 0 && notificationPanel) {
        notificationToggles.forEach(toggle => {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                console.log('Notification toggle clicked'); // Debug için log
                
                // Önce hesap panelini gizle
                if (accountPanel) accountPanel.classList.remove('show');
                
                // Bildirim panelini aç/kapat
                notificationPanel.classList.toggle('show');
                if (mobileOverlay) mobileOverlay.classList.toggle('show');
            });
        });
    }
    
    // Hesap paneli toggle
    if (accountToggles.length > 0 && accountPanel) {
        accountToggles.forEach(toggle => {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                console.log('Account toggle clicked'); // Debug için log
                
                // Önce bildirim panelini gizle
                if (notificationPanel) notificationPanel.classList.remove('show');
                
                // Hesap panelini aç/kapat
                accountPanel.classList.toggle('show');
                if (mobileOverlay) mobileOverlay.classList.toggle('show');
            });
        });
    }
    
    // Overlay tıklanınca tüm panelleri kapat
    if (mobileOverlay) {
        mobileOverlay.addEventListener('click', function() {
            if (notificationPanel) notificationPanel.classList.remove('show');
            if (accountPanel) accountPanel.classList.remove('show');
            mobileOverlay.classList.remove('show');
        });
    }
    
    // Bildirim ve hesap öğeleri dışında herhangi bir yere tıklandığında panelleri kapat
    document.addEventListener('click', function(event) {
        // Hedef elementlerin varlığını kontrol et
        if (!notificationPanel || !accountPanel) return;
        
        // Tıklanan eleman ve toggleların durumunu kontrol et
        const isNotificationPanel = notificationPanel.contains(event.target);
        const isNotificationToggle = Array.from(notificationToggles).some(toggle => toggle.contains(event.target));
        const isAccountPanel = accountPanel.contains(event.target);
        const isAccountToggle = Array.from(accountToggles).some(toggle => toggle.contains(event.target));
        
        // Eğer bu elementlerden birine tıklanmadıysa
        if (!isNotificationPanel && !isNotificationToggle && !isAccountPanel && !isAccountToggle) {
            notificationPanel.classList.remove('show');
            accountPanel.classList.remove('show');
            if (mobileOverlay) mobileOverlay.classList.remove('show');
        }
    });
    
    // Mobil nav-item'lerin tıklanabilir olmasını sağla (div içindeki anchor'lar için)
    const mobileNavItems = document.querySelectorAll('.mobile-bottom-nav .nav-item');
    mobileNavItems.forEach(item => {
        // Sadece içinde anchor olmayan öğeler için
        if (!item.hasAttribute('href') && item.querySelector('a')) {
            item.addEventListener('click', function(e) {
                // Eğer tıklanan eleman toggle değilse
                if (!e.target.closest('.js-notification-toggle') && !e.target.closest('.js-account-toggle')) {
                    const anchor = item.querySelector('a');
                    if (anchor && anchor.href) {
                        window.location.href = anchor.href;
                    }
                }
            });
        }
    });
}

/**
 * Sayfa kaydırma olaylarını yönetir
 */
function handleScrollEvents() {
    // Scroll olduğunda navbar görünümünü değiştir
    window.addEventListener('scroll', function() {
        const navbar = document.querySelector('.navbar');
        if (!navbar) return;
        
        if (window.scrollY > 10) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
        
        // Mobil görünümde yukarı/aşağı kaydırmaya göre navbar'ı göster/gizle
        if (window.innerWidth < 992) {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            
            if (scrollTop > window.lastScrollTop && scrollTop > 100) {
                // Aşağı kaydırma, navbarı gizle
                navbar.classList.add('hide-on-scroll');
            } else {
                // Yukarı kaydırma, navbarı göster
                navbar.classList.remove('hide-on-scroll');
            }
            
            window.lastScrollTop = scrollTop;
        }
    });
    
    // Sayfa yüklendiğinde scroll kontrolü
    if (window.scrollY > 10) {
        const navbar = document.querySelector('.navbar');
        if (navbar) navbar.classList.add('scrolled');
    }
    
    // Scroll pozisyonunu takip etmek için başlangıç değeri
    window.lastScrollTop = 0;
}

/**
 * Toast bildirim gösterir
 * @param {string} title - Bildirim başlığı
 * @param {string} message - Bildirim metni
 * @param {string} type - Bildirim tipi (success, error, warning, info)
 */
function showToast(title, message, type = 'info') {
    // Bootstrap Toast bileşeni yoksa oluştur
    if (!document.querySelector('.toast-container')) {
        const toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        toastContainer.style.zIndex = '1090';
        document.body.appendChild(toastContainer);
    }
    
    // Simge belirleme
    let icon = 'info-circle';
    let bgColor = 'bg-info';
    
    if (type === 'success') {
        icon = 'check-circle';
        bgColor = 'bg-success';
    } else if (type === 'error') {
        icon = 'exclamation-circle';
        bgColor = 'bg-danger';
    } else if (type === 'warning') {
        icon = 'exclamation-triangle';
        bgColor = 'bg-warning';
    }
    
    // Toast HTML oluştur
    const toastId = 'toast-' + Date.now();
    const toastHtml = `
        <div id="${toastId}" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header ${bgColor} text-white">
                <i class="fas fa-${icon} me-2"></i>
                <strong class="me-auto">${title}</strong>
                <small>Şimdi</small>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        </div>
    `;
    
    // Toast'u DOM'a ekle
    const toastContainer = document.querySelector('.toast-container');
    toastContainer.innerHTML += toastHtml;
    
    // Toast'u göster
    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement, {
        autohide: true,
        delay: 5000
    });
    toast.show();
    
    // Toast kapandığında DOM'dan kaldır
    toastElement.addEventListener('hidden.bs.toast', function() {
        toastElement.remove();
    });
}

/**
 * Konsola uyarı mesajları ekler
 * @param {string} message - Mesaj
 */
function mobileMenuDebug(message) {
    console.log('%c [Mobil Menü] ' + message, 'background: #7e57c2; color: white; padding: 2px 5px; border-radius: 3px;');
}