-- Create database
CREATE DATABASE IF NOT EXISTS us_business_opportunity;
USE us_business_opportunity;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    referral_code VARCHAR(10) UNIQUE NOT NULL,
    referred_by VARCHAR(10),
    wallet_balance DECIMAL(10,2) DEFAULT 0.00,
    level ENUM('Silver', 'Golden', 'Platinum') DEFAULT 'Silver',
    is_vip BOOLEAN DEFAULT FALSE,
    vip_expiry DATE,
    is_active BOOLEAN DEFAULT FALSE,
    is_admin BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);

-- Admin user
INSERT INTO users (username, email, password, referral_code, is_active, is_admin)
VALUES ('admin', 'uttombarmon45@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ADMIN123', TRUE, TRUE);

-- Transactions table
CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    type ENUM('registration', 'referral', 'level', 'login', 'withdrawal', 'spin', 'farming', 'vip') NOT NULL,
    description TEXT,
    status ENUM('pending', 'completed', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Withdrawals table
CREATE TABLE IF NOT EXISTS withdrawals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('bkash', 'nagad', 'ltc', 'usdt', 'binance') NOT NULL,
    payment_details TEXT NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Farming slots table
CREATE TABLE IF NOT EXISTS farming_slots (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    is_active BOOLEAN DEFAULT FALSE,
    last_claim TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Spin history table
CREATE TABLE IF NOT EXISTS spin_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    prize VARCHAR(50) NOT NULL,
    value DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Income proof table
CREATE TABLE IF NOT EXISTS income_proofs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    is_approved BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Settings table
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(50) UNIQUE NOT NULL,
    setting_value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default settings
INSERT INTO settings (setting_key, setting_value) VALUES
('telegram_group', 'https://t.me/+EywwO37aB3xkODU1'),
('spin_enabled', '1'),
('farming_enabled', '1'),
('vip_enabled', '1'),
('leaderboard_enabled', '1'),
('income_proof_enabled', '1'),
('live_counter_enabled', '1'),
('ai_chat_enabled', '1'),
('daily_login_bonus', '5'),
('farming_rate', '5'),
('farming_slot_limit', '1'); 