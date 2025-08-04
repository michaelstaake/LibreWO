<?php 
$title = 'Work Order Submitted - ' . ($companyName ?? 'LibreWO');
ob_start(); 
?>

<style>
.confetti {
    position: fixed;
    top: -20px;
    z-index: 9999;
    width: 15px;
    height: 15px;
    background: #f0f;
    animation: confetti-fall linear infinite;
    transform-origin: center;
}

.confetti:nth-child(1) { background: #ff0066; left: 10%; animation-duration: 1.5s; animation-delay: 0s; }
.confetti:nth-child(2) { background: #00ff66; left: 20%; animation-duration: 1.2s; animation-delay: 0.1s; }
.confetti:nth-child(3) { background: #6600ff; left: 30%; animation-duration: 1.8s; animation-delay: 0.2s; }
.confetti:nth-child(4) { background: #ffff00; left: 40%; animation-duration: 1s; animation-delay: 0.05s; }
.confetti:nth-child(5) { background: #ff6600; left: 50%; animation-duration: 1.5s; animation-delay: 0.15s; }
.confetti:nth-child(6) { background: #00ffff; left: 60%; animation-duration: 1.2s; animation-delay: 0.25s; }
.confetti:nth-child(7) { background: #ff0066; left: 70%; animation-duration: 1.8s; animation-delay: 0.08s; }
.confetti:nth-child(8) { background: #00ff66; left: 80%; animation-duration: 1s; animation-delay: 0.18s; }
.confetti:nth-child(9) { background: #6600ff; left: 90%; animation-duration: 1.5s; animation-delay: 0.28s; }
.confetti:nth-child(10) { background: #ffff00; left: 15%; animation-duration: 1.2s; animation-delay: 0.12s; }
.confetti:nth-child(11) { background: #ff6600; left: 25%; animation-duration: 1.8s; animation-delay: 0.22s; }
.confetti:nth-child(12) { background: #00ffff; left: 35%; animation-duration: 1s; animation-delay: 0.32s; }
.confetti:nth-child(13) { background: #ff0066; left: 45%; animation-duration: 1.5s; animation-delay: 0.03s; }
.confetti:nth-child(14) { background: #00ff66; left: 55%; animation-duration: 1.2s; animation-delay: 0.13s; }
.confetti:nth-child(15) { background: #6600ff; left: 65%; animation-duration: 1.8s; animation-delay: 0.23s; }
.confetti:nth-child(16) { background: #ffff00; left: 75%; animation-duration: 1s; animation-delay: 0.33s; }
.confetti:nth-child(17) { background: #ff6600; left: 85%; animation-duration: 1.5s; animation-delay: 0.38s; }
.confetti:nth-child(18) { background: #00ffff; left: 95%; animation-duration: 1.2s; animation-delay: 0.06s; }
.confetti:nth-child(19) { background: #ff0066; left: 5%; animation-duration: 1.8s; animation-delay: 0.16s; }
.confetti:nth-child(20) { background: #00ff66; left: 12%; animation-duration: 1s; animation-delay: 0.26s; }

@keyframes confetti-fall {
    0% {
        transform: translateY(-50px) rotate(0deg) scale(0.5);
        opacity: 1;
    }
    10% {
        transform: translateY(0px) rotate(180deg) scale(1.2);
        opacity: 1;
    }
    70% {
        transform: translateY(70vh) rotate(756deg) scale(0.9);
        opacity: 1;
    }
    85% {
        transform: translateY(85vh) rotate(918deg) scale(0.85);
        opacity: 0.7;
    }
    95% {
        transform: translateY(95vh) rotate(1026deg) scale(0.8);
        opacity: 0.3;
    }
    100% {
        transform: translateY(100vh) rotate(1080deg) scale(0.8);
        opacity: 0;
    }
}

.confetti-container {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 9999;
}
</style>

<div id="confetti-container" class="confetti-container"></div>

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
                    <p class="text-sm text-gray-600">Your work order has been created!</p>
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
                    <a href="<?= BASE_URL ?>/work-orders/view/<?= $workOrder['id'] ?>" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Go to Work Order
                    </a>
                    <a href="<?= BASE_URL ?>/work-orders/print/<?= $workOrder['id'] ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        <svg class="-ml-1 mr-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Print
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function createConfetti() {
    const confettiContainer = document.getElementById('confetti-container');
    const colors = ['#ff0066', '#00ff66', '#6600ff', '#ffff00', '#ff6600', '#00ffff', '#ff3366', '#33ff66', '#6633ff', '#ffff33'];
    
    // Create explosive burst of confetti - 5 rapid waves
    for (let wave = 0; wave < 5; wave++) {
        setTimeout(() => {
            for (let i = 0; i < 80; i++) {
                const confetti = document.createElement('div');
                confetti.classList.add('confetti');
                confetti.style.left = Math.random() * 100 + '%';
                confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                confetti.style.animationDuration = (Math.random() * 0.8 + 1) + 's'; // 1-1.8 seconds
                confetti.style.animationDelay = Math.random() * 0.3 + 's'; // 0-0.3 second delay
                confetti.style.width = (Math.random() * 10 + 12) + 'px'; // 12-22px
                confetti.style.height = (Math.random() * 10 + 12) + 'px'; // 12-22px
                
                // More dramatic shapes
                if (Math.random() > 0.3) {
                    confetti.style.borderRadius = '50%';
                } else {
                    confetti.style.transform = 'rotate(' + Math.random() * 360 + 'deg)';
                    if (Math.random() > 0.5) {
                        confetti.style.clipPath = 'polygon(50% 0%, 0% 100%, 100% 100%)'; // Triangle
                    }
                }
                
                confettiContainer.appendChild(confetti);
                
                // Remove confetti after short animation
                setTimeout(() => {
                    if (confetti.parentNode) {
                        confetti.parentNode.removeChild(confetti);
                    }
                }, 2500);
            }
        }, wave * 100); // Rapid succession - 100ms between waves
    }
    
    // Clean up container quickly
    setTimeout(() => {
        confettiContainer.innerHTML = '';
    }, 4000);
}

// Start explosive confetti immediately when page loads
document.addEventListener('DOMContentLoaded', function() {
    createConfetti();
});
</script>

<?php 
$content = ob_get_clean();
include ROOT_PATH . '/views/layout.php';
?>
