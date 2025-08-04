<?php 
$title = 'Merge Customers - ' . ($companyName ?? 'LibreWO');
ob_start(); 
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="py-6">
        <h1 class="text-3xl font-bold text-gray-900">Merge Customers</h1>
        
        <!-- Progress Indicator -->
        <div class="mt-6">
            <div class="flex items-center">
                <?php for ($i = 1; $i <= 3; $i++): ?>
                    <div class="flex items-center <?= $i < 3 ? 'flex-1' : '' ?>">
                        <div class="flex items-center justify-center w-8 h-8 rounded-full 
                            <?= $step >= $i ? 'bg-primary-600 text-white' : 'bg-gray-300 text-gray-600' ?>">
                            <?= $i ?>
                        </div>
                        <div class="ml-2 text-sm font-medium 
                            <?= $step >= $i ? 'text-primary-600' : 'text-gray-500' ?>">
                            <?= ['', 'Source', 'Destination', 'Confirm'][$i] ?>
                        </div>
                        <?php if ($i < 3): ?>
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

    <?php if (!empty($message)): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4">
            <?php if ($step === 1): ?>
                <!-- Step 1: Source Customer -->
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Step 1: Select Source Customer</h2>
                <p class="text-gray-600 mb-6">Select the customer that will be merged (deleted). All work orders from this customer will be moved to the destination customer.</p>
                
                <form method="POST" action="<?= BASE_URL ?>/customers/merge?step=2">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <input type="hidden" name="next_step" value="2">
                    
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Source Customer *</label>
                        <?php if ($sourceId && $sourceCustomer): ?>
                            <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-blue-800"><?= htmlspecialchars($sourceCustomer['name']) ?></h3>
                                        <?php if ($sourceCustomer['company']): ?>
                                        <p class="text-sm text-blue-600"><?= htmlspecialchars($sourceCustomer['company']) ?></p>
                                        <?php endif; ?>
                                        <p class="text-sm text-blue-600">
                                            <?= htmlspecialchars($sourceCustomer['phone']) ?> • 
                                            <?= count($sourceCustomer['work_orders']) ?> work orders
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="source_customer" value="<?= $sourceId ?>">
                        <?php else: ?>
                            <div class="relative">
                                <input type="text" 
                                       id="source_customer_search" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                                       placeholder="Search by name, company, email, or phone...">
                                <div id="source_customer_results" class="absolute z-10 w-full bg-white border border-gray-300 rounded-md mt-1 hidden max-h-60 overflow-auto"></div>
                            </div>
                            <input type="hidden" name="source_customer" id="selected_source_customer_id" required>
                        <?php endif; ?>
                    </div>
                    
                    <div class="flex justify-between">
                        <a href="<?= BASE_URL ?>/customers" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-md hover:bg-gray-400">
                            Cancel
                        </a>
                        <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded-md hover:bg-primary-700">
                            Next: Select Destination
                        </button>
                    </div>
                </form>
                
            <?php elseif ($step === 2): ?>
                <!-- Step 2: Destination Customer -->
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Step 2: Select Destination Customer</h2>
                <p class="text-gray-600 mb-6">Select the customer that will receive all work orders from the source customer. This customer's information will be preserved.</p>
                
                <form method="POST" action="<?= BASE_URL ?>/customers/merge?step=3">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <input type="hidden" name="next_step" value="3">
                    <input type="hidden" name="source_customer" value="<?= $sourceId ?>">
                    
                    <?php if ($sourceCustomer): ?>
                    <div class="mb-6 bg-gray-50 border border-gray-200 rounded-md p-4">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Source Customer (will be deleted):</h3>
                        <div class="text-sm text-gray-900">
                            <strong><?= htmlspecialchars($sourceCustomer['name']) ?></strong>
                            <?= $sourceCustomer['company'] ? ' - ' . htmlspecialchars($sourceCustomer['company']) : '' ?>
                            <span class="text-gray-600">(<?= count($sourceCustomer['work_orders']) ?> work orders)</span>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Destination Customer *</label>
                        <div class="relative">
                            <input type="text" 
                                   id="destination_customer_search" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                                   placeholder="Search by name, company, email, or phone...">
                            <div id="destination_customer_results" class="absolute z-10 w-full bg-white border border-gray-300 rounded-md mt-1 hidden max-h-60 overflow-auto"></div>
                        </div>
                        <input type="hidden" name="destination_customer" id="selected_destination_customer_id" required>
                        
                        <?php if ($destinationId && $destinationCustomer): ?>
                        <div class="mt-3 bg-green-50 border border-green-200 rounded-md p-3">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-green-800">Selected: <?= htmlspecialchars($destinationCustomer['name']) ?></h3>
                                    <?php if ($destinationCustomer['company']): ?>
                                    <p class="text-sm text-green-600"><?= htmlspecialchars($destinationCustomer['company']) ?></p>
                                    <?php endif; ?>
                                    <p class="text-sm text-green-600">
                                        <?= htmlspecialchars($destinationCustomer['phone']) ?> • 
                                        <?= count($destinationCustomer['work_orders']) ?> work orders
                                    </p>
                                </div>
                            </div>
                        </div>
                        <script>
                        // Pre-fill destination customer if already selected
                        document.addEventListener('DOMContentLoaded', function() {
                            document.getElementById('selected_destination_customer_id').value = '<?= $destinationId ?>';
                            document.getElementById('destination_customer_search').value = '<?= htmlspecialchars($destinationCustomer['name']) ?>';
                        });
                        </script>
                        <?php endif; ?>
                    </div>
                    
                    <div class="flex justify-between">
                        <a href="<?= BASE_URL ?>/customers/merge?step=1&source=<?= $sourceId ?>" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-md hover:bg-gray-400">
                            Back: Select Source
                        </a>
                        <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded-md hover:bg-primary-700">
                            Next: Confirm Merge
                        </button>
                    </div>
                </form>
                
            <?php elseif ($step === 3): ?>
                <!-- Step 3: Confirm -->
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Step 3: Confirm Merge</h2>
                <p class="text-red-600 mb-6">⚠️ <strong>Warning:</strong> This action cannot be undone. The source customer will be permanently deleted.</p>
                
                <form method="POST" action="<?= BASE_URL ?>/customers/merge">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <input type="hidden" name="source_customer" value="<?= $sourceId ?>">
                    <input type="hidden" name="destination_customer" value="<?= $destinationId ?>">
                    <input type="hidden" name="confirm_merge" value="1">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <?php if ($sourceCustomer): ?>
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <h3 class="text-lg font-medium text-red-800 mb-3">
                                <svg class="inline h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Customer to Delete
                            </h3>
                            <div class="space-y-2 text-sm">
                                <div><strong>Name:</strong> <?= htmlspecialchars($sourceCustomer['name']) ?></div>
                                <?php if ($sourceCustomer['company']): ?>
                                <div><strong>Company:</strong> <?= htmlspecialchars($sourceCustomer['company']) ?></div>
                                <?php endif; ?>
                                <?php if ($sourceCustomer['email']): ?>
                                <div><strong>Email:</strong> <?= htmlspecialchars($sourceCustomer['email']) ?></div>
                                <?php endif; ?>
                                <?php if ($sourceCustomer['phone']): ?>
                                <div><strong>Phone:</strong> <?= htmlspecialchars($sourceCustomer['phone']) ?></div>
                                <?php endif; ?>
                                <div><strong>Work Orders:</strong> <?= count($sourceCustomer['work_orders']) ?></div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($destinationCustomer): ?>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <h3 class="text-lg font-medium text-green-800 mb-3">
                                <svg class="inline h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Customer to Keep
                            </h3>
                            <div class="space-y-2 text-sm">
                                <div><strong>Name:</strong> <?= htmlspecialchars($destinationCustomer['name']) ?></div>
                                <?php if ($destinationCustomer['company']): ?>
                                <div><strong>Company:</strong> <?= htmlspecialchars($destinationCustomer['company']) ?></div>
                                <?php endif; ?>
                                <?php if ($destinationCustomer['email']): ?>
                                <div><strong>Email:</strong> <?= htmlspecialchars($destinationCustomer['email']) ?></div>
                                <?php endif; ?>
                                <?php if ($destinationCustomer['phone']): ?>
                                <div><strong>Phone:</strong> <?= htmlspecialchars($destinationCustomer['phone']) ?></div>
                                <?php endif; ?>
                                <div><strong>Current Work Orders:</strong> <?= count($destinationCustomer['work_orders']) ?></div>
                                <div><strong>Total After Merge:</strong> <?= count($destinationCustomer['work_orders']) + count($sourceCustomer['work_orders']) ?></div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">What will happen:</h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    <ul class="list-disc list-inside space-y-1">
                                        <li>All <?= count($sourceCustomer['work_orders']) ?> work orders from "<?= htmlspecialchars($sourceCustomer['name']) ?>" will be moved to "<?= htmlspecialchars($destinationCustomer['name']) ?>"</li>
                                        <li>Customer "<?= htmlspecialchars($sourceCustomer['name']) ?>" will be permanently deleted</li>
                                        <li>Customer "<?= htmlspecialchars($destinationCustomer['name']) ?>" will keep all its information and gain the transferred work orders</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-between">
                        <a href="<?= BASE_URL ?>/customers/merge?step=2&source=<?= $sourceId ?>&destination=<?= $destinationId ?>" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-md hover:bg-gray-400">
                            Back: Select Destination
                        </a>
                        <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-md hover:bg-red-700">
                            Confirm Merge
                        </button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Source customer search functionality
