<?php
require_once 'core/Controller.php';
require_once 'models/Customer.php';
require_once 'models/WorkOrder.php';

class CustomerController extends Controller {
    private $customerModel;
    private $workOrderModel;
    
    public function __construct() {
        parent::__construct();
        $this->customerModel = new Customer();
        $this->workOrderModel = new WorkOrder();
    }
    
    public function index() {
        $this->requireAdmin();
        
        $search = $_GET['search'] ?? '';
        $page = max(1, intval($_GET['page'] ?? 1));
        $limit = PAGINATION_LIMIT;
        $offset = ($page - 1) * $limit;
        
        $customers = $this->customerModel->getAllCustomers($limit, $offset, $search);
        $totalCount = $this->customerModel->getCustomerCount($search);
        $totalPages = ceil($totalCount / $limit);
        
        $this->view('customers/index', [
            'customers' => $customers,
            'search' => $search,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalCount' => $totalCount,
            'csrf_token' => $this->generateCSRF()
        ]);
    }
    
    public function details($id) {
        $this->requireAuth();
        
        $customer = $this->customerModel->getCustomerWithWorkOrders($id);
        if (!$customer) {
            $this->redirect('/404');
        }
        
        $error = '';
        $message = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['user_group'] === 'Admin') {
            try {
                $this->validateCSRF();
                
                $updateData = [
                    'name' => $this->sanitizeInput($_POST['name']),
                    'company' => $this->sanitizeInput($_POST['company']),
                    'email' => $this->sanitizeInput($_POST['email']),
                    'phone' => $this->sanitizeInput($_POST['phone'])
                ];
                
                if (empty($updateData['name']) || empty($updateData['phone'])) {
                    throw new Exception('Name and phone are required.');
                }
                
                if ($updateData['email'] && !filter_var($updateData['email'], FILTER_VALIDATE_EMAIL)) {
                    throw new Exception('Invalid email address.');
                }
                
                $this->customerModel->updateCustomer($id, $updateData);
                $this->logger->log('customer_updated', "Customer {$updateData['name']} updated", $_SESSION['user_id']);
                
                $message = 'Customer updated successfully.';
                $customer = array_merge($customer, $updateData);
                
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
        
        $this->view('customers/view', [
            'customer' => $customer,
            'workOrders' => $customer['work_orders'] ?? [],
            'error' => $error,
            'message' => $message,
            'csrf_token' => $this->generateCSRF(),
            'canEdit' => $_SESSION['user_group'] === 'Admin'
        ]);
    }
    
    public function merge() {
        $this->requireAdmin();
        
        $error = '';
        $message = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->validateCSRF();
                
                $keepId = intval($_POST['keep_customer']);
                $mergeId = intval($_POST['merge_customer']);
                
                if ($keepId === $mergeId) {
                    throw new Exception('Cannot merge a customer with itself.');
                }
                
                $keepCustomer = $this->customerModel->findById($keepId);
                $mergeCustomer = $this->customerModel->findById($mergeId);
                
                if (!$keepCustomer || !$mergeCustomer) {
                    throw new Exception('One or both customers not found.');
                }
                
                $this->customerModel->mergeCustomers($keepId, $mergeId);
                $this->logger->log('customers_merged', 
                    "Merged customer {$mergeCustomer['name']} into {$keepCustomer['name']}", $_SESSION['user_id']);
                
                $message = 'Customers merged successfully.';
                
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
        
        $customers = $this->customerModel->getAllCustomers();
        
        $this->view('customers/merge', [
            'customers' => $customers,
            'error' => $error,
            'message' => $message,
            'csrf_token' => $this->generateCSRF()
        ]);
    }
}
