<?php 
$title = 'Home - ' . ($companyName ?? 'LibreWO');
ob_start(); 
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="py-6">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
        <p class="mt-2 text-gray-600">Welcome back, <?= htmlspecialchars(!empty($user['name']) ? $user['name'] : $user['username']) ?>!</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-<?= $_SESSION['user_group'] === 'Limited' ? '3' : '4' ?> gap-6 mb-8">
        <?php if ($_SESSION['user_group'] !== 'Limited'): ?>
        <!-- Create Work Order -->
        <a href="<?= BASE_URL ?>/work-orders/create" class="bg-primary-600 hover:bg-primary-700 text-white p-6 rounded-lg shadow transition-colors">
            <div class="flex items-center">
                <div class="flex-1">
                    <p class="text-primary-100">Create</p>
                    <p class="text-2xl font-bold">Work Order</p>
                </div>
                <div class="text-primary-200">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </a>
        <?php endif; ?>

        <!-- Total Work Orders -->
        <a href="<?= BASE_URL ?>/work-orders" class="bg-white hover:bg-gray-50 p-6 rounded-lg shadow transition-colors block">
            <div class="flex items-center">
                <div class="flex-1">
                    <p class="text-gray-500">Total</p>
                    <p class="text-2xl font-bold text-gray-900"><?= $totalWorkOrders ?></p>
                    <p class="text-sm text-gray-500">Work Orders</p>
                </div>
                <div class="text-gray-400">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2v1a1 1 0 102 0V3h2v1a1 1 0 102 0V3a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </a>

        <!-- Priority Work Orders -->
        <a href="<?= BASE_URL ?>/work-orders?status=Priority" class="bg-white hover:bg-gray-50 p-6 rounded-lg shadow transition-colors block">
            <div class="flex items-center">
                <div class="flex-1">
                    <p class="text-gray-500">Priority</p>
                    <p class="text-2xl font-bold text-gray-900"><?= $priorityCount ?></p>
                    <p class="text-sm text-gray-500">Work Orders</p>
                </div>
                <div class="text-gray-400">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </a>

        <!-- Assigned to Me -->
        <a href="<?= BASE_URL ?>/work-orders?assigned_to=<?= $user['id'] ?>" class="bg-white hover:bg-gray-50 p-6 rounded-lg shadow transition-colors block">
            <div class="flex items-center">
                <div class="flex-1">
                    <p class="text-gray-500">Assigned</p>
                    <p class="text-2xl font-bold text-gray-900"><?= $assignedCount ?></p>
                    <p class="text-sm text-gray-500">to Me</p>
                </div>
                <div class="text-gray-400">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </a>
    </div>

    <!-- Status Breakdown -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Work Orders by Status</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <?php foreach ($statusCounts as $status => $count): ?>
                    <a href="<?= BASE_URL ?>/work-orders?status=<?= urlencode($status) ?>" 
                       class="block p-4 border rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="text-center">
                            <p class="text-2xl font-bold <?= $status === 'Open' ? 'text-orange-600' : 
                                ($status === 'In Progress' ? 'text-yellow-600' : 
                                ($status === 'Awaiting Parts' ? 'text-purple-600' : 
                                ($status === 'Closed' ? 'text-green-600' : 'text-gray-600'))) ?>">
                                <?= $count ?>
                            </p>
                            <p class="text-sm text-gray-600 mt-1"><?= $status ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
include ROOT_PATH . '/views/layout.php';
?>