document.getElementById('source_customer_search')?.addEventListener('input', function() {
    const query = this.value.trim();
    const resultsDiv = document.getElementById('source_customer_results');
    
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
                        document.getElementById('selected_source_customer_id').value = customerId;
                        document.getElementById('source_customer_search').value = this.querySelector('.font-medium').textContent;
                        resultsDiv.classList.add('hidden');
                    });
                });
            }
            resultsDiv.classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error searching customers:', error);
        });
});

// Destination customer search functionality
document.getElementById('destination_customer_search')?.addEventListener('input', function() {
    const query = this.value.trim();
    const resultsDiv = document.getElementById('destination_customer_results');
    const sourceCustomerId = document.getElementById('selected_source_customer_id')?.value || '<?= $sourceId ?>';
    
    if (query.length < 2) {
        resultsDiv.classList.add('hidden');
        return;
    }
    
    fetch(`<?= BASE_URL ?>/api/search-customers?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(customers => {
            // Filter out the source customer
            const filteredCustomers = customers.filter(customer => customer.id != sourceCustomerId);
            
            if (filteredCustomers.length === 0) {
                resultsDiv.innerHTML = '<div class="p-2 text-gray-500">No customers found</div>';
            } else {
                resultsDiv.innerHTML = filteredCustomers.map(customer => 
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
                        document.getElementById('selected_destination_customer_id').value = customerId;
                        document.getElementById('destination_customer_search').value = this.querySelector('.font-medium').textContent;
                        resultsDiv.classList.add('hidden');
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
    const sourceSearchInput = document.getElementById('source_customer_search');
    const sourceResultsDiv = document.getElementById('source_customer_results');
    const destSearchInput = document.getElementById('destination_customer_search');
    const destResultsDiv = document.getElementById('destination_customer_results');
    
    // Hide source results
    if (sourceSearchInput && sourceResultsDiv && 
        !sourceSearchInput.contains(e.target) && !sourceResultsDiv.contains(e.target)) {
        sourceResultsDiv.classList.add('hidden');
    }
    
    // Hide destination results
    if (destSearchInput && destResultsDiv && 
        !destSearchInput.contains(e.target) && !destResultsDiv.contains(e.target)) {
        destResultsDiv.classList.add('hidden');
    }
});
</script>

<?php 
$content = ob_get_clean();
include ROOT_PATH . '/views/layout.php';
?>
