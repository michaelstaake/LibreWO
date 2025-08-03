<?php
class Logger {
    private $logFile;
    
    public function __construct() {
        $this->logFile = LOG_PATH . '/system.log';
    }
    
    public function log($action, $details = '', $userId = null, $ip = null, $userAgent = null) {
        $timestamp = date('Y-m-d H:i:s');
        $userId = $userId ?? ($_SESSION['user_id'] ?? 'Anonymous');
        $ip = $ip ?? $this->getClientIP();
        $userAgent = $userAgent ?? ($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown');
        
        $logEntry = [
            'timestamp' => $timestamp,
            'user_id' => $userId,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'action' => $action,
            'details' => $details
        ];
        
        $logLine = json_encode($logEntry) . PHP_EOL;
        
        // Append to log file
        file_put_contents($this->logFile, $logLine, FILE_APPEND | LOCK_EX);
        
        // Also store in database for admin viewing
        $this->logToDatabase($logEntry);
    }
    
    private function logToDatabase($logEntry) {
        try {
            $db = new Database();
            $stmt = $db->prepare("
                INSERT INTO activity_logs (user_id, ip_address, user_agent, action, details, created_at) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $logEntry['user_id'] === 'Anonymous' ? null : $logEntry['user_id'],
                $logEntry['ip_address'],
                $logEntry['user_agent'],
                $logEntry['action'],
                $logEntry['details'],
                $logEntry['timestamp']
            ]);
        } catch (Exception $e) {
            // If database logging fails, continue with file logging
            error_log("Failed to log to database: " . $e->getMessage());
        }
    }
    
    public function getLogs($limit = 100, $offset = 0) {
        try {
            $db = new Database();
            $stmt = $db->prepare("
                SELECT al.*, u.username 
                FROM activity_logs al 
                LEFT JOIN users u ON al.user_id = u.id 
                ORDER BY al.created_at DESC 
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$limit, $offset]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    public function getLogCount() {
        try {
            $db = new Database();
            $stmt = $db->query("SELECT COUNT(*) as count FROM activity_logs");
            return $stmt->fetch()['count'];
        } catch (Exception $e) {
            return 0;
        }
    }
    
    private function getClientIP() {
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            return $_SERVER['HTTP_CF_CONNECTING_IP'];
        }
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}
