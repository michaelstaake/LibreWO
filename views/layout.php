<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? ($companyName ?? 'LibreWO') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
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
                    <div class="flex items-center space-x-8">
                        <a href="<?= BASE_URL ?>/" class="text-xl font-bold text-gray-900"><?= htmlspecialchars($companyName ?? 'LibreWO') ?></a>
                        <div class="hidden md:flex space-x-6">
                            <a href="<?= BASE_URL ?>/" class="text-gray-600 hover:text-gray-900">Home</a>
                            <a href="<?= BASE_URL ?>/work-orders" class="text-gray-600 hover:text-gray-900">Work Orders</a>
                            <?php if ($_SESSION['user_group'] === 'Admin'): ?>
                                <a href="<?= BASE_URL ?>/customers" class="text-gray-600 hover:text-gray-900">Customers</a>
                                <a href="<?= BASE_URL ?>/users" class="text-gray-600 hover:text-gray-900">Users</a>
                                <a href="<?= BASE_URL ?>/settings" class="text-gray-600 hover:text-gray-900">Settings</a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-600">Welcome, <?= htmlspecialchars($_SESSION['username']) ?></span>
                        <a href="<?= BASE_URL ?>/logout" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Log Out</a>
                    </div>
                </div>
                <!-- Mobile menu -->
                <div class="md:hidden pb-4">
                    <a href="<?= BASE_URL ?>/" class="block py-2 text-gray-600 hover:text-gray-900">Home</a>
                    <a href="<?= BASE_URL ?>/work-orders" class="block py-2 text-gray-600 hover:text-gray-900">Work Orders</a>
                    <?php if ($_SESSION['user_group'] === 'Admin'): ?>
                        <a href="<?= BASE_URL ?>/customers" class="block py-2 text-gray-600 hover:text-gray-900">Customers</a>
                        <a href="<?= BASE_URL ?>/users" class="block py-2 text-gray-600 hover:text-gray-900">Users</a>
                        <a href="<?= BASE_URL ?>/settings" class="block py-2 text-gray-600 hover:text-gray-900">Settings</a>
                    <?php endif; ?>
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
                <div class="text-center text-gray-600">
                    <p>Powered by <a href="https://librewo.com" target="_blank" class="text-primary-600 hover:text-primary-700">LibreWO</a></p>
                </div>
            </div>
        </footer>
    <?php endif; ?>

    <script>
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
    </script>
</body>
</html>
