<?php
// mobile/mining.php - Mining system mobile page
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=mining.php');
    exit;
}

// Get database connection
$conn = $GLOBALS['db']->getConnection();

// Get user details
$user_id = $_SESSION['user_id'];
$user = getUserDetails($user_id);

// Check if user_mining_packages table exists
$tableExists = true;
$result = $conn->query("SHOW TABLES LIKE 'user_mining_packages'");
if ($result->num_rows === 0) {
    $tableExists = false;
}

// Get user's mining packages
$user_mining = [];
if ($tableExists) {
    // Get mining packages with daily revenue calculation
    $query = "SELECT ump.*, mp.name, mp.hash_rate, mp.electricity_cost, mp.daily_revenue_rate, mp.package_price,
             (mp.hash_rate * mp.daily_revenue_rate) as daily_revenue
             FROM user_mining_packages ump 
             JOIN mining_packages mp ON ump.package_id = mp.id 
             WHERE ump.user_id = ? AND ump.status = 'active'";
    
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            // Calculate daily revenue if not set
            if (!isset($row['daily_revenue']) || $row['daily_revenue'] === null) {
                $row['daily_revenue'] = $row['hash_rate'] * $row['daily_revenue_rate'];
            }
            $user_mining[] = $row;
        }
    }
}

// Get available mining packages for purchase
$mining_packages = [];
$stmt = $conn->prepare("SELECT * FROM mining_packages WHERE is_active = 1 ORDER BY package_price ASC");
if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $mining_packages[] = $row;
    }
}

$page_title = 'Mining System';
include 'includes/mobile-header.php';
?>

