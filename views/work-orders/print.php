<?php 
$title = 'Print Work Order #' . $workOrder['id'] . ' - ' . ($companyName ?? 'LibreWO');
$hideNavigation = true;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { margin: 0; padding: 20px; font-size: 12pt; }
            .no-print { display: none !important; }
            .print-break { page-break-after: always; }
            .print-avoid-break { page-break-inside: avoid; }
        }
        body { background: white; }
    </style>
</head>
<body class="bg-white">
    <div class="max-w-4xl mx-auto p-6">
        <!-- Print Button (hidden when printing) -->
        <div class="no-print mb-6 flex justify-between items-center">
            <a href="<?= BASE_URL ?>/work-orders/view/<?= $workOrder['id'] ?>" class="text-gray-600 hover:text-gray-900">
                ← Back to Work Order
            </a>
            <button onclick="window.print()" class="bg-primary-600 text-white px-4 py-2 rounded hover:bg-primary-700">
                Print Work Order
            </button>
        </div>

        <!-- Company Header -->
        <div class="text-center mb-8 print-avoid-break">
            <?php if (!empty($companyLogoUrl)): ?>
                <div class="flex justify-center mb-4">
                    <img src="<?= htmlspecialchars($companyLogoUrl) ?>" alt="<?= htmlspecialchars($companyInfo['company_name'] ?? $companyName ?? 'LibreWO') ?>" class="h-16 w-auto">
                </div>
            <?php else: ?>
                <h1 class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($companyInfo['company_name'] ?? $companyName ?? 'LibreWO') ?></h1>
            <?php endif; ?>
            <?php if (!empty($companyInfo['company_address'])): ?>
                <p class="text-gray-600 mt-1"><?= nl2br(htmlspecialchars($companyInfo['company_address'])) ?></p>
            <?php endif; ?>
            <div class="flex justify-center space-x-4 mt-2 text-sm text-gray-600">
                <?php if (!empty($companyInfo['company_phone'])): ?>
                    <span>Phone: <?= htmlspecialchars($companyInfo['company_phone']) ?></span>
                <?php endif; ?>
                <?php if (!empty($companyInfo['company_email'])): ?>
                    <span>Email: <?= htmlspecialchars($companyInfo['company_email']) ?></span>
                <?php endif; ?>
                <?php if (!empty($companyInfo['company_website'])): ?>
                    <span>Web: <?= htmlspecialchars($companyInfo['company_website']) ?></span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Work Order Header -->
        <div class="border-b-2 border-gray-300 pb-4 mb-6 print-avoid-break">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">WORK ORDER</h2>
                    <p class="text-lg font-semibold text-primary-600">#<?= $workOrder['id'] ?></p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-600">Date Created: <?= date('M j, Y', strtotime($workOrder['created_at'])) ?></p>
                    <p class="text-sm text-gray-600">Time: <?= date('g:i A', strtotime($workOrder['created_at'])) ?></p>
                    <div class="mt-2">
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
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 print-avoid-break">
            <div class="border border-gray-300 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Customer Information</h3>
                <div class="space-y-2">
                    <p><strong>Name:</strong> <?= htmlspecialchars($workOrder['customer_name']) ?></p>
                    <?php if (!empty($workOrder['customer_company'])): ?>
                        <p><strong>Company:</strong> <?= htmlspecialchars($workOrder['customer_company']) ?></p>
                    <?php endif; ?>
                    <p><strong>Phone:</strong> <?= htmlspecialchars($workOrder['customer_phone']) ?></p>
                    <?php if (!empty($workOrder['customer_email'])): ?>
                        <p><strong>Email:</strong> <?= htmlspecialchars($workOrder['customer_email']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="border border-gray-300 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Device Information</h3>
                <div class="space-y-2">
                    <p><strong>Computer:</strong> <?= htmlspecialchars($workOrder['computer'] ?? 'N/A') ?></p>
                    <?php if (!empty($workOrder['model'])): ?>
                        <p><strong>Model:</strong> <?= htmlspecialchars($workOrder['model']) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($workOrder['serial_number'])): ?>
                        <p><strong>Serial Number:</strong> <?= htmlspecialchars($workOrder['serial_number']) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($workOrder['accessories'])): ?>
                        <p><strong>Accessories:</strong> <?= htmlspecialchars($workOrder['accessories']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Problem Description -->
        <div class="border border-gray-300 rounded-lg p-4 mb-6 print-avoid-break">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">Problem Description</h3>
            <p class="text-gray-700 whitespace-pre-wrap"><?= htmlspecialchars($workOrder['description']) ?></p>
        </div>

        <!-- Work Performed -->
        <?php if (!empty($workOrder['work_performed'])): ?>
            <div class="border border-gray-300 rounded-lg p-4 mb-6 print-avoid-break">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Work Performed</h3>
                <p class="text-gray-700 whitespace-pre-wrap"><?= htmlspecialchars($workOrder['work_performed']) ?></p>
            </div>
        <?php endif; ?>

        <!-- Technician and Priority -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 print-avoid-break">
            <div class="border border-gray-300 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Assignment</h3>
                <div class="space-y-2">
                    <?php if (!empty($workOrder['technician_name'])): ?>
                        <p><strong>Assigned Technician:</strong> <?= htmlspecialchars($workOrder['technician_name']) ?></p>
                    <?php else: ?>
                        <p><strong>Assigned Technician:</strong> Not Assigned</p>
                    <?php endif; ?>
                    <p><strong>Priority:</strong> 
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            <?php 
                            switch($workOrder['priority']) {
                                case 'Low': echo 'bg-green-100 text-green-800'; break;
                                case 'Medium': echo 'bg-yellow-100 text-yellow-800'; break;
                                case 'High': echo 'bg-red-100 text-red-800'; break;
                                case 'Urgent': echo 'bg-red-200 text-red-900'; break;
                                default: echo 'bg-gray-100 text-gray-800';
                            }
                            ?>">
                            <?= htmlspecialchars($workOrder['priority']) ?>
                        </span>
                    </p>
                </div>
            </div>

            <div class="border border-gray-300 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Dates</h3>
                <div class="space-y-2">
                    <p><strong>Created:</strong> <?= date('M j, Y g:i A', strtotime($workOrder['created_at'])) ?></p>
                    <?php if (!empty($workOrder['creator_display_name'])): ?>
                        <p><strong>Created By:</strong> <?= htmlspecialchars($workOrder['creator_display_name']) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($workOrder['updated_at']) && $workOrder['updated_at'] !== $workOrder['created_at']): ?>
                        <p><strong>Last Updated:</strong> <?= date('M j, Y g:i A', strtotime($workOrder['updated_at'])) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($workOrder['completed_at'])): ?>
                        <p><strong>Completed:</strong> <?= date('M j, Y g:i A', strtotime($workOrder['completed_at'])) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Pricing (if available) -->
        <?php if (!empty($workOrder['estimated_cost']) || !empty($workOrder['final_cost'])): ?>
            <div class="border border-gray-300 rounded-lg p-4 mb-6 print-avoid-break">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Pricing</h3>
                <div class="space-y-2">
                    <?php if (!empty($workOrder['estimated_cost'])): ?>
                        <p><strong>Estimated Cost:</strong> $<?= number_format($workOrder['estimated_cost'], 2) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($workOrder['final_cost'])): ?>
                        <p><strong>Final Cost:</strong> $<?= number_format($workOrder['final_cost'], 2) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Customer Notes -->
        <?php if (!empty($workOrder['customer_notes'])): ?>
            <div class="border border-gray-300 rounded-lg p-4 mb-6 print-avoid-break">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Customer Notes</h3>
                <p class="text-gray-700 whitespace-pre-wrap"><?= htmlspecialchars($workOrder['customer_notes']) ?></p>
            </div>
        <?php endif; ?>

        <!-- Internal Notes -->
        <?php if (!empty($workOrder['internal_notes'])): ?>
            <div class="border border-gray-300 rounded-lg p-4 mb-6 print-avoid-break">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Internal Notes</h3>
                <p class="text-gray-700 whitespace-pre-wrap"><?= htmlspecialchars($workOrder['internal_notes']) ?></p>
            </div>
        <?php endif; ?>

        <!-- Disclaimer -->
        <?php if (!empty($companyInfo['work_order_disclaimer'])): ?>
            <div class="border-t-2 border-gray-300 pt-4 mt-8 print-avoid-break">
                <h3 class="text-sm font-semibold text-gray-900 mb-2">Terms and Conditions</h3>
                <p class="text-xs text-gray-600 whitespace-pre-wrap"><?= htmlspecialchars($companyInfo['work_order_disclaimer']) ?></p>
            </div>
        <?php endif; ?>

        <!-- Signature Section -->
        <div class="border-t-2 border-gray-300 pt-6 mt-8">
            <div class="grid grid-cols-1 gap-8">
                <div>
                    <p class="text-sm font-semibold text-gray-900 mb-2">Customer Signature:</p>
                    <div class="border-b border-gray-400 h-12 mb-2"></div>
                    <p class="text-xs text-gray-600">Date: ________________</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Automatically open print dialog when page loads
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
