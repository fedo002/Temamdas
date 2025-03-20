<?php
// mobile/support.php - Support mobile page
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=support.php');
    exit;
}

$message = '';
$messageType = '';

try {
    // Get database connection
    $conn = $GLOBALS['db']->getConnection();

    // Get user info
    $user_id = $_SESSION['user_id'];
    
    // Get VIP level information to check support access
    $query = "SELECT u.vip_level, v.tgsupport, v.wpsupport 
              FROM users u
              LEFT JOIN vip_packages v ON u.vip_level = v.id
              WHERE u.id = ?";
    
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        throw new Exception("Prepare failed for VIP query: " . $conn->error);
    }
    
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $hasWhatsappSupport = false;
    $hasTelegramSupport = false;
    
    if ($result->num_rows > 0) {
        $vip_info = $result->fetch_assoc();
        $hasWhatsappSupport = isset($vip_info['wpsupport']) && (int)$vip_info['wpsupport'] === 1;
        $hasTelegramSupport = isset($vip_info['tgsupport']) && (int)$vip_info['tgsupport'] === 1;
    }
    
    // Get support tickets
    $query = "
        SELECT st.*, 
        (SELECT COUNT(*) FROM support_messages sm 
        WHERE sm.ticket_id = st.id AND sm.is_user_message = 0 AND sm.is_read = 0) as unread_messages
        FROM support_tickets st 
        WHERE st.user_id = ? 
        ORDER BY st.created_at DESC
    ";
    
    $stmt = $conn->prepare($query);
    
    if ($stmt === false) {
        throw new Exception("Prepare failed for tickets query: " . $conn->error);
    }
    
    $stmt->bind_param("i", $user_id);
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $tickets_result = $stmt->get_result();
    $user_tickets = [];
    
    while ($ticket = $tickets_result->fetch_assoc()) {
        $user_tickets[] = $ticket;
    }
} catch (Exception $e) {
    // Log error
    error_log($e->getMessage());
    
    // User-facing error message
    $message = "An error occurred. Please try again later.";
    $messageType = 'error';
    
    // Initialize empty arrays in case of error
    $user_tickets = [];
    $hasWhatsappSupport = false;
    $hasTelegramSupport = false;
}

