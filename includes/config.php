<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'real_madrid_fansite');

// Website settings
define('SITE_NAME', 'Los Blancos ID');
define('SITE_URL', 'http://localhost/ProjectAkhir');
define('ADMIN_EMAIL', 'admin@realmadridfanclub.com');

// Session configuration
session_start();

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Time zone
date_default_timezone_set('Asia/Jakarta');
?>