<?php
// mobile/transactions.php - Mobile-optimized transactions page
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=transactions.php');
    exit;
}

// Get database connection
$conn = $GLOBALS['db']->getConnection();

// Get user details
$user_id = $_SESSION['user_id'];
$user = getUserDetails($user_id);

// Pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$per_page = 10; // More transactions per page for mobile
$offset = ($page - 1) * $per_page;

// Filter parameters
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$valid_filters = ['all', 'deposit', 'withdraw', 'referral', 'game', 'mining', 'miningdeposit', 'vip', 'transfer', 'other', 'referral_transfer'];
if (!in_array($filter, $valid_filters)) {
    $filter = 'all';
}

// Date range filter
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

// Build query
$where_conditions = ["user_id = ?"];
$params = [$user_id];
$types = "i";

if ($filter !== 'all') {
    $where_conditions[] = "type = ?";
    $params[] = $filter;
    $types .= "s";
}

if (!empty($start_date)) {
    $where_conditions[] = "DATE(created_at) >= ?";
    $params[] = $start_date;
    $types .= "s";
}

if (!empty($end_date)) {
    $where_conditions[] = "DATE(created_at) <= ?";
    $params[] = $end_date;
    $types .= "s";
}

$where_clause = implode(" AND ", $where_conditions);

// Get total count for pagination
$count_query = "SELECT COUNT(*) as total FROM transactions WHERE $where_clause";
$stmt = $conn->prepare($count_query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
$total_records = $result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $per_page);

// Get transactions
$query = "SELECT * FROM transactions WHERE $where_clause ORDER BY created_at DESC LIMIT ?, ?";
$stmt = $conn->prepare($query);
$stmt->bind_param($types . "ii", ...[...$params, $offset, $per_page]);
$stmt->execute();
$result = $stmt->get_result();
$transactions = [];

while ($row = $result->fetch_assoc()) {
    $transactions[] = $row;
}

// Transaction type mapping
$transaction_types = [
    'deposit' => 'Deposit',
    'withdraw' => 'Withdrawal',
    'referral' => 'Referral Bonus',
    'game' => 'Game Reward',
    'mining' => 'Mining Package',
    'miningdeposit' => 'Mining Earnings',
    'vip' => 'VIP Package',
    'bonus' => 'Bonus',
    'transfer' => 'Transfer',
    'referral_transfer' => 'Referral Transfer',
    'other' => 'Other'
];

// Helper function for filter URL
function getFilterUrl($new_filter) {
    global $start_date, $end_date;
    $url = "transactions.php?filter=$new_filter";
    if (!empty($start_date)) $url .= "&start_date=$start_date";
    if (!empty($end_date)) $url .= "&end_date=$end_date";
    return $url;
}

// Page title
$page_title = 'Transactions';

// Include mobile header
include 'includes/mobile-header.php';
?>

