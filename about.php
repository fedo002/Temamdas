<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

$page_title = 'Hakkımızda';
include 'includes/mobile-header.php';
?>
<link rel="stylesheet" href="assets/css/about.css">

<div class="about-page">
    <div class="about-hero">
        <div class="container">
            <h1 data-i18n="about.title">About Us</h1>
            <p class="about-subtitle" data-i18n="about.subtitle">Learn about our story, mission, and team</p>
        </div>
    </div>
    
    <div class="container">
        <section class="about-section">
            <div class="row">
                <div class="col-md-6">
                    <div class="about-content">
                        <h2 data-i18n="about.our_story">Our Story</h2>
                        <div class="content-body">
                            <div data-i18n-html="about.about_story_default">
                                <p>Our story begins with a vision to create a platform that enables individuals to participate in the digital economy through innovative mining and VIP solutions.</p>
                                <p>We started as a small venture in 2020 and quickly grew into a reliable platform serving thousands of users worldwide.</p>
                                <p>Today, we continue to evolve our services to meet the changing needs of our users while staying committed to our founding principles.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="about-image">
                        <img src="assets/images/about/story.svg" alt="Our Story" class="img-fluid rounded">
                    </div>
                </div>
            </div>
        </section>
        
        <section class="about-section">
            <div class="row">
                <div class="col-md-6 order-md-2">
                    <div class="about-content">
                        <h2 data-i18n="about.our_mission">Our Mission</h2>
                        <div class="content-body">
                            <div data-i18n-html="about.about_mission_default">
                                <p>Our mission is to provide accessible, secure, and profitable mining solutions to everyone without requiring technical expertise.</p>
                                <p>We aim to allow every user to share in digital asset production, regardless of their level of technical knowledge. In line with this mission:</p>
                                <ul>
                                    <li>We develop highly efficient mining solutions using the latest technologies</li>
                                    <li>We take the highest level of security measures to protect our users' investments</li>
                                    <li>We invest in sustainable mining methods that minimize environmental impact</li>
                                    <li>We provide 24/7 customer support to maintain the highest level of user satisfaction</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 order-md-1">
                    <div class="about-image">
                        <img src="assets/images/about/mission.svg" alt="Our Mission" class="img-fluid rounded">
                    </div>
                </div>
            </div>
        </section>
        
        <section class="about-section">
            <div class="row">
                <div class="col-md-6">
                    <div class="about-content">
                        <h2 data-i18n="about.our_values">Our Values</h2>
                        <div class="content-body">
                            <div data-i18n-html="about.about_values_default">
                                <ul class="values-list">
                                    <li><strong>Integrity</strong> - We operate with transparency and honesty in all our dealings.</li>
                                    <li><strong>Innovation</strong> - We continuously evolve our technology to stay ahead.</li>
                                    <li><strong>Customer Focus</strong> - Our users success is our priority.</li>
                                    <li><strong>Security</strong> - We implement the highest security standards.</li>
                                    <li><strong>Sustainability</strong> - We are committed to eco-friendly mining solutions.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="about-image">
                        <img src="assets/images/about/values.svg" alt="Our Values" class="img-fluid rounded">
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Sponsorlar Bölümü -->
        <section class="sponsors-section">
            <h2 data-i18n="about.our_sponsors">Our Sponsors</h2>
            <p class="section-subtitle" data-i18n="about.sponsors_subtitle">Trusted by global brands</p>
            
            <div class="sponsors-grid">
                <div class="row g-4">
                    <!-- Gucci -->
                    <div class="col-6 col-md-3">
                        <a href="https://www.gucci.com" target="_blank" class="sponsor-card">
                            <div class="sponsor-logo">
                                <img src="assets/images/sponsors/gucci.png" alt="Gucci" class="img-fluid">
                            </div>
                            <h5 class="sponsor-name">Gucci</h5>
                        </a>
                    </div>
                    
                    <!-- Coinbase -->
                    <div class="col-6 col-md-3">
                        <a href="https://www.coinbase.com" target="_blank" class="sponsor-card">
                            <div class="sponsor-logo">
                                <img src="assets/images/sponsors/coinbase.png" alt="Coinbase" class="img-fluid">
                            </div>
                            <h5 class="sponsor-name">Coinbase</h5>
                        </a>
                    </div>
                    
                    <!-- Bitpanda -->
                    <div class="col-6 col-md-3">
                        <a href="https://www.bitpanda.com" target="_blank" class="sponsor-card">
                            <div class="sponsor-logo">
                                <img src="assets/images/sponsors/bitpanda.png" alt="Bitpanda" class="img-fluid">
                            </div>
                            <h5 class="sponsor-name">Bitpanda</h5>
                        </a>
                    </div>
                    
                    <!-- TikTok -->
                    <div class="col-6 col-md-3">
                        <a href="https://www.tiktok.com" target="_blank" class="sponsor-card">
                            <div class="sponsor-logo">
                                <img src="assets/images/sponsors/tiktok.png" alt="TikTok" class="img-fluid">
                            </div>
                            <h5 class="sponsor-name">TikTok</h5>
                        </a>
                    </div>
                    
                    <!-- Facebook -->
                    <div class="col-6 col-md-3">
                        <a href="https://www.facebook.com" target="_blank" class="sponsor-card">
                            <div class="sponsor-logo">
                                <img src="assets/images/sponsors/facebook.png" alt="Facebook" class="img-fluid">
                            </div>
                            <h5 class="sponsor-name">Facebook</h5>
                        </a>
                    </div>
                    
                    <!-- KFC -->
                    <div class="col-6 col-md-3">
                        <a href="https://www.kfc.com" target="_blank" class="sponsor-card">
                            <div class="sponsor-logo">
                                <img src="assets/images/sponsors/kfc.png" alt="KFC" class="img-fluid">
                            </div>
                            <h5 class="sponsor-name">KFC</h5>
                        </a>
                    </div>
                    
                    <!-- LC Waikiki -->
                    <div class="col-6 col-md-3">
                        <a href="https://www.lcwaikiki.com" target="_blank" class="sponsor-card">
                            <div class="sponsor-logo">
                                <img src="assets/images/sponsors/lcwaikiki.png" alt="LC Waikiki" class="img-fluid">
                            </div>
                            <h5 class="sponsor-name">LC Waikiki</h5>
                        </a>
                    </div>
                    
                    <!-- Samsung -->
                    <div class="col-6 col-md-3">
                        <a href="https://www.samsung.com" target="_blank" class="sponsor-card">
                            <div class="sponsor-logo">
                                <img src="assets/images/sponsors/samsung.png" alt="Samsung" class="img-fluid">
                            </div>
                            <h5 class="sponsor-name">Samsung</h5>
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<style>
body {
    background-color: #000;
    background-image: url();
}
/* Sponsor Bölümü Stil */
.sponsors-section {
    padding: 60px 0;
    text-align: center;
}

