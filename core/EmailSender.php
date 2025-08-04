<?php
// Email utility class for LibreWO
// This class provides a simple interface to PHPMailer

class EmailSender {
    private $mailer;
    private $companyName;
    
    public function __construct($companyName = 'LibreWO') {
        $this->companyName = $companyName;
        
        // Check if PHPMailer is available
        if (!file_exists(VENDOR_PATH . '/PHPMailer/PHPMailer.php')) {
            throw new Exception('PHPMailer not found. Please install PHPMailer in the vendors/PHPMailer directory.');
        }
        
        require_once VENDOR_PATH . '/PHPMailer/PHPMailer.php';
        require_once VENDOR_PATH . '/PHPMailer/SMTP.php';
        require_once VENDOR_PATH . '/PHPMailer/Exception.php';
        
        $this->mailer = new PHPMailer\PHPMailer\PHPMailer(true);
        $this->configureSMTP();
    }
    
    private function configureSMTP() {
        $this->mailer->isSMTP();
        $this->mailer->Host = SMTP_HOST;
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = SMTP_USER;
        $this->mailer->Password = SMTP_PASS;
        $this->mailer->SMTPSecure = SMTP_SECURE;
        $this->mailer->Port = SMTP_PORT;
        
        $this->mailer->setFrom(FROM_EMAIL, $this->companyName);
    }
    
    public function send2FACode($email, $code, $username) {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($email);
            
            $this->mailer->Subject = $this->companyName . ' - Two-Factor Authentication Code';
            
            $body = "
            <html>
            <body>
                <h2>Two-Factor Authentication</h2>
                <p>Hello {$username},</p>
                <p>You are attempting to log in to {$this->companyName} from a new location. Please use the verification code below to complete your login:</p>
                <h3 style='color: #2563eb; font-size: 24px; letter-spacing: 3px;'>{$code}</h3>
                <p>This code will expire in 15 minutes.</p>
                <p>If you did not attempt to log in, please contact your administrator immediately.</p>
                <hr>
                <p><small>This is an automated message from {$this->companyName}. Please do not reply to this email.</small></p>
            </body>
            </html>
            ";
            
            $this->mailer->isHTML(true);
            $this->mailer->Body = $body;
            
            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Failed to send 2FA email: " . $e->getMessage());
            return false;
        }
    }
    
    public function sendPasswordReset($email, $token, $username) {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($email);
            
            $this->mailer->Subject = $this->companyName . ' - Password Reset Request';
            
            $resetUrl = BASE_URL . "/reset-password?token={$token}";
            
            $body = "
            <html>
            <body>
                <h2>Password Reset Request</h2>
                <p>Hello {$username},</p>
                <p>You have requested a password reset for your {$this->companyName} account. Click the link below to reset your password:</p>
                <p><a href='{$resetUrl}' style='background-color: #2563eb; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Reset Password</a></p>
                <p>Or copy and paste this URL into your browser:</p>
                <p>{$resetUrl}</p>
                <p>This link will expire in 1 hour.</p>
                <p>If you did not request a password reset, please ignore this email.</p>
                <hr>
                <p><small>This is an automated message from {$this->companyName}. Please do not reply to this email.</small></p>
            </body>
            </html>
            ";
            
            $this->mailer->isHTML(true);
            $this->mailer->Body = $body;
            
            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Failed to send password reset email: " . $e->getMessage());
            return false;
        }
    }
}
