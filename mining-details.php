<?php
// mobile/mining-details.php - Mining package details mobile page
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=mining-details.php');
    exit;
}

// Get database connection
$conn = $GLOBALS['db']->getConnection();

// Get user details
$user_id = $_SESSION['user_id'];
$user = getUserDetails($user_id);

// Check for package ID parameter
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: mining.php');
    exit;
}

$package_id = (int)$_GET['id'];

// Get mining package details (ensure it belongs to the user)
$package = null;
$query = "SELECT ump.*, mp.name, mp.hash_rate, mp.electricity_cost, mp.daily_revenue_rate, mp.package_price,
         (mp.hash_rate * mp.daily_revenue_rate) as daily_revenue
         FROM user_mining_packages ump 
         JOIN mining_packages mp ON ump.package_id = mp.id 
         WHERE ump.id = ? AND ump.user_id = ?";

$stmt = $conn->prepare($query);
if ($stmt) {
    $stmt->bind_param("ii", $package_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $package = $result->fetch_assoc();
    } else {
        // Package not found or doesn't belong to user
        header('Location: mining.php');
        exit;
    }
}

// Get last 30 days mining earnings
$earnings_data = [];
$query = "SELECT date, revenue, electricity_cost, net_revenue FROM mining_earnings 
          WHERE user_id = ? AND user_mining_id = ? 
          ORDER BY date DESC LIMIT 30";

$stmt = $conn->prepare($query);
if ($stmt) {
    $stmt->bind_param("ii", $user_id, $package_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $earnings_data[] = $row;
    }
}

// Reverse data to show oldest to newest
$earnings_data = array_reverse($earnings_data);

// Prepare chart data
$dates = [];
$revenues = [];
$costs = [];
$net_revenues = [];

foreach ($earnings_data as $data) {
    $dates[] = date('d M', strtotime($data['date']));
    $revenues[] = number_format($data['revenue'], 6, '.', '');
    $costs[] = number_format($data['electricity_cost'], 6, '.', '');
    $net_revenues[] = number_format($data['net_revenue'], 6, '.', '');
}

// Calculate last 7 days statistics
$last_7_days = array_slice($earnings_data, -7);
$last_7_days_total = 0;
$last_7_days_avg = 0;

if (count($last_7_days) > 0) {
    foreach ($last_7_days as $day) {
        $last_7_days_total += $day['net_revenue'];
    }
    $last_7_days_avg = $last_7_days_total / count($last_7_days);
}

$page_title = 'Mining Details';
include 'includes/mobile-header.php';
?>

