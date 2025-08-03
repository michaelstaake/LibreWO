<?php
require_once 'core/Controller.php';
require_once 'models/Customer.php';
require_once 'models/User.php';

class ApiController extends Controller {
    private $customerModel;
    private $userModel;
    
    public function __construct() {
        parent::__construct();
        $this->customerModel = new Customer();
        $this->userModel = new User();
    }
    
    public function searchCustomers() {
        header('Content-Type: application/json');
        
        $query = $_GET['q'] ?? '';
        
        if (strlen($query) < 2) {
            echo json_encode([]);
            return;
        }
        
        $customers = $this->customerModel->searchCustomers($query);
        echo json_encode($customers);
    }
    
    public function updateWorkOrderStatus() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }
        
        try {
            $this->requireTechnician();
            
            $input = json_decode(file_get_contents('php://input'), true);
            $workOrderId = $input['work_order_id'] ?? 0;
            $status = $input['status'] ?? '';
            
            if (!$workOrderId || !$status) {
                throw new Exception('Missing required parameters');
            }
            
            require_once 'models/WorkOrder.php';
            $workOrderModel = new WorkOrder();
            
            $updateData = ['status' => $status];
            if ($status === 'Closed') {
                $updateData['closed_at'] = date('Y-m-d H:i:s');
            }
            
            $result = $workOrderModel->updateWorkOrder($workOrderId, $updateData);
            
            if ($result) {
                $this->logger->log('work_order_status_updated', 
                    "Work order #{$workOrderId} status changed to {$status}", $_SESSION['user_id']);
                
                echo json_encode(['success' => true]);
            } else {
                throw new Exception('Failed to update work order');
            }
            
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
