<?php die(); ?><!DOCTYPE html><html lang="tr" dir="ltr"><head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#7367f0">
    <title>Fonlar &#xC7;ekme | Digiminex Mobile</title>
    
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
    <style>
	.linguise_switcher .linguise_switcher_popup {
		background: #121212;
		color: #3a86ff;
}
	</style></head>
	
		
<body>
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
<div class="withdraw-page">
    <h1 class="page-title">Fonlar&#x131; &#xC7;ektirin</h1>
    
        <!-- Security Setup Required -->
    <div class="card withdraw-form-card">
        <h2><i class="fas fa-shield-alt"></i> Geri &#xC7;ekme G&#xFC;venli&#x11F;i Set</h2>
        
                <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i>
            Para &#xE7;ekme i&#x15F;lemleri i&#xE7;in bir g&#xFC;venlik &#x15F;ifresi ayarlaman&#x131;z gerekir.        </div>
                
        <form method="POST" action="/tr/withdraw.php" id="securityForm">
            <div class="form-group">
                <label for="new_withdraw_password">Para &#xE7;ekme g&#xFC;venlik &#x15F;ifresi</label>
                <input type="password" id="new_withdraw_password" name="new_withdraw_password" placeholder="Minimum 6 karakter" required>
            </div>
            
            <div class="form-group">
                <label for="confirm_withdraw_password">&#x15E;ifrenizi tekrar girin</label>
                <input type="password" id="confirm_withdraw_password" name="confirm_withdraw_password" placeholder="&#x15E;ifrenizi tekrar girin" required>
            </div>
            
            <div class="form-group">
                <label>Do&#x11F;rulama y&#xF6;ntemi</label>
                <div class="radio-group">
                    <label class="radio-label">
                        <input type="radio" name="verify_method" value="1" checked>
                        <span>&#x15E;ifre Do&#x11F;rulama Yaln&#x131;zca</span></label>
                    
                     <label class="radio-label">
                        <input type="radio" name="verify_method" value="2">
                        <span>&#x15F;ifre ve e -posta do&#x11F;rulamas&#x131; (2FA)</span></label>
                
                    </div>
            </div>
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <strong>Bilgi:</strong> &#x130;ki fakt&#xF6;rl&#xFC; kimlik do&#x11F;rulama (2FA) hesab&#x131;n&#x131;z i&#xE7;in ekstra g&#xFC;venlik sa&#x11F;lar. Her para &#xE7;ekme i&#xE7;in, hem &#x15F;ifrenizi hem de e-postan&#x131;za g&#xF6;nderilen bir kerelik kodu girmeniz gerekir.</div>
            
            <button type="submit" name="set_withdraw_password" class="btn btn-primary btn-block">
                <i class="fas fa-save"></i> G&#xFC;venlik Ayarlar&#x131;n&#x131; Kaydet            </button>
        </form>
    </div>
    </div>

<style>
:root {
    --primary-color: #3F88F6;
    --primary-dark: #2d6ed6;
    --primary-color-rgb: 63, 136, 246;
    --danger-color: #dc3545;
    --warning-color: #ffc107;
    --success-color: #28c76f;
    --info-color: #17a2b8;
    --gray-light: #f8f9fa;
    --gray: #6e6b7b;
    --border-color: #ddd;
}

.withdraw-page {
    padding: 15px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #333;
    background-color: #f8f9fa;
}

.page-title {
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 20px;
    color: var(--primary-color);
}

.withdraw-container {
    display: flex;
    flex-direction: column;
    gap: 20px;
    max-width: 800px;
    margin: 0 auto;
}

/* Balance Card */
.balance-card {
    background-color: var(--primary-color);
    color: white;
    border-radius: 12px;
    padding: 20px;
    text-align: center;
    box-shadow: 0 4px 15px rgba(var(--primary-color-rgb), 0.3);
}

.balance-label {
    font-size: 1rem;
    opacity: 0.9;
    margin-bottom: 10px;
}

.balance-value {
    font-size: 2rem;
    font-weight: 700;
}

