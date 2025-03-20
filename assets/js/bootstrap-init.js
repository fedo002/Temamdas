/**
 * Bootstrap Bileşenlerini Manuel Başlatma Scripti
 * 
 * Bootstrap dropdown menüleri ve açılır menüleri başlatır.
 * Bu script sayfanın yüklenmesi tamamlandığında çalışır.
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Bootstrap bileşenleri başlatılıyor...');
    
    // Bootstrap yüklendi mi kontrol et
    if (typeof bootstrap === 'undefined') {
        console.error('Hata: Bootstrap JS yüklenemedi!');
        return;
    }
    
    // Tüm dropdown menüleri başlat
    var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
    if (dropdownElementList.length > 0) {
        console.log(`${dropdownElementList.length} dropdown menü bulundu ve başlatıldı.`);
        
        dropdownElementList.forEach(function(dropdownToggleEl) {
            try {
                new bootstrap.Dropdown(dropdownToggleEl);
            } catch (e) {
                console.error('Dropdown başlatılırken hata:', e);
            }
        });
    }
    
    // Tüm collapse menüleri başlat
    var collapseElementList = [].slice.call(document.querySelectorAll('.collapse:not(.navbar-collapse)'));
    if (collapseElementList.length > 0) {
        console.log(`${collapseElementList.length} collapse menü bulundu ve başlatıldı.`);
        
        collapseElementList.forEach(function(collapseEl) {
            try {
                new bootstrap.Collapse(collapseEl, {
                    toggle: false
                });
            } catch (e) {
                console.error('Collapse başlatılırken hata:', e);
            }
        });
    }
    
    // Tooltips başlat (eğer varsa)
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    if (tooltipTriggerList.length > 0) {
        tooltipTriggerList.forEach(function(tooltipTriggerEl) {
            try {
                new bootstrap.Tooltip(tooltipTriggerEl);
            } catch (e) {
                console.error('Tooltip başlatılırken hata:', e);
            }
        });
    }
    
    // Bootstrap yükleme durumunu kontrol et
    if (bootstrap.Dropdown && bootstrap.Collapse) {
        console.log('Bootstrap bileşenleri başarıyla başlatıldı.');
    } else {
        console.warn('Bootstrap bazı bileşenleri eksik olabilir.');
    }
});