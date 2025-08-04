<?php
class LogsController extends Controller {
    
    public function index() {
        $this->requireAdmin();
        
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $limit = 50;
        $offset = ($page - 1) * $limit;
        
        $search = $_GET['search'] ?? '';
        $action = $_GET['action'] ?? '';
        $userId = $_GET['user_id'] ?? '';
        
        $logs = $this->logger->getLogs($limit, $offset, $search, $action, $userId);
        $totalLogs = $this->logger->getLogCount($search, $action, $userId);
        $totalPages = ceil($totalLogs / $limit);
        
        // Get unique actions for filter dropdown
        $uniqueActions = $this->logger->getUniqueActions();
        
        // Get all users for filter dropdown
        require_once 'models/User.php';
        $userModel = new User();
        $users = $userModel->getAllUsers();
        
        $this->view('logs/index', [
            'logs' => $logs,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalLogs' => $totalLogs,
            'search' => $search,
            'action' => $action,
            'userId' => $userId,
            'uniqueActions' => $uniqueActions,
            'users' => $users
        ]);
    }
}