<div class="mining-page">
    <div class="page-header">
        <h1 data-i18n="mining.title">Mining System</h1>
        <p data-i18n="mining.description">Earn passive income with our professional mining packages</p>
    </div>
    
    <?php if (!$tableExists): ?>
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i>
        <span>Mining system database tables have not been created yet. Please contact support.</span>
    </div>
    <?php elseif (count($user_mining) > 0): ?>
    <!-- Active Mining Packages -->
    <div class="section-title">
        <h2><i class="fas fa-microchip"></i> <span data-i18n="mining.active_packages">Your Active Mining Packages</span></h2>
    </div>
    
    <div class="mining-cards">
        <?php foreach($user_mining as $package): ?>
        <div class="mining-card">
            <div class="mining-card-header">
                <h3><?= htmlspecialchars($package['name']) ?></h3>
                <span class="status-badge <?= $package['status'] == 'active' ? 'status-active' : 'status-paused' ?>">
                    <?= ucfirst($package['status']) ?>
                </span>
            </div>
            
            <div class="mining-card-stats">
                <div class="stat-item">
                    <i class="fas fa-tachometer-alt"></i>
                    <div class="stat-content">
                        <span class="stat-value"><?= number_format($package['hash_rate'], 2) ?> TH/s</span>
                        <span class="stat-label" data-i18n="mining.hash_rate">Hash Rate</span>
                    </div>
                </div>
                
                <div class="stat-item">
                    <i class="fas fa-bolt"></i>
                    <div class="stat-content">
                        <span class="stat-value"><?= number_format($package['electricity_cost'], 4) ?></span>
                        <span class="stat-label" data-i18n="mining.electricity_cost">Electricity Cost</span>
                    </div>
                </div>
                
                <div class="stat-item">
                    <i class="fas fa-calendar-alt"></i>
                    <div class="stat-content">
                    <span class="stat-value"><?= date('d.m.Y', strtotime($package['purchase_date'])) ?></span>
                        <span class="stat-label" data-i18n="mining_details.purchase_date">Purchase Date</span>
                    </div>
                </div>
            </div>
            
            <div class="mining-card-earnings">
                <div class="earnings-item">
                    <div class="earnings-label" data-i18n="mining.daily_earnings_estimate">Daily Earnings (Est.)</div>
                    <div class="earnings-value"><?= number_format($package['daily_revenue'], 6) ?> USDT</div>
                </div>
                <div class="earnings-item">
                    <div class="earnings-label" data-i18n="mining.total_earnings">Total Earned</div>
                    <div class="earnings-value"><?= number_format($package['total_earned'] ?? 0, 6) ?> USDT</div>
                </div>
            </div>
            
            <div class="roi-progress">
                <?php 
                $totalEarned = $package['total_earned'] ?? 0;
                $percentage = $package['package_price'] > 0 ? min(($totalEarned / $package['package_price']) * 100, 100) : 0;
                ?>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?= $percentage ?>%"></div>
                </div>
                <div class="progress-labels">
                    <span><span data-i18n="mining.roi">ROI:</span> <?= number_format($percentage, 1) ?>%</span>
                    <span><span data-i18n="mining.target">Target:</span> <?= number_format($package['package_price'], 2) ?> USDT</span>
                </div>
            </div>
            
            <div class="mining-card-actions">
                <a href="mining-details.php?id=<?= $package['id'] ?>" class="btn btn-outline">
                    <i class="fas fa-chart-line"></i> <span data-i18n="buttons.details">Details</span>
                </a>
                
                <?php if($package['status'] == 'active'): ?>
                <button class="btn btn-warning" onclick="toggleMiningStatus('<?= $package['id'] ?>', 'pause')">
                    <i class="fas fa-pause"></i> <span data-i18n="mining.pause">Pause</span>
                </button>
                <?php else: ?>
                <button class="btn btn-success" onclick="toggleMiningStatus('<?= $package['id'] ?>', 'resume')">
                    <i class="fas fa-play"></i> <span data-i18n="mining.resume">Resume</span>
                </button>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Mining Performance Chart -->
    <div class="chart-section">
        <div class="chart-card">
            <div class="chart-header">
                <h3><i class="fas fa-chart-line"></i> <span data-i18n="mining.mining_performance">Mining Performance</span></h3>
            </div>
            <div class="chart-body">
                <canvas id="miningStatsChart" height="250"></canvas>
            </div>
        </div>
    </div>
    
    <?php else: ?>
    <!-- No Active Mining Packages -->
    <div class="empty-mining">
        <i class="fas fa-microchip"></i>
        <h3 data-i18n="mining.no_active_packages">No Active Mining Packages</h3>
        <p data-i18n="mining.no_active_packages">You don't have any active mining packages yet. Browse available packages below to start earning passive income.</p>
    </div>
    <?php endif; ?>
    
    <!-- Available Mining Packages -->
    <?php if (count($mining_packages) > 0): ?>
    <div class="section-title">
        <h2><i class="fas fa-shopping-cart"></i> <span data-i18n="packages.mining_packages">Available Mining Packages</span></h2>
    </div>
    
    <div class="available-packages">
        <?php foreach($mining_packages as $package): ?>
        <div class="package-card">
            <div class="package-header">
                <h3><?= htmlspecialchars($package['name']) ?></h3>
                <div class="package-price"><?= number_format($package['package_price'], 2) ?> USDT</div>
            </div>
            
            <div class="package-body">
                <ul class="package-features">
                    <li>
                        <i class="fas fa-tachometer-alt"></i>
                        <span><?= $package['hash_rate'] ?> TH/s <span data-i18n="mining.hash_rate">Hash Rate</span></span>
                    </li>
                    <li>
                        <i class="fas fa-bolt"></i>
                        <span><?= number_format($package['electricity_cost'], 4) ?> USDT/day <span data-i18n="mining.electricity_cost">Electricity Cost</span></span>
                    </li>
                    <li>
                        <i class="fas fa-percentage"></i>
                        <span><?= number_format($package['daily_revenue_rate'] * 100, 2) ?>% <span data-i18n="mining.daily_income_rate">Daily Income Rate</span></span>
                    </li>
                    <li>
                        <i class="fas fa-coins"></i>
                        <span><?= number_format($package['package_price'] * $package['daily_revenue_rate'], 2) ?> USDT <span data-i18n="mining.daily_estimated_earnings">Est. Daily Income</span></span>
                    </li>
                </ul>
                
                <a href="purchase.php?type=mining&package_id=<?= $package['id'] ?>" class="btn btn-primary btn-block">
                    <i class="fas fa-shopping-cart"></i> <span data-i18n="packages.buy">Purchase</span>
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<style>
/* Mining Page Styles */

