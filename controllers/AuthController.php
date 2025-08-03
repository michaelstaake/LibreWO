<?php
require_once 'core/Controller.php';
require_once 'models/User.php';
require_once 'models/Settings.php';

class AuthController extends Controller {
    private $userModel;
    
    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
    }
    
    public function login() {
        // If already logged in, redirect to home
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/');
        }
        
        $error = '';
        $message = '';
        $requires2FA = false;
        $pendingUserId = null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->validateCSRF();
                $ip = $this->getClientIP();
                
                // Handle 2FA verification
                if (isset($_POST['two_factor_code'])) {
                    $userId = $_SESSION['pending_2fa_user'] ?? null;
                    $code = $this->sanitizeInput($_POST['two_factor_code']);
                    
                    if ($userId && $this->userModel->verify2FACode($userId, $code)) {
                        $this->completeLogin($userId, $ip);
                        $this->redirect('/');
                    } else {
                        $error = 'Invalid or expired verification code.';
                        $requires2FA = true;
                        $pendingUserId = $userId;
                    }
                } else {
                    // Regular login with CAPTCHA verification
                    $username = $this->sanitizeInput($_POST['username']);
                    $password = $_POST['password'];
                    
                    // Verify CAPTCHA if enabled
                    $captchaProvider = $this->settingsModel->getSetting('captcha_provider', 'off');
                    $turnstileSiteKey = $this->settingsModel->getSetting('turnstile_site_key', '');
                    
                    if ($captchaProvider === 'turnstile' && !empty($turnstileSiteKey)) {
                        if (!$this->verifyTurnstile($_POST['cf-turnstile-response'] ?? '')) {
                            throw new Exception('CAPTCHA verification failed. Please try again.');
                        }
                    }
                
                    // Check login attempts
                    if ($this->userModel->getLoginAttempts($ip) >= MAX_LOGIN_ATTEMPTS) {
                        throw new Exception('Too many login attempts. Please try again later.');
                    }
                    $user = $this->userModel->findByUsername($username);
                    
                    if ($user && $this->userModel->verifyPassword($password, $user['password'])) {
                        // Check if 2FA is required
                        if (!$this->userModel->hasRecentLogin($user['id'], $ip)) {
                            $code = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
                            $this->userModel->store2FACode($user['id'], $code);
                            
                            if ($this->send2FAEmail($user['email'], $code)) {
                                $message = 'A verification code has been sent to your email address.';
                            } else {
                                $message = 'Verification code generated. Please check the system logs or contact your administrator.';
                            }
                            
                            $_SESSION['pending_2fa_user'] = $user['id'];
                            $requires2FA = true;
                            $pendingUserId = $user['id'];
                        } else {
                            $this->completeLogin($user['id'], $ip);
                            $this->redirect('/');
                        }
                    } else {
                        $this->userModel->recordLoginAttempt($ip, $username, false);
                        $error = 'Invalid username or password.';
                    }
                }
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
        
        // Get CAPTCHA settings
        $captchaSettings = [
            'captcha_provider' => $this->settingsModel->getSetting('captcha_provider', 'off'),
            'turnstile_site_key' => $this->settingsModel->getSetting('turnstile_site_key', '')
        ];
        
        $this->view('auth/login', [
            'error' => $error,
            'message' => $message ?? '',
            'requires2FA' => $requires2FA,
            'pendingUserId' => $pendingUserId,
            'captchaSettings' => $captchaSettings,
            'csrf_token' => $this->generateCSRF()
        ]);
    }
    
    private function completeLogin($userId, $ip) {
        $user = $this->userModel->findById($userId);
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_group'] = $user['user_group'];
        
        // Record login
        $this->userModel->recordLogin($userId, $ip);
        $this->userModel->recordLoginAttempt($ip, $user['username'], true);
        
        // Update last login
        $this->userModel->updateLastLogin($userId);
        
        // Log the login
        $this->logger->log('user_login', 'User logged in successfully', $userId, $ip);
        
        // Clean up pending 2FA
        unset($_SESSION['pending_2fa_user']);
    }
    
    public function logout() {
        if (isset($_SESSION['user_id'])) {
            $this->logger->log('user_logout', 'User logged out', $_SESSION['user_id']);
        }
        
        session_destroy();
        $this->redirect('/login');
    }
    
    public function forgotPassword() {
        $message = '';
        $error = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->validateCSRF();
                
                // Verify CAPTCHA if enabled
                $captchaProvider = $this->settingsModel->getSetting('captcha_provider', 'off');
                if ($captchaProvider === 'turnstile') {
                    if (!$this->verifyTurnstile($_POST['cf-turnstile-response'] ?? '')) {
                        throw new Exception('CAPTCHA verification failed. Please try again.');
                    }
                }
                
                $email = $this->sanitizeInput($_POST['email']);
                $user = $this->userModel->findByEmail($email);
                
                if ($user) {
                    $token = bin2hex(random_bytes(32));
                    $this->userModel->update($user['id'], [
                        'reset_token' => $token,
                        'reset_expires' => date('Y-m-d H:i:s', strtotime('+1 hour'))
                    ]);
                    
                    $this->sendPasswordResetEmail($user['email'], $token);
                    $this->logger->log('password_reset_requested', 'Password reset requested', $user['id']);
                }
                
                // Always show success message for security
                $message = 'If an account with that email exists, a password reset link has been sent.';
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
        
        // Get CAPTCHA settings
        $captchaSettings = [
            'captcha_provider' => $this->settingsModel->getSetting('captcha_provider', 'off'),
            'turnstile_site_key' => $this->settingsModel->getSetting('turnstile_site_key', '')
        ];
        
        $this->view('auth/forgot-password', [
            'message' => $message,
            'error' => $error,
            'captchaSettings' => $captchaSettings,
            'csrf_token' => $this->generateCSRF()
        ]);
    }
    
    public function resetPassword() {
        $token = $_GET['token'] ?? '';
        $error = '';
        $message = '';
        
        if (!$token) {
            $this->redirect('/login');
        }
        
        $user = $this->userModel->findOneWhere('reset_token = ? AND reset_expires > NOW()', [$token]);
        
        if (!$user) {
            $error = 'Invalid or expired reset token.';
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
            try {
                $this->validateCSRF();
                
                $password = $_POST['password'];
                $confirmPassword = $_POST['confirm_password'];
                
                if (strlen($password) < 8) {
                    throw new Exception('Password must be at least 8 characters long.');
                }
                
                if ($password !== $confirmPassword) {
                    throw new Exception('Passwords do not match.');
                }
                
                $this->userModel->updatePassword($user['id'], $password);
                $this->userModel->update($user['id'], [
                    'reset_token' => null,
                    'reset_expires' => null
                ]);
                
                $this->logger->log('password_reset_completed', 'Password reset completed', $user['id']);
                $message = 'Password has been reset successfully. You can now log in.';
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
        
        $this->view('auth/reset-password', [
            'token' => $token,
            'error' => $error,
            'message' => $message,
            'valid_token' => !empty($user),
            'csrf_token' => $this->generateCSRF()
        ]);
    }
    
    private function send2FAEmail($email, $code) {
        try {
            require_once ROOT_PATH . '/core/EmailSender.php';
            $emailSender = new EmailSender();
            
            // Get user info for personalized email
            $user = $this->userModel->findByEmail($email);
            $username = $user ? $user['username'] : 'User';
            
            return $emailSender->send2FACode($email, $code, $username);
        } catch (Exception $e) {
            // Fallback to error log if email fails
            error_log("Failed to send 2FA email to $email: " . $e->getMessage());
            error_log("2FA Code for $email: $code");
            return false;
        }
    }
    
    private function sendPasswordResetEmail($email, $token) {
        try {
            require_once ROOT_PATH . '/core/EmailSender.php';
            $emailSender = new EmailSender();
            
            // Get user info for personalized email
            $user = $this->userModel->findByEmail($email);
            $username = $user ? $user['username'] : 'User';
            
            return $emailSender->sendPasswordReset($email, $token, $username);
        } catch (Exception $e) {
            // Fallback to error log if email fails
            $resetUrl = BASE_URL . "/reset-password?token=$token";
            error_log("Failed to send password reset email to $email: " . $e->getMessage());
            error_log("Password reset URL for $email: $resetUrl");
            return false;
        }
    }
    
    private function verifyTurnstile($response) {
        if (empty($response)) {
            return false;
        }
        
        $secretKey = $this->settingsModel->getSetting('turnstile_secret_key', '');
        if (empty($secretKey)) {
            return false;
        }
        
        $data = [
            'secret' => $secretKey,
            'response' => $response,
            'remoteip' => $this->getClientIP()
        ];
        
        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data),
            ],
        ];
        
        $context = stream_context_create($options);
        $result = file_get_contents('https://challenges.cloudflare.com/turnstile/v0/siteverify', false, $context);
        
        if ($result === false) {
            return false;
        }
        
        $resultJson = json_decode($result, true);
        return isset($resultJson['success']) && $resultJson['success'] === true;
    }
}
