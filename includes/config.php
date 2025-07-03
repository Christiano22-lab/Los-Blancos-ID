<?php
// includes/config.php

// Prevent direct access
if (!defined('INCLUDED')) {
    define('INCLUDED', true);
}

// Environment configuration
define('ENVIRONMENT', 'development'); // Change to 'production' when live

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'los_blancos_db');

// Website settings
define('SITE_NAME', 'Los Blancos ID');
define('SITE_URL', 'http://localhost/Los-Blancos-ID');
define('ADMIN_EMAIL', 'admin@realmadridfanclub.com');

// File upload settings
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('UPLOAD_PATH', 'assets/uploads/');
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Pagination settings
define('MATCHES_PER_PAGE', 10);
define('NEWS_PER_PAGE', 6);
define('COMMENTS_PER_PAGE', 20);

// Security settings
define('PASSWORD_MIN_LENGTH', 8);
define('SESSION_TIMEOUT', 3600); // 1 hour in seconds
define('MAX_LOGIN_ATTEMPTS', 5);

// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    
    // Set session timeout
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
        session_unset();
        session_destroy();
        session_start();
    }
    $_SESSION['last_activity'] = time();
}

// Error reporting based on environment
if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
}

// Set timezone
date_default_timezone_set('Asia/Jakarta');

// Create PDO connection with error handling
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_PERSISTENT => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ];
    
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    
    // Test connection
    $pdo->query("SELECT 1");
    
} catch (PDOException $e) {
    // Log error instead of displaying it
    error_log("Database connection failed: " . $e->getMessage());
    
    if (ENVIRONMENT === 'development') {
        die("Database connection failed: " . $e->getMessage());
    } else {
        die("Database connection failed. Please try again later.");
    }
}

// Helper functions for common operations
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function isAdmin() {
    return isLoggedIn() && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function isModerator() {
    return isLoggedIn() && isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], ['admin', 'moderator']);
}

function getCurrentUserId() {
    return isLoggedIn() ? $_SESSION['user_id'] : null;
}

function getCurrentUserRole() {
    return isLoggedIn() ? $_SESSION['user_role'] : 'guest';
}

function redirectTo($url) {
    header("Location: " . $url);
    exit();
}

function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Auto-load common functions if they exist
$common_functions = [
    'includes/functions.php',
    'includes/match-functions.php',
    'includes/user-functions.php',
    'includes/news-functions.php'
];

foreach ($common_functions as $file) {
    if (file_exists($file)) {
        include_once $file;
    }
}

// Set global variables for templates
$current_year = date('Y');
$site_name = SITE_NAME;
$site_url = SITE_URL;

// Initialize current page if not set
if (!isset($current_page)) {
    $current_page = '';
}

// Initialize page title if not set
if (!isset($page_title)) {
    $page_title = SITE_NAME;
}

// Initialize breadcrumbs if not set
if (!isset($breadcrumbs)) {
    $breadcrumbs = [];
}
?>