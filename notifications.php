<?php
// mobile/notifications.php - Notifications page for mobile
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=notifications.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$page_title = "Notifications";

// Include notification functions
require_once 'includes/notification_functions.php';

// Mark all notifications as read
if (isset($_GET['mark_all']) && $_GET['mark_all'] == '1') {
    if (markAllNotificationsAsRead($user_id)) {
        $_SESSION['success_message'] = "All notifications marked as read";
    } else {
        $_SESSION['error_message'] = "Error marking notifications as read";
    }
    
    // Redirect back to notifications page
    header("Location: notifications.php");
    exit();
}

// Mark single notification as read
if (isset($_GET['mark_read']) && is_numeric($_GET['mark_read'])) {
    $notification_id = (int)$_GET['mark_read'];
    
    if (markNotificationAsRead($notification_id, $user_id)) {
        $_SESSION['success_message'] = "Notification marked as read";
    } else {
        $_SESSION['error_message'] = "Error marking notification as read";
    }
    
    // Redirect back to notifications page
    header("Location: notifications.php");
    exit();
}

// Get notifications
try {
    // Get unread notification count
    $notificationData = getUserNotifications($user_id);
    $unread_count = $notificationData['unread_count'];
    
    // Get all notifications (read and unread)
    $allNotificationsData = getAllUserNotifications($user_id);
    $notifications = $allNotificationsData['notifications'];
} catch (Exception $e) {
    $notifications = [];
    $unread_count = 0;
    $_SESSION['error_message'] = "Error loading notifications.";
}

// Include header
include 'includes/mobile-header.php';
?>

<div class="notifications-page">
    <div class="page-header">
        <h1>Notifications</h1>
        
        <?php if (count($notifications) > 0): ?>
        <a href="notifications.php?mark_all=1" class="mark-all-btn">
            <i class="fas fa-check-double"></i> Mark All Read
        </a>
        <?php endif; ?>
    </div>
    
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['success_message'] ?>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger">
            <?= $_SESSION['error_message'] ?>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>
    
    <div class="notifications-container">
        <?php if (count($notifications) > 0): ?>
            <?php foreach ($notifications as $notification): ?>
                <div class="notification-card <?= (isset($notification['is_read']) && $notification['is_read'] == 1) ? 'read' : 'unread' ?>">
                    <div class="notification-badge">
                        <?php if (isset($notification['notification_type']) && $notification['notification_type'] == 'vip'): ?>
                            <i class="fas fa-crown"></i>
                        <?php elseif (isset($notification['notification_type']) && $notification['notification_type'] == 'mining'): ?>
                            <i class="fas fa-microchip"></i>
                        <?php else: ?>
                            <i class="fas fa-bell"></i>
                        <?php endif; ?>
                    </div>
                    
                    <div class="notification-content">
                        <div class="notification-header">
                            <h3><?= htmlspecialchars($notification['title'] ?? 'Notification') ?></h3>
                            <span class="notification-time">
                                <?= isset($notification['created_at']) ? date('d.m.Y H:i', strtotime($notification['created_at'])) : date('d.m.Y H:i') ?>
                            </span>
                        </div>
                        
                        <p class="notification-text"><?= htmlspecialchars($notification['content'] ?? 'No content') ?></p>
                        
                        <?php if (isset($notification['is_read']) && $notification['is_read'] == 0): ?>
                            <div class="notification-actions">
                                <a href="notifications.php?mark_read=<?= $notification['notification_id'] ?? 0 ?>" class="mark-read-btn">
                                    <i class="fas fa-check"></i> Mark as Read
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-notifications">
                <i class="fas fa-bell-slash"></i>
                <h3>No Notifications</h3>
                <p>You don't have any notifications at the moment.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
/* Notifications Page Styles */
.notifications-page {
    padding: 15px;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.page-header h1 {
    font-size: 1.8rem;
    font-weight: 700;
    margin: 0;
    color: #000;
}

.mark-all-btn {
    display: flex;
    align-items: center;
    color: #000;
    font-size: 0.9rem;
    font-weight: 500;
    text-decoration: none;
}

.mark-all-btn i {
    margin-right: 5px;
}

.notifications-container {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.notification-card {
    background-color: white;
    border-radius: 12px;
    padding: 15px;
    display: flex;
    align-items: flex-start;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    position: relative;
    transition: all 0.3s ease;
}

.notification-card.unread {
    border-left: 4px solid var(--primary-color);
}

.notification-card.read {
    opacity: 0.7;
}

.notification-badge {
    width: 40px;
    height: 40px;
    min-width: 40px;
    border-radius: 50%;
    background-color: rgba(115, 103, 240, 0.1);
    color: var(--primary-color);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    margin-right: 15px;
}

.notification-card[data-type="vip"] .notification-badge {
    background-color: rgba(255, 159, 67, 0.1);
    color: #ff9f43;
}

.notification-card[data-type="mining"] .notification-badge {
    background-color: rgba(0, 207, 232, 0.1);
    color: #00cfe8;
}

.notification-content {
    flex: 1;
}

.notification-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 10px;
}

.notification-header h3 {
    font-size: 1rem;
    font-weight: 600;
    margin: 0;
    padding-right: 10px;
}

.notification-time {
    font-size: 0.8rem;
    color: var(--text-muted);
    white-space: nowrap;
}

.notification-text {
    font-size: 0.9rem;
    color: #666;
    margin: 0 0 10px;
    line-height: 1.5;
}

.notification-actions {
    display: flex;
    justify-content: flex-end;
}

.mark-read-btn {
    font-size: 0.8rem;
    color: var(--primary-color);
    background: none;
    border: none;
    display: flex;
    align-items: center;
    padding: 5px 0;
    cursor: pointer;
    text-decoration: none;
}

.mark-read-btn i {
    margin-right: 5px;
}

/* Empty State */
.empty-notifications {
    text-align: center;
    padding: 40px 20px;
    background-color: white;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.empty-notifications i {
    font-size: 3rem;
    color: #ddd;
    margin-bottom: 15px;
}

.empty-notifications h3 {
    font-size: 1.2rem;
    font-weight: 600;
    margin: 0 0 10px;
}

.empty-notifications p {
    color: var(--text-muted);
    margin: 0;
}
</style>

<?php include 'includes/mobile-footer.php'; ?>