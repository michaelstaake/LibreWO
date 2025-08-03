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

// Email Configuration for 2FA and notifications
define('SMTP_HOST', 'smtp.example.com');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');
define('SMTP_USER', 'noreply@example.com');
define('SMTP_PASS', 'password');
define('FROM_EMAIL', 'noreply@example.com');
define('FROM_NAME', 'LibreWO System');

// Security Configuration
define('SESSION_TIMEOUT', 3600); // 1 hour
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_ATTEMPT_TIMEOUT', 900); // 15 minutes

// Application Configuration
define('APP_NAME', 'LibreWO');
define('PAGINATION_LIMIT', 10);

// File paths
define('ROOT_PATH', dirname(__FILE__));
define('VENDOR_PATH', ROOT_PATH . '/vendors');
define('LOG_PATH', ROOT_PATH . '/logs');

// Create directories if they don't exist
if (!file_exists(LOG_PATH)) {
    mkdir(LOG_PATH, 0755, true);
}

// Timezone - Update to your local timezone
date_default_timezone_set('America/Los_Angeles');

// Error reporting - Set to 0 in production
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Production settings (uncomment for production)
/*
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', LOG_PATH . '/php_errors.log');
*/
