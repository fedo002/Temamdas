<?php
// mobile/packages.php - Packages mobile page
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get database connection
$conn = $GLOBALS['db']->getConnection();

// Get package type from URL (vip, mining or all)
$packageType = isset($_GET['type']) ? $_GET['type'] : 'all';

// Check valid package types
$validTypes = ['vip', 'mining', 'all'];
if (!in_array($packageType, $validTypes)) {
    $packageType = 'all';
}

// Get VIP packages
$vipPackages = [];
if ($packageType === 'all' || $packageType === 'vip') {
    $vipQuery = "SELECT * FROM vip_packages WHERE is_active = 1 ORDER BY price ASC";
    $vipResult = $conn->query($vipQuery);
    
    if ($vipResult && $vipResult->num_rows > 0) {
        while ($row = $vipResult->fetch_assoc()) {
            $vipPackages[] = $row;
        }
    }
}

// Get Mining packages
$miningPackages = [];
if ($packageType === 'all' || $packageType === 'mining') {
    $miningQuery = "SELECT * FROM mining_packages WHERE is_active = 1 ORDER BY package_price ASC";
    $miningResult = $conn->query($miningQuery);
    
    if ($miningResult && $miningResult->num_rows > 0) {
        while ($row = $miningResult->fetch_assoc()) {
            $miningPackages[] = $row;
        }
    }
}

// Get game settings for different VIP levels
$gameSettings = [];
$gameSettingsQuery = "SELECT setting_key, setting_value, vip_level FROM game_settings ORDER BY vip_level ASC";
$gameSettingsResult = $conn->query($gameSettingsQuery);

if ($gameSettingsResult && $gameSettingsResult->num_rows > 0) {
    while ($row = $gameSettingsResult->fetch_assoc()) {
        $vipLevel = $row['vip_level'];
        $key = $row['setting_key'];
        $value = $row['setting_value'];
        
        if (!isset($gameSettings[$vipLevel])) {
            $gameSettings[$vipLevel] = [];
        }
        
        $gameSettings[$vipLevel][$key] = $value;
    }
}

// Check if user is logged in
$userVipLevel = 0;
$userLoggedIn = false;

if (isset($_SESSION['user_id'])) {
    $userLoggedIn = true;
    $userId = $_SESSION['user_id'];
    
    // Get user's VIP level
    $stmt = $conn->prepare("SELECT vip_level FROM users WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $userVipLevel = $user['vip_level'];
        }
    }
}

// Set page title based on package type
if ($packageType === 'vip') {
    $page_title = 'VIP Packages';
} elseif ($packageType === 'mining') {
    $page_title = 'Mining Packages';
} else {
    $page_title = 'All Packages';
}

include 'includes/mobile-header.php';
?>

