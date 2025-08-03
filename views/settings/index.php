<?php 
$title = 'Settings - ' . ($companyName ?? 'LibreWO');
ob_start(); 
?>

<div class="max-w-4xl mx-auto py-8">
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
                <p class="mt-1 text-sm text-gray-600">This information appears on work orders and invoices</p>
            </div>
            <form method="POST" action="<?= BASE_URL ?>/settings" class="px-6 py-4">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                <input type="hidden" name="section" value="company">
                
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="company_name" class="block text-sm font-medium text-gray-700">Company Name</label>
                        <input type="text" id="company_name" name="company_name" value="<?= htmlspecialchars($settings['company_name'] ?? '') ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="company_phone" class="block text-sm font-medium text-gray-700">Phone</label>
                        <input type="tel" id="company_phone" name="company_phone" value="<?= htmlspecialchars($settings['company_phone'] ?? '') ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="company_email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" id="company_email" name="company_email" value="<?= htmlspecialchars($settings['company_email'] ?? '') ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="company_website" class="block text-sm font-medium text-gray-700">Website</label>
                        <input type="url" id="company_website" name="company_website" value="<?= htmlspecialchars($settings['company_website'] ?? '') ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="company_address" class="block text-sm font-medium text-gray-700">Address</label>
                        <textarea id="company_address" name="company_address" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"><?= htmlspecialchars($settings['company_address'] ?? '') ?></textarea>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="work_order_disclaimer" class="block text-sm font-medium text-gray-700">Work Order Disclaimer</label>
                        <textarea id="work_order_disclaimer" name="work_order_disclaimer" rows="4" placeholder="Enter any legal disclaimers or terms that should appear on work orders..." class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"><?= htmlspecialchars($settings['work_order_disclaimer'] ?? '') ?></textarea>
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

        <!-- Email Settings -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Email Settings</h2>
                <p class="mt-1 text-sm text-gray-600">Configure SMTP settings for sending emails</p>
            </div>
            <div class="px-6 py-4">
                <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-600">
                                Email settings are configured in the config.php file. Please contact your system administrator to modify SMTP settings.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">SMTP Host</dt>
                            <dd class="mt-1 text-sm text-gray-900"><?= defined('SMTP_HOST') ? SMTP_HOST : 'Not configured' ?></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">SMTP Port</dt>
                            <dd class="mt-1 text-sm text-gray-900"><?= defined('SMTP_PORT') ? SMTP_PORT : 'Not configured' ?></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">From Email</dt>
                            <dd class="mt-1 text-sm text-gray-900"><?= defined('FROM_EMAIL') ? FROM_EMAIL : 'Not configured' ?></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">From Name</dt>
                            <dd class="mt-1 text-sm text-gray-900"><?= defined('FROM_NAME') ? FROM_NAME : 'Not configured' ?></dd>
                        </div>
                    </dl>
                </div>
            </div>
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
                                Require Two-Factor Authentication for all users
                            </label>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">When enabled, all users must set up 2FA to access their accounts</p>
                    </div>

                    <div>
                        <label for="session_timeout" class="block text-sm font-medium text-gray-700">Session Timeout (minutes)</label>
                        <input type="number" id="session_timeout" name="session_timeout" value="<?= htmlspecialchars($settings['session_timeout'] ?? '60') ?>" min="5" max="1440" class="mt-1 block w-32 border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                        <p class="mt-1 text-sm text-gray-500">Users will be automatically logged out after this period of inactivity</p>
                    </div>

                    <div>
                        <label for="max_login_attempts" class="block text-sm font-medium text-gray-700">Maximum Login Attempts</label>
                        <input type="number" id="max_login_attempts" name="max_login_attempts" value="<?= htmlspecialchars($settings['max_login_attempts'] ?? '5') ?>" min="3" max="10" class="mt-1 block w-32 border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                        <p class="mt-1 text-sm text-gray-500">Number of failed login attempts before account lockout</p>
                    </div>

                    <div>
                        <label for="captcha_provider" class="block text-sm font-medium text-gray-700">CAPTCHA Protection</label>
                        <select id="captcha_provider" name="captcha_provider" class="mt-1 block w-48 border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm" onchange="toggleCaptchaFields()">
                            <option value="off" <?= ($settings['captcha_provider'] ?? 'off') === 'off' ? 'selected' : '' ?>>Disabled</option>
                            <option value="turnstile" <?= ($settings['captcha_provider'] ?? 'off') === 'turnstile' ? 'selected' : '' ?>>Cloudflare Turnstile</option>
                        </select>
                        <p class="mt-1 text-sm text-gray-500">Enable CAPTCHA protection for login and password reset forms</p>
                    </div>

                    <div id="turnstile-fields" style="<?= ($settings['captcha_provider'] ?? 'off') === 'turnstile' ? '' : 'display: none;' ?>">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="turnstile_site_key" class="block text-sm font-medium text-gray-700">Turnstile Site Key</label>
                                <input type="text" id="turnstile_site_key" name="turnstile_site_key" value="<?= htmlspecialchars($settings['turnstile_site_key'] ?? '') ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm" placeholder="0x4AAAAAAABkMYinukNB1Axe">
                                <p class="mt-1 text-sm text-gray-500">Your Cloudflare Turnstile site key (public)</p>
                            </div>
                            <div>
                                <label for="turnstile_secret_key" class="block text-sm font-medium text-gray-700">Turnstile Secret Key</label>
                                <input type="password" id="turnstile_secret_key" name="turnstile_secret_key" value="<?= htmlspecialchars($settings['turnstile_secret_key'] ?? '') ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm" placeholder="0x4AAAAAAABkMYinukNB1AxK">
                                <p class="mt-1 text-sm text-gray-500">Your Cloudflare Turnstile secret key (private)</p>
                            </div>
                        </div>
                        <div class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-md">
                            <p class="text-sm text-blue-800">
                                <strong>Setup Instructions:</strong><br>
                                1. Go to <a href="https://dash.cloudflare.com/profile/api-tokens" target="_blank" class="text-blue-600 hover:text-blue-500 underline">Cloudflare Dashboard</a><br>
                                2. Navigate to Turnstile and create a new site<br>
                                3. Copy the Site Key and Secret Key to the fields above<br>
                                4. Add your domain to the allowed domains list
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
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Database Type</dt>
                        <dd class="mt-1 text-sm text-gray-900">MySQL</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Installation Date</dt>
                        <dd class="mt-1 text-sm text-gray-900"><?= $settings['installation_date'] ?? 'Unknown' ?></dd>
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
    
    if (provider === 'turnstile') {
        turnstileFields.style.display = 'block';
    } else {
        turnstileFields.style.display = 'none';
    }
}
</script>

<?php 
$content = ob_get_clean();
include ROOT_PATH . '/views/layout.php';
?>
