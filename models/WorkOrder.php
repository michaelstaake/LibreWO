<?php
require_once 'core/Model.php';

class WorkOrder extends Model {
    protected $table = 'work_orders';
    
    public function countWorkOrders($status = null, $priority = null, $search = null) {
        $sql = "SELECT COUNT(*) as count FROM work_orders wo LEFT JOIN customers c ON wo.customer_id = c.id WHERE 1=1";
        $params = [];
        
        if ($status && $status !== 'All') {
            if ($status === 'Priority') {
                $sql .= " AND wo.priority IN ('High', 'Critical')";
            } else {
                $sql .= " AND wo.status = ?";
                $params[] = $status;
            }
        }
        
        if ($priority && $priority !== 'All') {
            $sql .= " AND wo.priority = ?";
            $params[] = $priority;
        }
        
        if ($search) {
            $sql .= " AND (c.name LIKE ? OR c.company LIKE ? OR wo.computer LIKE ? OR wo.model LIKE ? OR wo.description LIKE ?)";
            $searchTerm = "%$search%";
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch()['count'];
    }
    
    public function getWorkOrders($status = null, $priority = null, $search = null, $limit = 10, $offset = 0) {
        $sql = "
            SELECT wo.*, c.name as customer_name, c.company as customer_company,
                   u.username as technician_username, u.name as technician_name,
                   COALESCE(NULLIF(u.name, ''), u.username) as technician_display_name
            FROM work_orders wo
            LEFT JOIN customers c ON wo.customer_id = c.id
            LEFT JOIN users u ON wo.assigned_to = u.id
            WHERE 1=1
        ";
        
        $params = [];
        
        if ($status && $status !== 'All') {
            if ($status === 'Priority') {
                $sql .= " AND wo.priority = 'Priority'";
            } else {
                $sql .= " AND wo.status = ?";
                $params[] = $status;
            }
        }
        
        if ($search) {
            $sql .= " AND (wo.work_order_number LIKE ? OR c.name LIKE ? OR c.phone LIKE ? OR c.company LIKE ? OR c.email LIKE ?)";
            $searchTerm = "%$search%";
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        }
        
        $sql .= " ORDER BY wo.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function getWorkOrderById($id) {
        $stmt = $this->db->prepare("
            SELECT wo.*, c.name as customer_name, c.email as customer_email, 
                   c.phone as customer_phone, c.company as customer_company,
                   u.username as technician_username, u.name as technician_name,
                   COALESCE(NULLIF(u.name, ''), u.username) as technician_display_name,
                   creator.username as creator_username, creator.name as creator_name,
                   COALESCE(NULLIF(creator.name, ''), creator.username) as creator_display_name
            FROM work_orders wo
            LEFT JOIN customers c ON wo.customer_id = c.id
            LEFT JOIN users u ON wo.assigned_to = u.id
            LEFT JOIN users creator ON wo.created_by = creator.id
            WHERE wo.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function createWorkOrder($data) {
        $data['work_order_number'] = $this->generateWorkOrderNumber();
        $data['status'] = 'Open';
        $data['created_at'] = date('Y-m-d H:i:s');
        
        $workOrderId = $this->create($data);
        
        // Log the creation
        $this->logWorkOrderAction($workOrderId, 'created', 'Work order created');
        
        return $workOrderId;
    }
    
    public function updateWorkOrder($id, $data) {
        $oldData = $this->findById($id);
        $result = $this->update($id, $data);
        
        if ($result) {
            // Log changes
            foreach ($data as $field => $newValue) {
                if (isset($oldData[$field]) && $oldData[$field] != $newValue) {
                    $this->logWorkOrderAction($id, 'updated', 
                        "Changed $field from '{$oldData[$field]}' to '$newValue'");
                }
            }
        }
        
        return $result;
    }
    
    public function getWorkOrdersByCustomer($customerId) {
        return $this->findWhere('customer_id = ? ORDER BY created_at DESC', [$customerId]);
    }
    
    public function getWorkOrdersByTechnician($technicianId) {
        $stmt = $this->db->prepare("
            SELECT wo.*, c.name as customer_name, wo.computer as device_type, wo.model as device_model
            FROM work_orders wo
            LEFT JOIN customers c ON wo.customer_id = c.id
            WHERE wo.assigned_to = ? AND wo.status NOT IN (?, ?) 
            ORDER BY wo.created_at DESC
        ");
        $stmt->execute([$technicianId, 'Closed', 'Picked Up']);
        return $stmt->fetchAll();
    }
    
    public function getStatusCounts() {
        $stmt = $this->db->query("
            SELECT status, COUNT(*) as count 
            FROM work_orders 
            GROUP BY status
        ");
        $results = $stmt->fetchAll();
        
        $counts = [
            'Open' => 0,
            'In Progress' => 0,
            'Awaiting Parts' => 0,
            'Closed' => 0,
            'Picked Up' => 0
        ];
        
        foreach ($results as $row) {
            $counts[$row['status']] = $row['count'];
        }
        
        return $counts;
    }
    
    public function getPriorityCount() {
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM work_orders WHERE priority = 'Priority'");
        return $stmt->fetch()['count'];
    }
    
    public function getAssignedCount($userId) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count 
            FROM work_orders 
            WHERE assigned_to = ? AND status NOT IN ('Closed', 'Picked Up')
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch()['count'];
    }
    
    public function getTotalCount() {
        return $this->count();
    }
    
    private function generateWorkOrderNumber() {
        $prefix = 'WO';
        $year = date('Y');
        
        // Get the highest number for this year
        $stmt = $this->db->prepare("
            SELECT work_order_number 
            FROM work_orders 
            WHERE work_order_number LIKE ? 
            ORDER BY work_order_number DESC 
            LIMIT 1
        ");
        $stmt->execute(["$prefix$year%"]);
        $lastNumber = $stmt->fetch();
        
        if ($lastNumber) {
            $number = intval(substr($lastNumber['work_order_number'], -4)) + 1;
        } else {
            $number = 1;
        }
        
        return $prefix . $year . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
    
    public function logWorkOrderAction($workOrderId, $action, $details) {
        $stmt = $this->db->prepare("
            INSERT INTO work_order_logs (work_order_id, user_id, action, details, created_at) 
            VALUES (?, ?, ?, ?, NOW())
        ");
        return $stmt->execute([
            $workOrderId, 
            $_SESSION['user_id'] ?? null, 
            $action, 
            $details
        ]);
    }
    
    public function getWorkOrderLogs($workOrderId) {
        $stmt = $this->db->prepare("
            SELECT wol.*, u.username 
            FROM work_order_logs wol
            LEFT JOIN users u ON wol.user_id = u.id
            WHERE wol.work_order_id = ?
            ORDER BY wol.created_at DESC
        ");
        $stmt->execute([$workOrderId]);
        return $stmt->fetchAll();
    }
    
    public function getWorkOrdersByCustomerId($customerId) {
        $stmt = $this->db->prepare("SELECT * FROM work_orders WHERE customer_id = ?");
        $stmt->execute([$customerId]);
        return $stmt->fetchAll();
    }
    
    public function deleteWorkOrder($id) {
        try {
            $this->db->beginTransaction();
            
            // Delete related logs first
            $stmt = $this->db->prepare("DELETE FROM work_order_logs WHERE work_order_id = ?");
            $stmt->execute([$id]);
            
            // Delete the work order
            $stmt = $this->db->prepare("DELETE FROM work_orders WHERE id = ?");
            $result = $stmt->execute([$id]);
            
            $this->db->commit();
            return $result;
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
}
