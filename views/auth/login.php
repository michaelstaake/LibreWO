<?php 
$title = 'Login - ' . ($companyName ?? 'LibreWO');
$hideNavigation = true;
ob_start(); 
?>

<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Sign in to <?= htmlspecialchars($companyName ?? 'LibreWO') ?>
            </h2>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($message)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <?php if ($requires2FA): ?>
            <!-- Two-Factor Authentication Form -->
            <form class="mt-8 space-y-6" method="POST">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Two-Factor Authentication</h3>
                    <p class="text-gray-600 mb-4">Enter the verification code below:</p>
                    <input type="text" 
                           name="two_factor_code" 
                           maxlength="4" 
                           pattern="[0-9]{4}"
                           class="appearance-none rounded relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10"
                           placeholder="Enter 4-digit code"
                           required
                           autocomplete="off">
                </div>
                <div>
                    <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Verify Code
                    </button>
                </div>
                <div class="text-center">
                    <a href="<?= BASE_URL ?>/login" class="text-primary-600 hover:text-primary-500">
                        Back to login
                    </a>
                </div>
            </form>
        <?php else: ?>
            <!-- Regular Login Form -->
            <form class="mt-8 space-y-6" method="POST">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                <div class="rounded-md shadow-sm -space-y-px">
                    <div>
                        <label for="username" class="sr-only">Username</label>
                        <input id="username" 
                               name="username" 
                               type="text" 
                               required 
                               class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10"
                               placeholder="Username">
                    </div>
                    <div>
                        <label for="password" class="sr-only">Password</label>
                        <input id="password" 
                               name="password" 
                               type="password" 
                               required 
                               class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10"
                               placeholder="Password">
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="text-sm">
                        <a href="<?= BASE_URL ?>/forgot-password" class="font-medium text-primary-600 hover:text-primary-500">
                            Forgot your password?
                        </a>
                    </div>
                </div>

                <?php if (isset($captchaSettings) && $captchaSettings['captcha_provider'] === 'turnstile' && !empty($captchaSettings['turnstile_site_key'])): ?>
                <div class="flex justify-center">
                    <div class="cf-turnstile" data-sitekey="<?= htmlspecialchars($captchaSettings['turnstile_site_key']) ?>"></div>
                </div>
                <?php endif; ?>

                <div>
                    <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Sign in
                    </button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php if (isset($captchaSettings) && $captchaSettings['captcha_provider'] === 'turnstile' && !empty($captchaSettings['turnstile_site_key'])): ?>
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
<?php endif; ?>

<?php 
$content = ob_get_clean();
include ROOT_PATH . '/views/layout.php';
?>
