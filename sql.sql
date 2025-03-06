-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    trc20_address VARCHAR(100),
    balance DECIMAL(20,6) DEFAULT 0,
    referral_balance DECIMAL(20,6) DEFAULT 0,
    referral_code VARCHAR(20) UNIQUE,
    referrer_id INT,
    vip_level INT DEFAULT 0,
    total_deposit DECIMAL(20,6) DEFAULT 0,
    total_withdraw DECIMAL(20,6) DEFAULT 0,
    total_earnings DECIMAL(20,6) DEFAULT 0,
    status ENUM('active', 'blocked', 'pending') DEFAULT 'active',
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (referrer_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Admin Table
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'moderator', 'support') DEFAULT 'admin',
    status ENUM('active', 'blocked') DEFAULT 'active',
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- VIP Packages Table
CREATE TABLE IF NOT EXISTS vip_packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(20,6) NOT NULL,
    daily_game_limit INT DEFAULT 5,
    game_max_win_chance DECIMAL(5,4) DEFAULT 0.15,
    referral_rate DECIMAL(5,4) DEFAULT 0.05,
    mining_bonus_rate DECIMAL(5,4) DEFAULT 0.00,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE
);

-- Mining Packages Table
CREATE TABLE IF NOT EXISTS mining_packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    hash_rate DECIMAL(10,2) NOT NULL,
    electricity_cost DECIMAL(10,4) NOT NULL,
    daily_revenue_rate DECIMAL(6,4) NOT NULL,
    package_price DECIMAL(20,6) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- User Mining Packages Table
CREATE TABLE IF NOT EXISTS user_mining_packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    package_id INT NOT NULL,
    purchase_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expiry_date TIMESTAMP NULL,
    status ENUM('active', 'expired', 'paused') DEFAULT 'active',
    total_earned DECIMAL(20,6) DEFAULT 0,
    total_electricity_cost DECIMAL(20,6) DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (package_id) REFERENCES mining_packages(id)
);

-- Mining Earnings Table
CREATE TABLE IF NOT EXISTS mining_earnings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    user_mining_id INT NOT NULL,
    hash_rate DECIMAL(10,2),
    revenue DECIMAL(10,6),
    electricity_cost DECIMAL(10,6),
    net_revenue DECIMAL(10,6),
    date DATE,
    processed BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (user_mining_id) REFERENCES user_mining_packages(id)
);

-- Deposits Table
CREATE TABLE IF NOT EXISTS deposits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    amount DECIMAL(20,6) NOT NULL,
    fee DECIMAL(20,6) DEFAULT 0,
    status ENUM('pending', 'confirmed', 'failed') DEFAULT 'pending',
    payment_id VARCHAR(255),
    order_id VARCHAR(255),
    transaction_hash VARCHAR(255),
    payment_method VARCHAR(50) DEFAULT 'nowpayments',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Withdrawals Table
CREATE TABLE IF NOT EXISTS withdrawals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    amount DECIMAL(20,6) NOT NULL,
    fee DECIMAL(20,6) DEFAULT 0,
    status ENUM('pending', 'processing', 'completed', 'failed', 'cancelled') DEFAULT 'pending',
    trc20_address VARCHAR(255) NOT NULL,
    transaction_hash VARCHAR(255),
    admin_id INT,
    admin_note TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    processed_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (admin_id) REFERENCES admins(id)
);

-- Transactions Table
CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    related_id INT,
    type ENUM('deposit', 'withdraw', 'referral', 'game', 'mining', 'bonus', 'transfer', 'other') NOT NULL,
    amount DECIMAL(20,6) NOT NULL,
    before_balance DECIMAL(20,6),
    after_balance DECIMAL(20,6),
    status ENUM('pending', 'completed', 'failed', 'cancelled') DEFAULT 'completed',
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Game Attempts Table
CREATE TABLE IF NOT EXISTS game_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    attempt_result ENUM('win', 'lose', 'retry'),
    stake_amount DECIMAL(10,2) DEFAULT 0,
    win_amount DECIMAL(10,2) DEFAULT 0,
    stage INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Support Tickets Table
