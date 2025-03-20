<?php
// mobile/referrals.php - Referral system mobile page
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=referrals.php');
    exit;
}

// Get user and connection details
$user_id = $_SESSION['user_id'];
$user = getUserDetails($user_id);
$conn = $GLOBALS['db']->getConnection();

// Get VIP details
$vip_details = getVipDetails($user['vip_level']);

// Check if user exists
if (!$user) {
    $page_title = "Error";
    include 'includes/mobile-header.php';
    echo '<div class="alert alert-danger">User not found. Please login again.</div>';
    echo '<a href="logout.php" class="btn btn-primary">Logout</a>';
    include 'includes/mobile-footer.php';
    exit;
}

// Check referral code and generate if needed
$referralCode = $user['referral_code'];
if (empty($referralCode)) {
    $referralCode = generateReferralCode();
    
    // Check if code is unique
    $isUnique = false;
    while (!$isUnique) {
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE referral_code = ?");
        if ($stmt) {
            $stmt->bind_param("s", $referralCode);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            
            if ($row['count'] == 0) {
                $isUnique = true;
            } else {
                $referralCode = generateReferralCode();
            }
        } else {
            break;
        }
    }
    
    // Update user with new code
    $updateStmt = $conn->prepare("UPDATE users SET referral_code = ? WHERE id = ?");
    if ($updateStmt) {
        $updateStmt->bind_param("si", $referralCode, $user_id);
        $updateStmt->execute();
    }
}

// Get referral statistics
$referralStats = [
    'total_referrals' => 0,
    'active_referrals' => 0,
    'total_earnings' => 0,
    'pending_earnings' => 0,
    'available_earnings' => 0
];

// Check required tables
$tablesExist = true;
$requiredTables = ['referral_earnings', 'orders'];

foreach ($requiredTables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result->num_rows === 0) {
        $tablesExist = false;
    }
}

// Total referrals count
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE referrer_id = ?");
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $referralStats['total_referrals'] = $row['count'];
}

// Get other statistics if tables exist
if ($tablesExist) {
    // Active referrals (users who made at least one purchase)
    $stmt = $conn->prepare("SELECT COUNT(DISTINCT u.id) as count FROM users u 
                           INNER JOIN orders o ON u.id = o.user_id 
                           WHERE u.referrer_id = ? AND o.status = 'completed'");
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $referralStats['active_referrals'] = $row['count'];
    }
    
    // Total earnings
    $stmt = $conn->prepare("SELECT SUM(amount) as total FROM referral_earnings WHERE user_id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $referralStats['total_earnings'] = $row['total'] ?: 0;
    }
    
    // Pending earnings
    $stmt = $conn->prepare("SELECT SUM(amount) as total FROM referral_earnings 
                           WHERE user_id = ? AND status = 'pending'");
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $referralStats['pending_earnings'] = $row['total'] ?: 0;
    }
    
    // Available earnings
    $stmt = $conn->prepare("SELECT SUM(amount) as total FROM referral_earnings 
                           WHERE user_id = ? AND status = 'approved' AND is_paid = 0");
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $referralStats['available_earnings'] = $row['total'] ?: 0;
    }
}

// Get referred users
$referredUsers = [];
if ($tablesExist) {
    $stmt = $conn->prepare("SELECT u.id, u.username, u.email, u.created_at, 
                         (SELECT COUNT(*) FROM orders WHERE user_id = u.id AND status = 'completed') as total_orders,
                         (SELECT SUM(amount) FROM referral_earnings WHERE user_id = ? AND referred_user_id = u.id) as total_earnings
                         FROM users u
                         WHERE u.referrer_id = ?
                         ORDER BY u.created_at DESC");
    if ($stmt) {
        $stmt->bind_param("ii", $user_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $referredUsers[] = $row;
        }
    }
}

// Get reward history
$rewardsHistory = [];
if ($tablesExist) {
    $stmt = $conn->prepare("SELECT re.*, u.username as referred_username 
                         FROM referral_earnings re
                         LEFT JOIN users u ON re.referred_user_id = u.id
                         WHERE re.user_id = ?
                         ORDER BY re.created_at DESC
                         LIMIT 10");
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $rewardsHistory[] = $row;
        }
    }
}

$page_title = "My Referrals";
include 'includes/mobile-header.php';
?>

