<?php
require_once 'config.php';

// User Functions
function getUserById($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function getUserByEmail($email) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function getUserByReferralCode($code) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM users WHERE referral_code = ?");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function generateReferralCode() {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $code = '';
    for ($i = 0; $i < 6; $i++) {
        $code .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $code;
}

function registerUser($username, $email, $password, $referred_by = null) {
    global $conn;
    
    // Check if email already exists
    if (getUserByEmail($email)) {
        return ['success' => false, 'message' => 'Email already exists'];
    }
    
    // Generate unique referral code
    $referral_code = generateReferralCode();
    while (getUserByReferralCode($referral_code)) {
        $referral_code = generateReferralCode();
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert user
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, referral_code, referred_by) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $username, $email, $hashed_password, $referral_code, $referred_by);
    
    if ($stmt->execute()) {
        $user_id = $conn->insert_id;
        
        // Create initial farming slot
        $stmt = $conn->prepare("INSERT INTO farming_slots (user_id) VALUES (?)");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        
        return ['success' => true, 'user_id' => $user_id];
    }
    
    return ['success' => false, 'message' => 'Registration failed'];
}

function loginUser($email, $password) {
    $user = getUserByEmail($email);
    
    if ($user && password_verify($password, $user['password'])) {
        if (!$user['is_active']) {
            return ['success' => false, 'message' => 'Account not activated'];
        }
        
        $_SESSION['user_id'] = $user['id'];
        
        // Update last login
        global $conn;
        $stmt = $conn->prepare("UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->bind_param("i", $user['id']);
        $stmt->execute();
        
        return ['success' => true];
    }
    
    return ['success' => false, 'message' => 'Invalid credentials'];
}

// Transaction Functions
function addTransaction($user_id, $amount, $type, $description = '') {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO transactions (user_id, amount, type, description) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("idss", $user_id, $amount, $type, $description);
    return $stmt->execute();
}

function updateWallet($user_id, $amount) {
    global $conn;
    $stmt = $conn->prepare("UPDATE users SET wallet_balance = wallet_balance + ? WHERE id = ?");
    $stmt->bind_param("di", $amount, $user_id);
    return $stmt->execute();
}

// Referral Functions
function processReferralBonus($user_id, $referrer_id) {
    global $conn;
    
    // Get referrer's level
    $stmt = $conn->prepare("SELECT level FROM users WHERE id = ?");
    $stmt->bind_param("i", $referrer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $referrer = $result->fetch_assoc();
    
    // Calculate direct referral bonus
    $direct_bonus = REGISTRATION_FEE * (DIRECT_REFERRAL_PERCENTAGE / 100);
    
    // Add direct referral transaction
    addTransaction($referrer_id, $direct_bonus, 'referral', 'Direct referral bonus');
    updateWallet($referrer_id, $direct_bonus);
    
    // Process level bonuses
    $level_bonuses = [
        'Silver' => LEVEL1_PERCENTAGE,
        'Golden' => LEVEL2_PERCENTAGE,
        'Platinum' => LEVEL3_PERCENTAGE
    ];
    
    $current_level = $referrer['level'];
    $level_bonus = REGISTRATION_FEE * ($level_bonuses[$current_level] / 100);
    
    if ($level_bonus > 0) {
        addTransaction($referrer_id, $level_bonus, 'level', 'Level bonus for ' . $current_level);
        updateWallet($referrer_id, $level_bonus);
    }
}

// Farming Functions
function claimFarmingReward($user_id) {
    global $conn;
    
    // Check if user has active farming slot
    $stmt = $conn->prepare("SELECT * FROM farming_slots WHERE user_id = ? AND is_active = 1");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $slot = $result->fetch_assoc();
    
    if (!$slot) {
        return ['success' => false, 'message' => 'No active farming slot'];
    }
    
    // Check if 24 hours have passed since last claim
    $last_claim = strtotime($slot['last_claim']);
    $now = time();
    
    if ($last_claim && ($now - $last_claim) < 86400) {
        return ['success' => false, 'message' => 'Please wait 24 hours between claims'];
    }
    
    // Get farming rate from settings
    $stmt = $conn->prepare("SELECT setting_value FROM settings WHERE setting_key = 'farming_rate'");
    $stmt->execute();
    $result = $stmt->get_result();
    $setting = $result->fetch_assoc();
    $farming_rate = $setting['setting_value'];
    
    // Add farming reward
    addTransaction($user_id, $farming_rate, 'farming', 'Daily farming reward');
    updateWallet($user_id, $farming_rate);
    
    // Update last claim time
    $stmt = $conn->prepare("UPDATE farming_slots SET last_claim = CURRENT_TIMESTAMP WHERE id = ?");
    $stmt->bind_param("i", $slot['id']);
    $stmt->execute();
    
    return ['success' => true, 'amount' => $farming_rate];
}

// Admin Functions
function getSetting($key) {
    global $conn;
    $stmt = $conn->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
    $stmt->bind_param("s", $key);
    $stmt->execute();
    $result = $stmt->get_result();
    $setting = $result->fetch_assoc();
    return $setting ? $setting['setting_value'] : null;
}

function updateSetting($key, $value) {
    global $conn;
    $stmt = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
    $stmt->bind_param("ss", $value, $key);
    return $stmt->execute();
}

// Utility Functions
function redirect($url) {
    header("Location: $url");
    exit();
}

function setMessage($message, $type = 'success') {
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
}

function isAdmin() {
    if (!isset($_SESSION['user_id'])) {
        return false;
    }
    
    $user = getUserById($_SESSION['user_id']);
    return $user && $user['is_admin'];
}

function requireAdmin() {
    if (!isAdmin()) {
        setMessage('Access denied', 'danger');
        redirect('index.php');
    }
}

function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        setMessage('Please login first', 'warning');
        redirect('login.php');
    }
}
?> 