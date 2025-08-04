<?php
// Sample configuration file for LibreWO
// Copy this to config.php and update with your settings

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'librewo');
define('DB_USER', 'root');
define('DB_PASS', '');

// Base URL Configuration - IMPORTANT: Update this to match your installation
define('BASE_URL', 'https://example.com');

// Email Configuration for 2FA and password resets
define('SMTP_HOST', 'smtp.example.com');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');
define('SMTP_USER', 'noreply@example.com');
define('SMTP_PASS', 'password');
define('FROM_EMAIL', 'noreply@example.com');

// Security Configuration
define('SESSION_TIMEOUT', 3600); // 1 hour
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_ATTEMPT_TIMEOUT', 900); // 15 minutes

// Application Configuration
define('PAGINATION_LIMIT', 10);

// File paths
define('ROOT_PATH', dirname(__FILE__));
define('VENDOR_PATH', ROOT_PATH . '/vendors');

// Timezone - Update to your local timezone
date_default_timezone_set('America/Los_Angeles');

// Include version info for channel detection
require_once ROOT_PATH . '/version.php';

// Error reporting (based on channel)
if ($channel === 'release') {
    // Production mode - disable error display and reporting
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    // ini_set('error_log', '/path/to/your/error.log'); // Set custom error log path if needed
} else {
    // Development/Beta mode - enable full error reporting
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
}
