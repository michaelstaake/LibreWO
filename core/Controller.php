<?php
class Controller {
    protected $db;
    protected $logger;
    protected $settingsModel;
    
    public function __construct() {
        $this->db = new Database();
        $this->logger = new Logger();
        
        // Load settings model for global access
        require_once 'models/Settings.php';
        $this->settingsModel = new Settings();
    }
    
    protected function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
        
        // Check if user is still active
        require_once 'models/User.php';
        $userModel = new User();
        if (!$userModel->isUserActive($_SESSION['user_id'])) {
            // User has been deactivated, destroy session and redirect
            session_destroy();
            header('HTTP/1.1 403 Forbidden');
            header('Location: ' . BASE_URL . '/403?reason=account_deactivated');
            exit;
        }
    }
    
    protected function requireAdmin() {
        $this->requireAuth();
        if ($_SESSION['user_group'] !== 'Admin') {
            header('Location: ' . BASE_URL . '/403');
            exit;
        }
    }
    
    protected function requireTechnician() {
        $this->requireAuth();
        if (!in_array($_SESSION['user_group'], ['Admin', 'Technician'])) {
            header('Location: ' . BASE_URL . '/403');
            exit;
        }
    }
    
    protected function view($viewName, $data = []) {
        // Add company name and logo URL to all views
        $data['companyName'] = $this->settingsModel->getSetting('company_name', 'LibreWO');
        $data['companyLogoUrl'] = $this->settingsModel->getSetting('company_logo_url', '');
        
        extract($data);
        $viewFile = 'views/' . $viewName . '.php';
        
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            throw new Exception("View not found: " . $viewName);
        }
    }
    
    protected function redirect($path) {
        header('Location: ' . BASE_URL . $path);
        exit;
    }
    
    protected function json($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    protected function getClientIP() {
        // Support Cloudflare
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            return $_SERVER['HTTP_CF_CONNECTING_IP'];
        }
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    
    protected function getUserAgent() {
        return $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    }
    
    protected function sanitizeInput($input) {
        if (is_array($input)) {
            return array_map([$this, 'sanitizeInput'], $input);
        }
        if ($input === null) {
            return '';
        }
        return trim($input);
    }
    
    protected function validateCSRF() {
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || 
            !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            throw new Exception('CSRF token validation failed');
        }
    }
    
    protected function generateCSRF() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}