.sponsors-section h2 {
    margin-bottom: 20px;
    color: var(--primary-color);
    font-weight: 600;
}

.section-subtitle {
    margin-bottom: 40px;
    color: var(--text-secondary);
}

.sponsors-grid {
    margin-top: 30px;
}

.sponsor-card {
    display: block;
    padding: 20px;
    background-color: var(--card-bg);
    border-radius: 12px;
    transition: all 0.3s ease;
    text-align: center;
    text-decoration: none;
    height: 100%;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.sponsor-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
}

.sponsor-logo {
    width: 100%;
    height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 15px;
}

.sponsor-logo img {
    max-width: 80%;
    max-height: 80px;
    object-fit: contain;
}

.sponsor-name {
    margin: 0;
    color: var(--text-color);
    font-size: 1rem;
    font-weight: 500;
}

@media (max-width: 768px) {
    .sponsor-logo {
        height: 100px;
    }
    
    .sponsor-logo img {
        max-height: 60px;
    }
}

/* Değerler Listesi Stillemesi */
.values-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.values-list li {
    padding: 12px 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.values-list li:last-child {
    border-bottom: none;
}

.values-list strong {
    color: var(--primary-color);
    margin-right: 5px;
}

/* Genel Bölüm Stillemesi */
.about-section {
    padding: 60px 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.about-section:last-of-type {
    border-bottom: none;
}

.about-content h2 {
    margin-bottom: 20px;
    color: var(--primary-color);
}

.about-image img {
    width: 100%;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
}

.about-hero {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    padding: 80px 0;
    text-align: center;
    margin-bottom: 30px;
    border-radius: 0 0 50px 50px;
}

.about-hero h1 {
    color: white;
    font-size: 2.5rem;
    margin-bottom: 15px;
}

.about-subtitle {
    color: rgba(255, 255, 255, 0.9);
    font-size: 1.2rem;
    max-width: 600px;
    margin: 0 auto;
}
</style>

<?php require_once 'includes/mobile-footer.php'; ?>