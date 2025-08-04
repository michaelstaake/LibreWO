# LibreWO - Work Order Management System

LibreWO is a comprehensive work order management system designed for computer repair shops. Built with PHP and MySQL using an MVC architecture, it provides a secure, user-friendly interface for managing customers, work orders, and technicians.

## Features

- **User Management**: Three user levels (Admin, Technician, Limited) with role-based permissions
- **Work Order Management**: Complete work order lifecycle from creation to completion
- **Customer Management**: Customer database with search and merge capabilities
- **Two-Factor Authentication**: Email-based 2FA for enhanced security
- **Comprehensive Logging**: Detailed activity logs for all system actions
- **Print-Ready Work Orders**: Professional work order printouts with company branding

## Requirements

- **PHP 8.4 or higher** (Required)
- **Apache or LiteSpeed web server** (Recommended for .htaccess support)
- **MySQL 5.7 or higher / MariaDB 10.2 or higher**
- **PHP Extensions:**
  - PDO MySQL (Required)
  - OpenSSL (Required for security features)
- **File Permissions:** Write access to logs/ directory
- **URL Rewriting:** mod_rewrite (Apache) or equivalent

## Installation

1. **Download LibreWO**
   ```bash
   git clone https://github.com/yourcompany/librewo.git
   cd librewo
   ```

2. **Configure Database**
   - Create a MySQL database and user with adequate permissions
   - Update database information in `config.php`

3. **Configure Settings**
   - Update `config.php` with your email settings
   - Set your `BASE_URL` to match your installation path like https://example.com with no trailing slash

6. **Access Installation**
   - Navigate to your LibreWO URL in a web browser
   - The installer will automatically run if the database is empty
   - Create your admin user account

## Configuration

### Database Configuration
Update `config.php` with your database settings:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'librewo');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

### Email Configuration
Configure SMTP settings for 2FA and password reset emails:
```php
define('SMTP_HOST', 'smtp.example.com');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');
define('SMTP_USER', 'noreply@example.com');
define('SMTP_PASS', 'password');
```

### Base URL Configuration
Set your application's base URL:
```php
define('BASE_URL', 'https://example.com');
```

## User Roles

### Admin
- Full access to all features
- User management
- System settings
- Customer management
- Can delete work orders and customers

### Technician
- Create and modify work orders
- Create and modify customers
- Cannot delete anything
- No access to admin functions

### Limited
- View-only access
- Cannot create, edit, or delete anything

## Work Order Workflow

1. **Create Work Order** (4-step process)
   - Step 1: Select or create customer
   - Step 2: Enter computer details and accessories
   - Step 3: Describe the problem
   - Step 4: Confirm details and assign technician

2. **Manage Work Orders**
   - Update status (Open → In Progress → Awaiting Parts → Closed → Picked Up)
   - Add resolution notes
   - Assign to technicians
   - Track all changes in work order log

3. **Print Work Orders**
   - Professional printouts with company branding
   - Customer signature line
   - Customizable disclaimer


## Customization

### Company Branding
Administrators can customize:
- Company name, address, phone, email
- Company logo for work order printouts
- Work order disclaimer text

### CAPTCHA Integration
Support for:
- No CAPTCHA (default)
- Cloudflare Turnstile

## Logging

LibreWO maintains comprehensive logs:
- User logins/logouts
- Work order creation and updates
- Password resets
- Administrative actions
- All logs include IP address, user agent, and timestamp

## Maintenance

### Regular Cleanup
The system automatically cleans up:
- Login records older than 30 days
- Other logs older than 60 days
- Expired 2FA codes
- Old login attempt records

### Backup Recommendations
- Always remember to back up the database regularly!

## Support

For support and documentation, visit: https://librewo.com