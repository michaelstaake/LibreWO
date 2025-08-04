<?php
require_once 'core/Model.php';

class User extends Model {
    protected $table = 'users';
    
    public function findById($id) {
        return parent::findById($id);
    }
    
    public function updateUser($id, $data) {
        return parent::update($id, $data);
    }
    
    public function updateLastLogin($userId) {
        return $this->updateUser($userId, ['last_login' => date('Y-m-d H:i:s')]);
    }
    
    public function getTechnicians() {
        return $this->findWhere("user_group IN ('Admin', 'Technician')", [], "id, username, name, user_group");
    }
    
    public function countUsers() {
        return parent::count();
    }
    
    public function findByUsername($username) {
        return $this->findOneWhere('username = ? AND is_active = 1', [$username]);
    }
    
    public function findByEmail($email) {
        return $this->findOneWhere('email = ?', [$email]);
    }
    
    public function findByResetToken($token) {
        // First, find the user with the token regardless of expiration
        $user = $this->findOneWhere('reset_token = ?', [$token]);
        
        if (!$user) {
            return null; // No user found with this token
        }
        
        // Check if the token has expired
        $now = date('Y-m-d H:i:s');
        if ($user['reset_expires'] && $user['reset_expires'] > $now) {
            return $user; // Token is valid and not expired
        }
        
        return null; // Token has expired
    }
    
    public function findByResetTokenDebug($token) {
        return $this->findOneWhere('reset_token = ?', [$token]);
    }
    
    public function createUser($data) {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->create($data);
    }
    
    public function updatePassword($userId, $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        return $this->updateUser($userId, ['password' => $hashedPassword]);
    }
    
    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    public function recordLogin($userId, $ip) {
        $stmt = $this->db->prepare("
            INSERT INTO user_logins (user_id, ip_address, login_time) 
            VALUES (?, ?, NOW())
        ");
        return $stmt->execute([$userId, $ip]);
    }
    
    public function hasRecentLogin($userId, $ip, $days = 30) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count 
            FROM user_logins 
            WHERE user_id = ? AND ip_address = ? AND login_time > DATE_SUB(NOW(), INTERVAL ? DAY)
        ");
        $stmt->execute([$userId, $ip, $days]);
        return $stmt->fetch()['count'] > 0;
    }
    
    public function store2FACode($userId, $code) {
        $stmt = $this->db->prepare("
            INSERT INTO two_factor_codes (user_id, code, expires_at) 
            VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 15 MINUTE))
            ON DUPLICATE KEY UPDATE code = ?, expires_at = DATE_ADD(NOW(), INTERVAL 15 MINUTE)
        ");
        return $stmt->execute([$userId, $code, $code]);
    }
    
    public function verify2FACode($userId, $code) {
        $stmt = $this->db->prepare("
            SELECT * FROM two_factor_codes 
            WHERE user_id = ? AND code = ? AND expires_at > NOW()
        ");
        $stmt->execute([$userId, $code]);
        $result = $stmt->fetch();
        
        if ($result) {
            // Delete the code after successful verification
            $this->db->prepare("DELETE FROM two_factor_codes WHERE user_id = ?")->execute([$userId]);
            return true;
        }
        return false;
    }
    
    public function getLoginAttempts($ip) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count 
            FROM login_attempts 
            WHERE ip_address = ? AND success = 0 AND attempted_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)
        ");
        $stmt->execute([$ip]);
        return $stmt->fetch()['count'];
    }
    
    public function recordLoginAttempt($ip, $username = null, $success = false) {
        $stmt = $this->db->prepare("
            INSERT INTO login_attempts (ip_address, username, success, attempted_at) 
            VALUES (?, ?, ?, NOW())
        ");
        return $stmt->execute([$ip, $username, $success ? 1 : 0]);
    }
    
    public function cleanupOldLogins() {
        // Clean up login records older than 30 days
        $this->db->query("DELETE FROM user_logins WHERE login_time < DATE_SUB(NOW(), INTERVAL 30 DAY)");
        
        // Clean up old login attempts
        $this->db->query("DELETE FROM login_attempts WHERE attempted_at < DATE_SUB(NOW(), INTERVAL 1 DAY)");
        
        // Clean up expired 2FA codes
        $this->db->query("DELETE FROM two_factor_codes WHERE expires_at < NOW()");
    }
    
    public function isUserActive($userId) {
        $user = $this->findOneWhere('id = ?', [$userId]);
        return $user && $user['is_active'] == 1;
    }
    
    public function getDisplayName($user) {
        if (is_array($user)) {
            return !empty($user['name']) ? $user['name'] : $user['username'];
        }
        return !empty($user->name) ? $user->name : $user->username;
    }
    
    public function getAllUsers($limit = null, $offset = 0) {
        $sql = "SELECT id, username, name, email, user_group, is_active, created_at, last_login FROM users";
        if ($limit) {
            $sql .= " LIMIT $limit OFFSET $offset";
        }
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
}
