<?php
// Test script for PHPMailer 2FA email functionality
// This can be accessed via web browser to test the email sending

require_once 'config.php';

echo "<!DOCTYPE html><html><head><title>Email Test</title></head><body>";
echo "<h1>LibreWO Email Test</h1>";

try {
    require_once ROOT_PATH . '/core/EmailSender.php';
    
    echo "<h2>PHPMailer Integration Test</h2>";
    
    // Check if PHPMailer files exist
    $phpmailerPath = VENDOR_PATH . '/PHPMailer/PHPMailer.php';
    if (file_exists($phpmailerPath)) {
        echo "<p style='color: green;'>✓ PHPMailer found at: " . $phpmailerPath . "</p>";
    } else {
        echo "<p style='color: red;'>✗ PHPMailer not found at: " . $phpmailerPath . "</p>";
    }
    
    // Check email configuration
    echo "<h3>Email Configuration</h3>";
    echo "<p>SMTP Host: " . htmlspecialchars(SMTP_HOST) . "</p>";
    echo "<p>SMTP Port: " . SMTP_PORT . "</p>";
    echo "<p>SMTP User: " . htmlspecialchars(SMTP_USER) . "</p>";
    echo "<p>From Email: " . htmlspecialchars(FROM_EMAIL) . "</p>";
    echo "<p>From Name: " . htmlspecialchars(FROM_NAME) . "</p>";
    
    // Test EmailSender instantiation
    try {
        $emailSender = new EmailSender();
        echo "<p style='color: green;'>✓ EmailSender class instantiated successfully</p>";
        
        // Optionally, you can add a test email sending here
        // $testResult = $emailSender->send2FACode('test@example.com', '1234', 'TestUser');
        // echo "<p>Test email result: " . ($testResult ? 'Success' : 'Failed') . "</p>";
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ EmailSender error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<h3>Usage</h3>";
echo "<p>2FA emails will now be sent using PHPMailer instead of being logged to error logs.</p>";
echo "<p>If email sending fails, the system will fall back to error logging.</p>";

echo "</body></html>";
?>