<div class="referral-page">
    <!-- Referral Link Section -->
    <div class="section-container">
        <div class="referral-card">
            <h1 class="page-title" data-i18n="referrals.title">My Referrals</h1>
            <p class="page-subtitle" data-i18n="referrals.subtitle">Invite friends and earn rewards</p>
            
            <div class="card referral-link-card">
                <h2 data-i18n="referrals.your_referral_link">Your Referral Link</h2>
                <p data-i18n="referrals.share_link_text">Share this link with your friends and earn commissions when they sign up and make purchases.</p>
                
                <div class="referral-link-container">
                    <input type="text" id="referralLink" value="<?php echo htmlspecialchars('https://' . $_SERVER['HTTP_HOST'] . '/register.php?ref=' . $referralCode); ?>" readonly>
                    <button id="copyLinkBtn" class="btn">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
                
                <div class="referral-code">
                    <span data-i18n="referrals.your_code">Your Code:</span>
                    <strong><?php echo htmlspecialchars($referralCode); ?></strong>
                </div>
                
                <div class="social-share">
                    <p data-i18n="referrals.share_via">Share via:</p>
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
                        <a href="mailto:?subject=<?php echo urlencode('Join me on this platform'); ?>&body=<?php echo urlencode('Hello, I want to share this platform with you. Use my referral link to sign up: ' . 'https://' . $_SERVER['HTTP_HOST'] . '/register.php?ref=' . $referralCode); ?>" class="social-button email">
                            <i class="fas fa-envelope"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stats Section -->
    <div class="section-container">
        <div class="card stats-card">
            <h2 data-i18n="referrals.referral_stats">Referral Statistics</h2>
            
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <span class="stat-value"><?php echo $referralStats['total_referrals']; ?></span>
                        <span class="stat-label" data-i18n="referrals.total_referrals">Total Referrals</span>
                    </div>
                </div>
                
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="stat-info">
                        <span class="stat-value"><?php echo $referralStats['active_referrals']; ?></span>
                        <span class="stat-label" data-i18n="referrals.active_referrals">Active Referrals</span>
                    </div>
                </div>
                
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-sack-dollar"></i>
                    </div>
                    <div class="stat-info">
                        <span class="stat-value"><?php echo number_format($referralStats['total_earnings'], 2); ?> USD</span>
                        <span class="stat-label" data-i18n="referrals.total_earnings">Total Earnings</span>
                    </div>
                </div>
                
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div class="stat-info">
                        <span class="stat-value"><?php echo number_format($referralStats['available_earnings'], 2); ?> USD</span>
                        <span class="stat-label" data-i18n="referrals.available_earnings">Available Earnings</span>
                    </div>
                </div>
            </div>
            
            <?php if ($tablesExist && $referralStats['available_earnings'] > 0): ?>
            <div class="withdraw-section">
                <a href="withdraw-referral.php" class="btn btn-success">
                    <i class="fas fa-exchange-alt me-2"></i>
                    <span data-i18n="referrals.withdraw_earnings">Transfer to Balance</span>
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- How It Works Section -->
    <div class="section-container">
        <div class="card how-it-works-card">
            <h2 data-i18n="referrals.how_it_works">How It Works</h2>
            
            <div class="steps-container">
                <div class="step">
                    <div class="step-icon">
                        <i class="fas fa-link"></i>
                    </div>
                    <div class="step-content">
                        <h3 data-i18n="referrals.step_1_share">1. Share Your Link</h3>
                        <p data-i18n="referrals.step_1_text">Share your unique referral link with friends, family, or on social media.</p>
                    </div>
                </div>
                
                <div class="step">
                    <div class="step-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="step-content">
                        <h3 data-i18n="referrals.step_2_signup">2. They Sign Up</h3>
                        <p data-i18n="referrals.step_2_text">When someone clicks your link and creates an account, they become your referral.</p>
                    </div>
                </div>
                
                <div class="step">
                    <div class="step-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="step-content">
                        <h3 data-i18n="referrals.step_3_purchase">3. They Make a Purchase</h3>
                        <p data-i18n="referrals.step_3_text">When your referral purchases any package or service, you earn commission.</p>
                    </div>
                </div>
                
                <div class="step">
                    <div class="step-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="step-content">
                        <h3 data-i18n="referrals.step_4_earn">4. Earn Rewards</h3>
                        <p data-i18n="referrals.step_4_text">You earn 10% commission on their first purchase and 5% on all subsequent purchases.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Referral Tree Section - Yeni Tasarım -->
