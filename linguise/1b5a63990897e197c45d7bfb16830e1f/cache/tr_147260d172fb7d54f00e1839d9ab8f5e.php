<?php die(); ?><html lang="tr" dir="ltr"><head></head><body>dizi (3) {[&quot;user_id&quot;] =&gt; int (1) [&quot;kullan&#x131;c&#x131; ad&#x131;&quot;] =&gt; string (6) &quot;pranga&quot; [&quot;logged_in&quot;] =&gt; bool (true)}
<!-- Tablo yapısı: id (int(11)), username (varchar(50)), email (varchar(100)), password (varchar(255)), phone (varchar(255)), telegram (varchar(255)), phone_wp (varchar(255)), profile_photo (enum('0','1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24')), withdraw_password (varchar(255)), withdraw_verify_code (varchar(50)), verify_withdraw_password (varchar(255)), withdraw_verify_expires (timestamp), withdraw_verify (enum('2','1','0')), email_verify (enum('1','0')), email_verify_code (varchar(255)), email_verify_codetime (timestamp), full_name (varchar(100)), trc20_address (varchar(100)), balance (decimal(20,6)), referral_balance (decimal(20,6)), referral_code (varchar(20)), referrer_id (int(11)), vip_level (int(11)), user_lang (varchar(50)), total_deposit (decimal(20,6)), total_withdraw (decimal(20,6)), total_earnings (decimal(20,6)), status (enum('active','blocked','pending')), last_login (timestamp), created_at (timestamp),  --><!-- Kullanıcı verisi direkt SQL ile çekildi: {&quot;id&quot;:1,&quot;username&quot;:&quot;pranga&quot;,&quot;email&quot;:&quot;chepsop12@gmail.com&quot;,&quot;password&quot;:&quot;$2y$10$94mwgpGsmaoRySb3TLPpo.bw77GMbsxSMCnRKtSWrBUfNbAqzesL2&quot;,&quot;phone&quot;:&quot;+5522323152331&quot;,&quot;telegram&quot;:&quot;adamam&quot;,&quot;phone_wp&quot;:&quot;+5522323152331&quot;,&quot;profile_photo&quot;:&quot;&quot;,&quot;withdraw_password&quot;:&quot;$2y$10$RqdnrFMmv9DqD3JwJCCQuOV8utwf8R0tcUckE7iBEOeftlfSuzRVe&quot;,&quot;withdraw_verify_code&quot;:null,&quot;verify_withdraw_password&quot;:null,&quot;withdraw_verify_expires&quot;:null,&quot;withdraw_verify&quot;:&quot;2&quot;,&quot;email_verify&quot;:&quot;1&quot;,&quot;email_verify_code&quot;:&quot;8203&quot;,&quot;email_verify_codetime&quot;:&quot;2025-03-16 14:51:37&quot;,&quot;full_name&quot;:&quot;elvin mamedov&quot;,&quot;trc20_address&quot;:&quot;TDj1qAvQPF1Tv6LcTJVTpFKDg6FW6CGvLF&quot;,&quot;balance&quot;:&quot;37636.072510&quot;,&quot;referral_balance&quot;:&quot;85.332000&quot;,&quot;referral_code&quot;:&quot;CRC64VV1&quot;,&quot;referrer_id&quot;:null,&quot;vip_level&quot;:4,&quot;user_lang&quot;:&quot;tr&quot;,&quot;total_deposit&quot;:&quot;0.000000&quot;,&quot;total_withdraw&quot;:&quot;0.000000&quot;,&quot;total_earnings&quot;:&quot;0.000000&quot;,&quot;status&quot;:&quot;active&quot;,&quot;last_login&quot;:&quot;2025-03-19 02:56:28&quot;,&quot;created_at&quot;:&quot;2025-03-06 14:13:17&quot;} -->


    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#7367f0">
    <title>Profilim | Digiminex Mobile</title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="assets/images/apple-touch-icon.png">
    
    
    <!-- Prevent phone number detection -->
    <meta name="format-detection" content="telephone=no">
    
    <script defer src="assets/js/all.js"></script>
    <link href="assets/css/fontawesome.css" rel="stylesheet">
    <link href="assets/css/brands.css" rel="stylesheet">
    <link href="assets/css/solid.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet">
    
    <!-- Mobile CSS -->
    <link rel="stylesheet" href="assets/css/mobile.css">
    <link rel="stylesheet" href="assets/css/translation-system.css">

	
	<script async src="https://static.linguise.com/script-js/switcher.bundle.js?d=pk_YNgK025ZNYdLQVprpwbLmTm0DljVi7ht"></script>
	
    <!-- Page-specific CSS -->
        <link rel="stylesheet" href="assets/css/mobile-profile.css">
    
	
		<style>
	.linguise_switcher .linguise_switcher_popup {
		background: #121212;
		color: #3a86ff;
}
	</style>

    <!-- Mobile Header -->
    <header class="mobile-header">
        <div class="header-container">
            <a class="header-logo" href="/tr/index.php">
                <img src="assets/images/logo.png" alt="Digiminex" height="60">
            </a>
        </div>

    </header>

    <!-- Main Content Container -->
    <main class="mobile-content">
        <!-- Page content will be here -->
<!-- Form başlamadan önce -->
<div style="display:none">
Hata Ay&#x131;klama: Kullan&#x131;c&#x131; Ad&#x131;: Yok | E -posta: Yok | Tam Ad&#x131;: Yok</div>

<!-- Profile Photo Selection Modal -->
<div class="language-modal" id="profilePhotoModal">
    <div class="modal-inner">
        <div class="modal-header">
            <h3>Profil foto&#x11F;raf&#x131;n&#x131; se&#xE7;in</h3>
            <button type="button" class="close-modal" id="closePhotoModal">&#xD7;</button>
        </div>
        <div class="modal-content">
            <div class="photo-grid">
                <!-- Default option (initial letter) -->
                <div class="photo-option selected" data-photo-id="0">
                    <div class="avatar-circle default">
                        <span>U</span>
                    </div>
                </div>
                
                <!-- Avatar options based on VIP level -->
                                    <div class="photo-option " data-photo-id="1">
                        <div class="avatar-circle">
                            <img src="assets/images/avatars/avatar1.png" alt="Avatar 1">
                        </div>
                    </div>
                                    <div class="photo-option " data-photo-id="2">
                        <div class="avatar-circle">
                            <img src="assets/images/avatars/avatar2.png" alt="Avatar 2">
                        </div>
                    </div>
                                    <div class="photo-option " data-photo-id="3">
                        <div class="avatar-circle">
                            <img src="assets/images/avatars/avatar3.png" alt="Avatar 3">
                        </div>
                    </div>
                                    <div class="photo-option " data-photo-id="4">
                        <div class="avatar-circle">
                            <img src="assets/images/avatars/avatar4.png" alt="Avatar 4">
                        </div>
                    </div>
                                    <div class="photo-option " data-photo-id="5">
                        <div class="avatar-circle">
                            <img src="assets/images/avatars/avatar5.png" alt="Avatar 5">
                        </div>
                    </div>
                            </div>
            <input type="hidden" id="selectedPhotoId" value="0">
            
            <button type="button" class="btn btn-primary btn-block mt-3" id="savePhotoSelection">Kaydetmek</button>
        </div>
    </div>
</div>
<div class="profile-page">
    <!-- Profile Header -->
    <div class="profile-header">
        <div class="profile-avatar" id="openProfilePhoto">
                            <span class="avatar-text">U</span>
                        <div class="avatar-edit-icon">
                <i class="fas fa-camera"></i>
            </div>
        </div>
        <h2 class="profile-username">Kullan&#x131;c&#x131;</h2>
        <div class="profile-meta">
            <div class="meta-item">
                <i class="fas fa-trophy"></i>
                <span>VIP Seviyesi: Standart</span>
            </div>
            <div class="meta-item">
                <i class="fas fa-calendar-alt"></i>
                <span>O zamandan beri &#xFC;ye: Yok</span>
            </div>
        </div>
        
        <a href="/tr/packages.php?type=vip" class="btn btn-sm btn-primary mt-2">
            <i class="fas fa-crown me-1"></i> <span>VIP Paketlerini G&#xF6;r&#xFC;nt&#xFC;le</span>
        </a>
    </div>
    
    <!-- Profile Navigation -->
    <div class="profile-navigation">
        <button class="profile-nav-item active" data-tab="profile-info">
            <i class="fas fa-user"></i>
            <span>Ki&#x15F;isel Bilgi</span></button>
        
         <button class="profile-nav-item" data-tab="security">
            <i class="fas fa-shield-alt"></i>
            <span>G&#xFC;venli&#x11F;i</span></button>
    
        </div>
    
        
        
    <!-- Profile Content -->
    <div class="profile-content">
        <!-- Personal Info Tab -->
        <div class="profile-tab active" id="profile-info">
            <div class="content-card">
                <div class="card-header">
                    <h3>Profil Bilgileri</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action>
                        <!-- Hidden input for profile photo selection -->
                        <input type="hidden" id="profile_photo" name="profile_photo" value="0">
                        
                        <div class="form-group">
                            <label for="username">Kullan&#x131;c&#x131; ad&#x131;</label>
                            <input type="text" id="username" class="form-control" value readonly>
                             <small class="form-text">kullan&#x131;c&#x131; ad&#x131; de&#x11F;i&#x15F;tirilemez</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="full_name">Ad Soyad</label>
                            <input type="text" id="full_name" name="full_name" class="form-control" value>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">E -posta adresi</label>
                            <input type="email" id="email" name="email" readonly class="form-control" value required>
                        </div>
                        
                        <!-- Phone Number Field -->
                        <div class="form-group">
                            <label for="phone">Telefon numaras&#x131;</label>
                            <div class="input-group">
                                <input type="tel" id="phone" name="phone" class="form-control" value placeholder="+1xxxxxxxxxx">
                            </div>
                        </div>
                        
                        <!-- Use for WhatsApp Option -->
                        <div class="form-group wp-option">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="use_for_whatsapp" name="use_for_whatsapp">
                                <label class="form-check-label" for="use_for_whatsapp">WhatsApp i&#xE7;in bu numaray&#x131; da kullan&#x131;n</label>
                            </div>
                        </div>
                        
                        <!-- WhatsApp Phone Number Field -->
                        <div class="form-group" id="whatsapp_phone_container" style>
                            <label for="phone_wp">Whatsapp Numaras&#x131;</label>
                            <input type="tel" id="phone_wp" name="phone_wp" class="form-control" value placeholder="+1xxxxxxxxxx">
                        </div>
                        
                        <!-- Telegram Username -->
                        <div class="form-group">
                            <label for="telegram">Telegram Kullan&#x131;c&#x131; Ad&#x131;</label>
                            <div class="input-group">
                                <span class="input-group-text">@</span>
                                <input type="text" id="telegram" name="telegram" class="form-control" value placeholder="kullan&#x131;c&#x131; ad&#x131;">
                            </div>
                        </div>
                        
                        <!-- TRC20 Wallet Address -->
                        <div class="form-group">
                                <input type="text" id="trc20_address" name="trc20_address" class="form-control" value>
                                                            <small class="form-text">Para &#xE7;ekme i&#xE7;in kullan&#x131;lan</small>
                                                     
                            <label for="trc20_address">TRC20 c&#xFC;zdan adresi</label></div>
                        
                        <div class="form-group">
                            <label for="membership_date">&#xDC;yelik tarihi</label>
                            <input type="text" id="membership_date" class="form-control" value="N/A" readonly>
                        </div>
                        
                        <button type="submit" name="update_profile" class="btn btn-primary btn-block">
                            <i class="fas fa-save me-2"></i> <span>De&#x11F;i&#x15F;iklikleri Kaydet</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Security Tab (formerly Change Password) -->
        <div class="profile-tab" id="security">
            <div class="content-card">
                <div class="card-header">
                    <h3>G&#xFC;venlik Ayarlar&#x131;</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action>
                        <div class="form-group">
                            <label for="current_password">Mevcut &#x15E;ifre</label>
                            <input type="password" id="current_password" name="current_password" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="new_password">Yeni &#x15F;ifre</label>
                            <input type="password" id="new_password" name="new_password" class="form-control" required>
                             <small class="form-text">en az 6 karakter olmal&#x131;</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">Yeni &#x15F;ifreyi onaylay&#x131;n</label>
                            <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                        </div>
                        
                        <button type="submit" name="change_password" class="btn btn-primary btn-block">
                            <i class="fas fa-key me-2"></i> <span>&#x15E;ifreyi g&#xFC;ncelle</span>
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Additional security features can be added here in the future -->
            <div class="content-card mt-4">
                <div class="card-header">
                    <h3>Giri&#x15F; etkinli&#x11F;i</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">Son giri&#x15F;</div>
                        <div class="info-value">N/A</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">IP adresi</div>
                        <div class="info-value">196.251.81.231</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Account Actions -->
    <div class="account-actions">
        <a href="/tr/transactions.php" class="action-link">
            <i class="fas fa-exchange-alt"></i>
            <span>&#x130;&#x15F;lemlerim</span></a>
        
         
        <a href="/tr/logout.php" class="action-link text-danger">
            <i class="fas fa-sign-out-alt"></i>
            <span>oturum a&#xE7;may&#x131;</span></a>
    
         <a href="/tr/support.php" class="action-link">
            <i class="fas fa-headset"></i>
            <span>destekliyor</span></a>
        </div>
</div>

<!-- CSS for language-style modal -->
<style>
/* Modal styling to match language selector */
.language-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    align-items: center;
    justify-content: center;
}

.modal-inner {
    background-color: #fff;
    width: 90%;
    max-width: 360px;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    border-bottom: 1px solid #eee;
}

.modal-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
}

