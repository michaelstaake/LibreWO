# LibreWO Quick Start Guide

## System Requirements

Before installation, ensure your system meets these requirements:

- **PHP 8.4 or higher**
- **Apache or LiteSpeed web server** (with .htaccess support)
- **MySQL 5.7+ or MariaDB 10.2+**
- **PHP Extensions:** PDO MySQL, OpenSSL
- **File Permissions:** Writable logs/ directory
- **URL Rewriting:** mod_rewrite or equivalent

## Installation Steps

### 1. Setup Database
1. Create a MySQL database named `librewo`
2. Update `config.php` with your database credentials

### 2. Configure Settings
Update the following in `config.php`:

```php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'librewo');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');

// Base URL (important for emails and redirects)
define('BASE_URL', 'http://yoursite.com/librewo');

// Email Configuration (for 2FA and password resets)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');
define('SMTP_USER', 'your-email@gmail.com');
define('SMTP_PASS', 'your-app-password');
define('FROM_EMAIL', 'noreply@yourcompany.com');
define('FROM_NAME', 'LibreWO System');
```

### 3. Install PHPMailer
1. Download PHPMailer from https://github.com/PHPMailer/PHPMailer
2. Extract to `vendors/phpmailer/`
3. The structure should be: `vendors/phpmailer/src/PHPMailer.php`

### 4. Set Permissions
```bash
chmod 755 logs/
chmod 644 config.php
```

### 5. Access the Application
1. Navigate to your LibreWO URL
2. The installer will run automatically
3. Create your admin account
4. Start using LibreWO!

## Default User Roles

### Admin
- Full system access
- User management
- Settings configuration
- Can delete records

### Technician  
- Create/edit work orders
- Create/edit customers
- Cannot delete records
- No admin functions

### Limited
- View-only access
- Cannot create/edit/delete

## Security Features

- Two-factor authentication via email
- Session timeout protection
- CSRF protection on all forms
- SQL injection prevention
- Brute force login protection
- Comprehensive activity logging

## Getting Help

- Documentation: https://librewo.com
- Issues: Report on GitHub
- Community: LibreWO Forums

Enjoy using LibreWO!
