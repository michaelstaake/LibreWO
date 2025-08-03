<?php
require_once 'core/Controller.php';
require_once 'models/User.php';
require_once 'models/WorkOrder.php';

class UserController extends Controller {
    private $userModel;
    private $workOrderModel;
    
    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
        $this->workOrderModel = new WorkOrder();
    }
    
    public function index() {
        $this->requireAdmin();
        
        $page = max(1, intval($_GET['page'] ?? 1));
        $limit = PAGINATION_LIMIT;
        $offset = ($page - 1) * $limit;
        
        $users = $this->userModel->getAllUsers($limit, $offset);
        $totalCount = $this->userModel->countUsers();
        $totalPages = ceil($totalCount / $limit);
        
        $error = '';
        $message = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->validateCSRF();
                
                if (isset($_POST['create_user'])) {
                    $userData = [
                        'username' => $this->sanitizeInput($_POST['username']),
                        'name' => $this->sanitizeInput($_POST['name'] ?? ''),
                        'email' => $this->sanitizeInput($_POST['email']),
                        'password' => $_POST['password'],
                        'user_group' => $_POST['user_group']
                    ];
                    
                    // Validation
                    if (empty($userData['username']) || empty($userData['email']) || empty($userData['password'])) {
                        throw new Exception('All required fields must be filled.');
                    }
                    
                    if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
                        throw new Exception('Invalid email address.');
                    }
                    
                    if (strlen($userData['password']) < 8) {
                        throw new Exception('Password must be at least 8 characters long.');
                    }
                    
                    if (!in_array($userData['user_group'], ['Admin', 'Technician', 'Limited'])) {
                        throw new Exception('Invalid user group.');
                    }
                    
                    // Check if username or email already exists
                    if ($this->userModel->findByUsername($userData['username'])) {
                        throw new Exception('Username already exists.');
                    }
                    
                    if ($this->userModel->findByEmail($userData['email'])) {
                        throw new Exception('Email already exists.');
                    }
                    
                    $userId = $this->userModel->createUser($userData);
                    $this->logger->log('user_created', "User {$userData['username']} created", $_SESSION['user_id']);
                    
                    $message = 'User created successfully.';
                    
                    // Refresh users list
                    $users = $this->userModel->getAllUsers($limit, $offset);
                    
                } elseif (isset($_POST['is_active']) && isset($_POST['user_id'])) {
                    // Handle user activation/deactivation
                    $userId = intval($_POST['user_id']);
                    $isActive = intval($_POST['is_active']);
                    
                    $user = $this->userModel->findById($userId);
                    if (!$user) {
                        throw new Exception('User not found.');
                    }
                    
                    if ($user['id'] === $_SESSION['user_id']) {
                        throw new Exception('Cannot deactivate your own account.');
                    }
                    
                    $this->userModel->updateUser($userId, ['is_active' => $isActive]);
                    $this->logger->log('user_status_changed', "User {$user['username']} " . ($isActive ? 'activated' : 'deactivated'), $_SESSION['user_id']);
                    
                    $message = 'User status updated successfully.';
                    
                    // Refresh users list
                    $users = $this->userModel->getAllUsers($limit, $offset);
                    
                } elseif (isset($_POST['delete_user'])) {
                    $userId = intval($_POST['user_id']);
                    $user = $this->userModel->findById($userId);
                    
                    if (!$user) {
                        throw new Exception('User not found.');
                    }
                    
                    if ($user['id'] === $_SESSION['user_id']) {
                        throw new Exception('Cannot delete your own account.');
                    }
                    
                    // Check if user has work orders assigned
                    $assignedWorkOrders = $this->workOrderModel->getWorkOrdersByTechnician($userId);
                    if (!empty($assignedWorkOrders)) {
                        throw new Exception('Cannot delete user with assigned work orders.');
                    }
                    
                    $this->userModel->delete($userId);
                    $this->logger->log('user_deleted', "User {$user['username']} deleted", $_SESSION['user_id']);
                    
                    $message = 'User deleted successfully.';
                    
                    // Refresh users list
                    $users = $this->userModel->getAllUsers($limit, $offset);
                }
                
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
        
        $this->view('users/index', [
            'users' => $users,
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
        
        $user = $this->userModel->findById($id);
        if (!$user) {
            $this->redirect('/404');
        }
        
        // Get user's work orders
        $workOrders = $this->workOrderModel->getWorkOrdersByTechnician($id);
        
        $error = '';
        $message = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['user_group'] === 'Admin') {
            try {
                $this->validateCSRF();
                
                // Handle password change separately
                if (isset($_POST['password_change'])) {
                    if (empty($_POST['new_password'])) {
                        throw new Exception('New password is required.');
                    }
                    
                    if (strlen($_POST['new_password']) < 8) {
                        throw new Exception('Password must be at least 8 characters long.');
                    }
                    
                    $this->userModel->updatePassword($id, $_POST['new_password']);
                    $this->logger->log('user_password_changed', "Password changed for user {$user['username']}", $_SESSION['user_id']);
                    
                    $message = 'Password updated successfully.';
                } else {
                    // Handle regular user updates (excluding password)
                    $updateData = [
                        'name' => $this->sanitizeInput($_POST['name'] ?? ''),
                        'email' => $this->sanitizeInput($_POST['email']),
                        'user_group' => $_POST['user_group']
                    ];
                    
                    // Prevent admins from changing their own user group
                    if ($id == $_SESSION['user_id'] && $updateData['user_group'] !== $user['user_group']) {
                        throw new Exception('Cannot change your own user group.');
                    }
                    
                    if (!filter_var($updateData['email'], FILTER_VALIDATE_EMAIL)) {
                        throw new Exception('Invalid email address.');
                    }
                    
                    if (!in_array($updateData['user_group'], ['Admin', 'Technician', 'Limited'])) {
                        throw new Exception('Invalid user group.');
                    }
                    
                    // Check if email already exists (excluding current user)
                    $existingUser = $this->userModel->findByEmail($updateData['email']);
                    if ($existingUser && $existingUser['id'] != $id) {
                        throw new Exception('Email already exists.');
                    }
                    
                    $this->userModel->updateUser($id, $updateData);
                    $this->logger->log('user_updated', "User {$user['username']} updated", $_SESSION['user_id']);
                    
                    $message = 'User updated successfully.';
                    $user = array_merge($user, $updateData);
                }
                
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
        
        $this->view('users/view', [
            'user' => $user,
            'workOrders' => $workOrders,
            'error' => $error,
            'message' => $message,
            'csrf_token' => $this->generateCSRF(),
            'canEdit' => $_SESSION['user_group'] === 'Admin'
        ]);
    }
}
