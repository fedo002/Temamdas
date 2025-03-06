<?php
// about.php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$page_title = 'Hakkımızda';
include 'includes/header.php';
$stmt = $conn->prepare("SELECT * FROM site_content WHERE page = 'about' AND status = 'active'");
$stmt->execute();
$result = $stmt->get_result();
$aboutContent = $result->fetch_assoc();

// Fetch team members if available
$stmt = $conn->prepare("SELECT * FROM team_members WHERE status = 'active' ORDER BY display_order ASC");
$stmt->execute();
$result = $stmt->get_result();
$teamMembers = $result->fetch_all(MYSQLI_ASSOC);
?>

<div class="about-page">
    <div class="about-hero">
        <div class="container">
            <h1 data-i18n="about_us">About Us</h1>
            <p class="about-subtitle" data-i18n="about_subtitle">Learn about our story, our mission, and our team</p>
        </div>
    </div>
    
    <div class="container">
        <section class="about-section">
            <div class="row">
                <div class="col-md-6">
                    <div class="about-content">
                        <h2 data-i18n="our_story">Our Story</h2>
                        <div class="content-body">
                            <?php 
                            if (isset($aboutContent['story_content'])) {
                                echo '<div data-i18n-html="about_story">' . $aboutContent['story_content'] . '</div>';
                            } else {
                                echo '<p data-i18n="about_story_default">Our story begins with a vision to create a platform that empowers individuals to participate in the digital economy through innovative mining and VIP solutions.</p>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="about-image">
                        <img src="<?php echo isset($aboutContent['story_image']) ? $aboutContent['story_image'] : 'assets/images/about/story.jpg'; ?>" alt="Our Story" class="img-fluid rounded">
                    </div>
                </div>
            </div>
        </section>
        
        <section class="about-section">
            <div class="row">
                <div class="col-md-6 order-md-2">
                    <div class="about-content">
                        <h2 data-i18n="our_mission">Our Mission</h2>
                        <div class="content-body">
                            <?php 
                            if (isset($aboutContent['mission_content'])) {
                                echo '<div data-i18n-html="about_mission">' . $aboutContent['mission_content'] . '</div>';
                            } else {
                                echo '<p data-i18n="about_mission_default">Our mission is to provide accessible, secure, and profitable mining solutions to everyone, regardless of technical expertise.</p>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 order-md-1">
                    <div class="about-image">
                        <img src="<?php echo isset($aboutContent['mission_image']) ? $aboutContent['mission_image'] : 'assets/images/about/mission.jpg'; ?>" alt="Our Mission" class="img-fluid rounded">
                    </div>
                </div>
            </div>
        </section>
        
        <section class="about-section">
            <div class="row">
                <div class="col-md-6">
                    <div class="about-content">
                        <h2 data-i18n="our_values">Our Values</h2>
                        <div class="content-body">
                            <?php 
                            if (isset($aboutContent['values_content'])) {
                                echo '<div data-i18n-html="about_values">' . $aboutContent['values_content'] . '</div>';
                            } else {
                                echo '<div data-i18n-html="about_values_default">
                                    <ul class="values-list">
                                        <li><strong>Integrity</strong> - We operate with transparency and honesty in all our dealings.</li>
                                        <li><strong>Innovation</strong> - We continuously evolve our technology to stay ahead.</li>
                                        <li><strong>Customer Focus</strong> - Our users success is our priority.</li>
                                        <li><strong>Security</strong> - We implement the highest security standards.</li>
                                        <li><strong>Sustainability</strong> - We are committed to eco-friendly mining solutions.</li>
                                    </ul>
                                </div>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="about-image">
                        <img src="<?php echo isset($aboutContent['values_image']) ? $aboutContent['values_image'] : 'assets/images/about/values.jpg'; ?>" alt="Our Values" class="img-fluid rounded">
                    </div>
                </div>
            </div>
        </section>
        
        <?php if (!empty($teamMembers)): ?>
        <section class="team-section">
            <h2 data-i18n="our_team">Our Team</h2>
            <p class="section-subtitle" data-i18n="team_subtitle">Meet the people behind our success</p>
            
            <div class="team-grid">
                <?php foreach ($teamMembers as $member): ?>
                <div class="team-member">
                    <div class="member-photo">
                        <img src="<?php echo htmlspecialchars($member['photo_url']); ?>" alt="<?php echo htmlspecialchars($member['name']); ?>" class="img-fluid rounded-circle">
                    </div>
                    <h3 class="member-name"><?php echo htmlspecialchars($member['name']); ?></h3>
                    <p class="member-position"><?php echo htmlspecialchars($member['position']); ?></p>
                    <p class="member-bio"><?php echo htmlspecialchars($member['bio']); ?></p>
                    <div class="member-social">
                        <?php if (!empty($member['linkedin'])): ?>
                        <a href="<?php echo htmlspecialchars($member['linkedin']); ?>" target="_blank" class="social-link">
                            <i class="fab fa-linkedin"></i>
                        </a>
                        <?php endif; ?>
                        
                        <?php if (!empty($member['twitter'])): ?>
                        <a href="<?php echo htmlspecialchars($member['twitter']); ?>" target="_blank" class="social-link">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <?php endif; ?>
                        
                        <?php if (!empty($member['email'])): ?>
                        <a href="mailto:<?php echo htmlspecialchars($member['email']); ?>" class="social-link">
                            <i class="fas fa-envelope"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>