<div class="transactions-page">
    <!-- Page Header -->
    <div class="page-header">
        <h1 data-i18n="transactions.title">Transaction History</h1>
        
        <!-- Filter Button (opens filter modal) -->
        <button id="showFilterBtn" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-filter"></i>
            <span data-i18n="transactions.filter">Filter</span>
        </button>
    </div>
    
    <!-- Filter Tabs -->
    <div class="filter-tabs">
        <div class="scroll-container">
            <a href="<?= getFilterUrl('all') ?>" class="filter-tab <?= $filter === 'all' ? 'active' : '' ?>" data-i18n="transactions.all">All</a>
            <a href="<?= getFilterUrl('deposit') ?>" class="filter-tab <?= $filter === 'deposit' ? 'active' : '' ?>" data-i18n="transactions.deposit">Deposits</a>
            <a href="<?= getFilterUrl('withdraw') ?>" class="filter-tab <?= $filter === 'withdraw' ? 'active' : '' ?>" data-i18n="transactions.withdraw">Withdrawals</a>
            <a href="<?= getFilterUrl('game') ?>" class="filter-tab <?= $filter === 'game' ? 'active' : '' ?>" data-i18n="transactions.game">Game</a>
            <a href="<?= getFilterUrl('mining') ?>" class="filter-tab <?= $filter === 'mining' ? 'active' : '' ?>" data-i18n="transactions.mining">Mining</a>
            <a href="<?= getFilterUrl('miningdeposit') ?>" class="filter-tab <?= $filter === 'miningdeposit' ? 'active' : '' ?>" data-i18n="transactions.mining_income">Mining Income</a>
            <a href="<?= getFilterUrl('referral') ?>" class="filter-tab <?= $filter === 'referral' ? 'active' : '' ?>" data-i18n="transactions.referral">Referral</a>
        </div>
    </div>
    
    <!-- Date Filter Indicator -->
    <?php if (!empty($start_date) || !empty($end_date)): ?>
    <div class="date-filter-indicator">
        <div class="date-range">
            <i class="fas fa-calendar-alt"></i>
            <span>
                <?php if (!empty($start_date) && !empty($end_date)): ?>
                    <?= date('d M Y', strtotime($start_date)) ?> - <?= date('d M Y', strtotime($end_date)) ?>
                <?php elseif (!empty($start_date)): ?>
                    From <?= date('d M Y', strtotime($start_date)) ?>
                <?php elseif (!empty($end_date)): ?>
                    Until <?= date('d M Y', strtotime($end_date)) ?>
                <?php endif; ?>
            </span>
        </div>
        <a href="<?= getFilterUrl($filter) ?>" class="clear-filter">
            <i class="fas fa-times"></i>
        </a>
    </div>
    <?php endif; ?>
    
    <!-- Transactions List -->
    <div class="transactions-list">
        <?php if (empty($transactions)): ?>
            <div class="empty-state">
                <i class="fas fa-receipt"></i>
                <?php if ($filter !== 'all'): ?>
                    <p data-i18n="transactions.no_filtered_transactions">No transactions found for the selected filter.</p>
                <?php else: ?>
                    <p data-i18n="transactions.no_transactions">You don't have any transactions yet.</p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <?php foreach ($transactions as $tx): ?>
                <div class="transaction-item">
                    <div class="transaction-icon 
                        <?php
                        if ($tx['type'] == 'deposit') echo 'tx-deposit';
                        elseif ($tx['type'] == 'withdraw') echo 'tx-withdraw';
                        elseif ($tx['type'] == 'referral') echo 'tx-referral';
                        elseif ($tx['type'] == 'game') echo 'tx-game';
                        elseif ($tx['type'] == 'mining') echo 'tx-mining';
                        elseif ($tx['type'] == 'miningdeposit') echo 'tx-mining-income';
                        elseif ($tx['type'] == 'vip') echo 'tx-vip';
                        elseif ($tx['type'] == 'bonus') echo 'tx-bonus';
                        elseif ($tx['type'] == 'referral_transfer') echo 'tx-referral-transfer';
                        else echo 'tx-other';
                        ?>">
                        <i class="fas 
                        <?php
                        if ($tx['type'] == 'deposit') echo 'fa-arrow-down';
                        elseif ($tx['type'] == 'withdraw') echo 'fa-arrow-up';
                        elseif ($tx['type'] == 'referral') echo 'fa-user-friends';
                        elseif ($tx['type'] == 'game') echo 'fa-gamepad';
                        elseif ($tx['type'] == 'mining') echo 'fa-microchip';
                        elseif ($tx['type'] == 'miningdeposit') echo 'fa-coins';
                        elseif ($tx['type'] == 'vip') echo 'fa-crown';
                        elseif ($tx['type'] == 'bonus') echo 'fa-gift';
                        elseif ($tx['type'] == 'referral_transfer') echo 'fa-exchange-alt';
                        else echo 'fa-exchange-alt';
                        ?>"></i>
                    </div>
                    
                    <div class="transaction-details">
                        <div class="tx-upper">
                            <div class="tx-title">
                                <?php 
                                echo isset($transaction_types[$tx['type']]) ? 
                                    htmlspecialchars($transaction_types[$tx['type']]) : 
                                    ucfirst(htmlspecialchars($tx['type']));
                                ?>
                            </div>
                            <div class="tx-amount <?= $tx['amount'] >= 0 ? 'positive' : 'negative' ?>">
                                <?= ($tx['amount'] >= 0 ? '+' : '') . number_format($tx['amount'], 2) ?> USDT
                            </div>
                        </div>
                        
                        <div class="tx-lower">
                            <div class="tx-time">
                                <i class="fas fa-clock"></i>
                                <?= date('d M Y, H:i', strtotime($tx['created_at'])) ?>
                            </div>
                            <div class="tx-status">
                                <span class="status-badge status-<?= strtolower($tx['status']) ?>">
                                    <?= ucfirst($tx['status']) ?>
                                </span>
                            </div>
                        </div>
                        
                        <?php if (!empty($tx['description'])): ?>
                        <div class="tx-description">
                            <?= htmlspecialchars($tx['description']) ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <!-- Pagination (if multiple pages) -->
            <?php if ($total_pages > 1): ?>
            <div class="pagination-container">
                <div class="pagination">
                    <?php if ($page > 1): ?>
                    <a href="?page=1<?= $filter !== 'all' ? '&filter=' . $filter : '' ?><?= !empty($start_date) ? '&start_date=' . $start_date : '' ?><?= !empty($end_date) ? '&end_date=' . $end_date : '' ?>" class="pagination-item">
                        <i class="fas fa-angle-double-left"></i>
                    </a>
                    <a href="?page=<?= $page - 1 ?><?= $filter !== 'all' ? '&filter=' . $filter : '' ?><?= !empty($start_date) ? '&start_date=' . $start_date : '' ?><?= !empty($end_date) ? '&end_date=' . $end_date : '' ?>" class="pagination-item">
                        <i class="fas fa-angle-left"></i>
                    </a>
                    <?php endif; ?>
                    
                    <span class="pagination-info">
                        <?= $page ?> / <?= $total_pages ?>
                    </span>
                    
                    <?php if ($page < $total_pages): ?>
                    <a href="?page=<?= $page + 1 ?><?= $filter !== 'all' ? '&filter=' . $filter : '' ?><?= !empty($start_date) ? '&start_date=' . $start_date : '' ?><?= !empty($end_date) ? '&end_date=' . $end_date : '' ?>" class="pagination-item">
                        <i class="fas fa-angle-right"></i>
                    </a>
                    <a href="?page=<?= $total_pages ?><?= $filter !== 'all' ? '&filter=' . $filter : '' ?><?= !empty($start_date) ? '&start_date=' . $start_date : '' ?><?= !empty($end_date) ? '&end_date=' . $end_date : '' ?>" class="pagination-item">
                        <i class="fas fa-angle-double-right"></i>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Filter Modal -->
