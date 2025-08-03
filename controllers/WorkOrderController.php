<?php
require_once 'core/Controller.php';
require_once 'models/WorkOrder.php';
require_once 'models/Customer.php';
require_once 'models/User.php';
require_once 'models/Settings.php';

class WorkOrderController extends Controller {
    private $workOrderModel;
    private $customerModel;
    private $userModel;
    
    public function __construct() {
        parent::__construct();
        $this->workOrderModel = new WorkOrder();
        $this->customerModel = new Customer();
        $this->userModel = new User();
    }
    
    public function index() {
        $this->requireAuth();
        
        $status = $_GET['status'] ?? 'All';
        $search = $_GET['search'] ?? '';
        $page = max(1, intval($_GET['page'] ?? 1));
        $limit = PAGINATION_LIMIT;
        $offset = ($page - 1) * $limit;
        
        $workOrders = $this->workOrderModel->getWorkOrders($status, null, $search, $limit, $offset);
        $totalCount = $this->workOrderModel->countWorkOrders($status, null, $search);
        $totalPages = ceil($totalCount / $limit);
        
        $this->view('work-orders/index', [
            'workOrders' => $workOrders,
            'status' => $status,
            'search' => $search,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalCount' => $totalCount
        ]);
    }
    
    public function create() {
        $this->requireTechnician();
        
        $step = intval($_GET['step'] ?? 1);
        $error = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->validateCSRF();
                
                if ($step === 1) {
                    // Customer step
                    $customerId = $_POST['customer_id'] ?? null;
                    
                    if (!$customerId) {
                        // Create new customer
                        $customerData = [
                            'name' => $this->sanitizeInput($_POST['customer_name']),
                            'company' => $this->sanitizeInput($_POST['customer_company']),
                            'email' => $this->sanitizeInput($_POST['customer_email']),
                            'phone' => $this->sanitizeInput($_POST['customer_phone'])
                        ];
                        
                        if (empty($customerData['name']) || empty($customerData['phone'])) {
                            throw new Exception('Customer name and phone are required.');
                        }
                        
                        $customerId = $this->customerModel->createCustomer($customerData);
                    }
                    
                    $_SESSION['work_order_data']['customer_id'] = $customerId;
                    $this->redirect('/work-orders/create?step=2');
                    
                } elseif ($step === 2) {
                    // Computer step
                    $_SESSION['work_order_data']['computer'] = $this->sanitizeInput($_POST['computer']);
                    $_SESSION['work_order_data']['model'] = $this->sanitizeInput($_POST['model']);
                    $_SESSION['work_order_data']['serial_number'] = $this->sanitizeInput($_POST['serial_number']);
                    $_SESSION['work_order_data']['accessories'] = json_encode($_POST['accessories'] ?? []);
                    $_SESSION['work_order_data']['username'] = $this->sanitizeInput($_POST['username']);
                    $_SESSION['work_order_data']['password'] = $this->sanitizeInput($_POST['password']);
                    
                    if (empty($_SESSION['work_order_data']['computer'])) {
                        throw new Exception('Computer field is required.');
                    }
                    
                    $this->redirect('/work-orders/create?step=3');
                    
                } elseif ($step === 3) {
                    // Description step
                    $_SESSION['work_order_data']['description'] = $this->sanitizeInput($_POST['description']);
                    
                    if (empty($_SESSION['work_order_data']['description'])) {
                        throw new Exception('Problem description is required.');
                    }
                    
                    $this->redirect('/work-orders/create?step=4');
                    
                } elseif ($step === 4) {
                    // Confirm and create
                    $workOrderData = $_SESSION['work_order_data'];
                    $workOrderData['assigned_to'] = $_POST['assigned_to'] ?: null;
                    $workOrderData['priority'] = $_POST['priority'] ?? 'Standard';
                    $workOrderData['created_by'] = $_SESSION['user_id'];
                    
                    $workOrderId = $this->workOrderModel->createWorkOrder($workOrderData);
                    
                    // Clear session data
                    unset($_SESSION['work_order_data']);
                    
                    $this->logger->log('work_order_created', "Work order #{$workOrderId} created", $_SESSION['user_id']);
                    $this->redirect("/work-orders/submitted/{$workOrderId}");
                }
                
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
        
        // Get data for the view
        $customers = [];
        $technicians = $this->userModel->getTechnicians();
        $customer = null;
        
        if ($step >= 2 && isset($_SESSION['work_order_data']['customer_id'])) {
            $customer = $this->customerModel->findById($_SESSION['work_order_data']['customer_id']);
        }
        
        $this->view('work-orders/create', [
            'step' => $step,
            'error' => $error,
            'customers' => $customers,
            'technicians' => $technicians,
            'customer' => $customer,
            'workOrderData' => $_SESSION['work_order_data'] ?? [],
            'csrf_token' => $this->generateCSRF()
        ]);
    }
    
    public function details($id) {
        $this->requireAuth();
        
        $workOrder = $this->workOrderModel->getWorkOrderById($id);
        if (!$workOrder) {
            $this->redirect('/404');
        }
        
        // Check permissions
        if ($_SESSION['user_group'] === 'Limited') {
            // Limited users can only view
        }
        
        $error = '';
        $message = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['user_group'] !== 'Limited') {
            try {
                $this->validateCSRF();
                
                $updateData = [
                    'description' => $this->sanitizeInput($_POST['description']),
                    'resolution' => $this->sanitizeInput($_POST['resolution']),
                    'notes' => $this->sanitizeInput($_POST['notes']),
                    'status' => $_POST['status'],
                    'priority' => $_POST['priority'],
                    'assigned_to' => $_POST['assigned_to'] ?: null
                ];
                
                if ($updateData['status'] === 'Closed' && !$workOrder['closed_at']) {
                    $updateData['closed_at'] = date('Y-m-d H:i:s');
                }
                
                $this->workOrderModel->updateWorkOrder($id, $updateData);
                $this->logger->log('work_order_updated', "Work order #{$workOrder['work_order_number']} updated", $_SESSION['user_id']);
                
                $message = 'Work order updated successfully.';
                $workOrder = $this->workOrderModel->getWorkOrderById($id); // Refresh data
                
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
        
        $technicians = $this->userModel->getTechnicians();
        $workOrderLogs = $this->workOrderModel->getWorkOrderLogs($id);
        
        $this->view('work-orders/view', [
            'workOrder' => $workOrder,
            'technicians' => $technicians,
            'workOrderLogs' => $workOrderLogs,
            'error' => $error,
            'message' => $message,
            'csrf_token' => $this->generateCSRF(),
            'canEdit' => $_SESSION['user_group'] !== 'Limited'
        ]);
    }
    
    public function submitted($id) {
        $this->requireTechnician();
        
        $workOrder = $this->workOrderModel->getWorkOrderById($id);
        if (!$workOrder) {
            $this->redirect('/404');
        }
        
        $this->view('work-orders/submitted', [
            'workOrder' => $workOrder
        ]);
    }
    
    public function print($id) {
        $this->requireAuth();
        
        $workOrder = $this->workOrderModel->getWorkOrderById($id);
        if (!$workOrder) {
            $this->redirect('/404');
        }
        
        $companyInfo = $this->settingsModel->getCompanyInfo();
        
        $this->view('work-orders/print', [
            'workOrder' => $workOrder,
            'companyInfo' => $companyInfo
        ]);
    }
}
