<?php 
$title = '403 Forbidden - ' . ($companyName ?? 'LibreWO');
$hideNavigation = true;
ob_start(); 
?>

<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 text-center">
        <div>
            <h1 class="text-6xl font-bold text-red-600">403</h1>
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                <?php if (isset($_GET['reason']) && $_GET['reason'] === 'account_deactivated'): ?>
                    Account Deactivated
                <?php else: ?>
                    Access Forbidden
                <?php endif; ?>
            </h2>
            <p class="mt-2 text-gray-600">
                <?php if (isset($_GET['reason']) && $_GET['reason'] === 'account_deactivated'): ?>
                    Your account has been deactivated by an administrator. Please contact your system administrator for assistance.
                <?php else: ?>
                    You don't have permission to access this resource.
                <?php endif; ?>
            </p>
        </div>
        <div class="mt-8">
            <?php if (isset($_GET['reason']) && $_GET['reason'] === 'account_deactivated'): ?>
                <a href="<?= BASE_URL ?>/login" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Go to Login
                </a>
            <?php elseif (isset($_SESSION['user_id'])): ?>
                <a href="<?= BASE_URL ?>/" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Go to Dashboard
                </a>
            <?php else: ?>
                <a href="<?= BASE_URL ?>/login" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Go to Login
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
include ROOT_PATH . '/views/layout.php';
?>
