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
            body { margin: 0; padding: 10px; font-size: 11pt; line-height: 1.3; }
            .no-print { display: none !important; }
            .print-break { page-break-after: always; }
            .print-avoid-break { page-break-inside: avoid; }
            h1, h2, h3 { margin-top: 0; margin-bottom: 0.5rem; }
            .compact { margin-bottom: 0.75rem; }
            .extra-compact { margin-bottom: 0.5rem; }
        }
        body { background: white; }
    </style>
</head>
<body class="bg-white">
    <div class="max-w-full mx-auto p-3">
        <!-- Print Button (hidden when printing) -->
        <div class="no-print mb-6 flex justify-between items-center">
            <a href="<?= BASE_URL ?>/work-orders/view/<?= $workOrder['id'] ?>" class="text-gray-600 hover:text-gray-900">
                ← Back to Work Order
            </a>
            <button onclick="window.print()" class="bg-primary-600 text-white px-4 py-2 rounded hover:bg-primary-700">
                Print Work Order
            </button>
        </div>

        <!-- Row 1: Company Information (Left) | Work Order Number & Date (Right) -->
        <div class="grid grid-cols-2 gap-4 mb-4 print-avoid-break">
            <!-- Company Information -->
            <div class="text-left">
                <?php if (!empty($companyLogoUrl)): ?>
                    <div class="mb-2">
                        <img src="<?= htmlspecialchars($companyLogoUrl) ?>" alt="<?= htmlspecialchars($companyInfo['company_name'] ?? $companyName ?? 'LibreWO') ?>" class="h-12 w-auto">
                    </div>
                <?php else: ?>
                    <h1 class="text-lg font-bold text-gray-900 mb-2"><?= htmlspecialchars($companyInfo['company_name'] ?? $companyName ?? 'LibreWO') ?></h1>
                <?php endif; ?>
                <?php if (!empty($companyInfo['company_address'])): ?>
                    <p class="text-gray-600 text-xs mb-1"><?= nl2br(htmlspecialchars($companyInfo['company_address'])) ?></p>
                <?php endif; ?>
                <div class="text-xs text-gray-600 space-y-0.5">
                    <?php if (!empty($companyInfo['company_phone'])): ?>
                        <div><?= htmlspecialchars($companyInfo['company_phone']) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($companyInfo['company_email'])): ?>
                        <div><?= htmlspecialchars($companyInfo['company_email']) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($companyInfo['company_website'])): ?>
                        <div><?= htmlspecialchars($companyInfo['company_website']) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Work Order Number & Date -->
            <div class="text-right">
                <h2 class="text-2xl font-bold text-gray-900 mb-1">WORK ORDER #<?= $workOrder['id'] ?></h2>
                <p class="text-sm text-gray-600">Created: <?= date('M j, Y \a\t g:i A', strtotime($workOrder['created_at'])) ?></p>
            </div>
        </div>

        <!-- Row 2: Customer Information (Left) | Computer Information & Accessories (Right) -->
        <div class="grid grid-cols-2 gap-4 mb-4 print-avoid-break">
            <!-- Customer Information -->
            <div class="border border-gray-300 rounded-lg p-3">
                <h3 class="text-sm font-semibold text-gray-900 mb-2">Customer Information</h3>
                <div class="space-y-1">
                    <p class="text-xs"><strong>Name:</strong> <?= htmlspecialchars($workOrder['customer_name']) ?></p>
                    <?php if (!empty($workOrder['customer_company'])): ?>
                        <p class="text-xs"><strong>Company:</strong> <?= htmlspecialchars($workOrder['customer_company']) ?></p>
                    <?php endif; ?>
                    <p class="text-xs"><strong>Phone:</strong> <?= htmlspecialchars($workOrder['customer_phone']) ?></p>
                    <?php if (!empty($workOrder['customer_email'])): ?>
                        <p class="text-xs"><strong>Email:</strong> <?= htmlspecialchars($workOrder['customer_email']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Computer Information & Accessories -->
            <div class="border border-gray-300 rounded-lg p-3">
                <h3 class="text-sm font-semibold text-gray-900 mb-2">Device Information</h3>
                <div class="space-y-1">
                    <p class="text-xs"><strong>Computer:</strong> <?= htmlspecialchars($workOrder['computer'] ?? 'N/A') ?></p>
                    <?php if (!empty($workOrder['model'])): ?>
                        <p class="text-xs"><strong>Model:</strong> <?= htmlspecialchars($workOrder['model']) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($workOrder['serial_number'])): ?>
                        <p class="text-xs"><strong>Serial Number:</strong> <?= htmlspecialchars($workOrder['serial_number']) ?></p>
                    <?php endif; ?>
                    <p class="text-xs"><strong>Accessories:</strong> 
                        <?php 
                        if (!empty($workOrder['accessories'])) {
                            $accessories = json_decode($workOrder['accessories'], true);
                            if (is_array($accessories)) {
                                // Filter out empty values
                                $accessories = array_filter($accessories, function($item) {
                                    return !empty(trim($item));
                                });
                                
                                if (!empty($accessories)) {
                                    echo htmlspecialchars(implode(', ', $accessories));
                                } else {
                                    echo 'N/A';
                                }
                            } else {
                                // If not JSON, treat as plain text
                                echo htmlspecialchars($workOrder['accessories']);
                            }
                        } else {
                            echo 'N/A';
                        }
                        ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Row 3: Problem Description (Full Width) -->
        <div class="border border-gray-300 rounded-lg p-3 mb-4 print-avoid-break">
            <h3 class="text-sm font-semibold text-gray-900 mb-2">Problem Description</h3>
            <p class="text-xs text-gray-700 whitespace-pre-wrap"><?= htmlspecialchars($workOrder['description']) ?></p>
        </div>

        <!-- Work Performed (if available) -->
        <?php if (!empty($workOrder['work_performed'])): ?>
            <div class="border border-gray-300 rounded-lg p-3 mb-4 print-avoid-break">
                <h3 class="text-sm font-semibold text-gray-900 mb-2">Work Performed</h3>
                <p class="text-xs text-gray-700 whitespace-pre-wrap"><?= htmlspecialchars($workOrder['work_performed']) ?></p>
            </div>
        <?php endif; ?>

        <!-- Pricing (if available) -->
        <?php if (!empty($workOrder['estimated_cost']) || !empty($workOrder['final_cost'])): ?>
            <div class="border border-gray-300 rounded-lg p-3 mb-4 print-avoid-break">
                <h3 class="text-sm font-semibold text-gray-900 mb-2">Pricing</h3>
                <div class="space-y-1">
                    <?php if (!empty($workOrder['estimated_cost'])): ?>
                        <p class="text-xs"><strong>Estimated Cost:</strong> $<?= number_format($workOrder['estimated_cost'], 2) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($workOrder['final_cost'])): ?>
                        <p class="text-xs"><strong>Final Cost:</strong> $<?= number_format($workOrder['final_cost'], 2) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Customer Notes (if available) -->
        <?php if (!empty($workOrder['customer_notes'])): ?>
            <div class="border border-gray-300 rounded-lg p-3 mb-4 print-avoid-break">
                <h3 class="text-sm font-semibold text-gray-900 mb-2">Customer Notes</h3>
                <p class="text-xs text-gray-700 whitespace-pre-wrap"><?= htmlspecialchars($workOrder['customer_notes']) ?></p>
            </div>
        <?php endif; ?>

        <!-- Internal Notes (if available) -->
        <?php if (!empty($workOrder['internal_notes'])): ?>
            <div class="border border-gray-300 rounded-lg p-3 mb-4 print-avoid-break">
                <h3 class="text-sm font-semibold text-gray-900 mb-2">Internal Notes</h3>
                <p class="text-xs text-gray-700 whitespace-pre-wrap"><?= htmlspecialchars($workOrder['internal_notes']) ?></p>
            </div>
        <?php endif; ?>

        <!-- Row 4: Disclaimer (Full Width) -->
        <?php if (!empty($companyInfo['work_order_disclaimer'])): ?>
            <div class="border-t border-gray-300 pt-3 mt-4 print-avoid-break">
                <h3 class="text-xs font-semibold text-gray-900 mb-2">Terms and Conditions</h3>
                <div class="text-xs text-gray-600 leading-tight">
                    <?= strip_tags($companyInfo['work_order_disclaimer'], '<p><b>') ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Signature Section -->
        <div class="border-t border-gray-300 pt-3 mt-4">
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <p class="text-xs font-semibold text-gray-900 mb-2">Customer Signature:</p>
                    <div class="border-b border-gray-400 h-8 mb-1"></div>
                </div>
                <div>
                    <!-- Empty column for spacing -->
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
