</main>

    <!-- Mobile Bottom Navigation -->
    <nav class="mobile-bottom-nav">
        <a href="index.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">
            <i class="fas fa-home"></i>
            <span data-i18n="buttons.home">Home</span>
        </a>
        
        <a href="packages.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'packages.php' ? 'active' : '' ?>">
            <i class="fas fa-cubes"></i>
            <span data-i18n="buttons.packages">Packages</span>
        </a>
        
        <?php if(isset($_SESSION['user_id'])): ?>
            <a href="daily-game.php" class="nav-item nav-item-main <?= basename($_SERVER['PHP_SELF']) == 'daily-game.php' ? 'active' : '' ?>">
                <div class="main-btn">
                    <i class="fas fa-trophy"></i>
                </div>
                <span data-i18n="dashboard.daily_game">Game</span>
            </a>
            
            <a href="notifications.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'notifications.php' ? 'active' : '' ?>">
                <i class="fas fa-bell"></i>
                <?php if($unread_notification_count > 0): ?>
                    <span class="notification-badge"><?= $unread_notification_count > 9 ? '9+' : $unread_notification_count ?></span>
                <?php endif; ?>
                <span data-i18n="buttons.notifications">Notifications</span>
            </a>
            
            <a href="profile.php" class="nav-item <?= in_array(basename($_SERVER['PHP_SELF']), ['profile.php', 'dashboard.php']) ? 'active' : '' ?>">
                <i class="fas fa-user"></i>
                <span data-i18n="buttons.myAccount">Account</span>
            </a>
        <?php else: ?>
            <a href="login.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'login.php' ? 'active' : '' ?>">

                    <i class="fas fa-sign-in-alt"></i>
                <span data-i18n="buttons.login">Login</span>
            </a>
            
            <a href="register.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'register.php' ? 'active' : '' ?>">
                <i class="fas fa-user-plus"></i>
                <span data-i18n="buttons.register">Register</span>
            </a>
        <?php endif; ?>
    </nav>

    
    <!-- Backdrop Overlay -->
    <div class="backdrop-overlay" id="backdropOverlay"></div>
    
</body>
</html>