CREATE TABLE IF NOT EXISTS support_tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    subject VARCHAR(255) NOT NULL,
    status ENUM('open', 'in_progress', 'closed') DEFAULT 'open',
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_updated TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Support Messages Table
CREATE TABLE IF NOT EXISTS support_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_id INT NOT NULL,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    is_user_message BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ticket_id) REFERENCES support_tickets(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Payment Settings Table
CREATE TABLE IF NOT EXISTS payment_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(255) NOT NULL UNIQUE,
    setting_value TEXT,
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Site Settings Table
CREATE TABLE IF NOT EXISTS site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    description VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Admin Logs Table
CREATE TABLE IF NOT EXISTS admin_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT,
    action VARCHAR(50) NOT NULL,
    description TEXT,
    related_id INT,
    related_type VARCHAR(50),
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES admins(id)
);

-- Insert default VIP packages
INSERT INTO vip_packages (name, price, daily_game_limit, game_max_win_chance, referral_rate, mining_bonus_rate, description, is_active) VALUES
('Standart', 0, 5, 0.15, 0.05, 0.00, 'Ücretsiz Standart Paket', 1),
('Silver', 50, 7, 0.20, 0.07, 0.05, 'Günlük oyun limitini artır ve daha yüksek kazanma şansı', 1),
('Gold', 200, 10, 0.25, 0.10, 0.10, 'Daha fazla mining geliri ve referans bonusu', 1),
('Platinum', 500, 15, 0.30, 0.15, 0.15, 'En yüksek kazanç ve bonuslar', 1);

-- Insert default mining packages
INSERT INTO mining_packages (name, hash_rate, electricity_cost, daily_revenue_rate, package_price, is_active, description) VALUES
('Başlangıç Paketi', 10, 0.1, 0.02, 100, 1, 'Başlangıç seviyesi mining paketi'),
('Orta Seviye Paket', 50, 0.3, 0.05, 500, 1, 'Orta seviye mining paketi'),
('Profesyonel Paket', 100, 0.6, 0.10, 1000, 1, 'Profesyonel seviye mining paketi');

-- Insert default admin user
INSERT INTO admins (username, email, password, role, status) VALUES
('admin', 'admin@example.com', '$2y$10$nLJY0mruoM0bRX9bQC3LM.d2oGVdnm7cPzQzk1hQd.s3kGXrLNpBO', 'admin', 'active');
-- Default password: admin123

-- Insert default payment settings
INSERT INTO payment_settings (setting_key, setting_value, description) VALUES
('nowpayments_api_key', '', 'NOWPayments API Anahtarı'),
('nowpayments_ipn_secret', '', 'NOWPayments IPN Secret Anahtarı'),
('nowpayments_test_mode', '0', 'NOWPayments Test Modu (0: Kapalı, 1: Açık)'),
('min_deposit_amount', '10', 'Minimum yatırım tutarı (USDT)'),
('min_withdraw_amount', '20', 'Minimum çekim tutarı (USDT)'),
('withdraw_fee', '2', 'Çekim ücreti (%)'),
('trc20_address', '', 'Platform TRC-20 USDT Cüzdan Adresi');

-- Insert default site settings
INSERT INTO site_settings (setting_key, setting_value, description) VALUES
('site_name', 'Kazanç Platformu', 'Site Adı'),
('site_description', 'Birlikte Kazan, Birlikte Büyü', 'Site Açıklaması'),
('referral_active', '1', 'Referans sistemi aktif mi?'),
('referral_tier1_rate', '0.05', 'Tier 1 referans oranı'),
('referral_tier2_rate', '0.02', 'Tier 2 referans oranı'),
('mining_active', '1', 'Mining sistemi aktif mi?'),
('daily_game_active', '1', 'Günlük ödül oyunu aktif mi?'),
('max_win_chance', '0.15', 'Maksimum kazanma şansı (oyun)'),
('support_email', 'support@example.com', 'Destek e-posta adresi'),
('contact_email', 'contact@example.com', 'İletişim e-posta adresi');