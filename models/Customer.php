<?php
require_once 'core/Model.php';

class Customer extends Model {
    protected $table = 'customers';
    
    public function findById($id) {
        return parent::findById($id);
    }
    
    public function updateCustomer($id, $data) {
        return parent::update($id, $data);
    }
    
    public function searchCustomers($query) {
        $stmt = $this->db->prepare("
            SELECT * FROM customers 
            WHERE name LIKE ? OR company LIKE ? OR email LIKE ? OR phone LIKE ?
            ORDER BY name ASC
        ");
        $searchTerm = "%$query%";
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        return $stmt->fetchAll();
    }
    
    public function createCustomer($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->create($data);
    }
    
    public function getCustomerWithWorkOrders($customerId) {
        $customer = $this->findById($customerId);
        if (!$customer) {
            return null;
        }
        
        // Get work orders for this customer
        $stmt = $this->db->prepare("
            SELECT wo.*, u.username as technician_name
            FROM work_orders wo
            LEFT JOIN users u ON wo.assigned_to = u.id
            WHERE wo.customer_id = ?
            ORDER BY wo.created_at DESC
        ");
        $stmt->execute([$customerId]);
        $customer['work_orders'] = $stmt->fetchAll();
        
        return $customer;
    }
    
    public function getAllCustomers($limit = null, $offset = 0, $search = null) {
        $sql = "
            SELECT c.*, 
                   COUNT(wo.id) as work_order_count
            FROM customers c
            LEFT JOIN work_orders wo ON c.id = wo.customer_id
        ";
        $params = [];
        
        if ($search) {
            $sql .= " WHERE c.name LIKE ? OR c.company LIKE ? OR c.email LIKE ? OR c.phone LIKE ?";
            $searchTerm = "%$search%";
            $params = [$searchTerm, $searchTerm, $searchTerm, $searchTerm];
        }
        
        $sql .= " GROUP BY c.id ORDER BY c.name ASC";
        
        if ($limit) {
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function getCustomerCount($search = null) {
        $sql = "SELECT COUNT(*) as count FROM customers";
        $params = [];
        
        if ($search) {
            $sql .= " WHERE name LIKE ? OR company LIKE ? OR email LIKE ? OR phone LIKE ?";
            $searchTerm = "%$search%";
            $params = [$searchTerm, $searchTerm, $searchTerm, $searchTerm];
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch()['count'];
    }
    
    public function canDeleteCustomer($customerId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM work_orders WHERE customer_id = ?");
        $stmt->execute([$customerId]);
        return $stmt->fetch()['count'] == 0;
    }
    
    public function mergeCustomers($keepId, $mergeId) {
        try {
            $this->db->beginTransaction();
            
            // Update all work orders to use the kept customer
            $stmt = $this->db->prepare("UPDATE work_orders SET customer_id = ? WHERE customer_id = ?");
            $stmt->execute([$keepId, $mergeId]);
            
            // Delete the merged customer
            $this->delete($mergeId);
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
}
