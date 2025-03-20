<?php
// mobile/profile.php - Mobile-optimized profile page
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=profile.php');
    exit;
}

// Get user details
$user_id = $_SESSION['user_id'];
$user = getUserDetails($user_id);
$vip_details = getVipDetails($user['vip_level']);

// Process profile update
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $full_name = trim($_POST['full_name']);
        $email = trim($_POST['email']);
        $trc20_address = trim($_POST['trc20_address']);
        
        // Check if email has changed
        $is_email_changed = ($email !== $user['email']);
        
        // Validation
        $errors = [];
        
        // Email validation
        if (empty($email)) {
            $errors[] = "Email address is required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Please enter a valid email address.";
        } elseif ($is_email_changed && !isEmailAvailable($email)) {
            $errors[] = "This email address is already in use.";
        }
        
        // TRC20 address validation (optional)
        if (!empty($trc20_address) && !validateTRC20Address($trc20_address)) {
            $errors[] = "Invalid TRC20 wallet address.";
        }
        
        // If no errors, update profile
        if (empty($errors)) {
            $result = updateUserProfile($user_id, $full_name, $email, $trc20_address);
            
            if ($result['success']) {
                $success_message = "Your profile has been updated successfully.";
                // Refresh user data
                $user = getUserDetails($user_id);
            } else {
                $error_message = $result['message'];
            }
        } else {
            $error_message = implode('<br>', $errors);
        }
    } elseif (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Validation
        $errors = [];
        
        // Current password check
        if (empty($current_password)) {
            $errors[] = "Current password is required.";
        } elseif (!password_verify($current_password, $user['password'])) {
            $errors[] = "Current password is incorrect.";
        }
        
        // New password validation
        if (empty($new_password)) {
            $errors[] = "New password is required.";
        } elseif (strlen($new_password) < 6) {
            $errors[] = "New password must be at least 6 characters.";
        } elseif ($new_password !== $confirm_password) {
            $errors[] = "New password and confirmation do not match.";
        }
        
        // If no errors, change password
        if (empty($errors)) {
            $result = changeUserPassword($user_id, $new_password);
            
            if ($result['success']) {
                $success_message = "Your password has been changed successfully.";
            } else {
                $error_message = $result['message'];
            }
        } else {
            $error_message = implode('<br>', $errors);
        }
    }
}

// Get last 5 referral earnings
$referral_earnings = getUserReferralEarnings($user_id, 5);

// Get referred users
$referred_users = getReferredUsers($user_id, 5);

// Page title
$page_title = 'My Profile';

// Include mobile header
include 'includes/mobile-header.php';
?>

