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
        
        $error = '';
        $message = '';
        
        // Handle POST request for creating new customer
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->validateCSRF();
                
                $customerData = [
                    'name' => $this->sanitizeInput($_POST['name']),
                    'company' => $this->sanitizeInput($_POST['company']),
                    'email' => $this->sanitizeInput($_POST['email']),
                    'phone' => $this->sanitizeInput($_POST['phone'])
                ];
                
                // Validate required fields
                if (empty($customerData['name'])) {
                    throw new Exception('Customer name is required.');
                }
                
                if (empty($customerData['phone'])) {
                    throw new Exception('Phone number is required.');
                }
                
                // Validate email if provided
                if ($customerData['email'] && !filter_var($customerData['email'], FILTER_VALIDATE_EMAIL)) {
                    throw new Exception('Invalid email address.');
                }
                
                // Create the customer
                $customerId = $this->customerModel->createCustomer($customerData);
                
                // Log the creation
                $this->logger->log('customer_created', "Customer '{$customerData['name']}' created", $_SESSION['user_id']);
                
                // Redirect to prevent resubmission
                $this->redirect('/customers?message=' . urlencode('Customer created successfully.'));
                
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
        
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
            'error' => $error,
            'message' => $message,
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
        $step = intval($_GET['step'] ?? 1);
        $sourceId = intval($_GET['source'] ?? $_POST['source_customer'] ?? 0);
        $destinationId = intval($_POST['destination_customer'] ?? 0);
        
        // Handle form submissions
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->validateCSRF();
                
                if (isset($_POST['confirm_merge'])) {
                    // Step 3: Perform the merge
                    $sourceId = intval($_POST['source_customer']);
                    $destinationId = intval($_POST['destination_customer']);
                    
                    if ($sourceId === $destinationId) {
                        throw new Exception('Cannot merge a customer with itself.');
                    }
                    
                    $sourceCustomer = $this->customerModel->findById($sourceId);
                    $destinationCustomer = $this->customerModel->findById($destinationId);
                    
                    if (!$sourceCustomer || !$destinationCustomer) {
                        throw new Exception('One or both customers not found.');
                    }
                    
                    // Perform the merge
                    $this->customerModel->mergeCustomers($destinationId, $sourceId);
                    $this->logger->log('customers_merged', 
                        "Merged customer '{$sourceCustomer['name']}' into '{$destinationCustomer['name']}'", $_SESSION['user_id']);
                    
                    $this->redirect('/customers/view/' . $destinationId . '?message=' . urlencode('Customer merged successfully.'));
                    return;
                }
                
                // Step navigation
                if (isset($_POST['next_step'])) {
                    $step = intval($_POST['next_step']);
                    if ($step === 2 && !$sourceId) {
                        throw new Exception('Please select a source customer.');
                    }
                    if ($step === 3 && (!$sourceId || !$destinationId)) {
                        throw new Exception('Please select both source and destination customers.');
                    }
                }
                
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
        
        // Get customers for dropdowns
        $customers = $this->customerModel->getAllCustomers();
        
        // Get specific customer details if IDs are set
        $sourceCustomer = $sourceId ? $this->customerModel->getCustomerWithWorkOrders($sourceId) : null;
        $destinationCustomer = $destinationId ? $this->customerModel->getCustomerWithWorkOrders($destinationId) : null;
        
        $this->view('customers/merge', [
            'step' => $step,
            'customers' => $customers,
            'sourceId' => $sourceId,
            'destinationId' => $destinationId,
            'sourceCustomer' => $sourceCustomer,
            'destinationCustomer' => $destinationCustomer,
            'error' => $error,
            'message' => $message,
            'csrf_token' => $this->generateCSRF()
        ]);
    }
    
    public function delete($id) {
        $this->requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/customers/view/' . $id);
        }
        
        try {
            $this->validateCSRF();
            
            $customer = $this->customerModel->getCustomerById($id);
            if (!$customer) {
                $this->redirect('/404');
            }
            
            // Check if customer has any work orders
            $workOrders = $this->workOrderModel->getWorkOrdersByCustomerId($id);
            if (!empty($workOrders)) {
                $this->redirect('/customers/view/' . $id . '?error=' . urlencode('Cannot delete customer with existing work orders.'));
            }
            
            // Delete the customer
            $this->customerModel->deleteCustomer($id);
            
            // Log the deletion
            $this->logger->log('customer_deleted', "Customer '{$customer['name']}' deleted", $_SESSION['user_id']);
            
            // Redirect to customers list with success message
            $this->redirect('/customers?message=' . urlencode('Customer deleted successfully.'));
            
        } catch (Exception $e) {
            error_log("Error deleting customer: " . $e->getMessage());
            $this->redirect('/customers/view/' . $id . '?error=' . urlencode('Failed to delete customer.'));
        }
    }
}
