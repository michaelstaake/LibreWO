<?php 
$title = $user['username'] . ' - Users - ' . ($companyName ?? 'LibreWO');
ob_start(); 
?>

<div class="max-w-6xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($user['username']) ?></h1>
                <p class="mt-1 text-sm text-gray-600"><?= htmlspecialchars($user['email']) ?></p>
            </div>
            <?php if ($_SESSION['user_group'] === 'Admin'): ?>
            <div>
                <a href="<?= BASE_URL ?>/users" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z" />
                    </svg>
                    Back to Users
                </a>
            </div>
            <?php endif; ?>
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
        <!-- User Details -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">User Information</h2>
                </div>
                
                <?php if ($_SESSION['user_group'] === 'Admin'): ?>
                <!-- Admin View - Full Edit Form -->
                <form method="POST" class="px-6 py-4">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700">Username *</label>
                            <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" readonly class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100 cursor-not-allowed sm:text-sm">
                            <p class="mt-1 text-sm text-gray-500">Username cannot be changed</p>
                        </div>

                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                            <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                            <p class="mt-1 text-xs text-gray-500">If blank, username will be displayed</p>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                        </div>

                        <div>
                            <label for="user_group" class="block text-sm font-medium text-gray-700">Role *</label>
                            <select id="user_group" name="user_group" required <?= $user['id'] == $_SESSION['user_id'] ? 'disabled' : '' ?> class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm <?= $user['id'] == $_SESSION['user_id'] ? 'bg-gray-100' : '' ?>">
                                <option value="Admin" <?= $user['user_group'] === 'Admin' ? 'selected' : '' ?>>Admin</option>
                                <option value="Technician" <?= $user['user_group'] === 'Technician' ? 'selected' : '' ?>>Technician</option>
                                <option value="Limited" <?= $user['user_group'] === 'Limited' ? 'selected' : '' ?>>Limited</option>
                            </select>
                            <?php if ($user['id'] == $_SESSION['user_id']): ?>
                                <p class="mt-1 text-sm text-gray-500">You cannot change your own role</p>
                                <input type="hidden" name="user_group" value="<?= htmlspecialchars($user['user_group']) ?>">
                            <?php endif; ?>
                        </div>

                        <div>
                            <label for="is_active" class="block text-sm font-medium text-gray-700">Status</label>
                            <select id="is_active" name="is_active" <?= $user['id'] == $_SESSION['user_id'] ? 'disabled' : '' ?> class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm <?= $user['id'] == $_SESSION['user_id'] ? 'bg-gray-100' : '' ?>">
                                <option value="1" <?= ($user['is_active'] ?? 1) ? 'selected' : '' ?>>Active</option>
                                <option value="0" <?= !($user['is_active'] ?? 1) ? 'selected' : '' ?>>Inactive</option>
                            </select>
                            <?php if ($user['id'] == $_SESSION['user_id']): ?>
                                <p class="mt-1 text-sm text-gray-500">You cannot deactivate your own account</p>
                                <input type="hidden" name="is_active" value="<?= $user['is_active'] ?? 1 ?>">
                            <?php endif; ?>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Password</label>
                            <button type="button" onclick="openPasswordModal()" class="mt-1 inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m0 0a2 2 0 012 2v6a2 2 0 01-2 2H9a2 2 0 01-2-2V9a2 2 0 012-2m0 0V7a2 2 0 012-2h4zm-6 4h6m-6 4h6" />
                                </svg>
                                Change Password
                            </button>
                            <p class="mt-1 text-sm text-gray-500">Click to change the user's password</p>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            Update User
                        </button>
                    </div>
                </form>
                <?php else: ?>
                <!-- Non-Admin View - Read Only -->
                <div class="px-6 py-4">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Username</dt>
                            <dd class="mt-1 text-sm text-gray-900"><?= htmlspecialchars($user['username']) ?></dd>
                        </div>
                        <?php if (!empty($user['name'])): ?>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                            <dd class="mt-1 text-sm text-gray-900"><?= htmlspecialchars($user['name']) ?></dd>
                        </div>
                        <?php endif; ?>
                    </dl>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Summary Stats -->
        <div>
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Summary</h2>
                </div>
                <div class="px-6 py-4">
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Role</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    <?php 
                                    switch($user['user_group']) {
                                        case 'Admin': echo 'bg-red-100 text-red-800'; break;
                                        case 'Technician': echo 'bg-blue-100 text-blue-800'; break;
                                        case 'Limited': echo 'bg-gray-100 text-gray-800'; break;
                                        default: echo 'bg-gray-100 text-gray-800';
                                    }
                                    ?>">
                                    <?= htmlspecialchars($user['user_group']) ?>
                                </span>
                            </dd>
                        </div>
                        
                        <?php if ($_SESSION['user_group'] === 'Admin'): ?>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    <?= ($user['is_active'] ?? 1) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                    <?= ($user['is_active'] ?? 1) ? 'Active' : 'Inactive' ?>
                                </span>
                            </dd>
                        </div>
                        <?php endif; ?>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Current Work Orders</dt>
                            <dd class="mt-1 text-2xl font-semibold text-gray-900"><?= count($workOrders) ?></dd>
                        </div>
                        
                        <?php if ($_SESSION['user_group'] === 'Admin'): ?>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Last Login</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <?= $user['last_login'] ? date('M j, Y g:i A', strtotime($user['last_login'])) : 'Never' ?>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Created</dt>
                            <dd class="mt-1 text-sm text-gray-900"><?= date('M j, Y', strtotime($user['created_at'])) ?></dd>
                        </div>
                        <?php endif; ?>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Work Orders -->
    <?php if (!empty($workOrders)): ?>
    <div class="mt-8">
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Current Work Orders</h2>
            </div>
            <div class="overflow-hidden">
                <table class="min-w-full divide-y divide-gray-300">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Device</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                            <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($workOrders as $workOrder): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <a href="<?= BASE_URL ?>/work-orders/view/<?= $workOrder['id'] ?>" class="text-primary-600 hover:text-primary-500">
                                    #<?= $workOrder['id'] ?>
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?= htmlspecialchars($workOrder['customer_name']) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900" title="<?= htmlspecialchars($workOrder['device_type']) ?>">
                                    <?= htmlspecialchars(strlen($workOrder['device_type']) > 30 ? substr($workOrder['device_type'], 0, 30) . '...' : $workOrder['device_type']) ?>
                                </div>
                                <div class="text-sm text-gray-500" title="<?= htmlspecialchars($workOrder['device_model']) ?>">
                                    <?= htmlspecialchars(strlen($workOrder['device_model']) > 30 ? substr($workOrder['device_model'], 0, 30) . '...' : $workOrder['device_model']) ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-2">
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
                                    <?php if ($workOrder['priority'] === 'Priority'): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Priority
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= date('M j, Y', strtotime($workOrder['created_at'])) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="<?= BASE_URL ?>/work-orders/view/<?= $workOrder['id'] ?>" class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                    View
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Password Change Modal -->
<div id="passwordModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closePasswordModal()"></div>
        
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="passwordForm" method="POST">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m0 0a2 2 0 012 2v6a2 2 0 01-2 2H9a2 2 0 01-2-2V9a2 2 0 012-2m0 0V7a2 2 0 012-2h4zm-6 4h6m-6 4h6" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Change Password for <?= htmlspecialchars($user['username']) ?>
                            </h3>
                            <div class="mt-4">
                                <label for="modal_new_password" class="block text-sm font-medium text-gray-700">New Password *</label>
                                <input type="password" id="modal_new_password" name="new_password" required minlength="8" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                                <p class="mt-1 text-sm text-gray-500">Password must be at least 8 characters long</p>
                            </div>
                            <div class="mt-4">
                                <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm Password *</label>
                                <input type="password" id="confirm_password" name="confirm_password" required minlength="8" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Change Password
                    </button>
                    <button type="button" onclick="closePasswordModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                <input type="hidden" name="password_change" value="1">
            </form>
        </div>
    </div>
</div>

<script>
function openPasswordModal() {
    document.getElementById('passwordModal').classList.remove('hidden');
    document.getElementById('modal_new_password').focus();
}

function closePasswordModal() {
    document.getElementById('passwordModal').classList.add('hidden');
    document.getElementById('passwordForm').reset();
}

// Validate password confirmation
document.getElementById('passwordForm').addEventListener('submit', function(e) {
    const password = document.getElementById('modal_new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (password !== confirmPassword) {
        e.preventDefault();
        alert('Passwords do not match!');
        return false;
    }
    
    if (password.length < 8) {
        e.preventDefault();
        alert('Password must be at least 8 characters long!');
        return false;
    }
});

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePasswordModal();
    }
});
</script>

<?php 
$content = ob_get_clean();
include ROOT_PATH . '/views/layout.php';
?>
