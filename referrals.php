<?php
// referrals.php - Referral system main page
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/header.php';

// Redirect to login if not logged in
if (!isLoggedIn()) {
    header('Location: login.php?redirect=referrals.php');
    exit;
}

// Get user ID from session
$userId = $_SESSION['user_id'];

// Generate referral code if not exists
$stmt = $conn->prepare("SELECT referral_code FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (empty($user['referral_code'])) {
    // Generate unique referral code
    $referralCode = generateUniqueReferralCode($conn, $userId);
    
    // Update user with new referral code
    $updateStmt = $conn->prepare("UPDATE users SET referral_code = ? WHERE id = ?");
    $updateStmt->bind_param("si", $referralCode, $userId);
    $updateStmt->execute();
} else {
    $referralCode = $user['referral_code'];
}

// Get referral statistics
$referralStats = getReferralStats($conn, $userId);

// Get referral rewards history
$rewardsHistory = getReferralRewards($conn, $userId);

// Get referred users
$referredUsers = getReferredUsers($conn, $userId);

// Function to generate unique referral code
function generateUniqueReferralCode($conn, $userId) {
    $prefix = 'REF';
    $length = 8;
    $isUnique = false;
    $code = '';
    
    while (!$isUnique) {
        // Generate random alphanumeric code
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code = $prefix;
        
        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[rand(0, strlen($characters) - 1)];
        }
        
        // Check if code is unique
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE referral_code = ?");
        $stmt->bind_param("s", $code);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        if ($row['count'] == 0) {
            $isUnique = true;
        }
    }
    
    return $code;
}

