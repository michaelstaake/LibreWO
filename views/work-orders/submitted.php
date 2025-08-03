<?php 
$title = 'Work Order Submitted - ' . ($companyName ?? 'LibreWO');
ob_start(); 
?>

<div class="max-w-2xl mx-auto py-8">
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h1 class="text-xl font-semibold text-gray-900">Work Order Submitted Successfully</h1>
                    <p class="text-sm text-gray-600">Your work order has been created and assigned.</p>
                </div>
            </div>
        </div>

        <div class="px-6 py-4">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Work Order ID</dt>
                    <dd class="mt-1 text-sm text-gray-900"><?= htmlspecialchars($workOrder['id']) ?></dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd class="mt-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            <?= htmlspecialchars($workOrder['status']) ?>
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Customer</dt>
                    <dd class="mt-1 text-sm text-gray-900"><?= htmlspecialchars($workOrder['customer_name']) ?></dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Device</dt>
                    <dd class="mt-1 text-sm text-gray-900"><?= htmlspecialchars($workOrder['computer'] ?? 'N/A') ?><?= !empty($workOrder['model']) ? ' - ' . htmlspecialchars($workOrder['model']) : '' ?></dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Problem Description</dt>
                    <dd class="mt-1 text-sm text-gray-900"><?= nl2br(htmlspecialchars($workOrder['description'] ?? '')) ?></dd>
                </div>
                <?php if (!empty($workOrder['technician_name'])): ?>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Assigned To</dt>
                    <dd class="mt-1 text-sm text-gray-900"><?= htmlspecialchars($workOrder['technician_name']) ?></dd>
                </div>
                <?php endif; ?>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Created</dt>
                    <dd class="mt-1 text-sm text-gray-900"><?= date('M j, Y g:i A', strtotime($workOrder['created_at'])) ?></dd>
                </div>
            </dl>
        </div>

        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <div class="flex justify-between">
                <a href="<?= BASE_URL ?>/work-orders" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z" />
                    </svg>
                    Back to Work Orders
                </a>
                <div class="flex space-x-3">
                    <a href="<?= BASE_URL ?>/work-orders/view/<?= $workOrder['id'] ?>" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        View Details
                    </a>
                    <a href="<?= BASE_URL ?>/work-orders/print/<?= $workOrder['id'] ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Print
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
include ROOT_PATH . '/views/layout.php';
?>
