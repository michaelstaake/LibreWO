<?php
class Logger {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function log($action, $details = '', $userId = null, $ip = null, $userAgent = null) {
        $timestamp = date('Y-m-d H:i:s');
        $userId = $userId ?? ($_SESSION['user_id'] ?? null);
        $ip = $ip ?? $this->getClientIP();
        $userAgent = $userAgent ?? ($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown');
        
        try {
            $stmt = $this->db->prepare("
                INSERT INTO activity_logs (user_id, ip_address, user_agent, action, details, created_at) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $userId,
                $ip,
                $userAgent,
                $action,
                $details,
                $timestamp
            ]);
        } catch (Exception $e) {
            // Log to PHP error log if database logging fails
            error_log("Failed to log to database: " . $e->getMessage());
        }
    }
    
    public function cleanupOldLogs($days = 60) {
        try {
            $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));
            $stmt = $this->db->prepare("DELETE FROM activity_logs WHERE created_at < ?");
            $stmt->execute([$cutoffDate]);
            return $stmt->rowCount();
        } catch (Exception $e) {
            error_log("Failed to cleanup old logs: " . $e->getMessage());
            return 0;
        }
    }
    
    public function getLogs($limit = 100, $offset = 0, $search = null, $action = null, $userId = null) {
        try {
            $sql = "
                SELECT al.*, u.username,
                       COALESCE(NULLIF(u.name, ''), u.username) as display_name
                FROM activity_logs al 
                LEFT JOIN users u ON al.user_id = u.id 
                WHERE 1=1
            ";
            $params = [];
            
            if ($search) {
                $sql .= " AND (al.action LIKE ? OR al.details LIKE ? OR u.username LIKE ? OR u.name LIKE ? OR al.ip_address LIKE ?)";
                $searchTerm = "%$search%";
                $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm]);
            }
            
            if ($action) {
                $sql .= " AND al.action = ?";
                $params[] = $action;
            }
            
            if ($userId) {
                $sql .= " AND al.user_id = ?";
                $params[] = $userId;
            }
            
            $sql .= " ORDER BY al.created_at DESC LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Failed to get logs: " . $e->getMessage());
            return [];
        }
    }
    
    public function getLogCount($search = null, $action = null, $userId = null) {
        try {
            $sql = "
                SELECT COUNT(*) as count 
                FROM activity_logs al 
                LEFT JOIN users u ON al.user_id = u.id 
                WHERE 1=1
            ";
            $params = [];
            
            if ($search) {
                $sql .= " AND (al.action LIKE ? OR al.details LIKE ? OR u.username LIKE ? OR u.name LIKE ? OR al.ip_address LIKE ?)";
                $searchTerm = "%$search%";
                $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm]);
            }
            
            if ($action) {
                $sql .= " AND al.action = ?";
                $params[] = $action;
            }
            
            if ($userId) {
                $sql .= " AND al.user_id = ?";
                $params[] = $userId;
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch()['count'];
        } catch (Exception $e) {
            error_log("Failed to get log count: " . $e->getMessage());
            return 0;
        }
    }
    
    public function getUniqueActions() {
        try {
            $stmt = $this->db->query("SELECT DISTINCT action FROM activity_logs ORDER BY action");
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (Exception $e) {
            error_log("Failed to get unique actions: " . $e->getMessage());
            return [];
        }
    }
    
    private function getClientIP() {
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            return $_SERVER['HTTP_CF_CONNECTING_IP'];
        }
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}