<div class="packages-page">
    <div class="page-header">
        <h1>
            <?php 
            if ($packageType === 'vip') echo 'VIP Packages';
            elseif ($packageType === 'mining') echo 'Mining Packages';
            else echo 'Popular Packages';
            ?>
        </h1>
        
        <!-- Package type tabs -->
        <div class="package-tabs">
            <?php if ($packageType === 'all'): ?>
                <a href="packages.php?type=vip" class="tab active">VIP</a>
                <a href="packages.php?type=mining" class="tab">Mining</a>
            <?php else: ?>
                <a href="packages.php?type=vip" class="tab <?= $packageType === 'vip' ? 'active' : '' ?>">VIP</a>
                <a href="packages.php?type=mining" class="tab <?= $packageType === 'mining' ? 'active' : '' ?>">Mining</a>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Packages Container -->
    <div class="packages-container">
        <?php if ($packageType === 'all' || $packageType === 'vip'): ?>
            <!-- VIP Packages -->
            <?php if (!empty($vipPackages)): ?>
                <div class="packages-section">
                    <?php if ($packageType === 'all'): ?>
                        <div class="packages-carousel">
                            <?php foreach ($vipPackages as $package): ?>
                                <div class="package-card vip <?= ($userVipLevel == $package['id']) ? 'current' : '' ?>">
                                    <div class="package-header">
                                        <h3><?= htmlspecialchars($package['name']) ?></h3>
                                        <div class="package-price"><?= number_format($package['price'], 2) ?> USDT</div>
                                    </div>
                                    
                                    <div class="package-body">
                                        <?php if ($userVipLevel == $package['id']): ?>
                                            <div class="current-badge">Active Plan</div>
                                        <?php endif; ?>
                                        
                                        <ul class="package-features">
                                            <?php 
                                            // Günlük kazanç - stage1_base_reward from game_settings
                                            $dailyReward = isset($gameSettings[$package['id']]['stage1_base_reward']) ? 
                                                floatval($gameSettings[$package['id']]['stage1_base_reward']) : 
                                                (isset($gameSettings[0]['stage1_base_reward']) ? floatval($gameSettings[0]['stage1_base_reward']) : 5.0);
                                            ?>
                                            <li>
                                                <i class="fas fa-coins"></i>
                                                <span> <?= number_format($dailyReward, 2) ?> USDT Daily Earning</span>
                                            </li>
                                            
                                            <li>
                                                <i class="fas fa-percentage"></i>
                                                <span> <?= number_format($package['game_max_win_chance'] * 100, 1) ?>% Win Chance</span>
                                            </li>
                                            
                                            
                                            <?php 
                                            // Mining alınabilir durumu
                                            $canBuyMining = isset($package['miningbuy']) ? intval($package['miningbuy']) : 0;
                                            ?>
                                            <li class="<?= $canBuyMining ? 'available' : 'unavailable' ?>">
                                                <i class="fas fa-microchip"></i>
                                                <span> Mining Available</span>
                                            </li>
                                            
                                            <?php if ($package['tgsupport'] == 1): ?>
                                            <li>
                                                <i class="fab fa-telegram"></i>
                                                <span> Telegram Support</span>
                                            </li>
                                            <?php endif; ?>
                                            
                                            <?php if ($package['wpsupport'] == 1): ?>
                                            <li>
                                                <i class="fab fa-whatsapp"></i>
                                                <span> 24/7 WhatsApp Support</span>
                                            </li>
                                            <?php endif; ?>
                                            
                                            <?php 
                                            // Ekstra ödül yenileme hakkı - try_again from game_settings
                                            $retryCount = isset($gameSettings[$package['id']]['try_again']) ? 
                                                intval($gameSettings[$package['id']]['try_again']) : 0;
                                                
                                            if ($retryCount > 0):
                                            ?>
                                            <li>
                                                <i class="fas fa-redo"></i>
                                                <span> <?= $retryCount ?> Extra Reward Retry Rights</span>
                                            </li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                    
                                    <div class="package-footer">
                                        <?php if ($package['price'] > 0): ?>
                                            <?php if ($userLoggedIn): ?>
                                                <?php if ($userVipLevel == $package['id']): ?>
                                                    <button class="btn btn-success" disabled>Active Package</button>
                                                <?php elseif ($userVipLevel > $package['id']): ?>
                                                    <button class="btn btn-secondary" disabled>Lower Level</button>
                                                <?php else: ?>
                                                    <a href="purchase.php?type=vip&package_id=<?= $package['id'] ?>" class="btn btn-primary">Purchase</a>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <a href="login.php?redirect=packages.php%3Ftype%3Dvip" class="btn btn-primary">Login to Purchase</a>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <button class="btn btn-secondary" disabled>Default Package</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- View All VIP Packages Button -->
                        <div class="view-all-packages">
                            <a href="packages.php?type=vip" class="btn-view-all">View All VIP Packages  <i class="fas fa-arrow-right"></i>  </a>
                        </div>
                    <?php else: ?>
                        <div class="packages-grid">
                            <?php foreach ($vipPackages as $package): ?>
                                <div class="package-card vip <?= ($userVipLevel == $package['id']) ? 'current' : '' ?>">
                                    <div class="package-header">
                                        <h3><?= htmlspecialchars($package['name']) ?></h3>
                                        <div class="package-price"><?= number_format($package['price'], 2) ?> USDT</div>
                                    </div>
                                    
                                    <div class="package-body">
                                        <?php if ($userVipLevel == $package['id']): ?>
                                            <div class="current-badge">Active Plan</div>
                                        <?php endif; ?>
                                        
                                        <ul class="package-features">
                                            <?php 
                                            // Günlük kazanç - stage1_base_reward from game_settings
                                            $dailyReward = isset($gameSettings[$package['id']]['stage1_base_reward']) ? 
                                                floatval($gameSettings[$package['id']]['stage1_base_reward']) : 
                                                (isset($gameSettings[0]['stage1_base_reward']) ? floatval($gameSettings[0]['stage1_base_reward']) : 5.0);
                                            ?>
                                            <li>
                                                <i class="fas fa-coins"></i>   
                                                 <span>  <?= number_format($dailyReward, 2) ?> USDT Daily Earning</span> 
                                            </li>
                                            <li>
                                                <i class="fas fa-percentage"></i>  <span> <?= number_format($package['game_max_win_chance'] * 100, 1) ?> Win Chance</span>
                                            </li>
                                            <?php 
                                            // Mining alınabilir durumu
                                            $canBuyMining = isset($package['miningbuy']) ? intval($package['miningbuy']) : 0;
                                            ?>
                                            <li class="<?= $canBuyMining ? 'available' : 'unavailable' ?>">
                                                <i class="fas fa-microchip"></i>    <span> Mining Available</span> 
                                            </li>
                                            
                                            <?php if ($package['tgsupport'] == 1): ?>
                                            <li>
                                                <i class="fab fa-telegram"></i>   
                                                 <span> Telegram Support</span>
                                            </li>
                                            <?php endif; ?>
                                            
                                            <?php if ($package['wpsupport'] == 1): ?>
                                            <li>
                                                <i class="fab fa-whatsapp"></i>   
                                                 <span> 24/7 WhatsApp Support</span>
                                            </li>
                                            <?php endif; ?>
                                            
                                            <?php 
                                            // Ekstra ödül yenileme hakkı - try_again from game_settings
                                            $retryCount = isset($gameSettings[$package['id']]['try_again']) ? 
                                                intval($gameSettings[$package['id']]['try_again']) : 0;
                                                
                                            if ($retryCount > 0):
                                            ?>
                                            <li>
                                                 <i class="fas fa-redo"></i>  
                                                 <span> <?= $retryCount ?> Extra Reward Retry Rights</span>
                                            </li>
                                            <?php endif; ?>
                                            
                                            <li>
                                                <i class="fa-solid fa-timer"></i>  
                                                <span> Package Duration: <?= ($package['id'] == 0) ? 'Unlimited' : '40 Days' ?></span>
                                            </li>

                                        </ul>
                                    </div>
                                    
                                    <div class="package-footer">
                                        <?php if ($package['price'] > 0): ?>
                                            <?php if ($userLoggedIn): ?>
                                                <?php if ($userVipLevel == $package['id']): ?>
                                                    <button class="btn btn-success" disabled>Active Package</button>
                                                <?php elseif ($userVipLevel > $package['id']): ?>
                                                    <button class="btn btn-secondary" disabled>Lower Level</button>
                                                <?php else: ?>
                                                    <a href="purchase.php?type=vip&package_id=<?= $package['id'] ?>" class="btn btn-primary">Purchase</a>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <a href="login.php?redirect=packages.php%3Ftype%3Dvip" class="btn btn-primary">Login to Purchase</a>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <button class="btn btn-secondary" disabled>Default Package</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="no-packages">
                    <i class="fas fa-crown"></i>
                    <p>No VIP packages available at the moment.</p>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        
        <?php if ($packageType === 'all' || $packageType === 'mining'): ?>
            <!-- Mining Packages -->
            <?php if (!empty($miningPackages)): ?>
                <div class="packages-section">
                    <?php if ($packageType === 'all'): ?>
                        <h2 class="section-title">Mining Packages</h2>
                    <?php endif; ?>
                    
                    <div class="packages-grid">
                        <?php foreach ($miningPackages as $package): ?>
                            <div class="package-card vip">
                                <div class="package-header">
                                    <h3><?= htmlspecialchars($package['name']) ?></h3>
                                    <div class="package-price"><?= number_format($package['package_price'], 2) ?> USDT</div>
                                </div>
                                
                                <div class="package-body">
                                    <ul class="package-features">
                                        <li>
                                            <i class="fas fa-tachometer-alt"></i>  
                                            <span> <?= $package['hash_rate'] ?> TH/s Hash Rate</span>
                                        </li>
                                        <li>
                                            <i class="fas fa-bolt"></i>  
                                            <span> <?= number_format($package['hash_rate'] * $package['electricity_cost'], 2) ?>   USDT/day Electricity</span>
                                        </li>
                                        <li>
                                            <i class="fas fa-chart-line"></i>  
                                            <span> <?= number_format($package['daily_revenue_rate'] * 100, 2) ?> Daily Rate</span>
                                        </li>
                                        <li>
                                            <i class="fas fa-coins"></i>  
                                            <span> <?= number_format(($package['hash_rate'] * $package['daily_revenue_rate']) - ($package['hash_rate'] * $package['electricity_cost']), 2) ?> USDT Est. Daily</span>
                                        </li>
                                        <li>
                                            <i class="fas fa-coins"></i>  <span> <?= number_format((($package['hash_rate'] * $package['daily_revenue_rate']) - ($package['hash_rate'] * $package['electricity_cost'])) * 30, 2) ?> USDT Est. Monthly</span>
                                        </li>
                                    </ul>
                                </div>
                                
                                <div class="package-footer">
                                    <?php if ($userLoggedIn): ?>
                                        <a href="purchase.php?type=mining&package_id=<?= $package['id'] ?>" class="btn btn-primary">Purchase</a>
                                    <?php else: ?>
                                        <a href="login.php?redirect=packages.php%3Ftype%3Dmining" class="btn btn-primary">Login to Purchase</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="no-packages">
                    <i class="fas fa-microchip"></i>
                    <p>No mining packages available at the moment.</p>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<style>
