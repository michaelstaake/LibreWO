<?php 
$title = 'System Requirements - LibreWO';
$hideNavigation = true;
ob_start(); 
?>

<div class="min-h-screen flex items-cen        <div class="text-center text-sm text-gray-600 mt-4">
            <p>LibreWO v<?php require ROOT_PATH . '/version.php'; echo $version; ?> (<?= $channel ?>)</p>
        </div> justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                System Requirements Check
            </h2>
            <p class="mt-2 text-center text-gray-600">
                Your system must meet these requirements before installing LibreWO
            </p>
        </div>

        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Requirements Status</h3>
            </div>
            
            <div class="px-6 py-4">
                <div class="space-y-4">
                    <?php foreach ($requirements['checks'] as $check): ?>
                        <div class="flex items-center justify-between p-4 border rounded-lg <?= $check['status'] ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50' ?>">
                            <div class="flex-1">
                                <div class="flex items-center">
                                    <?php if ($check['status']): ?>
                                        <svg class="w-5 h-5 text-green-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="font-medium text-green-800"><?= $check['name'] ?></span>
                                    <?php else: ?>
                                        <svg class="w-5 h-5 text-red-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="font-medium text-red-800"><?= $check['name'] ?></span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="mt-1 text-sm <?= $check['status'] ? 'text-green-600' : 'text-red-600' ?>">
                                    <div>Required: <?= htmlspecialchars($check['required']) ?></div>
                                    <div>Current: <?= htmlspecialchars($check['current']) ?></div>
                                </div>
                            </div>
                            
                            <div class="ml-4">
                                <?php if ($check['status']): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        PASS
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        FAIL
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <?php if (!$requirements['meets_requirements']): ?>
            <!-- Failed Requirements Notice -->
            <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">
                            Requirements Not Met
                        </h3>
                        <div class="mt-2 text-sm text-red-700">
                            <p>Your system does not meet all the requirements to install LibreWO. Please address the failed requirements above and refresh this page.</p>
                        </div>
                        <div class="mt-4">
                            <div class="text-sm">
                                <button onclick="window.location.reload()" class="font-medium text-red-800 hover:text-red-600">
                                    Refresh Requirements Check →
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Requirements Help -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                <h3 class="text-lg font-medium text-blue-900 mb-4">Getting Help</h3>
                <div class="space-y-3 text-sm text-blue-800">
                    <div>
                        <strong>PHP 8.4+ Required:</strong> LibreWO requires PHP 8.4 or higher. Contact your hosting provider to upgrade PHP.
                    </div>
                    <div>
                        <strong>Web Server:</strong> Apache or LiteSpeed web servers are recommended for full compatibility with .htaccess URL rewriting.
                    </div>
                    <div>
                        <strong>PHP Extensions:</strong> Ensure PDO MySQL and OpenSSL extensions are enabled in your PHP configuration.
                    </div>
                    <div>
                        <strong>File Permissions:</strong> The logs/ directory must be writable. Set permissions to 755 or 777 if needed.
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- All Requirements Met -->
            <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-green-800">
                            All Requirements Met!
                        </h3>
                        <div class="mt-2 text-sm text-green-700">
                            <p>Your system meets all the requirements to install LibreWO. You can proceed with the installation.</p>
                        </div>
                        <div class="mt-4">
                            <a href="<?= BASE_URL ?>/install" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                Proceed to Installation
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="text-center text-sm text-gray-600">
            <p>LibreWO v<?php require '../../version.php'; echo $version; ?> (<?= $channel ?>)</p>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
include ROOT_PATH . '/views/layout.php';
?>