// Function to get referral statistics
function getReferralStats($conn, $userId) {
    $stats = [
        'total_referrals' => 0,
        'active_referrals' => 0,
        'total_earnings' => 0,
        'pending_earnings' => 0,
        'available_earnings' => 0
    ];
    
    // Total referrals
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE referred_by = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stats['total_referrals'] = $row['count'];
    
    // Active referrals (users who have made at least one purchase)
    $stmt = $conn->prepare("SELECT COUNT(DISTINCT u.id) as count FROM users u 
                           INNER JOIN orders o ON u.id = o.user_id 
                           WHERE u.referred_by = ? AND o.status = 'completed'");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stats['active_referrals'] = $row['count'];
    
    // Total earnings
    $stmt = $conn->prepare("SELECT SUM(amount) as total FROM referral_earnings WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stats['total_earnings'] = $row['total'] ?: 0;
    
    // Pending earnings
    $stmt = $conn->prepare("SELECT SUM(amount) as total FROM referral_earnings 
                           WHERE user_id = ? AND status = 'pending'");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stats['pending_earnings'] = $row['total'] ?: 0;
    
    // Available earnings
    $stmt = $conn->prepare("SELECT SUM(amount) as total FROM referral_earnings 
                           WHERE user_id = ? AND status = 'approved' AND is_paid = 0");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stats['available_earnings'] = $row['total'] ?: 0;
    
    return $stats;
}

// Function to get referral rewards history
function getReferralRewards($conn, $userId) {
    $rewards = [];
    
    $stmt = $conn->prepare("SELECT re.*, u.username as referred_username 
                           FROM referral_earnings re
                           LEFT JOIN users u ON re.referred_user_id = u.id
                           WHERE re.user_id = ?
                           ORDER BY re.created_at DESC");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $rewards[] = $row;
    }
    
    return $rewards;
}

// Function to get referred users
function getReferredUsers($conn, $userId) {
    $users = [];
    
    $stmt = $conn->prepare("SELECT u.id, u.username, u.email, u.created_at, 
                           (SELECT COUNT(*) FROM orders WHERE user_id = u.id AND status = 'completed') as total_orders,
                           (SELECT SUM(amount) FROM referral_earnings WHERE user_id = ? AND referred_user_id = u.id) as total_earnings
                           FROM users u
                           WHERE u.referred_by = ?
                           ORDER BY u.created_at DESC");
    $stmt->bind_param("ii", $userId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    
    return $users;
}
?>

<div class="referral-page">
    <div class="referral-hero">
        <div class="container">
            <h1 data-i18n="referrals">Referrals</h1>
            <p class="referral-subtitle" data-i18n="referral_subtitle">Invite friends and earn rewards</p>
        </div>
    </div>
    
    <div class="container">
        <div class="referral-container">
            <!-- Referral Link Section -->
            <section class="referral-section">
                <div class="referral-card">
                    <h2 data-i18n="your_referral_link">Your Referral Link</h2>
                    <p data-i18n="share_link_text">Share this link with your friends and earn rewards when they sign up and make purchases.</p>
                    
                    <div class="referral-link-container">
                        <input type="text" id="referralLink" value="<?php echo htmlspecialchars('https://' . $_SERVER['HTTP_HOST'] . '/register.php?ref=' . $referralCode); ?>" readonly>
                        <button id="copyLinkBtn" class="btn btn-primary" data-i18n="copy">Copy</button>
                    </div>
                    
                    <div class="referral-code">
                        <span data-i18n="your_code">Your code:</span>
                        <strong><?php echo htmlspecialchars($referralCode); ?></strong>
                    </div>
                    
                    <div class="social-share">
                        <p data-i18n="share_via">Share via:</p>
                        <div class="social-buttons">
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . '/register.php?ref=' . $referralCode); ?>" target="_blank" class="social-button facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . '/register.php?ref=' . $referralCode); ?>&text=<?php echo urlencode('Join me on this platform and get amazing benefits!'); ?>" target="_blank" class="social-button twitter">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="https://api.whatsapp.com/send?text=<?php echo urlencode('Join me on this platform and get amazing benefits! ' . 'https://' . $_SERVER['HTTP_HOST'] . '/register.php?ref=' . $referralCode); ?>" target="_blank" class="social-button whatsapp">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                            <a href="mailto:?subject=<?php echo urlencode('Join me on this platform'); ?>&body=<?php echo urlencode('Hey, I thought you might be interested in this platform. Sign up using my referral link: ' . 'https://' . $_SERVER['HTTP_HOST'] . '/register.php?ref=' . $referralCode); ?>" class="social-button email">
                                <i class="fas fa-envelope"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- Referral Stats Section -->
            <section class="referral-section">
                <div class="referral-card">
                    <h2 data-i18n="referral_stats">Referral Statistics</h2>
                    
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-info">
                                <span class="stat-value"><?php echo $referralStats['total_referrals']; ?></span>
                                <span class="stat-label" data-i18n="total_referrals">Total Referrals</span>
                            </div>
                        </div>
                        
                        <div class="stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-user-check"></i>
                            </div>
                            <div class="stat-info">
                                <span class="stat-value"><?php echo $referralStats['active_referrals']; ?></span>
                                <span class="stat-label" data-i18n="active_referrals">Active Referrals</span>
                            </div>
                        </div>
                        
                        <div class="stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-sack-dollar"></i>
                            </div>
                            <div class="stat-info">
                                <span class="stat-value"><?php echo number_format($referralStats['total_earnings'], 2); ?> USD</span>
                                <span class="stat-label" data-i18n="total_earnings">Total Earnings</span>
                            </div>
                        </div>
                        
                        <div class="stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <div class="stat-info">
                                <span class="stat-value"><?php echo number_format($referralStats['available_earnings'], 2); ?> USD</span>
                                <span class="stat-label" data-i18n="available_earnings">Available Earnings</span>
                            </div>
                        </div>
                    </div>
                    
                    <?php if ($referralStats['available_earnings'] > 0): ?>
                    <div class="withdraw-section">
                        <a href="withdraw-referral.php" class="btn btn-success" data-i18n="withdraw_earnings">Withdraw Earnings</a>
                    </div>
                    <?php endif; ?>
                </div>
            </section>
            
            <!-- Referral Program Details -->
            <section class="referral-section">
                <div class="referral-card">
                    <h2 data-i18n="how_it_works">How It Works</h2>
                    
                    <div class="steps-container">
                        <div class="step">
                            <div class="step-icon">
                                <i class="fas fa-link"></i>
                            </div>
                            <div class="step-content">
                                <h3 data-i18n="step_1_share">1. Share Your Link</h3>
                                <p data-i18n="step_1_text">Share your unique referral link with friends, family, or on social media.</p>
                            </div>
                        </div>
                        
                        <div class="step">
                            <div class="step-icon">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div class="step-content">
                                <h3 data-i18n="step_2_signup">2. They Sign Up</h3>
                                <p data-i18n="step_2_text">When someone clicks your link and creates an account, they become your referral.</p>
                            </div>
                        </div>
                        
                        <div class="step">
                            <div class="step-icon">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <div class="step-content">
                                <h3 data-i18n="step_3_purchase">3. They Make a Purchase</h3>
                                <p data-i18n="step_3_text">When your referral buys any package or service, you earn a commission.</p>
                            </div>
                        </div>
                        
                        <div class="step">
                            <div class="step-icon">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <div class="step-content">
                                <h3 data-i18n="step_4_earn">4. Earn Rewards</h3>
                                <p data-i18n="step_4_text">You earn 10% commission on their first purchase and 5% on all future purchases.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- Referred Users Section -->
            <?php if (!empty($referredUsers)): ?>
            <section class="referral-section">
                <div class="referral-card">
                    <h2 data-i18n="your_referrals">Your Referrals</h2>
                    
                    <div class="table-responsive">
                        <table class="referral-table">
                            <thead>
                                <tr>
                                    <th data-i18n="username">Username</th>
                                    <th data-i18n="joined_date">Joined Date</th>
                                    <th data-i18n="orders">Orders</th>
                                    <th data-i18n="earnings">Earnings</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($referredUsers as $user): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                    <td><?php echo $user['total_orders']; ?></td>
                                    <td><?php echo number_format($user['total_earnings'] ?: 0, 2); ?> USD</td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
            <?php endif; ?>
            
            <!-- Earnings History Section -->
            <?php if (!empty($rewardsHistory)): ?>
            <section class="referral-section">
                <div class="referral-card">
                    <h2 data-i18n="earnings_history">Earnings History</h2>
                    
                    <div class="table-responsive">
                        <table class="referral-table">
                            <thead>
                                <tr>
                                    <th data-i18n="date">Date</th>
                                    <th data-i18n="referral">Referral</th>
                                    <th data-i18n="amount">Amount</th>
                                    <th data-i18n="status">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rewardsHistory as $reward): ?>
                                <tr>
                                    <td><?php echo date('M d, Y', strtotime($reward['created_at'])); ?></td>
                                    <td><?php echo htmlspecialchars($reward['referred_username']); ?></td>
                                    <td><?php echo number_format($reward['amount'], 2); ?> USD</td>
                                    <td>
                                        <span class="status-badge status-<?php echo strtolower($reward['status']); ?>">
                                            <?php echo ucfirst($reward['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Copy referral link functionality
    const copyLinkBtn = document.getElementById('copyLinkBtn');
    const referralLink = document.getElementById('referralLink');
    
    if (copyLinkBtn && referralLink) {
        copyLinkBtn.addEventListener('click', function() {
            referralLink.select();
            document.execCommand('copy');
            
            // Change button text temporarily
            const originalText = this.textContent;
            this.textContent = 'Copied!';
            
            setTimeout(() => {
                this.textContent = originalText;
            }, 2000);
        });
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>