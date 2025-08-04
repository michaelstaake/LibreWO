<?php
require_once 'core/Controller.php';
require_once 'models/User.php';

class InstallController extends Controller {
    private $userModel;
    
    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
    }
    
    public function index() {
        // Check if database is already installed
        if ($this->db->isInstalled()) {
            http_response_code(403);
            $this->view('errors/403');
            return;
        }
        
        // Check system requirements
        $requirements = $this->checkSystemRequirements();
        if (!$requirements['meets_requirements']) {
            $this->view('install/requirements', [
                'requirements' => $requirements
            ]);
            return;
        }
        
        // Check if database connection works
        if (!$this->db->canConnect()) {
            $this->view('install/error', [
                'error' => 'Cannot connect to database. Please check your configuration.'
            ]);
            return;
        }
        
        $error = '';
        $success = false;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->validateCSRF();
                
                $username = $this->sanitizeInput($_POST['username']);
                $email = $this->sanitizeInput($_POST['email']);
                $password = $_POST['password'];
                $confirmPassword = $_POST['confirm_password'];
                
                // Validation
                if (empty($username) || empty($email) || empty($password)) {
                    throw new Exception('All fields are required.');
                }
                
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw new Exception('Invalid email address.');
                }
                
                if (strlen($password) < 8) {
                    throw new Exception('Password must be at least 8 characters long.');
                }
                
                if ($password !== $confirmPassword) {
                    throw new Exception('Passwords do not match.');
                }
                
                // Create database tables
                $this->createTables();
                
                // Create admin user
                $this->userModel->createUser([
                    'username' => $username,
                    'email' => $email,
                    'password' => $password,
                    'user_group' => 'Admin'
                ]);
                
                // Create default settings
                $this->createDefaultSettings();
                
                $success = true;
                $this->logger->log('system_installed', 'System installed successfully', null, $this->getClientIP());
                
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
        
        $this->view('install/index', [
            'error' => $error,
            'success' => $success,
            'csrf_token' => $this->generateCSRF()
        ]);
    }
    
    // Override the parent view method to avoid loading settings during installation
    protected function view($viewName, $data = []) {
        // During installation, use default company name and no logo
        $data['companyName'] = 'LibreWO';
        $data['companyLogoUrl'] = '';
        
        extract($data);
        $viewFile = 'views/' . $viewName . '.php';
        
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            http_response_code(404);
            require_once 'views/errors/404.php';
        }
    }
    
    private function createTables() {
        $sql = [
            "CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) UNIQUE NOT NULL,
                name VARCHAR(100) NULL,
                email VARCHAR(100) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                user_group ENUM('Admin', 'Technician', 'Limited') NOT NULL DEFAULT 'Limited',
                is_active TINYINT(1) NOT NULL DEFAULT 1,
                reset_token VARCHAR(255) NULL,
                reset_expires DATETIME NULL,
                created_at DATETIME NOT NULL,
                last_login DATETIME NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            
            "CREATE TABLE IF NOT EXISTS customers (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                company VARCHAR(100) NULL,
                email VARCHAR(100) NULL,
                phone VARCHAR(20) NOT NULL,
                created_at DATETIME NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            
            "CREATE TABLE IF NOT EXISTS work_orders (
                id INT AUTO_INCREMENT PRIMARY KEY,
                work_order_number VARCHAR(20) UNIQUE NOT NULL,
                customer_id INT NOT NULL,
                computer VARCHAR(100) NOT NULL,
                model VARCHAR(100) NULL,
                serial_number VARCHAR(100) NULL,
                accessories TEXT NULL,
                username VARCHAR(50) NULL,
                password VARCHAR(100) NULL,
                description TEXT NOT NULL,
                resolution TEXT NULL,
                notes TEXT NULL,
                status ENUM('Open', 'In Progress', 'Awaiting Parts', 'Closed', 'Picked Up') NOT NULL DEFAULT 'Open',
                priority ENUM('Standard', 'Priority') NOT NULL DEFAULT 'Standard',
                assigned_to INT NULL,
                created_by INT NOT NULL,
                created_at DATETIME NOT NULL,
                closed_at DATETIME NULL,
                FOREIGN KEY (customer_id) REFERENCES customers(id),
                FOREIGN KEY (assigned_to) REFERENCES users(id),
                FOREIGN KEY (created_by) REFERENCES users(id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            
            "CREATE TABLE IF NOT EXISTS work_order_logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                work_order_id INT NOT NULL,
                user_id INT NULL,
                action VARCHAR(50) NOT NULL,
                details TEXT NULL,
                created_at DATETIME NOT NULL,
                FOREIGN KEY (work_order_id) REFERENCES work_orders(id) ON DELETE CASCADE,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            
            "CREATE TABLE IF NOT EXISTS user_logins (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                ip_address VARCHAR(45) NOT NULL,
                login_time DATETIME NOT NULL,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            
            "CREATE TABLE IF NOT EXISTS login_attempts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                ip_address VARCHAR(45) NOT NULL,
                username VARCHAR(50) NULL,
                success BOOLEAN NOT NULL DEFAULT FALSE,
                attempted_at DATETIME NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            
            "CREATE TABLE IF NOT EXISTS two_factor_codes (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                code VARCHAR(4) NOT NULL,
                expires_at DATETIME NOT NULL,
                UNIQUE KEY unique_user (user_id),
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            
            "CREATE TABLE IF NOT EXISTS activity_logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NULL,
                ip_address VARCHAR(45) NOT NULL,
                user_agent TEXT NULL,
                action VARCHAR(100) NOT NULL,
                details TEXT NULL,
                created_at DATETIME NOT NULL,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            
            "CREATE TABLE IF NOT EXISTS settings (
                id INT AUTO_INCREMENT PRIMARY KEY,
                setting_key VARCHAR(100) UNIQUE NOT NULL,
                setting_value TEXT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        ];
        
        foreach ($sql as $query) {
            $this->db->query($query);
        }
    }
    
    private function createDefaultSettings() {
        require_once 'models/Settings.php';
        $settings = new Settings();
        
        $defaultSettings = [
            'company_name' => 'Your Computer Repair Shop',
            'company_address' => '',
            'company_phone' => '',
            'company_email' => '',
            'company_logo' => '',
            'work_order_disclaimer' => 'I authorize the above repair work to be done along with the necessary material, and hereby grant you and/or your employees permission to operate this vehicle/equipment for testing purposes. I promise to pay for all work performed at the above prices. We reserve the right to charge a storage fee for equipment left over 30 days from the time of notification that the work is complete.',
            'captcha_provider' => 'off',
            'turnstile_site_key' => '',
            'turnstile_secret_key' => '',
            'recaptcha_site_key' => '',
            'recaptcha_secret_key' => ''
        ];
        
        foreach ($defaultSettings as $key => $value) {
            $settings->setSetting($key, $value);
        }
    }
    
    private function checkSystemRequirements() {
        $requirements = [
            'php_version' => [
                'required' => '8.4.0',
                'current' => PHP_VERSION,
                'status' => version_compare(PHP_VERSION, '8.4.0', '>='),
                'name' => 'PHP Version'
            ],
            'web_server' => [
                'required' => 'Apache/LiteSpeed/Compatible',
                'current' => $this->getWebServer(),
                'status' => $this->isWebServerCompatible(),
                'name' => 'Web Server'
            ],
            'pdo_mysql' => [
                'required' => 'PDO MySQL Extension',
                'current' => extension_loaded('pdo_mysql') ? 'Available' : 'Not Available',
                'status' => extension_loaded('pdo_mysql'),
                'name' => 'PDO MySQL'
            ],
            'openssl' => [
                'required' => 'OpenSSL Extension',
                'current' => extension_loaded('openssl') ? 'Available' : 'Not Available',
                'status' => extension_loaded('openssl'),
                'name' => 'OpenSSL'
            ],
            'mod_rewrite' => [
                'required' => 'URL Rewriting Support',
                'current' => $this->hasUrlRewriting() ? 'Available' : 'Not Available',
                'status' => $this->hasUrlRewriting(),
                'name' => 'URL Rewriting'
            ]
        ];
        
        $allPassed = true;
        foreach ($requirements as $req) {
            if (!$req['status']) {
                $allPassed = false;
                break;
            }
        }
        
        return [
            'meets_requirements' => $allPassed,
            'checks' => $requirements
        ];
    }
    
    private function getWebServer() {
        $server = $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown';
        
        if (stripos($server, 'apache') !== false) {
            return 'Apache ' . $this->extractVersion($server, 'apache');
        } elseif (stripos($server, 'litespeed') !== false) {
            return 'LiteSpeed ' . $this->extractVersion($server, 'litespeed');
        } elseif (stripos($server, 'nginx') !== false) {
            return 'Nginx ' . $this->extractVersion($server, 'nginx');
        } elseif (stripos($server, 'microsoft-iis') !== false) {
            return 'IIS ' . $this->extractVersion($server, 'microsoft-iis');
        }
        
        return $server;
    }
    
    private function isWebServerCompatible() {
        $server = $_SERVER['SERVER_SOFTWARE'] ?? '';
        
        // Check for Apache or LiteSpeed (both support .htaccess)
        if (stripos($server, 'apache') !== false || 
            stripos($server, 'litespeed') !== false) {
            return true;
        }
        
        // Also accept if mod_rewrite functionality is available
        return $this->hasUrlRewriting();
    }
    
    private function hasUrlRewriting() {
        // Check if mod_rewrite is loaded (Apache)
        if (function_exists('apache_get_modules')) {
            return in_array('mod_rewrite', apache_get_modules());
        }
        
        // Check if URL rewriting is working by testing a simple rewrite
        // This is a basic check - in production, .htaccess rules would be tested
        if (isset($_SERVER['REQUEST_URI']) && 
            strpos($_SERVER['REQUEST_URI'], 'index.php') === false) {
            return true;
        }
        
        // For LiteSpeed and other compatible servers
        $server = $_SERVER['SERVER_SOFTWARE'] ?? '';
        if (stripos($server, 'litespeed') !== false) {
            return true;
        }
        
        return false;
    }
    
    private function extractVersion($serverString, $serverName) {
        $pattern = '/' . preg_quote($serverName, '/') . '\/([0-9.]+)/i';
        if (preg_match($pattern, $serverString, $matches)) {
            return $matches[1];
        }
        return '';
    }
}