/* Cards */
.card {
    background-color: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.card h2 {
    font-size: 1.3rem;
    font-weight: 600;
    margin: 0 0 20px;
    display: flex;
    align-items: center;
}

.card h2 i {
    margin-right: 10px;
    color: var(--primary-color);
}

.card h3 {
    font-size: 1.1rem;
    font-weight: 600;
    margin: 0 0 15px;
    display: flex;
    align-items: center;
}

.card h3 i {
    margin-right: 10px;
    color: var(--primary-color);
}

/* Form Styles */
.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    font-size: 0.9rem;
}

.input-group {
    display: flex;
    align-items: center;
}

.input-prefix {
    background-color: var(--gray-light);
    border: 1px solid var(--border-color);
    border-right: none;
    padding: 12px 15px;
    border-radius: 8px 0 0 8px;
    color: var(--gray);
}

.form-group input {
    flex: 1;
    padding: 12px 15px;
    border: 1px solid var(--border-color);
    border-radius: 0 8px 8px 0;
    font-size: 1rem;
    width: 100%;
    outline: none;
    transition: border-color 0.3s;
}

.form-group input:focus {
    border-color: var(--primary-color);
}

.form-group input#trc20_address {
    border-radius: 8px;
}

.saved-address {
    position: relative;
}

.saved-address-label {
    position: absolute;
    top: -10px;
    right: 10px;
    background-color: var(--primary-color);
    color: white;
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 0.7rem;
    font-weight: 500;
}

.form-hint {
    font-size: 0.8rem;
    color: var(--gray);
    margin-top: 5px;
}

/* Amount Shortcuts */
.amount-shortcuts {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.amount-btn {
    background-color: var(--gray-light);
    border: 1px solid var(--border-color);
    border-radius: 20px;
    padding: 8px 15px;
    font-size: 0.9rem;
    color: #333;
    cursor: pointer;
    transition: all 0.2s;
}

.amount-btn:hover {
    background-color: #e9ecef;
}

.amount-btn:active {
    transform: scale(0.98);
}

/* Fee Calculator */
.fee-calculator {
    background-color: var(--gray-light);
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 20px;
}

.fee-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
    font-size: 0.9rem;
}

.fee-row.total {
    margin-top: 8px;
    padding-top: 8px;
    border-top: 1px dashed var(--border-color);
    font-weight: 600;
}

/* Alerts */
.alert {
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 0.9rem;
    display: flex;
    align-items: flex-start;
}

.alert i {
    margin-right: 10px;
    font-size: 1.1rem;
    margin-top: 2px;
}

.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
}

.alert-warning {
    background-color: #fff3cd;
    color: #856404;
}

.alert-info {
    background-color: #d1ecf1;
    color: #0c5460;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
}

/* Buttons */
/* Buttons */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 12px 20px;
    border-radius: 8px;
    font-weight: 500;
    text-decoration: none;
    cursor: pointer;
    transition: background-color 0.3s, transform 0.1s;
    border: none;
    font-size: 1rem;
}

.btn:active {
    transform: translateY(1px);
}

.btn i {
    margin-right: 8px;
}

.btn-block {
    display: flex;
    width: 100%;
}

.btn-primary {
    background-color: var(--primary-color);
    color: white;
}

.btn-primary:hover {
    background-color: var(--primary-dark);
}

.btn-outline {
    background-color: transparent;
    border: 1px solid var(--primary-color);
    color: var(--primary-color);
}

.btn-outline:hover {
    background-color: rgba(var(--primary-color-rgb), 0.05);
}

.btn-sm {
    padding: 8px 15px;
    font-size: 0.9rem;
}

.btn-link {
    background: none;
    color: var(--primary-color);
    padding: 8px;
    border: none;
    text-decoration: underline;
}

.btn-link:hover {
    text-decoration: none;
}

.mt-3 {
    margin-top: 15px;
}

.text-center {
    text-align: center;
}

.d-inline {
    display: inline;
}

/* Radio Group */
.radio-group {
    margin-top: 10px;
}

.radio-label {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
    cursor: pointer;
}

