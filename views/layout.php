<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? ($companyName ?? 'LibreWO') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        /**
         * Phone Number Validation and Formatting
         * Handles different phone number formats based on system settings
         */
        class PhoneValidator {
            constructor() {
                this.format = 'default'; // Will be set by the application
                this.patterns = {
                    default: /^\d{7,}$/, // At least 7 digits
                    usa_format_a: /^\(\d{3}\) \d{3}-\d{4}$/, // (555) 555-5555
                    usa_format_b: /^\d{3}-\d{3}-\d{4}$/ // 555-555-5555
                };
                this.formatters = {
                    usa_format_a: this.formatUSAA.bind(this),
                    usa_format_b: this.formatUSAB.bind(this)
                };
            }

            setFormat(format) {
                this.format = format;
            }

            /**
             * Format phone number for USA Format A: (555) 555-5555
             */
            formatUSAA(value) {
                // Remove all non-digit characters
                const digits = value.replace(/\D/g, '');
                
                if (digits.length === 0) return '';
                if (digits.length <= 3) return `(${digits}`;
                if (digits.length <= 6) return `(${digits.slice(0, 3)}) ${digits.slice(3)}`;
                return `(${digits.slice(0, 3)}) ${digits.slice(3, 6)}-${digits.slice(6, 10)}`;
            }

            /**
             * Format phone number for USA Format B: 555-555-5555
             */
            formatUSAB(value) {
                // Remove all non-digit characters
                const digits = value.replace(/\D/g, '');
                
                if (digits.length === 0) return '';
                if (digits.length <= 3) return digits;
                if (digits.length <= 6) return `${digits.slice(0, 3)}-${digits.slice(3)}`;
                return `${digits.slice(0, 3)}-${digits.slice(3, 6)}-${digits.slice(6, 10)}`;
            }

            /**
             * Format input as user types
             */
            formatInput(value) {
                if (this.format === 'default') {
                    return value; // No formatting for default
                }
                
                if (this.formatters[this.format]) {
                    return this.formatters[this.format](value);
                }
                
                return value;
            }

            /**
             * Format existing phone number to current format
             * This is used to reformat existing data when the page loads
             */
            formatExistingNumber(value) {
                if (!value || this.format === 'default') {
                    return value; // No formatting for default or empty values
                }
                
                // Extract digits only
                const digits = value.replace(/\D/g, '');
                
                // Only format if we have exactly 10 digits (US phone number)
                if (digits.length === 10) {
                    if (this.formatters[this.format]) {
                        return this.formatters[this.format](digits);
                    }
                }
                
                // Return original value if we can't format it properly
                return value;
            }

            /**
             * Validate phone number against current format
             */
            validate(value) {
                if (!value) return { valid: false, message: 'Phone number is required.' };
                
                const pattern = this.patterns[this.format];
                if (!pattern) return { valid: false, message: 'Invalid phone format configuration.' };

                if (this.format === 'default') {
                    // For default format, just check if there are at least 7 digits
                    const digits = value.replace(/\D/g, '');
                    if (digits.length < 7) {
                        return { valid: false, message: 'Phone number must contain at least 7 digits.' };
                    }
                    return { valid: true };
                }

                if (!pattern.test(value)) {
                    const formatMessages = {
                        usa_format_a: 'Phone number must be in format: (555) 555-5555',
                        usa_format_b: 'Phone number must be in format: 555-555-5555'
                    };
                    return { 
                        valid: false, 
                        message: formatMessages[this.format] || 'Invalid phone number format.' 
                    };
                }

                return { valid: true };
            }

            /**
             * Validate phone number with optional requirement check
             */
            validateOptional(value, isRequired = true) {
                // If not required and empty, it's valid
                if (!isRequired && (!value || value.trim() === '')) {
                    return { valid: true };
                }
                
                // Use regular validation if required or has value
                return this.validate(value);
            }

            /**
             * Get placeholder text for input field
             */
            getPlaceholder() {
                const placeholders = {
                    default: 'Enter phone number (min 7 digits)',
                    usa_format_a: '(555) 555-5555',
                    usa_format_b: '555-555-5555'
                };
                return placeholders[this.format] || '';
            }

            /**
             * Add real-time validation to a phone input field
             */
            attachToInput(inputElement, errorElement = null) {
                if (!inputElement) return;

                // Check if this field should be treated as optional
                const isOptional = inputElement.hasAttribute('data-no-auto-format');

                // Set placeholder
                inputElement.placeholder = this.getPlaceholder();

                // Format as user types (for USA formats, but not for optional fields)
                if (!isOptional) {
                    inputElement.addEventListener('input', (e) => {
                        const cursorPosition = e.target.selectionStart;
                        const oldValue = e.target.value;
                        const newValue = this.formatInput(oldValue);
                        
                        if (newValue !== oldValue) {
                            e.target.value = newValue;
                            // Adjust cursor position to handle formatting
                            const offset = newValue.length - oldValue.length;
                            e.target.setSelectionRange(cursorPosition + offset, cursorPosition + offset);
                        }
                    });
                }

                // Validate on blur
                inputElement.addEventListener('blur', (e) => {
                    const result = isOptional ? 
                        this.validateOptional(e.target.value, false) : 
                        this.validate(e.target.value);
                    this.showValidationResult(inputElement, errorElement, result);
                });

                // Clear validation on focus
                inputElement.addEventListener('focus', (e) => {
                    this.clearValidationResult(inputElement, errorElement);
                });
            }

            /**
             * Show validation result
             */
            showValidationResult(inputElement, errorElement, result) {
                if (result.valid) {
                    inputElement.classList.remove('border-red-300', 'focus:ring-red-500', 'focus:border-red-500');
                    inputElement.classList.add('border-green-300', 'focus:ring-green-500', 'focus:border-green-500');
                    if (errorElement) {
                        errorElement.textContent = '';
                        errorElement.style.display = 'none';
                    }
                } else {
                    inputElement.classList.remove('border-green-300', 'focus:ring-green-500', 'focus:border-green-500');
                    inputElement.classList.add('border-red-300', 'focus:ring-red-500', 'focus:border-red-500');
                    if (errorElement) {
                        errorElement.textContent = result.message;
                        errorElement.style.display = 'block';
                    }
                }
            }

            /**
             * Clear validation styling
             */
            clearValidationResult(inputElement, errorElement) {
                inputElement.classList.remove(
                    'border-red-300', 'focus:ring-red-500', 'focus:border-red-500',
                    'border-green-300', 'focus:ring-green-500', 'focus:border-green-500'
                );
                if (errorElement) {
                    errorElement.style.display = 'none';
                }
            }
        }

        // Global phone validator instance
        window.phoneValidator = new PhoneValidator();

        /**
         * Initialize phone validation for all phone inputs on the page
         */
        function initializePhoneValidation(format = 'default') {
            window.phoneValidator.setFormat(format);
            
            // Find all phone inputs and attach validation
            const phoneInputs = document.querySelectorAll('input[type="tel"], input[name*="phone"]');
            phoneInputs.forEach(input => {
                // Skip auto-formatting for excluded fields (like settings)
                const shouldAutoFormat = !input.hasAttribute('data-no-auto-format');
                
                // Format existing value to current format (only if not excluded)
                if (shouldAutoFormat) {
                    const currentValue = input.value;
                    if (currentValue) {
                        const formattedValue = window.phoneValidator.formatExistingNumber(currentValue);
                        if (formattedValue !== currentValue) {
                            input.value = formattedValue;
                        }
                    }
                }
                
                // Create or find error element
                let errorElement = input.parentElement.querySelector('.phone-error');
                if (!errorElement) {
                    errorElement = document.createElement('div');
                    errorElement.className = 'phone-error text-sm text-red-600 mt-1';
                    errorElement.style.display = 'none';
                    input.parentElement.appendChild(errorElement);
                }
                
                window.phoneValidator.attachToInput(input, errorElement);
            });
        }

        /**
         * Validate all phone fields on form submission
         */
        function validateAllPhoneFields() {
            const phoneInputs = document.querySelectorAll('input[type="tel"], input[name*="phone"]');
            let allValid = true;
            
            phoneInputs.forEach(input => {
                const isOptional = input.hasAttribute('data-no-auto-format');
                const result = isOptional ? 
                    window.phoneValidator.validateOptional(input.value, false) : 
                    window.phoneValidator.validate(input.value);
                const errorElement = input.parentElement.querySelector('.phone-error');
                window.phoneValidator.showValidationResult(input, errorElement, result);
                
                if (!result.valid) {
                    allValid = false;
                }
            });
            
            return allValid;
        }
    </script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <?php if (isset($_SESSION['user_id']) && !isset($hideNavigation)): ?>
        <nav class="bg-white shadow-lg">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex justify-between items-center py-4">
                    <div class="flex items-center">
                        <a href="<?= BASE_URL ?>/" class="flex items-center">
                            <?php if (!empty($companyLogoUrl)): ?>
                                <img src="<?= htmlspecialchars($companyLogoUrl) ?>" alt="<?= htmlspecialchars($companyName ?? 'LibreWO') ?>" class="h-10 w-auto">
                            <?php else: ?>
                                <span class="text-xl font-bold text-gray-900"><?= htmlspecialchars($companyName ?? 'LibreWO') ?></span>
                            <?php endif; ?>
                        </a>
                    </div>
                    <div class="hidden md:flex items-center space-x-6">
                        <a href="<?= BASE_URL ?>/" class="text-gray-600 hover:text-gray-900">Home</a>
                        <a href="<?= BASE_URL ?>/work-orders" class="text-gray-600 hover:text-gray-900">Work Orders</a>
                        <?php if ($_SESSION['user_group'] === 'Admin'): ?>
                            <a href="<?= BASE_URL ?>/customers" class="text-gray-600 hover:text-gray-900">Customers</a>
                            <a href="<?= BASE_URL ?>/users" class="text-gray-600 hover:text-gray-900">Users</a>
                            <a href="<?= BASE_URL ?>/settings" class="text-gray-600 hover:text-gray-900">Settings</a>
                        <?php endif; ?>
                        <a href="<?= BASE_URL ?>/logout" class="text-gray-600 hover:text-gray-900">Log Out</a>
                    </div>
                    <!-- Mobile menu button -->
                    <div class="md:hidden">
                        <button id="mobile-menu-button" class="text-gray-600 hover:text-gray-900 focus:outline-none focus:text-gray-900">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
                <!-- Mobile menu -->
                <div id="mobile-menu" class="md:hidden pb-4 hidden">
                    <a href="<?= BASE_URL ?>/" class="block py-2 text-gray-600 hover:text-gray-900">Home</a>
                    <a href="<?= BASE_URL ?>/work-orders" class="block py-2 text-gray-600 hover:text-gray-900">Work Orders</a>
                    <?php if ($_SESSION['user_group'] === 'Admin'): ?>
                        <a href="<?= BASE_URL ?>/customers" class="block py-2 text-gray-600 hover:text-gray-900">Customers</a>
                        <a href="<?= BASE_URL ?>/users" class="block py-2 text-gray-600 hover:text-gray-900">Users</a>
                        <a href="<?= BASE_URL ?>/settings" class="block py-2 text-gray-600 hover:text-gray-900">Settings</a>
                    <?php endif; ?>
                    <a href="<?= BASE_URL ?>/logout" class="block py-2 text-gray-600 hover:text-gray-900">Log Out</a>
                </div>
            </div>
        </nav>
    <?php endif; ?>

    <main class="<?= isset($_SESSION['user_id']) && !isset($hideNavigation) ? 'pt-6' : '' ?>">
        <?= $content ?? '' ?>
    </main>

    <?php if (isset($_SESSION['user_id']) && !isset($hideNavigation)): ?>
        <footer class="bg-white border-t mt-12">
            <div class="max-w-7xl mx-auto px-4 py-6">
                <div class="flex justify-between items-center">
                    <div class="text-gray-600">
                        <span><?= htmlspecialchars(!empty($_SESSION['user_name']) ? $_SESSION['user_name'] : $_SESSION['username']) ?></span>
                    </div>
                    <div class="text-gray-600 flex items-center">
                        <p>Powered by <a href="https://librewo.com" target="_blank" class="text-primary-600 hover:text-primary-700">LibreWO</a></p>
                        <?php
                        // Show update notification if available
                        if (isset($_SESSION['update_info']) && is_array($_SESSION['update_info'])):
                            $updateInfo = $_SESSION['update_info'];
                            if ($updateInfo['status'] === 'success' && isset($updateInfo['update_available']) && $updateInfo['update_available']):
                        ?>
                            <button onclick="showUpdateModal()" class="ml-2 text-red-600 hover:text-red-700" title="Update available - Click to view details">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        <?php elseif ($updateInfo['status'] === 'success' && isset($updateInfo['update_available']) && !$updateInfo['update_available']): ?>
                            <span class="ml-2 text-green-600" title="You are running the latest version (<?= htmlspecialchars($updateInfo['current_version'] ?? 'Unknown') ?>)">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        <?php elseif ($updateInfo['status'] === 'error'): ?>
                            <span class="ml-2 text-yellow-600" title="<?= htmlspecialchars($updateInfo['message'] ?? 'Unable to check for updates') ?>">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </footer>

        <!-- Update Modal -->
        <?php if (isset($_SESSION['update_info']) && is_array($_SESSION['update_info']) && $_SESSION['update_info']['status'] === 'success' && isset($_SESSION['update_info']['update_available']) && $_SESSION['update_info']['update_available']): ?>
        <div id="updateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.96-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mt-2">Update Available</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500">
                            A new version of LibreWO is available!<br>
                            Current version: <?= htmlspecialchars($_SESSION['update_info']['current_version'] ?? 'Unknown') ?><br>
                            Latest version: <?= htmlspecialchars($_SESSION['update_info']['latest_version'] ?? 'Unknown') ?>
                        </p>
                    </div>
                    <div class="items-center px-4 py-3">
                        <a href="https://librewo.com" target="_blank" class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-300 inline-block text-center mb-2">
                            Download Update
                        </a>
                        <button onclick="hideUpdateModal()" class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    <?php endif; ?>

    <script>
        // Mobile menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            
            if (mobileMenuButton && mobileMenu) {
                mobileMenuButton.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                });
            }

            // Initialize phone validation
            <?php 
            // Get phone format setting
            if (isset($_SESSION['user_id'])) {
                require_once ROOT_PATH . '/models/Settings.php';
                $settingsModel = new Settings();
                $phoneFormat = $settingsModel->getSetting('phone_number_format', 'default');
                echo "initializePhoneValidation('" . htmlspecialchars($phoneFormat) . "');";
            }
            ?>
        });

        // Add form validation before submission
        document.addEventListener('submit', function(e) {
            // Check if form contains phone fields
            const form = e.target;
            const phoneInputs = form.querySelectorAll('input[type="tel"], input[name*="phone"]');
            
            if (phoneInputs.length > 0) {
                if (!validateAllPhoneFields()) {
                    e.preventDefault();
                    showAlert('Please fix the phone number format errors before submitting.', 'error');
                    return false;
                }
            }
        });

        // Global functions
        function showAlert(message, type = 'info') {
            const alertDiv = document.createElement('div');
            alertDiv.className = `fixed top-4 right-4 z-50 p-4 rounded-md shadow-lg ${
                type === 'error' ? 'bg-red-100 text-red-700 border border-red-300' :
                type === 'success' ? 'bg-green-100 text-green-700 border border-green-300' :
                'bg-blue-100 text-blue-700 border border-blue-300'
            }`;
            alertDiv.textContent = message;
            
            document.body.appendChild(alertDiv);
            
            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }

        // CSRF token for AJAX requests
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                          document.querySelector('input[name="csrf_token"]')?.value;

        // Update modal functions
        function showUpdateModal() {
            const modal = document.getElementById('updateModal');
            if (modal) {
                modal.classList.remove('hidden');
            }
        }

        function hideUpdateModal() {
            const modal = document.getElementById('updateModal');
            if (modal) {
                modal.classList.add('hidden');
            }
        }

        // Close modal when clicking outside
        document.addEventListener('click', function(event) {
            const modal = document.getElementById('updateModal');
            if (modal && event.target === modal) {
                hideUpdateModal();
            }
        });

        // Close modal with escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                hideUpdateModal();
            }
        });
    </script>
</body>
</html>