<div class="mining-details-page">
    <div class="page-header">
        <a href="mining.php" class="back-link">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 data-i18n="mining_details.title">Mining Details</h1>
    </div>
    
    <?php if ($package): ?>
    <div class="package-details-card">
        <div class="package-header">
            <h2><?= htmlspecialchars($package['name']) ?></h2>
            <span class="status-badge <?= $package['status'] == 'active' ? 'status-active' : 'status-paused' ?>">
                <?= ucfirst($package['status']) ?>
            </span>
        </div>
        
        <div class="package-meta">
            <div class="meta-item">
                <i class="fas fa-calendar-alt"></i>
                <div>
                    <span class="meta-label" data-i18n="mining_details.purchase_date">Purchase Date</span>
                    <span class="meta-value"><?= date('d.m.Y', strtotime($package['purchase_date'])) ?></span>
                </div>
            </div>
            
            <?php if ($package['expiry_date']): ?>
            <div class="meta-item">
                <i class="fas fa-hourglass-end"></i>
                <div>
                    <span class="meta-label" data-i18n="mining_details.expiry_date">Expiry Date</span>
                    <span class="meta-value"><?= date('d.m.Y', strtotime($package['expiry_date'])) ?></span>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="meta-item">
                <i class="fas fa-tachometer-alt"></i>
                <div>
                    <span class="meta-label" data-i18n="mining.hash_rate">Hash Rate</span>
                    <span class="meta-value"><?= number_format($package['hash_rate'], 2) ?> TH/s</span>
                </div>
            </div>
            
            <div class="meta-item">
                <i class="fas fa-bolt"></i>
                <div>
                    <span class="meta-label" data-i18n="mining_details.electricity_cost">Electricity Cost</span>
                    <span class="meta-value"><?= number_format($package['electricity_cost'], 4) ?> USDT/day</span>
                </div>
            </div>
            
            <div class="meta-item">
                <i class="fas fa-percentage"></i>
                <div>
                    <span class="meta-label" data-i18n="mining_details.daily_income_rate">Daily Income Rate</span>
                    <span class="meta-value"><?= number_format($package['daily_revenue_rate'] * 100, 2) ?>%</span>
                </div>
            </div>
            
            <div class="meta-item">
                <i class="fas fa-dollar-sign"></i>
                <div>
                    <span class="meta-label" data-i18n="mining_details.investment_amount">Investment Amount</span>
                    <span class="meta-value"><?= number_format($package['package_price'], 2) ?> USDT</span>
                </div>
            </div>
        </div>
        
        <div class="daily-earnings">
            <h3 data-i18n="mining_details.daily_estimated_earnings">Daily Estimated Earnings</h3>
            <div class="earnings-value"><?= number_format($package['daily_revenue'], 6) ?> USDT</div>
        </div>
        
        <div class="total-earnings">
            <h3 data-i18n="mining_details.total_earnings">Total Earnings</h3>
            <div class="earnings-value"><?= number_format($package['total_earned'] ?? 0, 6) ?> USDT</div>
        </div>
        
        <div class="roi-progress">
            <?php 
            $totalEarned = $package['total_earned'] ?? 0;
            $percentage = $package['package_price'] > 0 ? min(($totalEarned / $package['package_price']) * 100, 100) : 0;
            ?>
            <div class="progress-info">
                <span data-i18n="mining_details.investment_return">Investment Return</span>
                <span><?= number_format($percentage, 1) ?>%</span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" style="width: <?= $percentage ?>%"></div>
            </div>
            <div class="progress-target">
                <span><span data-i18n="mining_details.target">Target:</span> <?= number_format($package['package_price'], 2) ?> USDT</span>
            </div>
        </div>
        
        <div class="package-actions">
            <?php if($package['status'] == 'active'): ?>
            <button id="pauseButton" class="btn btn-warning" onclick="toggleMining('<?= $package['id'] ?>', 'pause')">
                <i class="fas fa-pause"></i> <span data-i18n="mining_details.pause_mining">Pause Mining</span>
            </button>
            <?php else: ?>
            <button id="resumeButton" class="btn btn-success" onclick="toggleMining('<?= $package['id'] ?>', 'resume')">
                <i class="fas fa-play"></i> <span data-i18n="mining_details.resume_mining">Resume Mining</span>
            </button>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Performance Analysis -->
    <div class="performance-card">
        <h2 data-i18n="mining_details.performance_analysis">Performance Analysis</h2>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h4 data-i18n="mining_details.last_7_days_total">Last 7 Days Total</h4>
                <div class="stat-value"><?= number_format($last_7_days_total, 6) ?> USDT</div>
            </div>
            
            <div class="stat-card">
                <h4 data-i18n="mining_details.daily_average">Daily Average</h4>
                <div class="stat-value"><?= number_format($last_7_days_avg, 6) ?> USDT</div>
            </div>
            
            <div class="stat-card">
                <h4 data-i18n="mining_details.estimated_monthly">Est. Monthly</h4>
                <div class="stat-value"><?= number_format($last_7_days_avg * 30, 6) ?> USDT</div>
            </div>
        </div>
        
        <!-- Projected Earnings Table -->
        <div class="projections-table">
            <h3 data-i18n="mining_details.period">Projected Earnings</h3>
            
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th data-i18n="mining_details.period">Period</th>
                            <th data-i18n="mining_details.earnings">Earnings (USDT)</th>
                            <th>ROI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $daily_revenue = $package['daily_revenue'];
                        $investment = $package['package_price'];
                        $periods = [
                            ['Weekly', 7],
                            ['Monthly', 30],
                            ['Quarterly', 90],
                            ['Biannual', 180],
                            ['Yearly', 365]
                        ];
                        
                        foreach ($periods as $period):
                            $earnings = $daily_revenue * $period[1];
                            $roi_percentage = ($earnings / $investment) * 100;
                        ?>
                        <tr>
                            <td data-i18n="mining_details.<?= strtolower($period[0]) ?>"><?= $period[0] ?></td>
                            <td><?= number_format($earnings, 6) ?> USDT</td>
                            <td><?= number_format($roi_percentage, 2) ?>%</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="table-note">
                <i class="fas fa-info-circle"></i> 
                <span data-i18n="mining_details.estimate_note">Projections are based on current hash rates and reward distribution. Actual earnings may vary.</span>
            </div>
        </div>
    </div>
    
    <!-- Earnings Chart -->
    <div class="chart-card">
        <h2 data-i18n="mining_details.daily_mining_earnings">Daily Mining Earnings</h2>
        
        <div class="chart-container">
            <canvas id="earningsChart" height="250"></canvas>
        </div>
    </div>
    
    <!-- Earnings History -->
    <div class="history-card">
        <h2 data-i18n="mining_details.earnings_history">Earnings History</h2>
        
        <?php if (count($earnings_data) > 0): ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th data-i18n="mining_details.date">Date</th>
                        <th data-i18n="mining_details.gross_earnings">Revenue</th>
                        <th data-i18n="mining_details.electricity_cost">Cost</th>
                        <th data-i18n="mining_details.net_earnings">Net</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (array_reverse($earnings_data) as $earning): ?>
                    <tr>
                    <td><?= date('d.m.Y', strtotime($earning['date'])) ?></td>
                        <td class="positive"><?= number_format($earning['revenue'], 6) ?></td>
                        <td class="negative"><?= number_format($earning['electricity_cost'], 6) ?></td>
                        <td class="positive"><?= number_format($earning['net_revenue'], 6) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-chart-line"></i>
            <p data-i18n="mining_details.no_earnings_yet">No earnings data available yet for this package.</p>
        </div>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i>
        <span data-i18n="mining_details.package_not_found">Mining package not found or you don't have access to it.</span>
    </div>
    <?php endif; ?>