.radio-label input[type="radio"] {
    margin-right: 10px;
}

/* Info List */
.info-list {
    list-style-type: none;
    padding: 0;
    margin: 0 0 20px;
}

.info-list li {
    display: flex;
    align-items: flex-start;
    margin-bottom: 15px;
}

.info-list li i {
    color: var(--success-color);
    margin-right: 10px;
    margin-top: 2px;
}

.info-list li div {
    flex: 1;
}

.info-list li strong {
    display: block;
    margin-bottom: 3px;
}

.info-list li span {
    font-size: 0.9rem;
    color: var(--gray);
}

/* Recent Withdrawals */
.withdrawals-list {
    margin-bottom: 15px;
}

.withdrawal-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 0;
    border-bottom: 1px solid #eee;
}

.withdrawal-item:last-child {
    border-bottom: none;
}

.withdrawal-amount {
    font-weight: 600;
    margin-bottom: 5px;
}

.withdrawal-date {
    font-size: 0.8rem;
    color: var(--gray);
}

.status-badge {
    display: inline-block;
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
}

.status-completed {
    background-color: #d4edda;
    color: #155724;
}

.status-pending {
    background-color: #fff3cd;
    color: #856404;
}

.status-processing {
    background-color: #d1ecf1;
    color: #0c5460;
}

.status-failed {
    background-color: #f8d7da;
    color: #721c24;
}

/* Success Container */
.success-container {
    text-align: center;
    background-color: white;
    border-radius: 12px;
    padding: 30px 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    max-width: 600px;
    margin: 0 auto;
}

.success-icon {
    font-size: 4rem;
    color: var(--success-color);
    margin-bottom: 20px;
}

.success-container h2 {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 15px;
}

.success-container p {
    color: var(--gray);
    margin-bottom: 25px;
}

.action-buttons {
    display: flex;
    gap: 10px;
    justify-content: center;
}

.withdraw-form-card {
    max-width: 600px;
    margin: 0 auto;
}