.mining-page {
    padding: 15px;
}

.page-header {
    margin-bottom: 20px;
}

.page-header h1 {
    font-size: 1.8rem;
    font-weight: 700;
    margin: 0 0 5px;
    color: var(--primary-color);
}

.page-header p {
    color: var(--text-muted);
    margin: 0;
}

.section-title {
    margin-bottom: 15px;
}

.section-title h2 {
    font-size: 1.2rem;
    font-weight: 600;
    color: #333;
    display: flex;
    align-items: center;
}

.section-title h2 i {
    margin-right: 8px;
    color: var(--primary-color);
}

/* Mining Cards */
.mining-cards {
    display: flex;
    flex-direction: column;
    gap: 15px;
    margin-bottom: 25px;
}

.mining-card {
    background-color: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.mining-card-header {
    padding: 15px;
    background-color: #20c997;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.mining-card-header h3 {
    font-size: 1.1rem;
    font-weight: 600;
    margin: 0;
}

.status-badge {
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
}

.status-active {
    background-color: rgba(255, 255, 255, 0.3);
}

.status-paused {
    background-color: rgba(0, 0, 0, 0.2);
}

.mining-card-stats {
    padding: 15px;
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    border-bottom: 1px solid #eee;
}

.stat-item {
    flex: 1 0 calc(50% - 15px);
    display: flex;
    align-items: center;
}

.stat-item i {
    width: 30px;
    height: 30px;
    background-color: rgba(32, 201, 151, 0.1);
    color: #20c997;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 10px;
}

.stat-content {
    flex: 1;
}

.stat-value {
    display: block;
    font-weight: 600;
    font-size: 0.9rem;
}

.stat-label {
    display: block;
    font-size: 0.75rem;
    color: var(--text-muted);
}

.mining-card-earnings {
    padding: 15px;
    display: flex;
    justify-content: space-between;
    border-bottom: 1px solid #eee;
}

.earnings-item {
    text-align: center;
    flex: 1;
}

.earnings-label {
    font-size: 0.8rem;
    color: var(--text-muted);
    margin-bottom: 5px;
}

.earnings-value {
    font-size: 1.1rem;
    font-weight: 600;
    color: #20c997;
}

.roi-progress {
    padding: 15px;
    border-bottom: 1px solid #eee;
}

.progress-bar {
    height: 8px;
    background-color: #e9ecef;
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 8px;
}

.progress-fill {
    height: 100%;
    background-color: #20c997;
    border-radius: 4px;
}

.progress-labels {
    display: flex;
    justify-content: space-between;
    font-size: 0.8rem;
    color: var(--text-muted);
}

.mining-card-actions {
    padding: 15px;
    display: flex;
    gap: 10px;
}

.mining-card-actions .btn {
    flex: 1;
    border-radius: 8px;
    padding: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none;
}

.mining-card-actions .btn i {
    margin-right: 5px;
}

.btn-outline {
    background-color: transparent;
    border: 1px solid #20c997;
    color: #20c997;
}

.btn-warning {
    background-color: #ff9f43;
    color: white;
    border: none;
}

.btn-success {
    background-color: #28c76f;
    color: white;
    border: none;
}

/* Chart Section */
.chart-section {
    margin-bottom: 25px;
}

.chart-card {
    background-color: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.chart-header {
    padding: 15px;
    border-bottom: 1px solid #eee;
}

.chart-header h3 {
    font-size: 1.1rem;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
}

.chart-header h3 i {
    margin-right: 8px;
    color: var(--primary-color);
}

.chart-body {
    padding: 15px;
    position: relative;
}

/* Empty State */
.empty-mining {
    background-color: white;
    border-radius: 12px;
    padding: 30px 20px;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    margin-bottom: 25px;
}

.empty-mining i {
    font-size: 3rem;
    color: #ddd;
    margin-bottom: 15px;
}

.empty-mining h3 {
    font-size: 1.2rem;
    font-weight: 600;
    margin: 0 0 10px;
}

.empty-mining p {
    color: var(--text-muted);
    margin: 0 0 20px;
}

/* Available Packages */
.available-packages {
    display: grid;
    grid-gap: 15px;
}

.package-card {
    background-color: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.package-header {
    padding: 15px;
    background-color: #20c997;
    color: white;
    text-align: center;
}

.package-header h3 {
    font-size: 1.1rem;
    font-weight: 600;
    margin: 0 0 5px;
}

.package-price {
    font-size: 1.3rem;
    font-weight: 700;
}

.package-body {
    padding: 15px;
}

.package-features {
    list-style-type: none;
    padding: 0;
    margin: 0 0 15px;
}

.package-features li {
    padding: 8px 0;
    border-bottom: 1px dashed #eee;
    display: flex;
    align-items: center;
}

.package-features li:last-child {
    border-bottom: none;
}

.package-features li i {
    color: #20c997;
    margin-right: 10px;
    width: 16px;
    text-align: center;
}

.package-features li span {
    flex: 1;
    font-size: 0.9rem;
}

.btn-block {
    display: block;
    width: 100%;
    padding: 12px;
    border-radius: 8px;
    text-align: center;
}

.btn-primary {
    background-color: var(--primary-color);
    color: white;
    text-decoration: none;
    font-weight: 500;
    border: none;
}

/* Alert */
.alert {
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: flex;
    align-items: flex-start;
}

.alert-warning {
    background-color: #fff3cd;
    color: #856404;
}

.alert i {
    margin-right: 10px;
    font-size: 1.2rem;
}

/* Responsive */
@media (min-width: 480px) {
    .available-packages {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (min-width: 768px) {
    .available-packages {
        grid-template-columns: repeat(3, 1fr);
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Mining Stats Chart
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('miningStatsChart');
    if (!ctx) return;
    
    // Get mining stats data
    fetch('ajax/mining_stats.php')
    .then(response => response.json())
    .then(data => {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.dates,
                datasets: [
                    {
                        label: 'Mining Revenue',
                        data: data.earnings,
                        borderColor: '#20c997',
                        backgroundColor: 'rgba(32, 201, 151, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Electricity Cost',
                        data: data.costs,
                        borderColor: '#ea5455',
                        backgroundColor: 'rgba(234, 84, 85, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Net Profit',
                        data: data.net_earnings,
                        borderColor: '#7367f0',
                        backgroundColor: 'rgba(115, 103, 240, 0.1)',
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 8,
                            padding: 15
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toFixed(6) + ' USDT';
                            }
                        }
                    },
                    x: {
                        ticks: {
                            maxRotation: 0
                        }
                    }
                },
                elements: {
                    point: {
                        radius: 0,
                        hoverRadius: 5
                    }
                }
            }
        });
    })
    .catch(error => {
        console.error('Error fetching mining stats:', error);
    });
});

// Toggle mining status
function toggleMiningStatus(packageId, action) {
    if (!confirm(action === 'pause' ? 
                'Are you sure you want to pause this mining package?' : 
                'Are you sure you want to resume this mining package?')) {
        return;
    }
    
    fetch('../ajax/toggle_mining.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'package_id=' + packageId + '&action=' + action
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert(action === 'pause' ? 'Mining paused successfully!' : 'Mining resumed successfully!');
            window.location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error changing mining status:', error);
        alert('An error occurred. Please try again later.');
    });
}
</script>

<?php include 'includes/mobile-footer.php'; ?>