.close-modal {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #777;
}

.modal-content {
    padding: 20px;
}

/* Photo grid styling */
.photo-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    margin-bottom: 15px;
}

.photo-option {
    display: flex;
    flex-direction: column;
    align-items: center;
    cursor: pointer;
    padding: 10px;
    border-radius: 8px;
    transition: background-color 0.2s;
}

.photo-option:hover {
    background-color: #f0f0f0;
}

.photo-option.selected {
    background-color: #e3f2fd;
    border: 2px solid #2196f3;
}

.avatar-circle {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f5f5f5;
}

.avatar-circle.default {
    background-color: #2196f3;
    color: white;
    font-size: 24px;
    font-weight: bold;
}

.avatar-circle img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Profile avatar and edit button */
.profile-avatar {
    position: relative;
    cursor: pointer;
}

.avatar-edit-icon {
    position: absolute;
    bottom: 0;
    right: 0;
    background-color: #2196f3;
    color: white;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}

/* Phone and WhatsApp styling */
.wp-option {
    margin-top: -10px;
    margin-bottom: 15px;
}

/* Information items in security tab */
.info-item {
    padding: 12px 0;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
}

.info-item:last-child {
    border-bottom: none;
}

.info-label {
    color: #666;
    font-weight: 500;
}

.info-value {
    font-weight: 600;
}