<div class="filter-modal" id="filterModal">
    <div class="filter-modal-content">
        <div class="filter-modal-header">
            <h3 data-i18n="transactions.advanced_filters">Advanced Filters</h3>
            <button class="close-btn" id="closeFilterModal">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="filter-modal-body">
            <form action="transactions.php" method="GET">
                <?php if ($filter !== 'all'): ?>
                <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="start_date" data-i18n="transactions.start_date">Start Date</label>
                    <input type="date" id="start_date" name="start_date" class="form-control" value="<?= htmlspecialchars($start_date) ?>">
                </div>
                
                <div class="form-group">
                    <label for="end_date" data-i18n="transactions.end_date">End Date</label>
                    <input type="date" id="end_date" name="end_date" class="form-control" value="<?= htmlspecialchars($end_date) ?>">
                </div>
                
                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary" data-i18n="transactions.apply_filters">Apply Filters</button>
                    <a href="?<?= $filter !== 'all' ? 'filter=' . $filter : '' ?>" class="btn btn-outline-secondary" data-i18n="transactions.clear_filters">Clear Filters</a>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal-backdrop" id="modalBackdrop"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter modal functionality
    const showFilterBtn = document.getElementById('showFilterBtn');
    const filterModal = document.getElementById('filterModal');
    const closeFilterModal = document.getElementById('closeFilterModal');
    const modalBackdrop = document.getElementById('modalBackdrop');
    
    // Show filter modal
    if (showFilterBtn) {
        showFilterBtn.addEventListener('click', function() {
            filterModal.classList.add('active');
            modalBackdrop.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    }
    
    // Close filter modal
    if (closeFilterModal) {
        closeFilterModal.addEventListener('click', function() {
            filterModal.classList.remove('active');
            modalBackdrop.classList.remove('active');
            document.body.style.overflow = '';
        });
    }
    
    // Close modal on backdrop click
    if (modalBackdrop) {
        modalBackdrop.addEventListener('click', function() {
            filterModal.classList.remove('active');
            modalBackdrop.classList.remove('active');
            document.body.style.overflow = '';
        });
    }
    
    // Make transaction items expandable
    const transactionItems = document.querySelectorAll('.transaction-item');
    transactionItems.forEach(function(item) {
        item.addEventListener('click', function() {
            this.classList.toggle('expanded');
        });
    });
});
</script>

<?php
// Include mobile footer
include 'includes/mobile-footer.php';
?>