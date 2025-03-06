<?php
// support.php
require_once 'includes/header.php';
require_once 'includes/config.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_ticket'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message_content = trim($_POST['message'] ?? '');
    $category = trim($_POST['category'] ?? '');
    
    // Basic validation
    if (empty($name) || empty($email) || empty($subject) || empty($message_content) || empty($category)) {
        $message = 'Please fill in all required fields.';
        $messageType = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Please enter a valid email address.';
        $messageType = 'error';
    } else {
        // Insert support ticket into database
        $stmt = $conn->prepare("INSERT INTO support_tickets (name, email, subject, message, category, status, created_at) VALUES (?, ?, ?, ?, ?, 'open', NOW())");
        $stmt->bind_param("sssss", $name, $email, $subject, $message_content, $category);
        
        if ($stmt->execute()) {
            $ticketId = $conn->insert_id;
            
            // Send confirmation email
            $to = $email;
            $emailSubject = "Support Ticket #{$ticketId} Confirmation";
            $headers = "From: support@yourdomain.com\r\n";
            $headers .= "Reply-To: support@yourdomain.com\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            
            $emailMessage = "
            <html>
            <head>
                <title>Support Ticket Confirmation</title>
            </head>
            <body>
                <h2>Support Ticket #{$ticketId} Confirmation</h2>
                <p>Dear {$name},</p>
                <p>We have received your support request with the following details:</p>
                <p><strong>Subject:</strong> {$subject}</p>
                <p><strong>Category:</strong> {$category}</p>
                <p><strong>Message:</strong><br>{$message_content}</p>
                <p>We will respond to your inquiry as soon as possible. Please keep this email for reference.</p>
                <p>Thank you for contacting us.</p>
                <p>Best regards,<br>Support Team</p>
            </body>
            </html>
            ";
            
            // Uncomment to send email in production
            // mail($to, $emailSubject, $emailMessage, $headers);
            
            $message = "Your support ticket has been submitted successfully. Ticket ID: #{$ticketId}";
            $messageType = 'success';
            
            // Clear form fields
            $name = $email = $subject = $message_content = $category = '';
        } else {
            $message = 'An error occurred while submitting your ticket. Please try again.';
            $messageType = 'error';
        }
    }
}

// Fetch FAQ categories
$stmt = $conn->prepare("SELECT DISTINCT category FROM faqs WHERE status = 'active' ORDER BY category ASC");
$stmt->execute();
$result = $stmt->get_result();
$faqCategories = [];
while ($row = $result->fetch_assoc()) {
    $faqCategories[] = $row['category'];
}

// Fetch FAQs
$stmt = $conn->prepare("SELECT * FROM faqs WHERE status = 'active' ORDER BY category ASC, display_order ASC");
$stmt->execute();
$result = $stmt->get_result();
$faqs = $result->fetch_all(MYSQLI_ASSOC);
?>