/* Packages Page Styles */
.packages-page {
    padding: 15px;
}

.page-header {
    margin-bottom: 20px;
    text-align: center;
}

.page-header h1 {
    font-size: 1.8rem;
    font-weight: 700;
    margin: 0 0 15px;
    color: #fff;
}

.package-tabs {
    display: flex;
    background-color: #1E1E1E;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    width: 80%;
    margin: 0 auto;
}

.package-tabs .tab {
    flex: 1;
    text-align: center;
    padding: 12px;
    font-size: 1rem;
    font-weight: 500;
    color: #fff;
    text-decoration: none;
    position: relative;
}

.package-tabs .tab.active {
    color: #fff;
    background-color: #3F88F6;
    border-radius: 10px;
    border: 2px solid #fff;
}

.packages-section {
    margin-bottom: 30px;
}

.section-title {
    font-size: 1.3rem;
    font-weight: 600;
    margin: 0 0 15px;
    color: #fff;
    text-align: center;
}

.packages-grid {
    display: grid;
    grid-gap: 15px;
}

/* Carousel style for homepage */
.packages-carousel {
    display: flex;
    overflow-x: auto;
    padding: 10px 0;
    scroll-snap-type: x mandatory;
    gap: 15px;
}

.packages-carousel .package-card {
    flex: 0 0 80%;
    scroll-snap-align: start;
}

