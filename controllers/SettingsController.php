<?php
require_once 'core/Controller.php';
require_once 'models/Settings.php';

class SettingsController extends Controller {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function index() {
        $this->requireAdmin();
        
        $error = '';
        $message = '';
        $activeTab = $_GET['tab'] ?? 'company';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->validateCSRF();
                
                if (isset($_POST['section']) && $_POST['section'] === 'company') {
                    $companyData = [
                        'company_name' => $this->sanitizeInput($_POST['company_name']),
                        'company_address' => $this->sanitizeInput($_POST['company_address']),
                        'company_phone' => $this->sanitizeInput($_POST['company_phone']),
                        'company_email' => $this->sanitizeInput($_POST['company_email']),
                        'company_logo_url' => $this->sanitizeInput($_POST['company_logo_url']),
                        'work_order_disclaimer' => $this->sanitizeInput($_POST['work_order_disclaimer'])
                    ];
                    
                    $this->settingsModel->updateCompanyInfo($companyData);
                    $this->logger->log('settings_updated', 'Company information updated', $_SESSION['user_id']);
                    $message = 'Company information updated successfully.';
                    
                } elseif (isset($_POST['section']) && $_POST['section'] === 'security') {
                    $securityData = [
                        'require_2fa' => isset($_POST['require_2fa']) ? 1 : 0,
                        'session_timeout' => intval($_POST['session_timeout']),
                        'max_login_attempts' => intval($_POST['max_login_attempts']),
                        'captcha_provider' => $_POST['captcha_provider'],
                        'turnstile_site_key' => $this->sanitizeInput($_POST['turnstile_site_key']),
                        'turnstile_secret_key' => $this->sanitizeInput($_POST['turnstile_secret_key']),
                        'recaptcha_site_key' => $this->sanitizeInput($_POST['recaptcha_site_key']),
                        'recaptcha_secret_key' => $this->sanitizeInput($_POST['recaptcha_secret_key'])
                    ];
                    
                    $this->settingsModel->updateSecuritySettings($securityData);
                    $this->logger->log('settings_updated', 'Security settings updated', $_SESSION['user_id']);
                    $message = 'Security settings updated successfully.';
                    
                } elseif (isset($_POST['section']) && $_POST['section'] === 'format') {
                    $formatData = [
                        'phone_number_format' => $_POST['phone_number_format']
                    ];
                    
                    $this->settingsModel->updateFormatSettings($formatData);
                    $this->logger->log('settings_updated', 'Format settings updated', $_SESSION['user_id']);
                    $message = 'Format settings updated successfully.';
                    
                }
                
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
        
        $companyInfo = $this->settingsModel->getCompanyInfo();
        $captchaSettings = $this->settingsModel->getCaptchaSettings();
        $allSettings = $this->settingsModel->getAllSettings();
        
        $this->view('settings/index', [
            'companyInfo' => $companyInfo,
            'captchaSettings' => $captchaSettings,
            'settings' => $allSettings,
            'error' => $error,
            'message' => $message,
            'activeTab' => $activeTab,
            'csrf_token' => $this->generateCSRF()
        ]);
    }
}
