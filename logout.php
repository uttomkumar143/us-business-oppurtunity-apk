<?php
require_once 'config.php';
require_once 'includes/functions.php';

// Destroy session
session_destroy();

// Clear remember me cookie
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
}

// Redirect to login page
redirect('login.php');
?> 