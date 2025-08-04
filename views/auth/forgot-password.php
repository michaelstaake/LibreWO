<?php 
$title = 'Forgot Password - ' . ($companyName ?? 'LibreWO');
$hideNavigation = true;
ob_start(); 
?>

<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Reset Password
            </h2>
            <p class="mt-2 text-center text-gray-600">
                Enter your email address and we'll send you a password reset link.
            </p>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($message)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                <?= htmlspecialchars($message) ?>
                <div class="mt-4">
                    <a href="<?= BASE_URL ?>/login" class="text-primary-600 hover:text-primary-500">
                        Return to Login
                    </a>
                </div>
            </div>
        <?php else: ?>
            <form class="mt-8 space-y-6" method="POST">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                <div>
                    <label for="email" class="sr-only">Email address</label>
                    <input id="email" 
                           name="email" 
                           type="email" 
                           required 
                           class="appearance-none rounded relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                           placeholder="Email address">
                </div>

                <?php if (isset($captchaSettings)): ?>
                    <?php if ($captchaSettings['captcha_provider'] === 'turnstile' && !empty($captchaSettings['turnstile_site_key'])): ?>
                    <div class="flex justify-center">
                        <div class="cf-turnstile" data-sitekey="<?= htmlspecialchars($captchaSettings['turnstile_site_key']) ?>"></div>
                    </div>
                    <?php elseif ($captchaSettings['captcha_provider'] === 'recaptcha' && !empty($captchaSettings['recaptcha_site_key'])): ?>
                    <div class="flex justify-center">
                        <div class="g-recaptcha" data-sitekey="<?= htmlspecialchars($captchaSettings['recaptcha_site_key']) ?>"></div>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>

                <div>
                    <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Send Reset Link
                    </button>
                </div>

                <div class="text-center">
                    <a href="<?= BASE_URL ?>/login" class="text-primary-600 hover:text-primary-500">
                        Back to Login
                    </a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php if (isset($captchaSettings)): ?>
    <?php if ($captchaSettings['captcha_provider'] === 'turnstile' && !empty($captchaSettings['turnstile_site_key'])): ?>
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    <?php elseif ($captchaSettings['captcha_provider'] === 'recaptcha' && !empty($captchaSettings['recaptcha_site_key'])): ?>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <?php endif; ?>
<?php endif; ?>

<?php 
$content = ob_get_clean();
include ROOT_PATH . '/views/layout.php';
?>
