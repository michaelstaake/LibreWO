<?php 
$title = 'Work Orders - ' . ($companyName ?? 'LibreWO');
ob_start(); 
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="py-6">
        <div class="flex justify-between items-center">
            <h1 class="text-3xl font-bold text-gray-900">Work Orders</h1>
            <?php if ($_SESSION['user_group'] !== 'Limited'): ?>
                <a href="<?= BASE_URL ?>/work-orders/create" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700">
                    Create Work Order
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex flex-wrap items-center gap-4">
                <!-- Status Filter -->
                <div class="flex space-x-2">
                    <?php 
                    $statuses = ['All', 'Priority', 'Open', 'In Progress', 'Awaiting Parts', 'Closed', 'Picked Up'];
                    foreach ($statuses as $filterStatus): 
                    ?>
                        <a href="<?= BASE_URL ?>/work-orders?status=<?= urlencode($filterStatus) ?><?= $search ? '&search=' . urlencode($search) : '' ?>"
                           class="px-3 py-1 rounded-full text-sm <?= $status === $filterStatus ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
                            <?= $filterStatus ?>
                        </a>
                    <?php endforeach; ?>
                </div>

                <!-- Search -->
                <div class="flex-1 max-w-md">
                    <form method="GET" class="flex">
                        <input type="hidden" name="status" value="<?= htmlspecialchars($status) ?>">
                        <input type="text" 
                               name="search" 
                               value="<?= htmlspecialchars($search) ?>"
                               placeholder="Search work orders..."
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                        <button type="submit" class="px-4 py-2 bg-gray-100 border border-l-0 border-gray-300 rounded-r-md hover:bg-gray-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Work Orders Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Work Order #
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Customer
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Date Opened
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Computer
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Technician
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($workOrders)): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                No work orders found.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($workOrders as $workOrder): ?>
                            <tr class="<?= $workOrder['priority'] === 'Priority' ? 'bg-red-50 hover:bg-red-100' : 'hover:bg-gray-50' ?>">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        #<?= $workOrder['id'] ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <a href="<?= BASE_URL ?>/customers/view/<?= $workOrder['customer_id'] ?>" class="text-primary-600 hover:text-primary-500">
                                            <?= htmlspecialchars($workOrder['customer_name']) ?>
                                        </a>
                                    </div>
                                    <?php if ($workOrder['customer_company']): ?>
                                        <div class="text-sm text-gray-500">
                                            <?= htmlspecialchars($workOrder['customer_company']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= date('M j, Y', strtotime($workOrder['created_at'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= htmlspecialchars($workOrder['computer']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php if ($workOrder['technician_display_name']): ?>
                                        <a href="<?= BASE_URL ?>/users/view/<?= $workOrder['assigned_to'] ?>" class="text-primary-600 hover:text-primary-500">
                                            <?= htmlspecialchars($workOrder['technician_display_name']) ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-gray-400">Unassigned</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        <?= $workOrder['status'] === 'Open' ? 'bg-orange-100 text-orange-800' :
                                            ($workOrder['status'] === 'In Progress' ? 'bg-yellow-100 text-yellow-800' :
                                            ($workOrder['status'] === 'Awaiting Parts' ? 'bg-purple-100 text-purple-800' :
                                            ($workOrder['status'] === 'Closed' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'))) ?>">
                                        <?= htmlspecialchars($workOrder['status']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="<?= BASE_URL ?>/work-orders/view/<?= $workOrder['id'] ?>" class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                        View
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="flex-1 flex justify-between sm:hidden">
                    <?php if ($currentPage > 1): ?>
                        <a href="<?= BASE_URL ?>/work-orders?page=<?= $currentPage - 1 ?><?= $status !== 'All' ? '&status=' . urlencode($status) : '' ?><?= $search ? '&search=' . urlencode($search) : '' ?>" 
                           class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Previous
                        </a>
                    <?php endif; ?>
                    <?php if ($currentPage < $totalPages): ?>
                        <a href="<?= BASE_URL ?>/work-orders?page=<?= $currentPage + 1 ?><?= $status !== 'All' ? '&status=' . urlencode($status) : '' ?><?= $search ? '&search=' . urlencode($search) : '' ?>" 
                           class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Next
                        </a>
                    <?php endif; ?>
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing page <span class="font-medium"><?= $currentPage ?></span> of <span class="font-medium"><?= $totalPages ?></span>
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <a href="<?= BASE_URL ?>/work-orders?page=<?= $i ?><?= $status !== 'All' ? '&status=' . urlencode($status) : '' ?><?= $search ? '&search=' . urlencode($search) : '' ?>" 
                                   class="relative inline-flex items-center px-4 py-2 border text-sm font-medium 
                                   <?= $i === $currentPage ? 'z-10 bg-primary-50 border-primary-500 text-primary-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50' ?>">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>
                        </nav>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php 
$content = ob_get_clean();
include ROOT_PATH . '/views/layout.php';
?>