.package-card {
    background-color: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s, box-shadow 0.3s;
    position: relative;
}

.package-card:active {
    transform: translateY(-5px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
}

.package-card.vip .package-header {
    background-color: #3F88F6;
    color: white;
}

.package-card.mining .package-header {
    background-color: #20c997;
    color: white;
}

.package-header {
    padding: 20px;
    text-align: center;
}

.package-header h3 {
    font-size: 1.2rem;
    font-weight: 600;
    margin: 0 0 10px;
}

.package-price {
    font-size: 1.5rem;
    font-weight: 700;
}

.package-body {
    padding: 20px;
    position: relative;
}

.current-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background-color: #28a745;
    color: white;
    padding: 4px 8px;
    border-radius: 10px;
    font-size: 0.7rem;
    font-weight: 500;
}

.package-features {
    list-style-type: none;
    padding: 0;
    margin: 0;
}

.package-features li {
    padding: 10px 0;
    border-bottom: 1px dashed #eee;
    display: flex;
    align-items: center;
}

.package-features li:last-child {
    border-bottom: none;
}

.package-features li i {
    color: #3F88F6;
    margin-right: 10px;
    width: 16px;
    text-align: center;
}

.package-features li span {
    flex: 1;
    font-size: 0.9rem;
}

/* Unavailable feature styling */
.package-features li.unavailable {
    color: #dc3545;
    text-decoration: line-through;
}

.package-features li.unavailable i {
    color: #dc3545;
}

.package-footer {
    padding: 15px 20px;
    border-top: 1px solid #eee;
    text-align: center;
}

.btn {
    display: inline-block;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 500;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    border: none;
    outline: none;
    text-decoration: none;
    width: 100%;
}

.btn-primary {
    background-color: #3F88F6;
    color: white;
}

.btn-success {
    background-color: #28c76f;
    color: white;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
}

.no-packages {
    text-align: center;
    padding: 40px 20px;
    background-color: white;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.no-packages i {
    font-size: 3rem;
    color: #ddd;
    margin-bottom: 15px;
}

.no-packages p {
    color: #666;
    margin: 0;
}

.package-card.current {
    border: 2px solid #28a745;
}

/* View All Button */
.view-all-packages {
    text-align: center;
    margin-top: 15px;
}

.btn-view-all {
    display: inline-block;
    padding: 12px 24px;
    background-color: #3F88F6;
    color: white;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 500;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.btn-view-all i {
    margin-left: 5px;
}

/* Responsive adjustments */
@media (min-width: 480px) {
    .packages-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .packages-carousel .package-card {
        flex: 0 0 60%;
    }
}

@media (min-width: 768px) {
    .packages-grid {
        grid-template-columns: repeat(3, 1fr);
    }
    
    .packages-carousel .package-card {
        flex: 0 0 40%;
    }
}
</style>

<?php include 'includes/mobile-footer.php'; ?>