/* Form styling improvements */
.form-group {
    margin-bottom: 20px;
}

.form-check {
    display: flex;
    align-items: center;
}

.form-check-input {
    margin-right: 10px;
}

.btn-block {
    display: block;
    width: 100%;
}

.btn-primary {
    background-color: #2196f3;
    border-color: #2196f3;
    color: white;
}

/* Toast notification styling */
.toast-notification {
    position: fixed;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    padding: 12px 24px;
    border-radius: 8px;
    color: white;
    font-weight: bold;
    box-shadow: 0 3px 10px rgba(0,0,0,0.2);
    z-index: 9999;
    transition: all 0.3s ease;
    opacity: 0;
    visibility: hidden;
}

.toast-notification.show {
    opacity: 1;
    visibility: visible;
    bottom: 30px;
}

.toast-success {
    background-color: #43a047;
}

.toast-error {
    background-color: #e53935;
}

.toast-info {
    background-color: #039be5;
}

.toast-warning {
    background-color: #fb8c00;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Kullanıcı verilerinin yüklenip yüklenmediğini kontrol edelim
    const usernameField = document.getElementById('username');
    if (usernameField && !usernameField.value) {
        console.error('Kullanıcı verileri yüklenemedi!');
        showToast('Kullanıcı bilgileri yüklenemedi. Lütfen sayfayı yenileyin.', 'error');
    }
    
    // Tab navigation
    const navItems = document.querySelectorAll('.profile-nav-item');
    const tabPanes = document.querySelectorAll('.profile-tab');
    
    navItems.forEach(item => {
        item.addEventListener('click', function() {
            // Remove active class from all items
            navItems.forEach(navItem => navItem.classList.remove('active'));
            tabPanes.forEach(pane => pane.classList.remove('active'));
            
            // Add active class to clicked item
            this.classList.add('active');
            
            // Show corresponding tab
            const tabId = this.getAttribute('data-tab');
            document.getElementById(tabId).classList.add('active');
        });
    });
    
    // WhatsApp option functionality
    const whatsappCheckbox = document.getElementById('use_for_whatsapp');
    const whatsappContainer = document.getElementById('whatsapp_phone_container');
    
    if (whatsappCheckbox && whatsappContainer) {
        whatsappCheckbox.addEventListener('change', function() {
            if (this.checked) {
                whatsappContainer.style.display = 'none';
            } else {
                whatsappContainer.style.display = 'block';
            }
        });
    }
    
    // Profile photo selection functionality
    const openProfilePhotoBtn = document.getElementById('openProfilePhoto');
    const profilePhotoModal = document.getElementById('profilePhotoModal');
    const closePhotoModalBtn = document.getElementById('closePhotoModal');
    const photoOptions = document.querySelectorAll('.photo-option');
    const savePhotoSelectionBtn = document.getElementById('savePhotoSelection');
    
    // Open modal
    if (openProfilePhotoBtn && profilePhotoModal) {
        openProfilePhotoBtn.addEventListener('click', function() {
            profilePhotoModal.style.display = 'flex';
        });
    }
    
    // Close modal
    if (closePhotoModalBtn && profilePhotoModal) {
        closePhotoModalBtn.addEventListener('click', function() {
            profilePhotoModal.style.display = 'none';
        });
        
        // Also close when clicking outside modal
        window.addEventListener('click', function(event) {
            if (event.target == profilePhotoModal) {
                profilePhotoModal.style.display = 'none';
            }
        });
    }
    
    // Photo selection
    if (photoOptions) {
        photoOptions.forEach(option => {
            option.addEventListener('click', function() {
                // Remove selected class from all options
                photoOptions.forEach(opt => opt.classList.remove('selected'));
                
                // Add selected class to clicked option
                this.classList.add('selected');
                
                // Store selected photo ID
                const photoId = this.getAttribute('data-photo-id');
                document.getElementById('selectedPhotoId').value = photoId;
            });
        });
    }
    
    // Save photo selection
    if (savePhotoSelectionBtn) {
        savePhotoSelectionBtn.addEventListener('click', function() {
            // Get selected photo ID
            const selectedPhotoId = document.getElementById('selectedPhotoId').value;
            
            // Set it to hidden input
            const profilePhotoInput = document.getElementById('profile_photo');
            if (profilePhotoInput) {
                profilePhotoInput.value = selectedPhotoId;
            }
            
            // Close modal
            profilePhotoModal.style.display = 'none';
            
            // Submit form with error handling
            try {
                const form = document.querySelector('form');
                if (form) {
                    // Manually add a hidden field for update_profile
                    let hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'update_profile';
                    hiddenInput.value = '1';
                    form.appendChild(hiddenInput);
                    
                    form.submit();
                } else {
                    showToast('Form not found', 'error');
                }
            } catch (e) {
                console.error('Error submitting form:', e);
                showToast('Error updating profile', 'error');
            }
        });
    }
    
    // Form gönderim hataları için ek kontroller
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            try {
                // Form gönderilirken ek kontroller
                console.log('Form gönderiliyor...');
                
                // Bir hata olursa
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> İşleniyor...';
                    submitBtn.disabled = true;
                }
                
                // İşlemi 5 saniye sonra iptal et (sunucu yanıt vermezse)
                setTimeout(function() {
                    if (submitBtn && submitBtn.disabled) {
                        submitBtn.innerHTML = submitBtn.innerHTML.replace('<i class="fas fa-spinner fa-spin me-2"></i> İşleniyor...', '<i class="fas fa-exclamation-triangle me-2"></i> Tekrar Deneyin');
                        submitBtn.disabled = false;
                        showToast('İşlem zaman aşımına uğradı, lütfen tekrar deneyin.', 'warning');
                    }
                }, 5000);
            } catch (err) {
                console.error('Form gönderim hatası:', err);
                // Hata olursa formun normal davranışını engelleme
                e.preventDefault();
                showToast('Form gönderilirken bir hata oluştu: ' + err.message, 'error');
            }
        });
    });
});