</div>

<style>
/* Mining Details Page Styles */
.mining-details-page {
    padding: 15px;
}

.page-header {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
}

.back-link {
    width: 36px;
    height: 36px;
    background-color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary-color);
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    margin-right: 15px;
}

.page-header h1 {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0;
    color: var(--primary-color);
}

/* Cards */
.package-details-card,
.performance-card,
.chart-card,
.history-card {
    background-color: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
}

.package-header {
    padding: 15px;
    background-color: #20c997;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.package-header h2 {
    font-size: 1.2rem;
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

.package-meta {
    padding: 15px;
}

.meta-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 10px;
}

.meta-item:last-child {
    margin-bottom: 0;
}

.meta-item i {
    width: 24px;
    height: 24px;
    background-color: rgba(32, 201, 151, 0.1);
    color: #20c997;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 10px;
    margin-top: 2px;
}

.meta-item div {
    flex: 1;
}

.meta-label {
    display: block;
    font-size: 0.8rem;
    color: var(--text-muted);
    margin-bottom: 2px;
}

.meta-value {
    font-weight: 600;
}

.daily-earnings,
.total-earnings {
    padding: 15px;
    text-align: center;
    border-top: 1px solid #eee;
}

.daily-earnings h3,
.total-earnings h3 {
    font-size: 1rem;
    font-weight: 600;
    margin: 0 0 10px;
    color: #333;
}

.earnings-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #20c997;
}

.roi-progress {
    padding: 15px;
    border-top: 1px solid #eee;
}

