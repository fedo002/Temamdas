/**
 * Admin Panel JavaScript Dosyası
 */

document.addEventListener("DOMContentLoaded", function() {
    // Sidebar Toggle
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('content');
    
    if (sidebarToggle && sidebar && content) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('sidebar-show');
            
            // Mobil görünüm kontrolü
            if (window.innerWidth > 992) {
                sidebar.classList.toggle('sidebar-collapsed');
                content.classList.toggle('content-full');
            }
        });
        
        // Sayfa yüklendiğinde mobil görünüm kontrolü
        if (window.innerWidth <= 992) {
            sidebar.classList.remove('sidebar-collapsed');
            content.classList.remove('content-full');
        } else {
            // Kullanıcı tercihini localStorage'dan al
            const sidebarState = localStorage.getItem('sidebarCollapsed');
            if (sidebarState === 'true') {
                sidebar.classList.add('sidebar-collapsed');
                content.classList.add('content-full');
            }
        }
        
        // Sidebar durumunu kaydet
        sidebarToggle.addEventListener('click', function() {
            if (window.innerWidth > 992) {
                localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('sidebar-collapsed'));
            }
        });
    }
    
    // Mobil görünümde sidebar dışına tıklandığında kapatma
    document.addEventListener('click', function(event) {
        if (
            window.innerWidth <= 992 && 
            sidebar && 
            !sidebar.contains(event.target) && 
            event.target !== sidebarToggle &&
            !sidebarToggle.contains(event.target) &&
            sidebar.classList.contains('sidebar-show')
        ) {
            sidebar.classList.remove('sidebar-show');
        }
    });
    
    // Dropdown menüleri etkinleştir
    const dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
    dropdownElementList.map(function(dropdownToggleEl) {
        return new bootstrap.Dropdown(dropdownToggleEl);
    });
    
    // Tooltips etkinleştir
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Form validasyonu etkinleştir
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
    
    // Alert mesajları için otomatik kapat
    const autoCloseAlerts = document.querySelectorAll('.alert-dismissible.auto-close');
    autoCloseAlerts.forEach(alert => {
        setTimeout(() => {
            const closeButton = alert.querySelector('.btn-close');
            if (closeButton) {
                closeButton.click();
            }
        }, 5000); // 5 saniye sonra kapat
    });
    
    // Tarih seçici etkinleştir
    const datePickers = document.querySelectorAll('.datepicker');
    if (typeof flatpickr !== 'undefined') {
        datePickers.forEach(input => {
            flatpickr(input, {
                dateFormat: "d.m.Y",
                locale: "tr"
            });
        });
    }
    
    // Select2 etkinleştir
    const selects = document.querySelectorAll('.select2');
    if (typeof $.fn.select2 !== 'undefined') {
        $(selects).select2({
            theme: 'bootstrap-5'
        });
    }
    
    // Resmi önizle
    const imageInputs = document.querySelectorAll('.image-upload');
    imageInputs.forEach(input => {
        input.addEventListener('change', function(event) {
            const previewElement = document.getElementById(this.dataset.preview);
            if (previewElement && this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewElement.src = e.target.result;
                    previewElement.style.display = 'block';
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
    
    // Sayfa açılışında animasyon
    const fadeElements = document.querySelectorAll('.fade-in');
    fadeElements.forEach(element => {
        element.classList.add('show');
    });
    
    // Kullanıcı işlem onayı
    const confirmActions = document.querySelectorAll('[data-confirm]');
    confirmActions.forEach(element => {
        element.addEventListener('click', function(event) {
            const message = this.dataset.confirm || 'Bu işlemi gerçekleştirmek istediğinize emin misiniz?';
            if (!confirm(message)) {
                event.preventDefault();
            }
        });
    });
    
    // Kopyalama butonu
    const copyButtons = document.querySelectorAll('.btn-copy');
    copyButtons.forEach(button => {
        button.addEventListener('click', function() {
            const text = this.dataset.copyText;
            const textArea = document.createElement('textarea');
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            
            // Kopyalama başarılı bildirimi
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-check"></i> Kopyalandı';
            setTimeout(() => {
                this.innerHTML = originalText;
            }, 2000);
        });
    });
});

// Sayfa yüklenmesi tamamlandığında loader'ı gizle
window.addEventListener('load', function() {
    const loader = document.querySelector('.page-loader');
    if (loader) {
        loader.classList.add('fade-out');
        setTimeout(() => {
            loader.style.display = 'none';
        }, 300);
    }
});

// Pencere boyutu değiştiğinde
window.addEventListener('resize', function() {
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('content');
    
    if (window.innerWidth <= 992) {
        if (sidebar && content) {
            sidebar.classList.remove('sidebar-collapsed');
            content.classList.remove('content-full');
        }
    }
});