// Create support ticket
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_ticket'])) {
    $subject = trim($_POST['subject'] ?? '');
    $message_content = trim($_POST['message'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $process = trim($_POST['process'] ?? ''); // New process field
    
    // Basic validation
    if (empty($subject) || empty($message_content) || empty($category) || empty($process)) {
        $message = 'Please fill in all required fields.';
        $messageType = 'error';
    } else {
        try {
            // Begin transaction
            $conn->begin_transaction();
            
            // Check if the support_tickets table has the process column
            $tableCheck = $conn->query("SHOW COLUMNS FROM support_tickets LIKE 'process'");
            $processColumnExists = $tableCheck->num_rows > 0;
            
            if ($processColumnExists) {
                // Create support ticket with process field
                $stmt = $conn->prepare("INSERT INTO support_tickets (user_id, subject, status, priority, process, created_at, last_updated) VALUES (?, ?, 'open', ?, ?, NOW(), NOW())");
                $stmt->bind_param("isss", $user_id, $subject, $category, $process);
            } else {
                // Create support ticket without process field
                $stmt = $conn->prepare("INSERT INTO support_tickets (user_id, subject, status, priority, created_at, last_updated) VALUES (?, ?, 'open', ?, NOW(), NOW())");
                $stmt->bind_param("iss", $user_id, $subject, $category);
            }
            
            if ($stmt->execute()) {
                $ticketId = $conn->insert_id;
                
                // Save first message
                $stmt = $conn->prepare("INSERT INTO support_messages (ticket_id, user_id, message, is_user_message, created_at) VALUES (?, ?, ?, 1, NOW())");
                $stmt->bind_param("iis", $ticketId, $user_id, $message_content);
                
                if ($stmt->execute()) {
                    // Commit transaction
                    $conn->commit();
                    
                    $message = "Support ticket created successfully. Ticket ID: #{$ticketId}";
                    $messageType = 'success';
                    
                    // Clear form fields
                    $subject = $message_content = $category = $process = '';
                    
                    // Refresh tickets list
                    $query = "SELECT st.*, 
                              (SELECT COUNT(*) FROM support_messages sm WHERE sm.ticket_id = st.id AND sm.is_read = 0 AND sm.is_user_message = 0) as unread_messages 
                              FROM support_tickets st WHERE st.user_id = ? ORDER BY st.last_updated DESC";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $tickets_result = $stmt->get_result();
                    $user_tickets = [];
                    
                    while ($ticket = $tickets_result->fetch_assoc()) {
                        $user_tickets[] = $ticket;
                    }
                } else {
                    // Rollback transaction
                    $conn->rollback();
                    $message = 'An error occurred while creating the ticket. Please try again.';
                    $messageType = 'error';
                }
            } else {
                // Rollback transaction
                $conn->rollback();
                $message = 'An error occurred while creating the ticket. Please try again.';
                $messageType = 'error';
            }
        } catch (Exception $e) {
            // Rollback transaction
            $conn->rollback();
            $message = 'An error occurred: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
}

// Reply to a ticket
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_ticket'])) {
    $ticket_id = (int)$_POST['ticket_id'];
    $reply_message = trim($_POST['reply_message']);
    
    if (empty($reply_message)) {
        $message = 'Please enter a message.';
        $messageType = 'error';
    } else {
        // Check if user owns this ticket
        $stmt = $conn->prepare("SELECT id FROM support_tickets WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $ticket_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $message = 'You do not have permission to reply to this ticket.';
            $messageType = 'error';
        } else {
            // Save reply
            $stmt = $conn->prepare("INSERT INTO support_messages (ticket_id, user_id, message, is_user_message, created_at) VALUES (?, ?, ?, 1, NOW())");
            $stmt->bind_param("iis", $ticket_id, $user_id, $reply_message);
            
            if ($stmt->execute()) {
                // Update ticket status
                $stmt = $conn->prepare("UPDATE support_tickets SET status = 'awaiting_response', last_updated = NOW() WHERE id = ?");
                $stmt->bind_param("i", $ticket_id);
                $stmt->execute();
                
                $message = 'Your reply has been sent.';
                $messageType = 'success';
                
                // Refresh tickets
                $query = "SELECT st.*, 
                          (SELECT COUNT(*) FROM support_messages sm WHERE sm.ticket_id = st.id AND sm.is_read = 0 AND sm.is_user_message = 0) as unread_messages 
                          FROM support_tickets st WHERE st.user_id = ? ORDER BY st.last_updated DESC";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $tickets_result = $stmt->get_result();
                $user_tickets = [];
                
                while ($ticket = $tickets_result->fetch_assoc()) {
                    $user_tickets[] = $ticket;
                }
            } else {
                $message = 'An error occurred while sending your reply. Please try again.';
                $messageType = 'error';
            }
        }
    }
}

// View ticket
if (isset($_GET['view_ticket']) && is_numeric($_GET['view_ticket'])) {
    $ticket_id = (int)$_GET['view_ticket'];
    
    try {
        // Check if user owns this ticket
        $stmt = $conn->prepare("SELECT * FROM support_tickets WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $ticket_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $message = "You don't have permission to view this ticket.";
            $messageType = 'error';
            $active_ticket = null;
            $ticket_messages = [];
        } else {
            $active_ticket = $result->fetch_assoc();
            
            // Get ticket messages
            $stmt = $conn->prepare("
                SELECT sm.*, 
                       COALESCE(u.full_name, u.username, 'Support Team') as sender_name
                FROM support_messages sm
                LEFT JOIN users u ON sm.user_id = u.id
                WHERE sm.ticket_id = ?
                ORDER BY sm.created_at ASC
            ");
            $stmt->bind_param("i", $ticket_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $ticket_messages = [];
            while ($message_row = $result->fetch_assoc()) {
                $ticket_messages[] = $message_row;
            }
            
            // Mark admin messages as read
            $stmt = $conn->prepare("
                UPDATE support_messages 
                SET is_read = 1 
                WHERE ticket_id = ? AND is_user_message = 0
            ");
            $stmt->bind_param("i", $ticket_id);
            $stmt->execute();
        }
    } catch (Exception $e) {
        error_log($e->getMessage());
        $message = "An error occurred: " . $e->getMessage();
        $messageType = 'error';
        $active_ticket = null;
        $ticket_messages = [];
    }
}

$page_title = 'Support';
include 'includes/mobile-header.php';
?>

<div class="support-page">
    <h1 class="page-title">Support</h1>
    
    <!-- VIP Support Buttons (conditional) -->
    <div class="vip-support-buttons">
        <?php if ($hasWhatsappSupport || $hasTelegramSupport): ?>
            <?php if ($hasWhatsappSupport): ?>
                <a href="https://wa.me/yoursupportnumber" class="vip-support-btn whatsapp-btn">
                    <i class="fab fa-whatsapp"></i> WhatsApp Support
                </a>
            <?php endif; ?>
            
            <?php if ($hasTelegramSupport): ?>
                <a href="https://t.me/yourtelegramname" class="vip-support-btn telegram-btn">
                    <i class="fab fa-telegram"></i> Telegram Support
                </a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    
    <!-- Tabs Navigation -->
    <div class="tabs-container">
        <div class="tab-buttons">
            <button class="tab-button <?php echo (!isset($_GET['view_ticket'])) ? 'active' : ''; ?>" data-tab="tickets">
                <i class="fas fa-ticket-alt"></i> My Tickets
            </button>
            <button class="tab-button" data-tab="new-ticket">
                <i class="fas fa-plus-circle"></i> New Ticket
            </button>
        </div>
        
        <!-- Tickets Tab -->
        <div class="tab-content">
            <div class="tab-pane <?php echo (!isset($_GET['view_ticket'])) ? 'active' : ''; ?>" id="tickets">
                <?php if (!empty($message) && !isset($_GET['view_ticket'])): ?>
                <div class="alert alert-<?php echo $messageType === 'success' ? 'success' : 'danger'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
                <?php endif; ?>
                
                <?php if (isset($active_ticket)): ?>
                    <!-- Ticket Detail View -->
                    <div class="ticket-detail">
                        <div class="ticket-header">
                            <div class="ticket-title">
                                <h2><?php echo htmlspecialchars($active_ticket['subject']); ?></h2>
                                <div class="ticket-meta">
                                    <span class="ticket-id">#<?php echo $active_ticket['id']; ?></span>
                                    <span class="ticket-status status-<?php echo strtolower($active_ticket['status']); ?>">
                                        <?php 
                                        $status = $active_ticket['status'];
                                        if ($status == 'open') echo 'Open';
                                        elseif ($status == 'closed') echo 'Closed';
                                        elseif ($status == 'awaiting_response') echo 'Awaiting Response';
                                        else echo ucfirst($status);
                                        ?>
                                    </span>
                                </div>
                            </div>
                            <div class="ticket-info">
                                <div><strong>Priority:</strong> <?php echo ucfirst($active_ticket['priority']); ?></div>
                                <?php if (isset($active_ticket['process'])): ?>
                                <div><strong>Issue:</strong> <span class="process-name"><?php echo $active_ticket['process']; ?></span></div>
                                <?php endif; ?>
                                <div><strong>Created:</strong> <?php echo date('d M Y', strtotime($active_ticket['created_at'])); ?></div>
                                <div><strong>Updated:</strong> <?php echo date('d M Y', strtotime($active_ticket['last_updated'])); ?></div>
                            </div>
                            <a href="support.php" class="back-btn">
                                <i class="fas fa-arrow-left"></i> Back to tickets
                            </a>
                        </div>
                        
                        <div class="ticket-conversation">
                            <?php if (!empty($ticket_messages)): ?>
                                <?php foreach ($ticket_messages as $msg): ?>
                                    <div class="message <?php echo $msg['is_user_message'] ? 'user-message' : 'admin-message'; ?>">
                                        <div class="message-header">
                                            <strong>
                                                <?php 
                                                echo $msg['is_user_message'] ? 'You' : 'Support Team'; 
                                                ?>
                                            </strong>
                                            <span><?php echo date('d M Y H:i', strtotime($msg['created_at'])); ?></span>
                                        </div>
                                        <div class="message-content">
                                            <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="no-messages">No messages yet.</p>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($active_ticket['status'] !== 'closed'): ?>
                            <div class="reply-form">
                                <h3>Reply to This Ticket</h3>
                                <form method="POST" action="">
                                    <input type="hidden" name="ticket_id" value="<?php echo $active_ticket['id']; ?>">
                                    <div class="form-group">
                                        <textarea name="reply_message" class="form-control" rows="4" placeholder="Type your reply here..." required></textarea>
                                    </div>
                                    <button type="submit" name="reply_ticket" class="btn btn-primary">
                                        <i class="fas fa-paper-plane"></i> Send Reply
                                    </button>
                                </form>
                            </div>
                        <?php else: ?>
                            <div class="closed-ticket-message">
                                <p>This ticket is closed. If you have another question, please create a new support ticket.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <!-- Tickets List -->
                    <div class="tickets-list">
                        <?php if (empty($user_tickets)): ?>
                            <div class="no-tickets">
                                <i class="fas fa-ticket-alt"></i>
                                <p>You haven't created any support tickets yet.</p>
                                <button class="btn btn-primary tab-trigger" data-tab="new-ticket">
                                    <i class="fas fa-plus-circle"></i> Create New Ticket
                                </button>
                            </div>
                        <?php else: ?>
                            <div class="tickets-grid">
                                <?php foreach ($user_tickets as $ticket): ?>
                                    <a href="support.php?view_ticket=<?php echo $ticket['id']; ?>" class="ticket-card">
                                        <div class="ticket-card-header">
                                            <span class="ticket-id">#<?php echo $ticket['id']; ?></span>
                                            <span class="ticket-status status-<?php echo strtolower($ticket['status']); ?>">
                                                <?php 
                                                $status = $ticket['status'];
                                                if ($status == 'open') echo 'Open';
                                                elseif ($status == 'closed') echo 'Closed';
                                                elseif ($status == 'awaiting_response') echo 'Awaiting Response';
                                                else echo ucfirst($status);
                                                ?>
                                            </span>
                                        </div>
                                        <div class="ticket-card-body">
                                            <h3 class="ticket-subject"><?php echo htmlspecialchars($ticket['subject']); ?></h3>
                                            <?php if ($ticket['unread_messages'] > 0): ?>
                                                <span class="unread-badge"><?php echo $ticket['unread_messages']; ?> new</span>
                                            <?php endif; ?>
                                            <div class="ticket-meta">
                                                <div class="ticket-priority">
                                                    <i class="fas fa-flag"></i> <?php echo ucfirst($ticket['priority']); ?>
                                                </div>
                                                <div class="ticket-date">
                                                    <i class="fas fa-clock"></i> <?php echo date('d M Y', strtotime($ticket['last_updated'])); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- New Ticket Tab -->
            <div class="tab-pane" id="new-ticket">
                <div class="new-ticket-form">
                    <div class="contact-info">
                        <h3><i class="fas fa-headset"></i> Contact Support</h3>
                        <p>Have a question or need help? Fill out the form, and our support team will get back to you as soon as possible.</p>
                        
                        <div class="contact-method">
                            <h4><i class="fas fa-comment-alt"></i> Chat Support</h4>
                            <p>Working Hours: 09:00 - 18:00, Weekdays</p>
                        </div>
                    </div>
                    
                    <form method="POST" action="support.php?new_ticket=1" class="support-form">
                        <h3><i class="fas fa-plus-circle"></i> Create New Ticket</h3>
                        
                        <div class="form-group">
                            <label for="process">Issue Type</label>
                            <select id="process" name="process" required>
                                <option value="">Select Issue Type</option>
                                <option value="withdrawal_issues">Withdrawal Issues</option>
                                <option value="deposit_issues">Deposit Issues</option>
                                <option value="password_issues">Password Issues</option>
                                <option value="event_issues">Event Issues</option>
                                <option value="daily_game_issues">Daily Game Issues</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="category">Ticket Priority</label>
                            <select id="category" name="category" required>
                                <option value="">Select Priority</option>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="subject">Subject</label>
                            <input type="text" id="subject" name="subject" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea id="message" name="message" rows="5" required></textarea>
                        </div>
                        
                        <button type="submit" name="submit_ticket" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Create Ticket
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Support Page Styles */
.support-page {
    padding: 15px;
}

.page-title {
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 20px;
    color: var(--dark-color);
}

/* VIP Support Buttons */
.vip-support-buttons {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
}

.vip-support-btn {
    flex: 1;
    padding: 12px 16px;
    text-align: center;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.95rem;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
}

.vip-support-btn i {
    margin-right: 8px;
    font-size: 1.2rem;
}

.whatsapp-btn {
    background-color: #25D366;
    color: white;
}

.telegram-btn {
    background-color: #0088cc;
    color: white;
}

/* Tab navigation */
.tabs-container {
    background-color: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
}

.tab-buttons {
    display: flex;
    border-bottom: 1px solid #eee;
}

.tab-button {
    flex: 1;
    padding: 15px;
    text-align: center;
    background: none;
    border: none;
    font-size: 0.9rem;
    font-weight: 500;
    color: #6e6b7b;
    cursor: pointer;
    position: relative;
}

.tab-button i {
    margin-right: 5px;
}

.tab-button.active {
    color: var(--primary-color);
}

.tab-button.active::after {
    content: '';
    position: absolute;
    bottom: -1px;
    left: 0;
    width: 100%;
    height: 3px;
    background-color: var(--primary-color);
}

.tab-content {
    padding: 20px;
}

.tab-pane {
    display: none;
}

.tab-pane.active {
    display: block;
}

/* Tickets List */
.tickets-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 15px;
}

.ticket-card {
    background-color: #f8f9fa;
    border-radius: 10px;
    overflow: hidden;
    text-decoration: none;
    color: inherit;
    display: block;
    transition: transform 0.3s, box-shadow 0.3s;
}

.ticket-card:active {
    transform: scale(0.98);
}

.ticket-card-header {
    padding: 12px 15px;
    background-color: #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.ticket-id {
    font-weight: 500;
    font-size: 0.9rem;
}

.ticket-status {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 500;
}

.status-open {
    background-color: #7367f0;
    color: white;
}

.status-closed {
    background-color: #82868b;
    color: white;
}

.status-pending {
    background-color: #ff9f43;
    color: white;
}

.status-awaiting_response {
    background-color: #28c76f;
    color: white;
}

.ticket-card-body {
    padding: 15px;
    position: relative;
}

.ticket-subject {
    font-size: 1rem;
    font-weight: 600;
    margin: 0 0 10px;
    padding-right: 40px;
}

.unread-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    background-color: #ea5455;
    color: white;
    padding: 3px 8px;
    border-radius: 10px;
    font-size: 0.7rem;
    font-weight: 500;
}

.ticket-meta {
    display: flex;
    justify-content: space-between;
    font-size: 0.8rem;
    color: #6e6b7b;
}

.ticket-priority i, 
.ticket-date i {
    margin-right: 5px;
}

/* No tickets message */
.no-tickets {
    text-align: center;
    padding: 30px 20px;
}

.no-tickets i {
    font-size: 3rem;
    color: #ddd;
    margin-bottom: 15px;
}

.no-tickets p {
    margin-bottom: 20px;
    color: #6e6b7b;
}

/* Ticket Detail View */
.ticket-detail {
    background-color: #f8f9fa;
    border-radius: 10px;
    overflow: hidden;
}

.ticket-header {
    padding: 15px;
    background-color: white;
    border-bottom: 1px solid #eee;
}

.ticket-title h2 {
    font-size: 1.2rem;
    font-weight: 600;
    margin: 0 0 10px;
}

.ticket-meta {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
}

.ticket-info {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    font-size: 0.8rem;
    color: #6e6b7b;
    margin-bottom: 15px;
}

.back-btn {
    display: inline-block;
    padding: 8px 12px;
    background-color: #f0f0f0;
    color: #333;
    text-decoration: none;
    border-radius: 6px;
    font-size: 0.9rem;
}

.ticket-conversation {
    padding: 15px;
}

.message {
    margin-bottom: 20px;
    border-radius: 10px;
    overflow: hidden;
}

.user-message {
    background-color: rgba(115, 103, 240, 0.1);
}

.admin-message {
    background-color: white;
}

.message-header {
    padding: 10px 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.9rem;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.message-header span {
    font-size: 0.8rem;
    color: #6e6b7b;
}

.message-content {
    padding: 15px;
    font-size: 0.95rem;
    line-height: 1.5;
}

.no-messages {
    text-align: center;
    padding: 20px;
    color: #6e6b7b;
}

/* Reply Form */
.reply-form {
    padding: 15px;
    background-color: white;
    border-top: 1px solid #eee;
}

.reply-form h3 {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 15px;
}

.form-group {
    margin-bottom: 15px;
}

.form-control {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 0.95rem;
}

textarea.form-control {
    resize: vertical;
}

.btn {
    padding: 12px 20px;
    border-radius: 8px;
    font-weight: 500;
    font-size: 0.95rem;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: background-color 0.3s;
}

.btn i {
    margin-right: 8px;
}

.btn-primary {
    background-color: var(--primary-color);
    color: white;
}

.closed-ticket-message {
    padding: 15px;
    background-color: #fff3cd;
    color: #856404;
    text-align: center;
    border-top: 1px solid #ffeeba;
}

/* New Ticket Form */
.new-ticket-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.contact-info {
    background-color: #f8f9fa;
    border-radius: 10px;
    padding: 15px;
}

.contact-info h3 {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 10px;
}

.contact-info p {
    color: #6e6b7b;
    margin-bottom: 15px;
}

.contact-method {
    background-color: white;
    border-radius: 8px;
    padding: 15px;
}

.contact-method h4 {
    font-size: 1rem;
    font-weight: 600;
    margin: 0 0 10px;
}

.contact-method p {
    margin: 0;
    font-size: 0.9rem;
}

.support-form {
    background-color: white;
    border-radius: 10px;
    padding: 20px;
}

.support-form h3 {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 20px;
}

.support-form label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    font-size: 0.9rem;
}

.support-form input,
.support-form select,
.support-form textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 0.95rem;
}

.support-form button {
    width: 100%;
    margin-top: 10px;
}

/* Alert Boxes */
.alert {
    padding: 12px 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 0.95rem;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab functionality
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabPanes = document.querySelectorAll('.tab-pane');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            
            // Remove active class from all buttons and panes
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabPanes.forEach(pane => pane.classList.remove('active'));
            
            // Add active class to clicked button and corresponding pane
            this.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        });
    });
    
    // Tab trigger buttons (e.g. from "no tickets" message)
    const tabTriggers = document.querySelectorAll('.tab-trigger');
    tabTriggers.forEach(trigger => {
        trigger.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            const tabButton = document.querySelector(`.tab-button[data-tab="${tabId}"]`);
            if (tabButton) {
                tabButton.click();
            }
        });
    });
    
    // Check URL parameter to set active tab
    const urlParams = new URLSearchParams(window.location.search);
    const newTicketParam = urlParams.get('new_ticket');
    
    if (newTicketParam) {
        const newTicketTab = document.querySelector('.tab-button[data-tab="new-ticket"]');
        if (newTicketTab) {
            newTicketTab.click();
        }
    }
    
    // Display issue type in user-friendly format
    const processValues = {
        'withdrawal_issues': 'Withdrawal Issues',
        'deposit_issues': 'Deposit Issues',
        'password_issues': 'Password Issues',
        'event_issues': 'Event Issues',
        'daily_game_issues': 'Daily Game Issues',
        'other': 'Other'
    };
    
    // Update displayed process names if present on the page
    const processElements = document.querySelectorAll('.process-name');
    processElements.forEach(element => {
        const processValue = element.textContent.trim();
        if (processValues[processValue]) {
            element.textContent = processValues[processValue];
        }
    });
});
</script>

<?php include 'includes/mobile-footer.php'; ?>