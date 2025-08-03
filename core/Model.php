<?php
class Model {
    protected $db;
    protected $table;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    protected function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    protected function findAll($limit = null, $offset = 0) {
        $sql = "SELECT * FROM {$this->table}";
        if ($limit) {
            $sql .= " LIMIT $limit OFFSET $offset";
        }
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    protected function count($where = null, $params = []) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        if ($where) {
            $sql .= " WHERE " . $where;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch()['count'];
    }
    
    protected function create($data) {
        $fields = array_keys($data);
        $placeholders = str_repeat('?,', count($fields) - 1) . '?';
        
        $sql = "INSERT INTO {$this->table} (" . implode(',', $fields) . ") VALUES ($placeholders)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_values($data));
        
        return $this->db->lastInsertId();
    }
    
    protected function update($id, $data) {
        $fields = array_keys($data);
        $setClause = implode(' = ?, ', $fields) . ' = ?';
        
        $sql = "UPDATE {$this->table} SET $setClause WHERE id = ?";
        $params = array_merge(array_values($data), [$id]);
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
    
    protected function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    protected function findWhere($where, $params = []) {
        $sql = "SELECT * FROM {$this->table} WHERE " . $where;
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    protected function findOneWhere($where, $params = []) {
        $sql = "SELECT * FROM {$this->table} WHERE " . $where . " LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }
}
