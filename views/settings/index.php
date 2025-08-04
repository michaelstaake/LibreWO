<?php 
$title = 'Settings - ' . ($companyName ?? 'LibreWO');
ob_start(); 
?>

<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Settings</h1>
        <p class="mt-1 text-sm text-gray-600">Configure your LibreWO installation</p>
    </div>

    <?php if (isset($error) && $error): ?>
        <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-600"><?= htmlspecialchars($error) ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($message) && $message): ?>
        <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-600"><?= htmlspecialchars($message) ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="space-y-6">
        <!-- Company Information -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Company Information</h2>
                <p class="mt-1 text-sm text-gray-600">This information appears across the site, emails, and print outs.</p>
            </div>
            <form method="POST" action="<?= BASE_URL ?>/settings" class="px-6 py-4">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                <input type="hidden" name="section" value="company">
                
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="company_name" class="block text-sm font-medium text-gray-700">Company Name</label>
                        <input type="text" id="company_name" name="company_name" value="<?= htmlspecialchars($settings['company_name'] ?? '') ?>" class="mt-1 block w-full px-4 py-3 border-2 border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm bg-white">
                    </div>

                    <div>
                        <label for="company_logo_url" class="block text-sm font-medium text-gray-700">Company Logo URL</label>
                        <input type="url" id="company_logo_url" name="company_logo_url" value="<?= htmlspecialchars($settings['company_logo_url'] ?? '') ?>" placeholder="https://example.com/logo.png" class="mt-1 block w-full px-4 py-3 border-2 border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm bg-white">
                        <p class="mt-1 text-sm text-gray-500">Enter the URL of your logo image</p>
                    </div>

                    <div>
                        <label for="company_phone" class="block text-sm font-medium text-gray-700">Phone</label>
                        <input type="tel" id="company_phone" name="company_phone" value="<?= htmlspecialchars($settings['company_phone'] ?? '') ?>" data-no-auto-format class="mt-1 block w-full px-4 py-3 border-2 border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm bg-white">
                    </div>

                    <div>
                        <label for="company_email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" id="company_email" name="company_email" value="<?= htmlspecialchars($settings['company_email'] ?? '') ?>" class="mt-1 block w-full px-4 py-3 border-2 border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm bg-white">
                    </div>

                    <div>
                        <label for="company_website" class="block text-sm font-medium text-gray-700">Website</label>
                        <input type="url" id="company_website" name="company_website" value="<?= htmlspecialchars($settings['company_website'] ?? '') ?>" class="mt-1 block w-full px-4 py-3 border-2 border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm bg-white">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="company_address" class="block text-sm font-medium text-gray-700">Address</label>
                        <textarea id="company_address" name="company_address" rows="3" class="mt-1 block w-full px-4 py-3 border-2 border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm bg-white"><?= htmlspecialchars($settings['company_address'] ?? '') ?></textarea>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="work_order_disclaimer" class="block text-sm font-medium text-gray-700">Work Order Disclaimer</label>
                        <textarea id="work_order_disclaimer" name="work_order_disclaimer" rows="4" placeholder="Enter any legal disclaimers or terms that should appear on work orders..." class="mt-1 block w-full px-4 py-3 border-2 border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm bg-white"><?= htmlspecialchars($settings['work_order_disclaimer'] ?? '') ?></textarea>
                        <p class="mt-1 text-sm text-gray-500">This text will appear at the bottom of printed work orders</p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Save Company Information
                    </button>
                </div>
            </form>
        </div>

        <!-- Security Settings -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Security Settings</h2>
                <p class="mt-1 text-sm text-gray-600">Configure security and authentication options</p>
            </div>
            <form method="POST" action="<?= BASE_URL ?>/settings" class="px-6 py-4">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                <input type="hidden" name="section" value="security">
                
                <div class="space-y-6">
                    <div>
                        <div class="flex items-center">
                            <input id="require_2fa" name="require_2fa" type="checkbox" <?= ($settings['require_2fa'] ?? false) ? 'checked' : '' ?> class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                            <label for="require_2fa" class="ml-2 block text-sm text-gray-900">
                                Force Two-Factor Authentication
                            </label>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">Enabled: 2FA required for every login. Disabled: 2FA only required from logins from new IP.</p>
                    </div>

                    <div>
                        <label for="session_timeout" class="block text-sm font-medium text-gray-700">Session Timeout (minutes)</label>
                        <input type="number" id="session_timeout" name="session_timeout" value="<?= htmlspecialchars($settings['session_timeout'] ?? '60') ?>" min="5" max="1440" class="mt-1 block w-32 px-4 py-3 border-2 border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm bg-white">
                        <p class="mt-1 text-sm text-gray-500">Users will be automatically logged out after this period of inactivity</p>
                    </div>

                    <div>
                        <label for="max_login_attempts" class="block text-sm font-medium text-gray-700">Maximum Login Attempts</label>
                        <input type="number" id="max_login_attempts" name="max_login_attempts" value="<?= htmlspecialchars($settings['max_login_attempts'] ?? '5') ?>" min="3" max="10" class="mt-1 block w-32 px-4 py-3 border-2 border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm bg-white">
                        <p class="mt-1 text-sm text-gray-500">Number of failed login attempts before account lockout</p>
                    </div>

                    <div>
                        <label for="captcha_provider" class="block text-sm font-medium text-gray-700">CAPTCHA Protection</label>
                        <select id="captcha_provider" name="captcha_provider" class="mt-1 block w-48 px-4 py-3 border-2 border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm bg-white" onchange="toggleCaptchaFields()">
                            <option value="off" <?= ($settings['captcha_provider'] ?? 'off') === 'off' ? 'selected' : '' ?>>Disabled</option>
                            <option value="turnstile" <?= ($settings['captcha_provider'] ?? 'off') === 'turnstile' ? 'selected' : '' ?>>Cloudflare Turnstile</option>
                            <option value="recaptcha" <?= ($settings['captcha_provider'] ?? 'off') === 'recaptcha' ? 'selected' : '' ?>>Google reCAPTCHA v2</option>
                        </select>
                        <p class="mt-1 text-sm text-gray-500">Enable CAPTCHA protection for login and password reset forms</p>
                    </div>

                    <div id="turnstile-fields" style="<?= ($settings['captcha_provider'] ?? 'off') === 'turnstile' ? '' : 'display: none;' ?>">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="turnstile_site_key" class="block text-sm font-medium text-gray-700">Turnstile Site Key</label>
                                <input type="text" id="turnstile_site_key" name="turnstile_site_key" value="<?= htmlspecialchars($settings['turnstile_site_key'] ?? '') ?>" class="mt-1 block w-full px-4 py-3 border-2 border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm bg-white" placeholder="0x4AAAAAAABkMYinukNB1Axe">
                                <p class="mt-1 text-sm text-gray-500">Your Cloudflare Turnstile site key (public)</p>
                            </div>
                            <div>
                                <label for="turnstile_secret_key" class="block text-sm font-medium text-gray-700">Turnstile Secret Key</label>
                                <input type="password" id="turnstile_secret_key" name="turnstile_secret_key" value="<?= htmlspecialchars($settings['turnstile_secret_key'] ?? '') ?>" class="mt-1 block w-full px-4 py-3 border-2 border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm bg-white" placeholder="0x4AAAAAAABkMYinukNB1AxK">
                                <p class="mt-1 text-sm text-gray-500">Your Cloudflare Turnstile secret key (private)</p>
                            </div>
                        </div>
                        <div class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-md">
                            <p class="text-sm text-blue-800">
                                <strong>Setup Instructions:</strong><br>
                                1. Go to <a href="https://dash.cloudflare.com/" target="_blank" class="text-blue-600 hover:text-blue-500 underline">Cloudflare Dashboard</a><br>
                                2. Navigate to Turnstile and create a new site<br>
                                3. Copy the Site Key and Secret Key to the fields above<br>
                                4. Add your domain to the allowed domains list
                            </p>
                        </div>
                    </div>

                    <div id="recaptcha-fields" style="<?= ($settings['captcha_provider'] ?? 'off') === 'recaptcha' ? '' : 'display: none;' ?>">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="recaptcha_site_key" class="block text-sm font-medium text-gray-700">reCAPTCHA Site Key</label>
                                <input type="text" id="recaptcha_site_key" name="recaptcha_site_key" value="<?= htmlspecialchars($settings['recaptcha_site_key'] ?? '') ?>" class="mt-1 block w-full px-4 py-3 border-2 border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm bg-white" placeholder="6LdRcP0oAAAAAH...">
                                <p class="mt-1 text-sm text-gray-500">Your Google reCAPTCHA site key (public)</p>
                            </div>
                            <div>
                                <label for="recaptcha_secret_key" class="block text-sm font-medium text-gray-700">reCAPTCHA Secret Key</label>
                                <input type="password" id="recaptcha_secret_key" name="recaptcha_secret_key" value="<?= htmlspecialchars($settings['recaptcha_secret_key'] ?? '') ?>" class="mt-1 block w-full px-4 py-3 border-2 border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm bg-white" placeholder="6LdRcP0oAAAAAG...">
                                <p class="mt-1 text-sm text-gray-500">Your Google reCAPTCHA secret key (private)</p>
                            </div>
                        </div>
                        <div class="mt-2 p-3 bg-green-50 border border-green-200 rounded-md">
                            <p class="text-sm text-green-800">
                                <strong>Setup Instructions:</strong><br>
                                1. Go to <a href="https://www.google.com/recaptcha/admin" target="_blank" class="text-green-600 hover:text-green-500 underline">Google reCAPTCHA Admin Console</a><br>
                                2. Register a new site with reCAPTCHA v2 "I'm not a robot" Checkbox<br>
                                3. Add your domain to the allowed domains list<br>
                                4. Copy the Site Key and Secret Key to the fields above
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Save Security Settings
                    </button>
                </div>
            </form>
        </div>

        <!-- Data Format Settings -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Data Format Settings</h2>
                <p class="mt-1 text-sm text-gray-600">Configure how data is formatted and validated throughout the system</p>
            </div>
            <form method="POST" action="<?= BASE_URL ?>/settings" class="px-6 py-4">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                <input type="hidden" name="section" value="format">
                
                <div class="space-y-6">
                    <div>
                        <label for="phone_number_format" class="block text-sm font-medium text-gray-700">Phone Number Format</label>
                        <select id="phone_number_format" name="phone_number_format" class="mt-1 block w-64 px-4 py-3 border-2 border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm bg-white" onchange="showPhoneFormatExample()">
                            <option value="default" <?= ($settings['phone_number_format'] ?? 'default') === 'default' ? 'selected' : '' ?>>Default (minimum 7 digits)</option>
                            <option value="usa_format_a" <?= ($settings['phone_number_format'] ?? 'default') === 'usa_format_a' ? 'selected' : '' ?>>USA Format A: (555) 555-5555</option>
                            <option value="usa_format_b" <?= ($settings['phone_number_format'] ?? 'default') === 'usa_format_b' ? 'selected' : '' ?>>USA Format B: 555-555-5555</option>
                        </select>
                        <p class="mt-1 text-sm text-gray-500">Controls how phone numbers are formatted and validated across the system</p>
                        
                        <div id="phone_format_example" class="mt-3 p-3 bg-gray-50 border border-gray-200 rounded-md">
                            <p class="text-sm text-gray-700">
                                <strong>Example:</strong> <span id="example_text"></span><br>
                                <strong>Pattern:</strong> <span id="pattern_text"></span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Save Format Settings
                    </button>
                </div>
            </form>
        </div>

        <!-- System Information -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">System Information</h2>
            </div>
            <div class="px-6 py-4">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">LibreWO Version</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <?php 
                            if (file_exists(ROOT_PATH . '/version.php')) {
                                require ROOT_PATH . '/version.php';
                                echo $version . ' (' . $channel . ')';
                            } else {
                                echo 'Unknown';
                            }
                            ?>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">PHP Version</dt>
                        <dd class="mt-1 text-sm text-gray-900"><?= PHP_VERSION ?></dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>

