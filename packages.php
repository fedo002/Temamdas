<?php
// packages.php
require_once 'includes/config.php';
require_once 'includes/header.php';

// Get package type from URL (vip, mining, or all)
$packageType = isset($_GET['type']) ? $_GET['type'] : 'all';

// Validate package type
$validTypes = ['vip', 'mining', 'all'];
if (!in_array($packageType, $validTypes)) {
    $packageType = 'all';
}

// Build SQL query based on package type
$sql = "SELECT * FROM packages WHERE 1=1";
if ($packageType !== 'all') {
    $sql .= " AND package_type = ?";
}
$sql .= " ORDER BY price ASC";

// Prepare and execute query
$stmt = $conn->prepare($sql);
if ($packageType !== 'all') {
    $stmt->bind_param("s", $packageType);
}
$stmt->execute();
$result = $stmt->get_result();
$packages = $result->fetch_all(MYSQLI_ASSOC);

// Group packages by type for display
$vipPackages = array_filter($packages, function($package) {
    return $package['package_type'] === 'vip';
});

$miningPackages = array_filter($packages, function($package) {
    return $package['package_type'] === 'mining';
});
?>

<div class="packages-page">
    <div class="container">
        <h1 class="page-title"><?php echo ($packageType === 'all') ? 'All Packages' : ucfirst($packageType) . ' Packages'; ?></h1>
        
        <div class="packages-nav">
            <a href="packages.php?type=all" class="<?php echo ($packageType === 'all') ? 'active' : ''; ?>">All Packages</a>
            <a href="packages.php?type=vip" class="<?php echo ($packageType === 'vip') ? 'active' : ''; ?>">VIP Packages</a>
            <a href="packages.php?type=mining" class="<?php echo ($packageType === 'mining') ? 'active' : ''; ?>">Mining Packages</a>
        </div>
        
        <?php if ($packageType === 'all' || $packageType === 'vip'): ?>
            <section class="vip-packages">
                <h2>VIP Packages</h2>
                <?php if (empty($vipPackages)): ?>
                    <p class="no-packages">No VIP packages available at the moment.</p>
                <?php else: ?>
                    <div class="packages-grid">
                        <?php foreach ($vipPackages as $package): ?>
                            <div class="package-card vip-package">
                                <div class="package-header">
                                    <h3><?php echo htmlspecialchars($package['name']); ?></h3>
                                    <div class="package-price"><?php echo number_format($package['price'], 2); ?> <?php echo htmlspecialchars($package['currency']); ?></div>
                                </div>
                                <div class="package-body">
                                    <div class="package-image">
                                        <img src="<?php echo htmlspecialchars($package['image_url']); ?>" alt="<?php echo htmlspecialchars($package['name']); ?>">
                                    </div>
                                    <div class="package-description">
                                        <?php echo htmlspecialchars($package['description']); ?>
                                    </div>
                                    <ul class="package-features">
                                        <?php 
                                        $features = explode(',', $package['features']);
                                        foreach ($features as $feature): 
                                        ?>
                                            <li><?php echo htmlspecialchars(trim($feature)); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <div class="package-footer">
                                    <a href="purchase.php?package_id=<?php echo $package['id']; ?>" class="btn btn-primary">Purchase Now</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        <?php endif; ?>
        
        <?php if ($packageType === 'all' || $packageType === 'mining'): ?>
            <section class="mining-packages">
                <h2>Mining Packages</h2>
                <?php if (empty($miningPackages)): ?>
                    <p class="no-packages">No Mining packages available at the moment.</p>
                <?php else: ?>
                    <div class="packages-grid">
                        <?php foreach ($miningPackages as $package): ?>
                            <div class="package-card mining-package">
                                <div class="package-header">
                                    <h3><?php echo htmlspecialchars($package['name']); ?></h3>
                                    <div class="package-price"><?php echo number_format($package['price'], 2); ?> <?php echo htmlspecialchars($package['currency']); ?></div>
                                </div>
                                <div class="package-body">
                                    <div class="package-image">
                                        <img src="<?php echo htmlspecialchars($package['image_url']); ?>" alt="<?php echo htmlspecialchars($package['name']); ?>">
                                    </div>
                                    <div class="package-description">
                                        <?php echo htmlspecialchars($package['description']); ?>
                                    </div>
                                    <ul class="package-features">
                                        <?php 
                                        $features = explode(',', $package['features']);
                                        foreach ($features as $feature): 
                                        ?>
                                            <li><?php echo htmlspecialchars(trim($feature)); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <div class="package-footer">
                                    <a href="purchase.php?package_id=<?php echo $package['id']; ?>" class="btn btn-primary">Purchase Now</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>