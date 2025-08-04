<?php
require_once 'core/Controller.php';
require_once 'models/WorkOrder.php';

class HomeController extends Controller {
    private $workOrderModel;
    
    public function __construct() {
        parent::__construct();
        $this->workOrderModel = new WorkOrder();
    }
    
    public function index() {
        $this->requireAuth();
        
        // Get dashboard statistics
        $statusCounts = $this->workOrderModel->getStatusCounts();
        $totalWorkOrders = $this->workOrderModel->getTotalCount();
        $priorityCount = $this->workOrderModel->getPriorityCount();
        $assignedCount = $this->workOrderModel->getAssignedCount($_SESSION['user_id']);
        
        $this->view('home/index', [
            'statusCounts' => $statusCounts,
            'totalWorkOrders' => $totalWorkOrders,
            'priorityCount' => $priorityCount,
            'assignedCount' => $assignedCount,
            'user' => [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'name' => $_SESSION['user_name'] ?? null,
                'user_group' => $_SESSION['user_group']
            ]
        ]);
    }
}