// Toast notification function
function showToast(message, type = 'info') {
    // Create toast element if it doesn't exist
    let toast = document.querySelector('.toast-notification');
    if (!toast) {
        toast = document.createElement('div');
        toast.className = 'toast-notification';
        document.body.appendChild(toast);
    }
    
    // Set type and message
    toast.className = `toast-notification toast-${type}`;
    toast.innerHTML = message;
    
    // Show toast
    toast.classList.add('show');
    
    // Hide after 3 seconds
    setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);
}

// Sayfa yüklendiğinde veritabanı sorununun geçici olarak kullanıcıya bildirilmesi
setTimeout(function() {
    const usernameField = document.getElementById('username');
    if (usernameField && !usernameField.value) {
        showToast('Kullanıcı bilgileri yüklenemedi. Lütfen sayfayı yenileyin.', 'warning');
    }
}, 1000);

// Önbellek sorunlarını önlemek için sayfayı döngüsel olarak kontrol etme
let checkCount = 0;
const maxChecks = 3;
const checkInterval = setInterval(function() {
    const usernameField = document.getElementById('username');
    
    if (checkCount >= maxChecks) {
        clearInterval(checkInterval);
        return;
    }
    
    if (usernameField && !usernameField.value) {
        console.log('Kullanıcı verilerini tekrar kontrol etme denemesi: ' + (checkCount + 1));
        // Sayfayı yenileme işlemi burada yapılabilir
        // window.location.reload(); - Bu satırı etkinleştirmek isterseniz yorum işaretini kaldırın
    } else {
        clearInterval(checkInterval);
    }
    
    checkCount++;
}, 3000);
</script>

