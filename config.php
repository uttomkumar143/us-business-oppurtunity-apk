<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'us_business_opportunity');

// Site Configuration
define('SITE_NAME', 'US Business Opportunity');
define('SITE_URL', 'http://localhost/us_business_oppurtunity');
define('ADMIN_EMAIL', 'uttombarmon45@gmail.com');

// Payment Configuration
define('REGISTRATION_FEE', 800); // BDT
define('REGISTRATION_FEE_USD', 8); // USD
define('MIN_WITHDRAWAL', 200); // BDT
define('MIN_WITHDRAWAL_USD', 2); // USD

// Referral Configuration
define('DIRECT_REFERRAL_PERCENTAGE', 35);
define('LEVEL1_PERCENTAGE', 12);
define('LEVEL2_PERCENTAGE', 8);
define('LEVEL3_PERCENTAGE', 5);

// Daily Login Bonus
define('DAILY_LOGIN_BONUS', 5); // BDT

// VIP Membership
define('VIP_MONTHLY_FEE', 200); // BDT
define('VIP_QUARTERLY_FEE', 500); // BDT

// Spin System
define('SPIN_COST', 10); // BDT

// Database Connection
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
?> 