@media (max-width: 500px) {
    .action-buttons {
        flex-direction: column;
    }
    
    .page-title {
        font-size: 1.5rem;
    }
    
    .balance-value {
        font-size: 1.8rem;
    }
    
    .card {
        padding: 15px;
    }
    
    .form-group input, 
    .input-prefix {
        padding: 10px 12px;
    }
    
    .btn {
        padding: 10px 16px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Amount buttons
    const amountBtns = document.querySelectorAll('.amount-btn');
    const amountInput = document.getElementById('amount');
    
    // Get settings from hidden inputs
    const maxBalance = parseFloat(document.getElementById('user-balance')?.value || 0);
    const networkFee = parseFloat(document.getElementById('network-fee')?.value || 1);
    const minWithdraw = parseFloat(document.getElementById('min-withdraw')?.value || 10);
    
    // Fee calculator elements
    const withdrawAmountText = document.getElementById('withdrawAmount');
    const networkFeeText = document.getElementById('networkFee');
    const totalWithdrawText = document.getElementById('totalWithdraw');
    
    // Fee calculation function - No platform fee
    function calculateFee() {
        const amount = parseFloat(amountInput.value) || 0;
        const total = amount + networkFee; // Only add network fee, no percentage fee
        
        if (withdrawAmountText) withdrawAmountText.textContent = amount.toFixed(2) + ' USDT';
        if (networkFeeText) networkFeeText.textContent = networkFee.toFixed(2) + ' USDT';
        if (totalWithdrawText) totalWithdrawText.textContent = total.toFixed(2) + ' USDT';
    }
    
    // Amount button event listeners
    if (amountBtns) {
        amountBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                if (this.dataset.amount === 'max') {
                    // Calculate maximum withdrawable amount accounting for network fee only
                    const maxWithdrawAmount = Math.max(0, maxBalance - networkFee);
                    amountInput.value = Math.floor(maxWithdrawAmount * 100) / 100;
                } else {
                    const amount = parseFloat(this.dataset.amount);
                    amountInput.value = amount.toFixed(2);
                }
                
                calculateFee();
            });
        });
    }
    
    // Update fee on amount input
    if (amountInput) {
        amountInput.addEventListener('input', calculateFee);
        // Calculate fee on page load
        calculateFee();
    }
    
    // Password confirmation check
    const newPasswordInput = document.getElementById('new_withdraw_password');
    const confirmPasswordInput = document.getElementById('confirm_withdraw_password');
    const securityForm = document.getElementById('securityForm');
    
    if (securityForm && newPasswordInput && confirmPasswordInput) {
        securityForm.addEventListener('submit', function(e) {
            if (newPasswordInput.value !== confirmPasswordInput.value) {
                e.preventDefault();
                showAlert('Passwords do not match. Please check again.');
                return false;
            }
            
            if (newPasswordInput.value.length < 6) {
                e.preventDefault();
                showAlert('Password must be at least 6 characters long.');
                return false;
            }
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            }
        });
    }
    
    // Withdrawal form validation
    const withdrawForm = document.getElementById('withdrawForm');
    const trc20AddressInput = document.getElementById('trc20_address');
    
    if (withdrawForm && amountInput && trc20AddressInput) {
        withdrawForm.addEventListener('submit', function(e) {
            const amount = parseFloat(amountInput.value) || 0;
            const address = trc20AddressInput.value.trim();
            
            let hasError = false;
            
            // Amount validation
            if (amount < minWithdraw) {
                e.preventDefault();
                showAlert(`Minimum withdrawal amount must be ${minWithdraw} USDT.`);
                hasError = true;
            }
            
            // Calculate total amount with fees (network fee only, no platform fee)
            const totalAmount = amount + networkFee;
            
            if (totalAmount > maxBalance) {
                e.preventDefault();
                showAlert(`Insufficient balance. Total amount (including network fee): ${totalAmount.toFixed(2)} USDT`);
                hasError = true;
            }
            
            // TRC20 address validation
            if (address === '') {
                e.preventDefault();
                showAlert('TRC20 wallet address is required.');
                hasError = true;
            } else if (address.length !== 34 || address.charAt(0) !== 'T') {
                e.preventDefault();
                showAlert('Invalid TRC20 wallet address.');
                hasError = true;
            }
            
            if (hasError) {
                return false;
            }
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            }
        });
    }
    
    // Show alert message
    function showAlert(message) {
        // Check if there's already an alert
        const existingAlert = document.querySelector('.alert-danger');
        
        if (existingAlert) {
            existingAlert.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
        } else {
            // Create new alert
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-danger';
            alertDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
            
            // Insert at the beginning of the form's parent
            const form = document.querySelector('form');
            if (form && form.parentNode) {
                form.parentNode.insertBefore(alertDiv, form);
            }
        }
        
        // Scroll to alert
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }
});
</script>

</main>

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
            
            <a href="/tr/profile.php" class="nav-item ">
                <i class="fas fa-user"></i>
                <span data-i18n="buttons.myAccount">Hesap</span>
            </a>
            </nav>

    
    <!-- Backdrop Overlay -->
    <div class="backdrop-overlay" id="backdropOverlay"></div>
    
<script defer src="https://static.cloudflareinsights.com/beacon.min.js/vcd15cbe7772f49c399c6a5babf22c1241717689176015" integrity="sha512-ZpsOmlRQV6y907TI0dKBHq9Md29nnaEIPlkf84rnaERnq6zvWvPUqr2ft8M1aS28oN72PdrCzSjY4U6VaAw1EQ==" data-cf-beacon="{&quot;rayId&quot;:&quot;92365620f9758869&quot;,&quot;version&quot;:&quot;2025.1.0&quot;,&quot;r&quot;:1,&quot;serverTiming&quot;:{&quot;name&quot;:{&quot;cfExtPri&quot;:true,&quot;cfL4&quot;:true,&quot;cfSpeedBrain&quot;:true,&quot;cfCacheStatus&quot;:true}},&quot;token&quot;:&quot;af7256c2312a464d847f1edbf0c05061&quot;,&quot;b&quot;:1}" crossorigin="anonymous"></script>

</body></html>