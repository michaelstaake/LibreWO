# LibreWO - Work Order Management System

LibreWO is a comprehensive work order management system designed for computer repair shops. Built with PHP and MySQL using an MVC architecture, it provides a secure, user-friendly interface for managing customers, work orders, and technicians.

## Features

### Core Features
- **User Management**: Three user levels (Admin, Technician, Limited) with role-based permissions
- **Work Order Management**: Complete work order lifecycle from creation to completion
- **Customer Management**: Customer database with search and merge capabilities
- **Two-Factor Authentication**: Email-based 2FA for enhanced security
- **Comprehensive Logging**: Detailed activity logs for all system actions
- **Print-Ready Work Orders**: Professional work order printouts with company branding

### Security Features
- Prepared statements for SQL injection prevention
- CSRF protection on all forms
- Session security with timeout handling
- IP-based login tracking
- Brute force protection with login attempt limits
- Cloudflare support for real IP detection

### Technical Features
- **MVC Architecture**: Clean, maintainable code structure
- **Responsive Design**: Mobile-friendly interface using Tailwind CSS
- **Pretty URLs**: SEO-friendly URLs with .htaccess routing
- **Email Integration**: PHPMailer support for notifications
- **Database Migrations**: Automated installation system

## Requirements

### System Requirements
- **PHP 8.4 or higher** (Required)
- **Apache or LiteSpeed web server** (Recommended for .htaccess support)
- **MySQL 5.7 or higher / MariaDB 10.2 or higher**
- **PHP Extensions:**
  - PDO MySQL (Required)
  - OpenSSL (Required for security features)
- **File Permissions:** Write access to logs/ directory
- **URL Rewriting:** mod_rewrite (Apache) or equivalent

### Additional Requirements
- PHPMailer (place in vendors/ folder)
- Web server with .htaccess support for pretty URLs

## Installation

1. **Download LibreWO**
   ```bash
   git clone https://github.com/yourcompany/librewo.git
   cd librewo
   ```

2. **Configure Database**
   - Create a MySQL database named `librewo`
   - Update database credentials in `config.php`

3. **Configure Settings**
   - Update `config.php` with your database and email settings
   - Set your `BASE_URL` to match your installation path

4. **Place PHPMailer**
   - Download PHPMailer and extract to `vendors/phpmailer/`
   - The structure should be `vendors/phpmailer/src/PHPMailer.php`

5. **Set Permissions**
   ```bash
   chmod 755 logs/
   chmod 644 config.php
   ```

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
Configure SMTP settings for email notifications:
```php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');
define('SMTP_USER', 'your-email@gmail.com');
define('SMTP_PASS', 'your-password');
```

### Base URL Configuration
Set your application's base URL:
```php
define('BASE_URL', 'http://yoursite.com/librewo');
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

## Security Features

### Two-Factor Authentication
- Triggered when logging in from a new IP address
- 4-digit code sent via email
- Codes expire after 15 minutes

### Login Protection
- Maximum 5 login attempts per IP per 15 minutes
- All login attempts are logged
- Session timeout after 1 hour of inactivity

### Data Protection
- All forms protected with CSRF tokens
- User input sanitized and validated
- Database queries use prepared statements
- Sensitive files protected via .htaccess

## File Structure

```
librewo/
├── config.php              # Configuration file
├── index.php               # Main entry point
├── version.php             # Version information
├── .htaccess               # URL rewriting and security
├── core/                   # Core framework files
│   ├── Controller.php
│   ├── Database.php
│   ├── Logger.php
│   ├── Model.php
│   └── Router.php
├── controllers/            # Application controllers
├── models/                 # Data models
├── views/                  # View templates
├── database/              # Database schema
├── logs/                  # System logs
└── vendors/               # Third-party libraries
    └── phpmailer/         # PHPMailer library
```

## API Endpoints

LibreWO includes AJAX endpoints for dynamic functionality:

- `GET /api/search-customers?q=query` - Search customers
- `POST /api/work-order-status` - Update work order status

## Customization

### Company Branding
Administrators can customize:
- Company name, address, phone, email
- Company logo for work order printouts
- Work order disclaimer text

### CAPTCHA Integration
Support for:
- No CAPTCHA (default)
- Google reCAPTCHA
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
- Expired 2FA codes
- Old login attempt records

### Backup Recommendations
- Regular database backups
- Backup log files
- Backup uploaded company logos

## Support

For support and documentation, visit: https://librewo.com

## License

LibreWO is open-source software. Please check the LICENSE file for details.

## Version

Current version: 1.0.0 (beta)

---

**Powered by LibreWO** - Professional work order management for computer repair shops.