<script>
function toggleCaptchaFields() {
    const provider = document.getElementById('captcha_provider').value;
    const turnstileFields = document.getElementById('turnstile-fields');
    const recaptchaFields = document.getElementById('recaptcha-fields');
    
    if (provider === 'turnstile') {
        turnstileFields.style.display = 'block';
        recaptchaFields.style.display = 'none';
    } else if (provider === 'recaptcha') {
        turnstileFields.style.display = 'none';
        recaptchaFields.style.display = 'block';
    } else {
        turnstileFields.style.display = 'none';
        recaptchaFields.style.display = 'none';
    }
}

function showPhoneFormatExample() {
    const format = document.getElementById('phone_number_format').value;
    const exampleText = document.getElementById('example_text');
    const patternText = document.getElementById('pattern_text');
    
    switch(format) {
        case 'default':
            exampleText.textContent = '1234567890 or 555-1234 or any format with 7+ digits';
            patternText.textContent = 'Minimum 7 digits, any format allowed';
            break;
        case 'usa_format_a':
            exampleText.textContent = '(555) 555-5555';
            patternText.textContent = '(XXX) XXX-XXXX format required';
            break;
        case 'usa_format_b':
            exampleText.textContent = '555-555-5555';
            patternText.textContent = 'XXX-XXX-XXXX format required';
            break;
    }
}

// Initialize phone format example on page load
document.addEventListener('DOMContentLoaded', function() {
    showPhoneFormatExample();
});
</script>

<?php 
$content = ob_get_clean();
include ROOT_PATH . '/views/layout.php';
?>
