<?php 
$title = 'Create Work Order - ' . ($companyName ?? 'LibreWO');
ob_start(); 
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="py-6">
        <h1 class="text-3xl font-bold text-gray-900">Create Work Order</h1>
        
        <!-- Progress Indicator -->
        <div class="mt-6">
            <div class="flex items-center">
                <?php for ($i = 1; $i <= 4; $i++): ?>
                    <div class="flex items-center <?= $i < 4 ? 'flex-1' : '' ?>">
                        <div class="flex items-center justify-center w-8 h-8 rounded-full 
                            <?= $step >= $i ? 'bg-primary-600 text-white' : 'bg-gray-300 text-gray-600' ?>">
                            <?= $i ?>
                        </div>
                        <div class="ml-2 text-sm font-medium 
                            <?= $step >= $i ? 'text-primary-600' : 'text-gray-500' ?>">
                            <?= ['', 'Customer', 'Computer', 'Description', 'Confirm'][$i] ?>
                        </div>
                        <?php if ($i < 4): ?>
                            <div class="flex-1 h-0.5 mx-4 
                                <?= $step > $i ? 'bg-primary-600' : 'bg-gray-300' ?>"></div>
                        <?php endif; ?>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>

    <?php if (!empty($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4">
            <?php if ($step === 1): ?>
                <!-- Step 1: Customer -->
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Step 1: Select or Add Customer</h2>
                
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    
                    <!-- Customer Search -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search Existing Customer</label>
                        <div class="relative">
                            <input type="text" 
                                   id="customer_search" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                                   placeholder="Search by name, company, email, or phone...">
                            <div id="customer_results" class="absolute z-10 w-full bg-white border border-gray-300 rounded-md mt-1 hidden max-h-60 overflow-auto"></div>
                        </div>
                        <input type="hidden" name="customer_id" id="selected_customer_id">
                    </div>

                    <div class="text-center text-gray-500 mb-6">OR</div>

                    <!-- New Customer Form -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium text-gray-900">Add New Customer</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="customer_name" class="block text-sm font-medium text-gray-700">
                                    Name *
                                </label>
                                <input type="text" 
                                       name="customer_name" 
                                       id="customer_name"
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                            </div>
                            
                            <div>
                                <label for="customer_phone" class="block text-sm font-medium text-gray-700">
                                    Phone *
                                </label>
                                <input type="tel" 
                                       name="customer_phone" 
                                       id="customer_phone"
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                            </div>
                            
                            <div>
                                <label for="customer_company" class="block text-sm font-medium text-gray-700">
                                    Company
                                </label>
                                <input type="text" 
                                       name="customer_company" 
                                       id="customer_company"
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                            </div>
                            
                            <div>
                                <label for="customer_email" class="block text-sm font-medium text-gray-700">
                                    Email
                                </label>
                                <input type="email" 
                                       name="customer_email" 
                                       id="customer_email"
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end mt-6">
                        <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded-md hover:bg-primary-700">
                            Next: Computer Details
                        </button>
                    </div>
                </form>

            <?php elseif ($step === 2): ?>
                <!-- Step 2: Computer -->
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Step 2: Computer Information</h2>
                
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    
                    <div class="space-y-4">
                        <div>
                            <label for="computer" class="block text-sm font-medium text-gray-700">
                                Computer *
                            </label>
                            <input type="text" 
                                   name="computer" 
                                   id="computer"
                                   required
                                   value="<?= htmlspecialchars($workOrderData['computer'] ?? '') ?>"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                                   placeholder="e.g., Dell Laptop, HP Desktop">
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="model" class="block text-sm font-medium text-gray-700">
                                    Model
                                </label>
                                <input type="text" 
                                       name="model" 
                                       id="model"
                                       value="<?= htmlspecialchars($workOrderData['model'] ?? '') ?>"
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                            </div>
                            
                            <div>
                                <label for="serial_number" class="block text-sm font-medium text-gray-700">
                                    Serial Number
                                </label>
                                <input type="text" 
                                       name="serial_number" 
                                       id="serial_number"
                                       value="<?= htmlspecialchars($workOrderData['serial_number'] ?? '') ?>"
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Accessories
                            </label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                <?php 
                                $accessories = ['Power Adapter', 'External Storage', 'Keyboard', 'Mouse', 'Monitor', 'Printer'];
                                $selectedAccessories = json_decode($workOrderData['accessories'] ?? '[]', true);
                                foreach ($accessories as $accessory): 
                                ?>
                                    <label class="flex items-center">
                                        <input type="checkbox" 
                                               name="accessories[]" 
                                               value="<?= $accessory ?>"
                                               <?= in_array($accessory, $selectedAccessories) ? 'checked' : '' ?>
                                               class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                        <span class="ml-2 text-sm text-gray-700"><?= $accessory ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                            <div class="mt-2">
                                <input type="text" 
                                       name="accessories[]" 
                                       placeholder="Other (specify)"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="username" class="block text-sm font-medium text-gray-700">
                                    Username
                                </label>
                                <input type="text" 
                                       name="username" 
                                       id="username"
                                       value="<?= htmlspecialchars($workOrderData['username'] ?? '') ?>"
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                            </div>
                            
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700">
                                    Password
                                </label>
                                <input type="text" 
                                       name="password" 
                                       id="password"
                                       value="<?= htmlspecialchars($workOrderData['password'] ?? '') ?>"
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between mt-6">
                        <a href="<?= BASE_URL ?>/work-orders/create?step=1" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-md hover:bg-gray-400">
                            Previous
                        </a>
                        <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded-md hover:bg-primary-700">
                            Next: Problem Description
                        </button>
                    </div>
                </form>

            <?php elseif ($step === 3): ?>
                <!-- Step 3: Description -->
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Step 3: Problem Description</h2>
                
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Describe the problem *
                        </label>
                        <textarea name="description" 
                                  id="description" 
                                  rows="6" 
                                  required
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                                  placeholder="Please describe the issue in detail..."><?= htmlspecialchars($workOrderData['description'] ?? '') ?></textarea>
                    </div>

                    <div class="flex justify-between mt-6">
                        <a href="<?= BASE_URL ?>/work-orders/create?step=2" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-md hover:bg-gray-400">
                            Previous
                        </a>
                        <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded-md hover:bg-primary-700">
                            Next: Confirm
                        </button>
                    </div>
                </form>

            <?php elseif ($step === 4): ?>
                <!-- Step 4: Confirm -->
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Step 4: Confirm Work Order</h2>
                
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    
                    <!-- Summary -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Work Order Summary</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h4 class="font-medium text-gray-700">Customer</h4>
                                <p class="text-gray-900"><?= htmlspecialchars($customer['name']) ?></p>
                                <?php if ($customer['company']): ?>
                                    <p class="text-gray-600"><?= htmlspecialchars($customer['company']) ?></p>
                                <?php endif; ?>
                                <p class="text-gray-600"><?= htmlspecialchars($customer['phone']) ?></p>
                                <?php if ($customer['email']): ?>
                                    <p class="text-gray-600"><?= htmlspecialchars($customer['email']) ?></p>
                                <?php endif; ?>
                            </div>
                            
                            <div>
                                <h4 class="font-medium text-gray-700">Computer</h4>
                                <p class="text-gray-900"><?= htmlspecialchars($workOrderData['computer']) ?></p>
                                <?php if ($workOrderData['model']): ?>
                                    <p class="text-gray-600">Model: <?= htmlspecialchars($workOrderData['model']) ?></p>
                                <?php endif; ?>
                                <?php if ($workOrderData['serial_number']): ?>
                                    <p class="text-gray-600">S/N: <?= htmlspecialchars($workOrderData['serial_number']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <h4 class="font-medium text-gray-700">Problem Description</h4>
                            <p class="text-gray-900 mt-1"><?= nl2br(htmlspecialchars($workOrderData['description'])) ?></p>
                        </div>
                    </div>

                    <!-- Assignment and Priority -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label for="assigned_to" class="block text-sm font-medium text-gray-700">
                                Assign to Technician
                            </label>
                            <select name="assigned_to" 
                                    id="assigned_to"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                                <option value="">Unassigned</option>
                                <?php foreach ($technicians as $technician): ?>
                                    <option value="<?= $technician['id'] ?>">
                                        <?= !empty($technician['name']) ? htmlspecialchars($technician['name']) : htmlspecialchars($technician['username']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label for="priority" class="block text-sm font-medium text-gray-700">
                                Priority
                            </label>
                            <select name="priority" 
                                    id="priority"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                                <option value="Standard">Standard</option>
                                <option value="Priority">Priority</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-between">
                        <a href="<?= BASE_URL ?>/work-orders/create?step=3" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-md hover:bg-gray-400">
                            Previous
                        </a>
                        <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-md hover:bg-green-700">
                            Create Work Order
                        </button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Customer search functionality
document.getElementById('customer_search')?.addEventListener('input', function() {
    const query = this.value.trim();
    const resultsDiv = document.getElementById('customer_results');
    
    if (query.length < 2) {
        resultsDiv.classList.add('hidden');
        return;
    }
    
    fetch(`<?= BASE_URL ?>/api/search-customers?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(customers => {
            if (customers.length === 0) {
                resultsDiv.innerHTML = '<div class="p-2 text-gray-500">No customers found</div>';
            } else {
                resultsDiv.innerHTML = customers.map(customer => 
                    `<div class="p-2 hover:bg-gray-100 cursor-pointer customer-option" data-customer-id="${customer.id}">
                        <div class="font-medium">${customer.name}</div>
                        ${customer.company ? `<div class="text-sm text-gray-600">${customer.company}</div>` : ''}
                        <div class="text-sm text-gray-600">${customer.phone}</div>
                    </div>`
                ).join('');
                
                // Add click handlers
                resultsDiv.querySelectorAll('.customer-option').forEach(option => {
                    option.addEventListener('click', function() {
                        const customerId = this.dataset.customerId;
                        document.getElementById('selected_customer_id').value = customerId;
                        document.getElementById('customer_search').value = this.querySelector('.font-medium').textContent;
                        resultsDiv.classList.add('hidden');
                        
                        // Clear new customer form
                        document.getElementById('customer_name').value = '';
                        document.getElementById('customer_phone').value = '';
                        document.getElementById('customer_company').value = '';
                        document.getElementById('customer_email').value = '';
                    });
                });
            }
            resultsDiv.classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error searching customers:', error);
        });
});

// Hide results when clicking outside
document.addEventListener('click', function(e) {
    const searchInput = document.getElementById('customer_search');
    const resultsDiv = document.getElementById('customer_results');
    
    if (searchInput && resultsDiv && !searchInput.contains(e.target) && !resultsDiv.contains(e.target)) {
        resultsDiv.classList.add('hidden');
    }
});
</script>

<?php 
$content = ob_get_clean();
include ROOT_PATH . '/views/layout.php';
?>
