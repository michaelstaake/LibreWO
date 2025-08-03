<?php 
$title = 'Work Order #' . $workOrder['id'] . ' - ' . ($companyName ?? 'LibreWO');
ob_start(); 
?>

<div class="max-w-6xl mx-auto py-8">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center space-x-3 mb-2">
                    <h1 class="text-2xl font-bold text-gray-900">Work Order #<?= $workOrder['id'] ?></h1>
                    
                    <!-- Status Badge -->
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                        <?php 
                        switch($workOrder['status']) {
                            case 'Open': echo 'bg-orange-100 text-orange-800'; break;
                            case 'In Progress': echo 'bg-yellow-100 text-yellow-800'; break;
                            case 'Awaiting Parts': echo 'bg-purple-100 text-purple-800'; break;
                            case 'Closed': echo 'bg-green-100 text-green-800'; break;
                            case 'Picked Up': echo 'bg-gray-100 text-gray-800'; break;
                            default: echo 'bg-gray-100 text-gray-800';
                        }
                        ?>">
                        <?= htmlspecialchars($workOrder['status']) ?>
                    </span>
                    
                    <!-- Priority Badge (only show if Priority) -->
                    <?php if ($workOrder['priority'] === 'Priority'): ?>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        Priority
                    </span>
                    <?php endif; ?>
                </div>
                <p class="mt-1 text-sm text-gray-600">
                    Created <?= date('M j, Y g:i A', strtotime($workOrder['created_at'])) ?>
                </p>
            </div>
            <div class="flex space-x-3">
                <a href="<?= BASE_URL ?>/work-orders/print/<?= $workOrder['id'] ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Print
                </a>
            </div>
        </div>
    </div>

    <?php if (isset($error) && $error): ?>
        <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-600"><?= htmlspecialchars($error) ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($message) && $message): ?>
        <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-600"><?= htmlspecialchars($message) ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Work Order Details -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Work Order Details</h2>
                </div>
                <div class="px-6 py-4">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Device Type</dt>
                            <dd class="mt-1 text-sm text-gray-900"><?= htmlspecialchars($workOrder['computer'] ?? '') ?></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Device Model</dt>
                            <dd class="mt-1 text-sm text-gray-900"><?= htmlspecialchars($workOrder['model'] ?? '') ?></dd>
                        </div>
                        <?php if ($workOrder['serial_number']): ?>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Serial Number</dt>
                            <dd class="mt-1 text-sm text-gray-900"><?= htmlspecialchars($workOrder['serial_number']) ?></dd>
                        </div>
                        <?php endif; ?>
                        <?php if ($workOrder['technician_display_name']): ?>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Assigned To</dt>
                            <dd class="mt-1 text-sm text-gray-900"><?= htmlspecialchars($workOrder['technician_display_name']) ?></dd>
                        </div>
                        <?php endif; ?>
                        <?php if ($workOrder['username'] || $workOrder['password']): ?>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Login Information</dt>
                            <dd class="mt-1">
                                <button type="button" onclick="openLoginModal()" class="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                    <svg class="-ml-0.5 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    View Login Info
                                </button>
                            </dd>
                        </div>
                        <?php endif; ?>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Problem Description</dt>
                            <dd class="mt-1 text-sm text-gray-900"><?= nl2br(htmlspecialchars($workOrder['description'])) ?></dd>
                        </div>
                        <?php if ($workOrder['resolution']): ?>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Resolution</dt>
                            <dd class="mt-1 text-sm text-gray-900"><?= nl2br(htmlspecialchars($workOrder['resolution'])) ?></dd>
                        </div>
                        <?php endif; ?>
                        <?php if ($workOrder['notes']): ?>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Notes</dt>
                            <dd class="mt-1 text-sm text-gray-900"><?= nl2br(htmlspecialchars($workOrder['notes'])) ?></dd>
                        </div>
                        <?php endif; ?>
                    </dl>
                </div>
            </div>

            <!-- Update Form (if not Limited user) -->
            <?php if ($_SESSION['user_group'] !== 'Limited'): ?>
            <div class="mt-6 bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Update Work Order</h2>
                </div>
                <form method="POST" class="px-6 py-4">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select id="status" name="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                                <option value="Open" <?= $workOrder['status'] === 'Open' ? 'selected' : '' ?>>Open</option>
                                <option value="In Progress" <?= $workOrder['status'] === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                                <option value="Awaiting Parts" <?= $workOrder['status'] === 'Awaiting Parts' ? 'selected' : '' ?>>Awaiting Parts</option>
                                <option value="Closed" <?= $workOrder['status'] === 'Closed' ? 'selected' : '' ?>>Closed</option>
                                <option value="Picked Up" <?= $workOrder['status'] === 'Picked Up' ? 'selected' : '' ?>>Picked Up</option>
                            </select>
                        </div>

                        <div>
                            <label for="priority" class="block text-sm font-medium text-gray-700">Priority</label>
                            <select id="priority" name="priority" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                                <option value="Standard" <?= $workOrder['priority'] === 'Standard' ? 'selected' : '' ?>>Standard</option>
                                <option value="Priority" <?= $workOrder['priority'] === 'Priority' ? 'selected' : '' ?>>Priority</option>
                            </select>
                        </div>

                        <div>
                            <label for="assigned_to" class="block text-sm font-medium text-gray-700">Assigned To</label>
                            <select id="assigned_to" name="assigned_to" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                                <option value="">Unassigned</option>
                                <?php foreach ($technicians as $tech): ?>
                                    <option value="<?= $tech['id'] ?>" <?= $workOrder['assigned_to'] == $tech['id'] ? 'selected' : '' ?>>
                                        <?= !empty($tech['name']) ? htmlspecialchars($tech['name']) : htmlspecialchars($tech['username']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="sm:col-span-2 lg:col-span-3">
                            <label for="description" class="block text-sm font-medium text-gray-700">Problem Description</label>
                            <textarea id="description" name="description" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"><?= htmlspecialchars($workOrder['description']) ?></textarea>
                        </div>

                        <div class="sm:col-span-2">
                            <label for="resolution" class="block text-sm font-medium text-gray-700">Resolution</label>
                            <textarea id="resolution" name="resolution" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"><?= htmlspecialchars($workOrder['resolution'] ?? '') ?></textarea>
                        </div>

                        <div class="sm:col-span-2">
                            <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                            <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"><?= htmlspecialchars($workOrder['notes'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            Update Work Order
                        </button>
                    </div>
                </form>
            </div>
            <?php endif; ?>
        </div>

        <!-- Customer Information -->
        <div>
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Customer Information</h2>
                </div>
                <div class="px-6 py-4">
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Name</dt>
                            <dd class="mt-1 text-sm text-gray-900"><?= htmlspecialchars($workOrder['customer_name']) ?></dd>
                        </div>
                        <?php if ($workOrder['customer_company']): ?>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Company</dt>
                            <dd class="mt-1 text-sm text-gray-900"><?= htmlspecialchars($workOrder['customer_company']) ?></dd>
                        </div>
                        <?php endif; ?>
                        <?php if ($workOrder['customer_email']): ?>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <a href="mailto:<?= htmlspecialchars($workOrder['customer_email']) ?>" class="text-primary-600 hover:text-primary-500">
                                    <?= htmlspecialchars($workOrder['customer_email']) ?>
                                </a>
                            </dd>
                        </div>
                        <?php endif; ?>
                        <?php if ($workOrder['customer_phone']): ?>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Phone</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <a href="tel:<?= htmlspecialchars($workOrder['customer_phone']) ?>" class="text-primary-600 hover:text-primary-500">
                                    <?= htmlspecialchars($workOrder['customer_phone']) ?>
                                </a>
                            </dd>
                        </div>
                        <?php endif; ?>
                        <div class="pt-4 border-t border-gray-200">
                            <a href="<?= BASE_URL ?>/customers/view/<?= $workOrder['customer_id'] ?>" class="text-sm text-primary-600 hover:text-primary-500">
                                View Customer Details →
                            </a>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Activity Log -->
            <?php if (!empty($workOrderLogs)): ?>
            <div class="mt-6 bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Activity Log</h2>
                </div>
                <div class="px-6 py-4">
                    <div class="space-y-3">
                        <?php foreach ($workOrderLogs as $log): ?>
                        <div class="flex items-start space-x-3 py-2 border-b border-gray-100 last:border-b-0">
                            <div class="flex-shrink-0">
                                <span class="h-6 w-6 rounded-full bg-gray-400 flex items-center justify-center">
                                    <svg class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-900"><?= htmlspecialchars($log['details'] ?? '') ?></p>
                                        <p class="text-xs text-gray-500 mt-1">by <?= htmlspecialchars($log['username'] ?? 'Unknown') ?></p>
                                    </div>
                                    <div class="flex-shrink-0 ml-4">
                                        <span class="text-xs text-gray-500"><?= date('M j, g:i A', strtotime($log['created_at'])) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Login Information Modal -->
<div id="loginModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Login Information</h3>
                <button type="button" onclick="closeLoginModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="space-y-4">
                <?php if ($workOrder['username']): ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Username</label>
                    <div class="mt-1 flex items-center space-x-2">
                        <input type="text" id="usernameField" value="<?= htmlspecialchars($workOrder['username']) ?>" readonly class="block w-full border-gray-300 rounded-md shadow-sm bg-gray-50 text-sm">
                        <button type="button" onclick="copyToClipboard('usernameField')" class="inline-flex items-center px-2 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </button>
                    </div>
                </div>
                <?php endif; ?>
                <?php if ($workOrder['password']): ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Password</label>
                    <div class="mt-1 flex items-center space-x-2">
                        <input type="password" id="passwordField" value="<?= htmlspecialchars($workOrder['password']) ?>" readonly class="block w-full border-gray-300 rounded-md shadow-sm bg-gray-50 text-sm">
                        <button type="button" onclick="togglePasswordVisibility()" class="inline-flex items-center px-2 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                            <svg id="eyeIcon" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                        <button type="button" onclick="copyToClipboard('passwordField')" class="inline-flex items-center px-2 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </button>
                    </div>
                </div>
                <?php endif; ?>
                <?php if (!$workOrder['username'] && !$workOrder['password']): ?>
                <div class="text-center text-gray-500 py-4">
                    No login information available for this work order.
                </div>
                <?php endif; ?>
            </div>
            <div class="mt-6 flex justify-end">
                <button type="button" onclick="closeLoginModal()" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function openLoginModal() {
    document.getElementById('loginModal').classList.remove('hidden');
}

function closeLoginModal() {
    document.getElementById('loginModal').classList.add('hidden');
}

function togglePasswordVisibility() {
    const passwordField = document.getElementById('passwordField');
    const eyeIcon = document.getElementById('eyeIcon');
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        eyeIcon.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
        `;
    } else {
        passwordField.type = 'password';
        eyeIcon.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
        `;
    }
}

function copyToClipboard(fieldId) {
    const field = document.getElementById(fieldId);
    field.select();
    field.setSelectionRange(0, 99999); // For mobile devices
    
    try {
        document.execCommand('copy');
        // Show temporary success message
        const button = event.target.closest('button');
        const originalHTML = button.innerHTML;
        button.innerHTML = '<svg class="h-4 w-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>';
        setTimeout(() => {
            button.innerHTML = originalHTML;
        }, 1000);
    } catch (err) {
        console.error('Failed to copy: ', err);
    }
}

// Close modal when clicking outside
document.getElementById('loginModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeLoginModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && !document.getElementById('loginModal').classList.contains('hidden')) {
        closeLoginModal();
    }
});
</script>

<?php 
$content = ob_get_clean();
include ROOT_PATH . '/views/layout.php';
?>