.progress-info {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
    font-weight: 500;
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

.progress-target {
    font-size: 0.85rem;
    color: var(--text-muted);
    text-align: right;
}

.package-actions {
    padding: 15px;
    border-top: 1px solid #eee;
}

.btn {
    display: block;
    width: 100%;
    padding: 12px;
    border-radius: 8px;
    font-weight: 500;
    text-align: center;
    cursor: pointer;
    border: none;
    color: white;
}

.btn i {
    margin-right: 8px;
}

.btn-warning {
    background-color: #ff9f43;
}

.btn-success {
    background-color: #28c76f;
}

/* Performance Card */
.performance-card,
.chart-card,
.history-card {
    padding: 15px;
}

.performance-card h2,
.chart-card h2,
.history-card h2 {
    font-size: 1.2rem;
    font-weight: 600;
    margin: 0 0 15px;
    color: #333;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
    margin-bottom: 20px;
}

.stat-card {
    background-color: #f8f9fa;
    border-radius: 8px;
    padding: 12px;
    text-align: center;
}

.stat-card h4 {
    font-size: 0.8rem;
    font-weight: 500;
    color: var(--text-muted);
    margin: 0 0 8px;
}

.stat-card .stat-value {
    font-size: 0.9rem;
    font-weight: 600;
    color: #20c997;
}

/* Projections Table */
.projections-table {
    margin-top: 20px;
}

.projections-table h3 {
    font-size: 1.1rem;
    font-weight: 600;
    margin: 0 0 15px;
    color: #333;
}

.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    padding: 10px;
    text-align: left;
    border-bottom: 1px solid #eee;
}

th {
    font-weight: 600;
    background-color: #f8f9fa;
}

td.positive {
    color: #20c997;
}

td.negative {
    color: #ea5455;
}

.table-note {
    margin-top: 15px;
    font-size: 0.85rem;
    color: var(--text-muted);
}

.table-note i {
    margin-right: 5px;
}

/* Chart Container */
.chart-container {
    position: relative;
    height: 250px;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 30px 20px;
}

.empty-state i {
    font-size: 2.5rem;
    color: #ddd;
    margin-bottom: 15px;
}

.empty-state p {
    color: var(--text-muted);
    margin: 0;
}

/* Alert */
.alert {
    padding: 15px;
    border-radius: 8px;
    display: flex;
    align-items: flex-start;
}

.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
}

.alert i {
    margin-right: 10px;
    font-size: 1.2rem;
}

/* Responsive Adjustments */
@media (max-width: 380px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if (count($dates) > 0): ?>
    // Create earnings chart
    const ctx = document.getElementById('earningsChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($dates) ?>,
            datasets: [
                {
                    label: 'Revenue',
                    data: <?= json_encode($revenues) ?>,
                    borderColor: '#20c997',
                    backgroundColor: 'rgba(32, 201, 151, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Electricity Cost',
                    data: <?= json_encode($costs) ?>,
                    borderColor: '#ea5455',
                    backgroundColor: 'rgba(234, 84, 85, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Net Profit',
                    data: <?= json_encode($net_revenues) ?>,
                    borderColor: '#7367f0',
                    backgroundColor: 'rgba(115, 103, 240, 0.1)',
                    tension: 0.4,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
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
                            return value + ' USDT';
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
    <?php endif; ?>
});

// Toggle mining status
function toggleMining(packageId, action) {
    if (!confirm(action === 'pause' ? 
                'Are you sure you want to pause this mining package?' : 
                'Are you sure you want to resume this mining package?')) {
        return;
    }
    
    document.getElementById(action === 'pause' ? 'pauseButton' : 'resumeButton').disabled = true;
    
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
            document.getElementById(action === 'pause' ? 'pauseButton' : 'resumeButton').disabled = false;
        }
    })
    .catch(error => {
        console.error('Error changing mining status:', error);
        alert('An error occurred. Please try again later.');
        document.getElementById(action === 'pause' ? 'pauseButton' : 'resumeButton').disabled = false;
    });
}
</script>

<?php include 'includes/mobile-footer.php'; ?>