<div class="profile-page">
    <!-- Profile Header -->
    <div class="profile-header">
        <div class="profile-avatar">
            <span class="avatar-text"><?= strtoupper(substr($user['username'], 0, 1)) ?></span>
        </div>
        <h2 class="profile-username"><?= htmlspecialchars($user['username']) ?></h2>
        <div class="profile-meta">
            <div class="meta-item">
                <i class="fas fa-trophy"></i>
                <span>VIP Level: <?= getVipLevelName($user['vip_level']) ?></span>
            </div>
            <div class="meta-item">
                <i class="fas fa-calendar-alt"></i>
                <span>Member since: <?= date('d.m.Y', strtotime($user['created_at'])) ?></span>
            </div>
        </div>
        
        <a href="packages.php?type=vip" class="btn btn-sm btn-outline-primary mt-2">
            <i class="fas fa-crown me-1"></i> <span data-i18n="profile.view_vip_packages">View VIP Packages</span>
        </a>
    </div>
    
    <!-- Profile Navigation -->
    <div class="profile-navigation">
        <button class="profile-nav-item active" data-tab="profile-info">
            <i class="fas fa-user"></i>
            <span data-i18n="profile.personal_info">Personal Info</span>
        </button>
        <button class="profile-nav-item" data-tab="password-change">
            <i class="fas fa-key"></i>
            <span data-i18n="profile.change_password">Change Password</span>
        </button>
        <button class="profile-nav-item" data-tab="referral-info">
            <i class="fas fa-users"></i>
            <span data-i18n="profile.referral_info">Referrals</span>
        </button>
    </div>
    
    <?php if ($success_message): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle me-2"></i> <?= $success_message ?>
    </div>
    <?php endif; ?>
    
    <?php if ($error_message): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle me-2"></i> <?= $error_message ?>
    </div>
    <?php endif; ?>
    
    <!-- Profile Content -->
    <div class="profile-content">
        <!-- Personal Info Tab -->
        <div class="profile-tab active" id="profile-info">
            <div class="content-card">
                <div class="card-header">
                    <h3 data-i18n="profile.profile_information">Profile Information</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="username" data-i18n="profile.username">Username</label>
                            <input type="text" id="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" readonly>
                            <small class="form-text" data-i18n="profile.username_note">Username cannot be changed</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="full_name" data-i18n="profile.full_name">Full Name</label>
                            <input type="text" id="full_name" name="full_name" class="form-control" value="<?= htmlspecialchars($user['full_name'] ?? '') ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="email" data-i18n="profile.email">Email Address</label>
                            <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="trc20_address" data-i18n="profile.trc20_address">TRC20 Wallet Address</label>
                            <input type="text" id="trc20_address" name="trc20_address" class="form-control" value="<?= htmlspecialchars($user['trc20_address'] ?? '') ?>">
                            <small class="form-text" data-i18n="profile.trc20_address_note">Used for withdrawals</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="membership_date" data-i18n="profile.membership_date">Membership Date</label>
                            <input type="text" id="membership_date" class="form-control" value="<?= date('d.m.Y H:i', strtotime($user['created_at'])) ?>" readonly>
                        </div>
                        
                        <button type="submit" name="update_profile" class="btn btn-primary btn-block">
                            <i class="fas fa-save me-2"></i> <span data-i18n="profile.save_changes">Save Changes</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Change Password Tab -->
        <div class="profile-tab" id="password-change">
            <div class="content-card">
                <div class="card-header">
                    <h3 data-i18n="profile.change_password">Change Password</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="current_password" data-i18n="profile.current_password">Current Password</label>
                            <input type="password" id="current_password" name="current_password" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="new_password" data-i18n="profile.new_password">New Password</label>
                            <input type="password" id="new_password" name="new_password" class="form-control" required>
                            <small class="form-text" data-i18n="profile.password_requirements">Must be at least 6 characters</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password" data-i18n="profile.confirm_password">Confirm New Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                        </div>
                        
                        <button type="submit" name="change_password" class="btn btn-primary btn-block">
                            <i class="fas fa-key me-2"></i> <span data-i18n="profile.update_password">Update Password</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Referral Info Tab -->
        <div class="profile-tab" id="referral-info">
            <div class="content-card">
                <div class="card-header">
                    <h3 data-i18n="profile.referral_code">Your Referral Code</h3>
                </div>
                <div class="card-body">
                    <div class="referral-code-container">
                        <label data-i18n="profile.referral_code">Your Referral Code</label>
                        <div class="referral-code-wrapper">
                            <input type="text" id="referralCode" class="form-control" value="<?= htmlspecialchars($user['referral_code']) ?>" readonly>
                            <button type="button" id="copyReferralBtn" class="copy-btn">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="referral-link-container mt-3">
                        <label data-i18n="profile.referral_link">Your Referral Link</label>
                        <div class="referral-code-wrapper">
                            <input type="text" id="referralLink" class="form-control" value="<?= SITE_URL ?>register.php?ref=<?= htmlspecialchars($user['referral_code']) ?>" readonly>
                            <button type="button" id="copyLinkBtn" class="copy-btn">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="referral-stats mt-4">
                        <div class="stats-grid">
                            <div class="stat-item">
                                <div class="stat-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="stat-value"><?= countReferredUsers($user_id) ?></div>
                                <div class="stat-label" data-i18n="profile.total_referrals">Total Referrals</div>
                            </div>
                            
                            <div class="stat-item">
                                <div class="stat-icon">
                                    <i class="fas fa-wallet"></i>
                                </div>
                                <div class="stat-value"><?= number_format($user['referral_balance'], 2) ?></div>
                                <div class="stat-label" data-i18n="profile.referral_balance">Referral Balance</div>
                            </div>
                            
                            <div class="stat-item">
                                <div class="stat-icon">
                                    <i class="fas fa-percentage"></i>
                                </div>
                                <div class="stat-value"><?= number_format($vip_details['referral_rate'] * 100, 1) ?>%</div>
                                <div class="stat-label" data-i18n="profile.commission_rate">Commission Rate</div>
                            </div>
                            
                            <div class="stat-item">
                                <div class="stat-icon">
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                                <div class="stat-value"><?= number_format(getTotalReferralEarnings($user_id), 2) ?></div>
                                <div class="stat-label" data-i18n="profile.total_earnings">Total Earnings</div>
                            </div>
                        </div>
                    </div>
                    
                    <?php if ($user['referral_balance'] > 0): ?>
                    <div class="referral-actions mt-4">
                        <a href="withdraw-referral.php" class="btn btn-primary btn-block">
                            <i class="fas fa-exchange-alt me-2"></i> <span data-i18n="profile.transfer_to_balance">Transfer to Main Balance</span>
                        </a>
                    </div>
                    <?php endif; ?>
                    
                    <div class="social-share mt-4">
                        <h4 data-i18n="profile.share_referral">Share Your Referral Link</h4>
                        <div class="social-buttons">
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(SITE_URL . 'register.php?ref=' . $user['referral_code']) ?>" target="_blank" class="social-btn facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url=<?= urlencode(SITE_URL . 'register.php?ref=' . $user['referral_code']) ?>&text=<?= urlencode('Join me on this amazing platform!') ?>" target="_blank" class="social-btn twitter">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="https://api.whatsapp.com/send?text=<?= urlencode('Join me on this platform and start earning! ' . SITE_URL . 'register.php?ref=' . $user['referral_code']) ?>" target="_blank" class="social-btn whatsapp">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                            <a href="mailto:?subject=<?= urlencode('Join me on this platform') ?>&body=<?= urlencode("Hi,\n\nI'm inviting you to join this platform using my referral link:\n\n" . SITE_URL . 'register.php?ref=' . $user['referral_code']) ?>" class="social-btn email">
                                <i class="fas fa-envelope"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Referral Information -->
            <?php if (count($referred_users) > 0 || count($referral_earnings) > 0): ?>
            <div class="referral-data mt-4">
                <div class="content-card">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#referred-users" type="button" role="tab" aria-selected="true" data-i18n="profile.referred_users">Referred Users</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#earnings-history" type="button" role="tab" aria-selected="false" data-i18n="profile.earnings_history">Earnings History</button>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="referred-users" role="tabpanel">
                            <?php if (count($referred_users) > 0): ?>
                                <div class="user-list">
                                    <?php foreach ($referred_users as $ref_user): ?>
                                        <div class="user-item">
                                            <div class="user-info">
                                                <div class="user-name"><?= htmlspecialchars($ref_user['username']) ?></div>
                                                <div class="user-date"><?= date('d.m.Y', strtotime($ref_user['created_at'])) ?></div>
                                            </div>
                                            <div class="user-status">
                                                <span class="status-badge <?= $ref_user['total_deposit'] > 0 ? 'status-active' : 'status-pending' ?>">
                                                    <?= $ref_user['total_deposit'] > 0 ? 'Active' : 'Pending' ?>
                                                </span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php if (countReferredUsers($user_id) > 5): ?>
                                    <div class="view-all-link">
                                        <a href="referrals.php" data-i18n="profile.view_all_referrals">View All Referrals</a>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="empty-state">
                                    <i class="fas fa-users"></i>
                                    <p data-i18n="profile.no_referrals">You don't have any referrals yet</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="tab-pane" id="earnings-history" role="tabpanel">
                            <?php if (count($referral_earnings) > 0): ?>
                                <div class="earnings-list">
                                    <?php foreach ($referral_earnings as $earning): ?>
                                        <div class="earning-item">
                                            <div class="earning-info">
                                                <div class="earning-amount"><?= number_format($earning['amount'], 2) ?> USDT</div>
                                                <div class="earning-desc"><?= htmlspecialchars($earning['description']) ?></div>
                                            </div>
                                            <div class="earning-date"><?= date('d.m.Y', strtotime($earning['created_at'])) ?></div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php if (count(getUserReferralEarnings($user_id, 1000)) > 5): ?>
                                    <div class="view-all-link">
                                        <a href="referral_earnings.php" data-i18n="profile.view_all_earnings">View All Earnings</a>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="empty-state">
                                    <i class="fas fa-money-bill-wave"></i>
                                    <p data-i18n="profile.no_earnings">You don't have any referral earnings yet</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- How Referrals Work -->
            <div class="content-card mt-4">
                <div class="card-header">
                    <h3 data-i18n="profile.how_referrals_work">How Referrals Work</h3>
                </div>
                <div class="card-body">
                    <div class="steps-container">
                        <div class="step">
                            <div class="step-number">1</div>
                            <div class="step-content">
                                <h4 data-i18n="profile.step1_title">Share Your Link</h4>
                                <p data-i18n="profile.step1_desc">Share your unique referral link with friends, family, or on social media</p>
                            </div>
                        </div>
                        <div class="step">
                            <div class="step-number">2</div>
                            <div class="step-content">
                                <h4 data-i18n="profile.step2_title">They Sign Up</h4>
                                <p data-i18n="profile.step2_desc">When someone clicks your link and creates an account, they become your referral</p>
                            </div>
                        </div>
                        <div class="step">
                            <div class="step-number">3</div>
                            <div class="step-content">
                                <h4 data-i18n="profile.step3_title">They Purchase</h4>
                                <p data-i18n="profile.step3_desc">When your referrals purchase a package or service, you earn commission</p>
                            </div>
                        </div>
                        <div class="step">
                            <div class="step-number">4</div>
                            <div class="step-content">
                                <h4 data-i18n="profile.step4_title">You Earn Rewards</h4>
                                <p data-i18n="profile.step4_desc">You earn <?= number_format($vip_details['referral_rate'] * 100, 1) ?>% commission on their purchases</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Account Actions -->
    <div class="account-actions">
        <a href="transactions.php" class="action-link">
            <i class="fas fa-exchange-alt"></i>
            <span data-i18n="profile.my_transactions">My Transactions</span>
        </a>
        <a href="support.php" class="action-link">
            <i class="fas fa-headset"></i>
            <span data-i18n="profile.support">Support</span>
        </a>
        <a href="logout.php" class="action-link text-danger">
            <i class="fas fa-sign-out-alt"></i>
            <span data-i18n="profile.logout">Logout</span>
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
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
    
    // Copy referral code functionality
    const copyReferralBtn = document.getElementById('copyReferralBtn');
    const referralCodeInput = document.getElementById('referralCode');
    
    if (copyReferralBtn && referralCodeInput) {
        copyReferralBtn.addEventListener('click', function() {
            referralCodeInput.select();
            document.execCommand('copy');
            
            // Show success message
            showToast('Referral code copied!', 'success');
            
            // Change button icon temporarily
            const icon = this.querySelector('i');
            icon.classList.remove('fa-copy');
            icon.classList.add('fa-check');
            
            setTimeout(() => {
                icon.classList.remove('fa-check');
                icon.classList.add('fa-copy');
            }, 2000);
        });
    }
    
    // Copy referral link functionality
    const copyLinkBtn = document.getElementById('copyLinkBtn');
    const referralLinkInput = document.getElementById('referralLink');
    
    if (copyLinkBtn && referralLinkInput) {
        copyLinkBtn.addEventListener('click', function() {
            referralLinkInput.select();
            document.execCommand('copy');
            
            // Show success message
            showToast('Referral link copied!', 'success');
            
            // Change button icon temporarily
            const icon = this.querySelector('i');
            icon.classList.remove('fa-copy');
            icon.classList.add('fa-check');
            
            setTimeout(() => {
                icon.classList.remove('fa-check');
                icon.classList.add('fa-copy');
            }, 2000);
        });
    }
    
    // Bootstrap tabs initialization
    const tabElms = document.querySelectorAll('button[data-bs-toggle="tab"]');
    tabElms.forEach(tabEl => {
        tabEl.addEventListener('click', function(event) {
            event.preventDefault();
            
            // Remove active class from all tab buttons and panes
            document.querySelectorAll('.nav-link').forEach(nav => nav.classList.remove('active'));
            document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('active'));
            
            // Add active class to clicked tab button
            this.classList.add('active');
            
            // Show corresponding tab pane
            const target = document.querySelector(this.getAttribute('data-bs-target'));
            if (target) {
                target.classList.add('active');
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
</script>

<?php
// Include mobile footer
include 'includes/mobile-footer.php';
?>