<?php 
$title = 'Installation Error - LibreWO';
$hideNavigation = true;
ob_start(); 
?>

<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Installation Error
            </h2>
            <p class="mt-2 text-center text-gray-600">
                LibreWO cannot be installed due to configuration issues
            </p>
        </div>

        <div class="bg-red-50 border border-red-200 rounded-lg p-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">
                        Configuration Error
                    </h3>
                    <div class="mt-2 text-sm text-red-700">
                        <p><?= htmlspecialchars($error) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
            <h3 class="text-lg font-medium text-blue-900 mb-4">How to Fix This</h3>
            <div class="space-y-3 text-sm text-blue-800">
                <div>
                    <strong>Check Database Configuration:</strong> Verify your database settings in config.php are correct.
                </div>
                <div>
                    <strong>Database Server:</strong> Ensure your MySQL/MariaDB server is running and accessible.
                </div>
                <div>
                    <strong>Database Permissions:</strong> Make sure the database user has CREATE, ALTER, INSERT, UPDATE, DELETE, and SELECT permissions.
                </div>
                <div>
                    <strong>Database Exists:</strong> Verify that the database specified in DB_NAME exists.
                </div>
            </div>
        </div>

        <div class="text-center">
            <button onclick="window.location.reload()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                Retry Installation
            </button>
        </div>

        <div class="text-center text-sm text-gray-600">
            <p>LibreWO v<?php require ROOT_PATH . '/version.php'; echo $version; ?> (<?= $channel ?>)</p>
            <p class="mt-2">
                <a href="<?= BASE_URL ?>/db_test.php" class="text-primary-600 hover:text-primary-500">
                    Test Database Connection
                </a>
            </p>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
include ROOT_PATH . '/views/layout.php';
?>
