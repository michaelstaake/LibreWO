<?php 
$title = 'Install LibreWO';
$hideNavigation = true;
ob_start(); 
?>

<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Install LibreWO
            </h2>
            <p class="mt-2 text-center text-gray-600">
                Set up your work order management system
            </p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                <h4 class="font-bold">Installation Complete!</h4>
                <p>LibreWO has been successfully installed. You can now log in with the admin account you created.</p>
                <div class="mt-4">
                    <a href="<?= BASE_URL ?>/login" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        Go to Login
                    </a>
                </div>
            </div>
        <?php else: ?>
            <form class="mt-8 space-y-6" method="POST">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                
                <div class="space-y-4">
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700">
                            Admin Username
                        </label>
                        <input id="username" 
                               name="username" 
                               type="text" 
                               required 
                               class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                               placeholder="Enter admin username">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">
                            Admin Email
                        </label>
                        <input id="email" 
                               name="email" 
                               type="email" 
                               required 
                               class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                               placeholder="Enter admin email">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">
                            Password
                        </label>
                        <input id="password" 
                               name="password" 
                               type="password" 
                               required 
                               minlength="8"
                               class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                               placeholder="Minimum 8 characters">
                    </div>

                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700">
                            Confirm Password
                        </label>
                        <input id="confirm_password" 
                               name="confirm_password" 
                               type="password" 
                               required 
                               minlength="8"
                               class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                               placeholder="Confirm password">
                    </div>
                </div>

                <div>
                    <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Install LibreWO
                    </button>
                </div>
            </form>
        <?php endif; ?>

        <div class="text-center text-sm text-gray-600">
            <p>LibreWO v<?php require ROOT_PATH . '/version.php'; echo $version; ?> (<?= $channel ?>)</p>
        </div>
    </div>
</div>

<script>
    // Validate password confirmation
    document.getElementById('confirm_password').addEventListener('input', function() {
        const password = document.getElementById('password').value;
        const confirmPassword = this.value;
        
        if (password !== confirmPassword) {
            this.setCustomValidity('Passwords do not match');
        } else {
            this.setCustomValidity('');
        }
    });
</script>

<?php 
$content = ob_get_clean();
include ROOT_PATH . '/views/layout.php';
?>