<!-- Veritabanı bağlantı sorunu olabilir. Lütfen config.php dosyasını kontrol edin. --></main>

    <!-- Mobile Bottom Navigation -->
    <nav class="mobile-bottom-nav">
        <a href="/tr/index.php" class="nav-item ">
            <i class="fas fa-home"></i>
            <span data-i18n="buttons.home">Ana sayfa</span>
        </a>
        
        <a href="/tr/packages.php" class="nav-item ">
            <i class="fas fa-cubes"></i>
            <span data-i18n="buttons.packages">Paketler</span>
        </a>
        
                    <a href="/tr/daily-game.php" class="nav-item nav-item-main ">
                <div class="main-btn">
                    <i class="fas fa-trophy"></i>
                </div>
                <span data-i18n="dashboard.daily_game">Oyun</span>
            </a>
            
            <a href="/tr/notifications.php" class="nav-item ">
                <i class="fas fa-bell"></i>
                                <span data-i18n="buttons.notifications">Bildirimler</span>
            </a>
            
            <a href="/tr/profile.php" class="nav-item active">
                <i class="fas fa-user"></i>
                <span data-i18n="buttons.myAccount">Hesap</span>
            </a>
            </nav>

    
    <!-- Backdrop Overlay -->
    <div class="backdrop-overlay" id="backdropOverlay"></div>
    

</body></html>