<?php
class Database {
    private $connection;
    private $host;
    private $dbname;
    private $username;
    private $password;
    
    public function __construct() {
        $this->host = DB_HOST;
        $this->dbname = DB_NAME;
        $this->username = DB_USER;
        $this->password = DB_PASS;
    }
    
    public function connect() {
        if ($this->connection === null) {
            try {
                $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->dbname . ";charset=utf8mb4";
                $this->connection = new PDO($dsn, $this->username, $this->password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]);
            } catch (PDOException $e) {
                throw new Exception("Database connection failed: " . $e->getMessage());
            }
        }
        return $this->connection;
    }
    
    public function isInstalled() {
        try {
            $pdo = $this->connect();
            $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function canConnect() {
        try {
            $this->connect();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function prepare($sql) {
        return $this->connect()->prepare($sql);
    }
    
    public function query($sql) {
        return $this->connect()->query($sql);
    }
    
    public function lastInsertId() {
        return $this->connect()->lastInsertId();
    }
    
    public function beginTransaction() {
        return $this->connect()->beginTransaction();
    }
    
    public function commit() {
        return $this->connect()->commit();
    }
    
    public function rollback() {
        return $this->connect()->rollback();
    }
}