<div class="support-page">
    <div class="support-hero">
        <div class="container">
            <h1 data-i18n="support_help">Support & Help Center</h1>
            <p class="support-subtitle" data-i18n="support_subtitle">Get the help you need with our resources and support team</p>
        </div>
    </div>
    
    <div class="container">
        <div class="support-tabs">
            <button class="tab-button active" data-tab="faq" data-i18n="faq">FAQ</button>
            <button class="tab-button" data-tab="contact" data-i18n="contact_us">Contact Us</button>
            <button class="tab-button" data-tab="ticket" data-i18n="check_ticket">Check Ticket Status</button>
        </div>
        
        <div class="tab-content">
            <!-- FAQ Tab -->
            <div class="tab-pane active" id="faq">
                <div class="faq-search">
                    <input type="text" id="faqSearch" placeholder="Search FAQs..." data-i18n-attr="placeholder" data-i18n="search_faqs_placeholder">
                    <button class="search-btn">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                
                <div class="faq-categories">
                    <button class="category-btn active" data-category="all" data-i18n="all">All</button>
                    <?php foreach ($faqCategories as $category): ?>
                    <button class="category-btn" data-category="<?php echo htmlspecialchars($category); ?>">
                        <?php echo htmlspecialchars($category); ?>
                    </button>
                    <?php endforeach; ?>
                </div>
                
                <div class="faq-list">
                    <?php if (empty($faqs)): ?>
                    <div class="no-faqs" data-i18n="no_faqs">No FAQs available at the moment.</div>
                    <?php else: ?>
                        <?php 
                        $currentCategory = '';
                        foreach ($faqs as $faq): 
                            if ($currentCategory !== $faq['category']) {
                                $currentCategory = $faq['category'];
                                echo '<div class="faq-category-title" data-category="' . htmlspecialchars($currentCategory) . '">' . htmlspecialchars($currentCategory) . '</div>';
                            }
                        ?>
                        <div class="faq-item" data-category="<?php echo htmlspecialchars($faq['category']); ?>">
                            <div class="faq-question">
                                <h3><?php echo htmlspecialchars($faq['question']); ?></h3>
                                <span class="faq-toggle">
                                    <i class="fas fa-plus"></i>
                                </span>
                            </div>
                            <div class="faq-answer">
                                <div class="faq-answer-content">
                                    <?php echo $faq['answer']; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Contact Tab -->
            <div class="tab-pane" id="contact">
                <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $messageType === 'success' ? 'success' : 'danger'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
                <?php endif; ?>
                
                <div class="contact-form-container">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="contact-info">
                                <h2 data-i18n="get_in_touch">Get in Touch</h2>
                                <p data-i18n="contact_intro">Have a question or need assistance? Fill out the form and our support team will get back to you as soon as possible.</p>
                                
                                <div class="contact-methods">
                                    <div class="contact-method">
                                        <div class="icon">
                                            <i class="fas fa-envelope"></i>
                                        </div>
                                        <div class="details">
                                            <h3 data-i18n="email_us">Email Us</h3>
                                            <p>support@yourdomain.com</p>
                                        </div>
                                    </div>
                                    
                                    <div class="contact-method">
                                        <div class="icon">
                                            <i class="fas fa-phone-alt"></i>
                                        </div>
                                        <div class="details">
                                            <h3 data-i18n="call_us">Call Us</h3>
                                            <p>+1 (123) 456-7890</p>
                                            <p class="text-muted" data-i18n="business_hours">Business hours: 9AM - 6PM, Mon-Fri</p>
                                        </div>
                                    </div>
                                    
                                    <div class="contact-method">
                                        <div class="icon">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </div>
                                        <div class="details">
                                            <h3 data-i18n="visit_us">Visit Us</h3>
                                            <p>123 Main Street, Suite 456<br>New York, NY 10001</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <form method="POST" action="" class="contact-form">
                                <h2 data-i18n="submit_ticket">Submit a Ticket</h2>
                                
                                <div class="form-group">
                                    <label for="name" data-i18n="your_name">Your Name</label>
                                    <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($name ?? ''); ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label for="email" data-i18n="your_email">Your Email</label>
                                    <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($email ?? ''); ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label for="category" data-i18n="ticket_category">Ticket Category</label>
                                    <select id="category" name="category" required>
                                        <option value="" data-i18n="select_category">Select Category</option>
                                        <option value="General Inquiry" <?php if (isset($category) && $category === 'General Inquiry') echo 'selected'; ?> data-i18n="general_inquiry">General Inquiry</option>
                                        <option value="Technical Support" <?php if (isset($category) && $category === 'Technical Support') echo 'selected'; ?> data-i18n="technical_support">Technical Support</option>
                                        <option value="Billing" <?php if (isset($category) && $category === 'Billing') echo 'selected'; ?> data-i18n="billing">Billing</option>
                                        <option value="Account" <?php if (isset($category) && $category === 'Account') echo 'selected'; ?> data-i18n="account">Account</option>
                                        <option value="Other" <?php if (isset($category) && $category === 'Other') echo 'selected'; ?> data-i18n="other">Other</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="subject" data-i18n="subject">Subject</label>
                                    <input type="text" id="subject" name="subject" required value="<?php echo htmlspecialchars($subject ?? ''); ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label for="message" data-i18n="message">Message</label>
                                    <textarea id="message" name="message" rows="5" required><?php echo htmlspecialchars($message_content ?? ''); ?></textarea>
                                </div>
                                
                                <button type="submit" name="submit_ticket" class="btn btn-primary" data-i18n="submit">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Ticket Status Tab -->
            <div class="tab-pane" id="ticket">
                <div class="ticket-status-container">
                    <h2 data-i18n="check_ticket_status">Check Your Ticket Status</h2>
                    <p data-i18n="ticket_status_intro">Enter your ticket ID and email to check the status of your support request.</p>
                    
                    <form id="ticketStatusForm" class="ticket-status-form">
                        <div class="form-group">
                            <label for="ticketId" data-i18n="ticket_id">Ticket ID</label>
                            <input type="text" id="ticketId" name="ticketId" required placeholder="e.g. 123456" data-i18n-attr="placeholder" data-i18n="ticket_id_placeholder">
                        </div>
                        
                        <div class="form-group">
                            <label for="ticketEmail" data-i18n="your_email">Email Address</label>
                            <input type="email" id="ticketEmail" name="ticketEmail" required placeholder="Enter your email" data-i18n-attr="placeholder" data-i18n="email_placeholder">
                        </div>
                        
                        <button type="submit" class="btn btn-primary" data-i18n="check_status">Check Status</button>
                    </form>
                    
                    <div id="ticketResult" class="ticket-result" style="display: none;">
                        <div class="ticket-details">
                            <h3 data-i18n="ticket_details">Ticket Details</h3>
                            <div class="ticket-info">
                                <div class="ticket-field">
                                    <span class="field-label" data-i18n="ticket_id">Ticket ID:</span>
                                    <span class="field-value" id="resultTicketId"></span>
                                </div>
                                <div class="ticket-field">
                                    <span class="field-label" data-i18n="status">Status:</span>
                                    <span class="field-value status-badge" id="resultStatus"></span>
                                </div>
                                <div class="ticket-field">
                                    <span class="field-label" data-i18n="subject">Subject:</span>
                                    <span class="field-value" id="resultSubject"></span>
                                </div>
                                <div class="ticket-field">
                                    <span class="field-label" data-i18n="category">Category:</span>
                                    <span class="field-value" id="resultCategory"></span>
                                </div>
                                <div class="ticket-field">
                                    <span class="field-label" data-i18n="submitted_on">Submitted on:</span>
                                    <span class="field-value" id="resultSubmitted"></span>
                                </div>
                                <div class="ticket-field">
                                    <span class="field-label" data-i18n="last_updated">Last updated:</span>
                                    <span class="field-value" id="resultUpdated"></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="ticket-conversation">
                            <h3 data-i18n="conversation">Conversation</h3>
                            <div id="ticketConversation" class="conversation-container"></div>
                        </div>
                    </div>
                    
                    <div id="ticketError" class="alert alert-danger" style="display: none;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Tab switching functionality
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabPanes = document.querySelectorAll('.tab-pane');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            
            // Remove active class from all buttons and panes
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabPanes.forEach(pane => pane.classList.remove('active'));
            
            // Add active class to current button and pane
            this.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        });
    });
    
    // FAQ functionality
    const faqItems = document.querySelectorAll('.faq-item');
    const faqSearch = document.getElementById('faqSearch');
    const categoryButtons = document.querySelectorAll('.category-btn');
    
    // Toggle FAQ answers
    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');
        const answer = item.querySelector('.faq-answer');
        const toggle = item.querySelector('.faq-toggle');
        
        question.addEventListener('click', function() {
            answer.classList.toggle('active');
            toggle.querySelector('i').classList.toggle('fa-plus');
            toggle.querySelector('i').classList.toggle('fa-minus');
        });
    });
    
    // FAQ search functionality
    if (faqSearch) {
        faqSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            faqItems.forEach(item => {
                const question = item.querySelector('h3').textContent.toLowerCase();
                const answer = item.querySelector('.faq-answer-content').textContent.toLowerCase();
                const categoryTitle = item.previousElementSibling;
                
                if (question.includes(searchTerm) || answer.includes(searchTerm)) {
                    item.style.display = 'block';
                    if (categoryTitle && categoryTitle.classList.contains('faq-category-title')) {
                        categoryTitle.style.display = 'block';
                    }
                } else {
                    item.style.display = 'none';
                }
            });
            
            // Hide category titles if all FAQs in that category are hidden
            document.querySelectorAll('.faq-category-title').forEach(title => {
                const category = title.getAttribute('data-category');
                const visibleFaqs = document.querySelectorAll(`.faq-item[data-category="${category}"]:not([style="display: none;"])`);
                
                if (visibleFaqs.length === 0) {
                    title.style.display = 'none';
                }
            });
        });
    }
    
    // FAQ category filtering
    categoryButtons.forEach(button => {
        button.addEventListener('click', function() {
            const category = this.getAttribute('data-category');
            
            // Remove active class from all category buttons
            categoryButtons.forEach(btn => btn.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Clear search
            if (faqSearch) {
                faqSearch.value = '';
            }
            
            // Show/hide FAQ items based on category
            faqItems.forEach(item => {
                const itemCategory = item.getAttribute('data-category');
                const categoryTitle = item.previousElementSibling;
                
                if (category === 'all' || category === itemCategory) {
                    item.style.display = 'block';
                    if (categoryTitle && categoryTitle.classList.contains('faq-category-title')) {
                        categoryTitle.style.display = 'block';
                    }
                } else {
                    item.style.display = 'none';
                }
            });
            
            // Hide category titles if all FAQs in that category are hidden
            document.querySelectorAll('.faq-category-title').forEach(title => {
                const titleCategory = title.getAttribute('data-category');
                
                if (category === 'all' || category === titleCategory) {
                    title.style.display = 'block';
                } else {
                    title.style.display = 'none';
                }
            });
        });
    });
    
    // Ticket status check functionality
    const ticketStatusForm = document.getElementById('ticketStatusForm');
    
    if (ticketStatusForm) {
        ticketStatusForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const ticketId = document.getElementById('ticketId').value;
            const email = document.getElementById('ticketEmail').value;
            
            // Hide previous results and errors
            document.getElementById('ticketResult').style.display = 'none';
            document.getElementById('ticketError').style.display = 'none';
            
            // Make AJAX request to check ticket status
            fetch('ajax/check-ticket.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `ticketId=${encodeURIComponent(ticketId)}&email=${encodeURIComponent(email)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Populate ticket details
                    document.getElementById('resultTicketId').textContent = data.ticket.id;
                    document.getElementById('resultStatus').textContent = data.ticket.status;
                    document.getElementById('resultStatus').className = `field-value status-badge status-${data.ticket.status.toLowerCase()}`;
                    document.getElementById('resultSubject').textContent = data.ticket.subject;
                    document.getElementById('resultCategory').textContent = data.ticket.category;
                    document.getElementById('resultSubmitted').textContent = data.ticket.created_at;
                    document.getElementById('resultUpdated').textContent = data.ticket.updated_at;
                    
                    // Populate conversation
                    const conversationContainer = document.getElementById('ticketConversation');
                    conversationContainer.innerHTML = '';
                    
                    data.conversations.forEach(message => {
                        const messageEl = document.createElement('div');
                        messageEl.className = `conversation-message ${message.is_admin ? 'admin-message' : 'user-message'}`;
                        
                        messageEl.innerHTML = `
                            <div class="message-header">
                                <span class="message-sender">${message.is_admin ? 'Support Team' : 'You'}</span>
                                <span class="message-time">${message.created_at}</span>
                            </div>
                            <div class="message-content">${message.message}</div>
                        `;
                        
                        conversationContainer.appendChild(messageEl);
                    });
                    
                    // Show ticket result
                    document.getElementById('ticketResult').style.display = 'block';
                } else {
                    // Show error message
                    document.getElementById('ticketError').textContent = data.message;
                    document.getElementById('ticketError').style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error checking ticket status:', error);
                document.getElementById('ticketError').textContent = 'An error occurred while checking the ticket status. Please try again.';
                document.getElementById('ticketError').style.display = 'block';
            });
        });
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>