<div class="section-container">
    <div class="card referral-tree-card">
        <h2 data-i18n="referrals.referral_tree">Your Referral Network</h2>
        
        <?php
        // Get all referrals to build the tree
        $referral_tree = [];
        $level1_referrals = [];
        
        // Get level 1 referrals (direct)
        $stmt = $conn->prepare("SELECT id, username, SUBSTRING(username, 1, 1) as initial, created_at, 
                               (SELECT COUNT(*) FROM users WHERE referrer_id = u.id) as referred_count
                               FROM users u WHERE referrer_id = ? ORDER BY created_at DESC");
        if ($stmt) {
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                // Initialize children array for level 1
                $row['children'] = [];
                
                // Get level 2 referrals
                $level2_referrals = [];
                $stmt2 = $conn->prepare("SELECT id, username, SUBSTRING(username, 1, 1) as initial, created_at, 
                                       (SELECT COUNT(*) FROM users WHERE referrer_id = u.id) as referred_count
                                       FROM users u WHERE referrer_id = ? ORDER BY created_at DESC LIMIT 5");
                $stmt2->bind_param('i', $row['id']);
                $stmt2->execute();
                $result2 = $stmt2->get_result();
                
                while ($row2 = $result2->fetch_assoc()) {
                    // Initialize children array for level 2
                    $row2['children'] = [];
                    
                    // Get level 3 referrals
                    $level3_referrals = [];
                    $stmt3 = $conn->prepare("SELECT id, username, SUBSTRING(username, 1, 1) as initial, created_at
                                           FROM users WHERE referrer_id = ? ORDER BY created_at DESC LIMIT 3");
                    $stmt3->bind_param('i', $row2['id']);
                    $stmt3->execute();
                    $result3 = $stmt3->get_result();
                    
                    while ($row3 = $result3->fetch_assoc()) {
                        $level3_referrals[] = $row3;
                    }
                    
                    $row2['children'] = $level3_referrals;
                    $level2_referrals[] = $row2;
                }
                
                $row['children'] = $level2_referrals;
                $level1_referrals[] = $row;
            }
        }
        
        $total_level1 = count($level1_referrals);
        $total_level2 = 0;
        $total_level3 = 0;
        
        foreach ($level1_referrals as $l1) {
            if (isset($l1['children']) && is_array($l1['children'])) {
                $total_level2 += count($l1['children']);
                
                foreach ($l1['children'] as $l2) {
                    if (isset($l2['children']) && is_array($l2['children'])) {
                        $total_level3 += count($l2['children']);
                    }
                }
            }
        }
        
        $total_network = $total_level1 + $total_level2 + $total_level3;
        ?>
        
        <?php if ($total_network > 0): ?>
            <!-- Referral Ağacı Özeti -->
            <div class="referral-network-summary">
                <div class="network-stat">
                    <div class="network-counter"><?= $total_network ?></div>
                    <span class="network-label">Total Network</span>
                </div>
                
                <div class="network-levels">
                    <div class="network-level level-1">
                        <div class="level-icon">1</div>
                        <div class="level-details">
                            <span class="level-value"><?= $total_level1 ?></span>
                            <span class="level-label">Level 1</span>
                        </div>
                    </div>
                    <div class="network-level level-2">
                        <div class="level-icon">2</div>
                        <div class="level-details">
                            <span class="level-value"><?= $total_level2 ?></span>
                            <span class="level-label">Level 2</span>
                        </div>
                    </div>
                    <div class="network-level level-3">
                        <div class="level-icon">3</div>
                        <div class="level-details">
                            <span class="level-value"><?= $total_level3 ?></span>
                            <span class="level-label">Level 3</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Yenilenmiş Ağaç Görünümü -->
            <div class="modern-tree-container">
                <!-- Ana Kullanıcı -->
                <div class="modern-tree-root">
                    <div class="modern-tree-node root-node">
                        
                        <div class="node-info">
                            <span class="node-name">YOU</span>
                        </div>
                        <div class="node-count">
                            <span><?= $total_network ?></span>
                        </div>
                    </div>
                    
                    <!-- Bağlantı çizgisi -->
                    <div class="connector-line"></div>
                </div>
                
                <!-- Referans Seviyeleri -->
                <div class="levels-container">
                    <!-- Seviye 1 -->
                    <div class="referral-level level-1">
                        <h3 class="level-title">Level 1 <span class="level-count"><?= $total_level1 ?></span></h3>
                        <div class="level-items">
                            <?php
                            $showCount = min(4, $total_level1);
                            for ($i = 0; $i < $showCount; $i++):
                                if (isset($level1_referrals[$i])):
                                    $l1 = $level1_referrals[$i];
                                    $hasChildren = !empty($l1['children']);
                            ?>
                            <div class="level-item" id="level1-<?= $l1['id'] ?>">
                                <div class="item-content" onclick="toggleLevelItem('level1-<?= $l1['id'] ?>')">
                                    <div class="item-avatar level1-avatar">
                                        <span><?= $l1['initial'] ?></span>
                                    </div>
                                    <div class="item-details">
                                        <span class="item-name"><?= htmlspecialchars($l1['username']) ?></span>
                                        <span class="item-date"><?= date('d.m.Y', strtotime($l1['created_at'])) ?></span>
                                    </div>
                                    <?php if ($hasChildren): ?>
                                    <div class="item-expand">
                                        <i class="fas fa-chevron-down"></i>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if ($hasChildren): ?>
                                <div class="sub-levels">
                                    <div class="sub-level-items level2-items">
                                        <?php
                                        $subShowCount = min(3, count($l1['children']));
                                        for ($j = 0; $j < $subShowCount; $j++):
                                            $l2 = $l1['children'][$j];
                                            $hasGrandchildren = !empty($l2['children']);
                                        ?>
                                        <div class="sub-level-item" id="level2-<?= $l2['id'] ?>">
                                            <div class="item-content" onclick="toggleLevelItem('level2-<?= $l2['id'] ?>')">
                                                <div class="item-avatar level2-avatar">
                                                    <span><?= $l2['initial'] ?></span>
                                                </div>
                                                <div class="item-details">
                                                    <span class="item-name"><?= htmlspecialchars($l2['username']) ?></span>
                                                    <span class="item-date"><?= date('d.m.Y', strtotime($l2['created_at'])) ?></span>
                                                </div>
                                                <?php if ($hasGrandchildren): ?>
                                                <div class="item-expand">
                                                    <i class="fas fa-chevron-down"></i>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <?php if ($hasGrandchildren): ?>
                                            <div class="sub-levels">
                                                <div class="sub-level-items level3-items">
                                                    <?php foreach ($l2['children'] as $l3): ?>
                                                    <div class="sub-level-item">
                                                        <div class="item-content">
                                                            <div class="item-avatar level3-avatar">
                                                                <span><?= $l3['initial'] ?></span>
                                                            </div>
                                                            <div class="item-details">
                                                                <span class="item-name"><?= htmlspecialchars($l3['username']) ?></span>
                                                                <span class="item-date"><?= date('d.m.Y', strtotime($l3['created_at'])) ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                        <?php endfor; ?>
                                        
                                        <?php if (count($l1['children']) > $subShowCount): ?>
                                        <div class="view-more-button">
                                            <button onclick="viewMoreMembers(<?= $l1['id'] ?>, 2)">
                                                +<?= count($l1['children']) - $subShowCount ?> more
                                            </button>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php
                                endif;
                            endfor;
                            
                            if ($total_level1 > $showCount):
                            ?>
                            <div class="view-more-button">
                                <button onclick="viewMoreMembers(0, 1)">
                                    +<?= $total_level1 - $showCount ?> more
                                </button>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Seviye 2 Özeti -->
                    <div class="level-summary level-2">
                        <div class="summary-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="summary-content">
                            <span class="summary-title">Level 2 Referrals</span>
                            <span class="summary-count"><?= $total_level2 ?> members</span>
                        </div>
                    </div>
                    
                    <!-- Seviye 3 Özeti -->
                    <div class="level-summary level-3">
                        <div class="summary-icon">
                            <i class="fas fa-user-friends"></i>
                        </div>
                        <div class="summary-content">
                            <span class="summary-title">Level 3 Referrals</span>
                            <span class="summary-count"><?= $total_level3 ?> members</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Members Modal -->
            <div id="membersDetailModal" class="members-modal">
                <div class="members-modal-content">
                    <div class="members-modal-header">
                        <h3 id="membersModalTitle">Team Members</h3>
                        <span class="close-modal" onclick="closeMembersModal()">&times;</span>
                    </div>
                    <div class="members-modal-body" id="membersModalBody">
                        <!-- Will be populated via JavaScript -->
                    </div>
                </div>
            </div>
            
        <?php else: ?>
            <!-- Boş Referans Ağacı -->
            <div class="empty-network">
                <div class="empty-network-icon">
                    <i class="fas fa-users-slash"></i>
                </div>
                <h3>No Referrals Yet</h3>
                <p>Share your referral link to start building your network and earn rewards!</p>
                <button class="share-link-btn" onclick="scrollToReferralLink()">
                    <i class="fas fa-share-alt"></i> Share Your Link
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
/* -- Yeni Referans Ağacı Stilleri -- */
.referral-tree-card {
    background-color: #fff;
    border-radius: 16px;
    box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
    padding: 0;
    overflow: hidden;
}

.referral-tree-card h2 {
    padding: 20px;
    margin: 0;
    font-size: 18px;
    border-bottom: 1px solid #f0f0f0;
}

/* Ağaç Özeti */
.referral-network-summary {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 20px;
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: space-between;
    align-items: center;
}

.network-stat {
    flex: 1;
    min-width: 120px;
    text-align: center;
}

.network-counter {
    font-size: 42px;
    font-weight: 700;
    color: #3F88F6;
    line-height: 1;
    margin-bottom: 5px;
    text-shadow: 0 2px 10px rgba(63, 136, 246, 0.2);
}

.network-label {
    font-size: 14px;
    color: #6c757d;
    font-weight: 500;
}

.network-levels {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    flex: 2;
    justify-content: flex-end;
}

.network-level {
    display: flex;
    align-items: center;
    background: white;
    border-radius: 12px;
    padding: 10px 15px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.level-icon {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 14px;
    margin-right: 10px;
    color: white;
}

.network-level.level-1 .level-icon {
    background-color: #3F88F6;
}

.network-level.level-2 .level-icon {
    background-color: #FF9F43;
}

.network-level.level-3 .level-icon {
    background-color: #28C76F;
}

.level-details {
    display: flex;
    flex-direction: column;
}

.level-value {
    font-size: 18px;
    font-weight: 600;
    line-height: 1.1;
}

.level-label {
    font-size: 12px;
    color: #6c757d;
}

/* Modern Ağaç Görünümü */
.modern-tree-container {
    padding: 20px;
}

.modern-tree-root {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-bottom: 30px;
}

.modern-tree-node {
    display: flex;
    align-items: center;
    padding: 15px 20px;
    background: linear-gradient(135deg, #7367F0, #4839EB);
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(115, 103, 240, 0.3);
    color: white;
    position: relative;
    min-width: 200px;
}

.node-avatar {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    font-weight: 700;
    margin-right: 12px;
}

.node-info {
    flex: 1;
}

.node-name {
    font-size: 16px;
    font-weight: 600;
}

.node-count {
    background: rgba(255, 255, 255, 0.2);
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
}

.connector-line {
    width: 2px;
    height: 30px;
    background-color: #cfd8dc;
    margin: 5px 0;
}

/* Referans Seviyeleri */
.levels-container {
    border-radius: 12px;
    overflow: hidden;
}

.referral-level {
    margin-bottom: 20px;
}

.level-title {
    font-size: 16px;
    font-weight: 600;
    margin: 0 0 15px 0;
    color: #495057;
    display: flex;
    align-items: center;
}

.level-count {
    margin-left: 10px;
    background: #e9ecef;
    padding: 2px 8px;
    border-radius: 20px;
    font-size: 12px;
    color: #6c757d;
}

.level-items {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.level-item, .sub-level-item {
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
}

.level-item.expanded, .sub-level-item.expanded {
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.item-content {
    display: flex;
    align-items: center;
    padding: 15px;
    background: white;
    cursor: pointer;
    transition: background 0.2s ease;
    position: relative;
}

.item-content:hover {
    background: #f8f9fa;
}

.item-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    font-weight: 600;
    color: white;
    margin-right: 12px;
}

.level1-avatar {
    background: #3F88F6;
}

.level2-avatar {
    background: #FF9F43;
}

.level3-avatar {
    background: #28C76F;
}

.item-details {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.item-name {
    font-size: 14px;
    font-weight: 600;
    color: #343a40;
    margin-bottom: 2px;
}

.item-date {
    font-size: 12px;
    color: #6c757d;
}

.item-expand {
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
    transition: transform 0.3s ease;
}

.level-item.expanded .item-expand, 
.sub-level-item.expanded .item-expand {
    transform: rotate(180deg);
}

.sub-levels {
    padding: 0 15px 15px 40px;
    background: white;
    display: none;
    border-top: 1px solid #f0f0f0;
}

.level-item.expanded .sub-levels,
.sub-level-item.expanded .sub-levels {
    display: block;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.sub-level-items {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-top: 10px;
}

.level2-items .sub-level-item {
    border-left: 2px solid #FF9F43;
}

.level3-items .sub-level-item {
    border-left: 2px solid #28C76F;
}

.view-more-button {
    margin-top: 10px;
    text-align: center;
}

.view-more-button button {
    background: #f1f2f5;
    border: none;
    padding: 8px 15px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
    color: #6c757d;
    cursor: pointer;
    transition: all 0.2s ease;
}

.view-more-button button:hover {
    background: #e9ecef;
    color: #343a40;
}

/* Seviye Özetleri */
.level-summary {
    display: flex;
    align-items: center;
    padding: 15px;
    border-radius: 12px;
    margin-bottom: 15px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.level-summary.level-2 {
    background: linear-gradient(to right, #fff5e6, #fff);
    border-left: 3px solid #FF9F43;
}

.level-summary.level-3 {
    background: linear-gradient(to right, #e6f9f0, #fff);
    border-left: 3px solid #28C76F;
}

.summary-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    margin-right: 15px;
}

.level-2 .summary-icon {
    background: rgba(255, 159, 67, 0.1);
    color: #FF9F43;
}

.level-3 .summary-icon {
    background: rgba(40, 199, 111, 0.1);
    color: #28C76F;
}

.summary-content {
    display: flex;
    flex-direction: column;
}

.summary-title {
    font-size: 14px;
    font-weight: 600;
    color: #343a40;
}

.summary-count {
    font-size: 13px;
    color: #6c757d;
}

/* Boş Ağaç */
.empty-network {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 40px 20px;
    text-align: center;
}

.empty-network-icon {
    font-size: 60px;
    color: #e9ecef;
    margin-bottom: 20px;
}

.empty-network h3 {
    font-size: 18px;
    font-weight: 600;
    margin: 0 0 10px;
    color: #343a40;
}

.empty-network p {
    font-size: 14px;
    color: #6c757d;
    margin: 0 0 20px;
    max-width: 280px;
}

.share-link-btn {
    background: #3F88F6;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.2s ease;
}

.share-link-btn:hover {
    background: #2d6ecd;
}

/* Modal Stilleri */
.members-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    align-items: center;
    justify-content: center;
}

.members-modal-content {
    background: white;
    border-radius: 16px;
    width: 90%;
    max-width: 400px;
    max-height: 85vh;
    overflow: hidden;
    box-shadow: 0 5px 25px rgba(0, 0, 0, 0.2);
    animation: modalSlideIn 0.3s forwards;
}

@keyframes modalSlideIn {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}

.members-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    border-bottom: 1px solid #f0f0f0;
}

.members-modal-header h3 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
}

.close-modal {
    font-size: 22px;
    color: #6c757d;
    cursor: pointer;
}

.members-modal-body {
    padding: 20px;
    overflow-y: auto;
    max-height: calc(85vh - 60px);
}

.modal-member-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.modal-member-item {
    display: flex;
    align-items: center;
    padding: 10px;
    border-radius: 10px;
    background: #f8f9fa;
    transition: background 0.2s ease;
}

.modal-member-item:hover {
    background: #f1f3f5;
}

/* Duyarlı Ayarlamalar */
@media (max-width: 768px) {
    .network-levels {
        justify-content: space-around;
        flex: 1 0 100%;
        margin-top: 10px;
    }
    
    .modern-tree-node {
        min-width: 180px;
    }
}

@media (max-width: 480px) {
    .network-level {
        flex: 1;
        min-width: 90px;
    }
    
    .level-icon {
        width: 24px;
        height: 24px;
        font-size: 12px;
    }
    
    .level-value {
        font-size: 16px;
    }
    
    .modern-tree-node {
        min-width: 160px;
    }
}
</style>

<script>
// Referans ağacı için JavaScript
function toggleLevelItem(itemId) {
    const item = document.getElementById(itemId);
    if (item) {
        item.classList.toggle('expanded');
    }
}

function viewMoreMembers(parentId, level) {
    const modal = document.getElementById('membersDetailModal');
    const modalTitle = document.getElementById('membersModalTitle');
    const modalBody = document.getElementById('membersModalBody');
    
    // Set title based on level
    if (level === 1) {
        modalTitle.textContent = 'Your Level 1 Referrals';
    } else if (level === 2) {
        // Find the parent username
        const parentItem = document.querySelector(`.level-item#level1-${parentId} .item-name`);
        if (parentItem) {
            modalTitle.textContent = `${parentItem.textContent}'s Referrals`;
        } else {
            modalTitle.textContent = 'Level 2 Referrals';
        }
    }
    
    // Show loading
    modalBody.innerHTML = '<div class="loading-spinner"><div class="spinner"></div><p>Loading members...</p></div>';
    
    // Show modal
    modal.style.display = 'flex';
    
    // Fetch members data
    fetch(`get-referrals.php?parent_id=${parentId}&level=${level}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success' && data.members && data.members.length > 0) {
                let html = '<div class="modal-member-list">';
                
                data.members.forEach(member => {
                    const avatarClass = level === 1 ? 'level1-avatar' : 'level2-avatar';
                    html += `
                        <div class="modal-member-item">
                            <div class="item-avatar ${avatarClass}">
                                <span>${member.username.charAt(0)}</span>
                            </div>
                            <div class="item-details">
                                <span class="item-name">${member.username}</span>
                                <span class="item-date">${member.joined_date}</span>
                            </div>
                        </div>
                    `;
                });
                
                html += '</div>';
                modalBody.innerHTML = html;
            } else {
                modalBody.innerHTML = '<div class="empty-result"><p>No members found.</p></div>';
            }
        })
        .catch(error => {
            console.error('Error fetching members:', error);
            modalBody.innerHTML = '<div class="empty-result"><p>An error occurred while loading members. Please try again later.</p></div>';
        });
}

function closeMembersModal() {
    const modal = document.getElementById('membersDetailModal');
    modal.style.display = 'none';
}

// Close modal when clicking outside of it
window.addEventListener('click', function(event) {
    const modal = document.getElementById('membersDetailModal');
    if (event.target === modal) {
        closeMembersModal();
    }
});

// Referral link'e kaydırma fonksiyonu
function scrollToReferralLink() {
    const referralSection = document.querySelector('.referral-link-section');
    if (referralSection) {
        referralSection.scrollIntoView({ behavior: 'smooth' });
    }
}

// Sayfa yüklendiğinde tüm ağaçları kapat
document.addEventListener('DOMContentLoaded', function() {
    // Eğer sayfa bir URL hash ile yüklendiyse ve o elementler mevcutsa açık olarak başlat
    if (window.location.hash) {
        const targetId = window.location.hash.substring(1);
        const targetElement = document.getElementById(targetId);
        if (targetElement) {
            targetElement.classList.add('expanded');
            
            // Eğer bu bir alt seviyedeyse, üst seviyesini de aç
            const parentLevel = targetElement.closest('.level-item');
            if (parentLevel) {
                parentLevel.classList.add('expanded');
            }
            
            // Elementin görünür olduğundan emin ol
            setTimeout(() => {
                targetElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 300);
        }
    }
});
</script>

    
    <!-- Earnings History Section -->
    <?php if (!empty($rewardsHistory)): ?>
    <div class="section-container">
        <div class="card earnings-history-card">
            <h2 data-i18n="referrals.earnings_history">Earnings History</h2>
            
            <div class="table-container">
                <table class="referral-table">
                    <thead>
                        <tr>
                            <th data-i18n="referrals.date">Date</th>
                            <th data-i18n="referrals.referral">Referral</th>
                            <th data-i18n="referrals.amount">Amount</th>
                            <th data-i18n="referrals.status">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rewardsHistory as $reward): ?>
                        <tr>
                            <td><?php echo date('d.m.Y', strtotime($reward['created_at'])); ?></td>
                            <td><?php echo htmlspecialchars($reward['referred_username']); ?></td>
                            <td><?php echo number_format($reward['amount'], 2); ?> USD</td>
                            <td>
                                <span class="status-badge status-<?php echo strtolower($reward['status']); ?>">
                                    <?php 
                                    $status = strtolower($reward['status']);
                                    if ($status == 'pending') echo '<span data-i18n="referrals.pending">Pending</span>';
                                    elseif ($status == 'approved') echo '<span data-i18n="referrals.approved">Approved</span>';
                                    elseif ($status == 'rejected') echo '<span data-i18n="referrals.rejected">Rejected</span>';
                                    elseif ($status == 'paid') echo '<span data-i18n="referrals.paid">Paid</span>';
                                    else echo ucfirst($status);
                                    ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- CSS for Mobile Referrals Page -->
<style>
.referral-page {
    padding: 15px;
}

.section-container {
    margin-bottom: 20px;
}

.card {
    background-color: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.page-title {
    font-size: 1.8rem;
    font-weight: 700;
    margin: 0 0 5px;
    color: #000;
}

.page-subtitle {
    font-size: 1rem;
    color: #000;
    margin: 0 0 20px;
}

.referral-link-card h2,
.stats-card h2,
.how-it-works-card h2,
.referred-users-card h2,
.earnings-history-card h2 {
    font-size: 1.2rem;
    font-weight: 600;
    margin: 0 0 15px;
}

.referral-link-container {
    display: flex;
    margin-bottom: 15px;
    position: relative;
}

#referralLink {
    flex: 1;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 8px 0 0 8px;
    font-size: 0.9rem;
    color: #333;
    background-color: #f8f9fa;
}

#copyLinkBtn {
    background-color: var(--primary-color);
    color: white;
    border: none;
    border-radius: 0 8px 8px 0;
    padding: 0 15px;
    cursor: pointer;
    transition: background-color 0.3s;
}

#copyLinkBtn:hover {
    background-color: #5e50ee;
}

.referral-code {
    margin-bottom: 20px;
    padding: 10px 15px;
    background-color: #f8f9fa;
    border-radius: 8px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.referral-code strong {
    font-size: 1.1rem;
    color: var(--primary-color);
}

.social-share p {
    margin-bottom: 10px;
    font-weight: 500;
}

.social-buttons {
    display: flex;
    gap: 10px;
}

.social-button {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    text-decoration: none;
    transition: transform 0.3s;
}

.social-button:active {
    transform: scale(0.95);
}

.facebook {
    background-color: #3b5998;
}

.twitter {
    background-color: #1da1f2;
}

.whatsapp {
    background-color: #25d366;
}

.email {
    background-color: #ea4335;
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
}

.stats-card .stat-item {
    background-color: #f8f9fa;
    border-radius: 10px;
    padding: 15px;
    display: flex;
    align-items: center;
}

.stat-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: rgba(115, 103, 240, 0.1);
    color: var(--primary-color);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    margin-right: 15px;
}

.stat-info {
    flex: 1;
}

.stat-value {
    display: block;
    font-size: 1.1rem;
    font-weight: 700;
    margin-bottom: 5px;
}

.stat-label {
    font-size: 0.8rem;
    color: #6e6b7b;
}

.withdraw-section {
    margin-top: 20px;
    text-align: center;
}

.btn-success {
    background-color: #28c76f;
    color: white;
    border: none;
    border-radius: 8px;
    padding: 12px 20px;
    font-weight: 500;
    text-decoration: none;
    display: inline-block;
}

/* How It Works */
.steps-container {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.step {
    display: flex;
    align-items: flex-start;
}

.step-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: rgba(115, 103, 240, 0.1);
    color: var(--primary-color);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    margin-right: 15px;
    flex-shrink: 0;
}

.step-content h3 {
    font-size: 1rem;
    font-weight: 600;
    margin: 0 0 5px;
}

.step-content p {
    font-size: 0.9rem;
    color: #6e6b7b;
    margin: 0;
}

/* Tables */
.table-container {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.referral-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.9rem;
}

.referral-table th, 
.referral-table td {
    padding: 12px 10px;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.referral-table th {
    font-weight: 600;
    color: #333;
    background-color: #f8f9fa;
}

.status-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 500;
}

.status-pending {
    background-color: #ff9f43;
    color: white;
}

.status-approved {
    background-color: #28c76f;
    color: white;
}

.status-rejected {
    background-color: #ea5455;
    color: white;
}

.status-paid {
    background-color: #7367f0;
    color: white;
}

@media (max-width: 480px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .referral-table th:nth-child(2),
    .referral-table td:nth-child(2) {
        display: none;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Copy referral link functionality
    const copyLinkBtn = document.getElementById('copyLinkBtn');
    const referralLink = document.getElementById('referralLink');
    
    if (copyLinkBtn && referralLink) {
        copyLinkBtn.addEventListener('click', function() {
            referralLink.select();
            document.execCommand('copy');
            
            // Show success indicator
            const originalIcon = this.innerHTML;
            this.innerHTML = '<i class="fas fa-check"></i>';
            
            setTimeout(() => {
                this.innerHTML = originalIcon;
            }, 2000);
            
            // Show toast notification
            const toast = document.createElement('div');
            toast.className = 'toast-notification';
            toast.textContent = 'Link copied to clipboard!';
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.classList.add('show');
            }, 100);
            
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => {
                    document.body.removeChild(toast);
                }, 300);
            }, 2000);
        });
    }
});
</script>

<!-- Toast Notification Style -->
<style>
.toast-notification {
    position: fixed;
    bottom: 70px;
    left: 50%;
    transform: translateX(-50%) translateY(20px);
    background-color: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 12px 20px;
    border-radius: 25px;
    font-size: 0.9rem;
    z-index: 1000;
    opacity: 0;
    transition: transform 0.3s, opacity 0.3s;
}

.toast-notification.show {
    opacity: 1;
    transform: translateX(-50%) translateY(0);
}
</style>

<?php include 'includes/mobile-footer.php'; ?>