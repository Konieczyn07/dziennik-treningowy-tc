<?php
class User {
    private $conn;
    private $table = "users";

    public $id;
    public $username;
    public $email;
    public $password;

    public function __construct($db) {
        $this->conn = $db;
    }
	
    public function register() {
        if($this->userExists()) {
            return false;
        }
        
        $query = 'INSERT INTO users (username, email, password) VALUES (?, ?, ?)';
        
        $hashed_password = password_hash($this->password, PASSWORD_DEFAULT);
        
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        
        $params = array($this->username, $this->email, $hashed_password);
        $stmt = sqlsrv_query($this->conn, $query, $params);
        if($stmt) {
            return true;
        }
        return false;
    }

    public function login() {
        $query = "SELECT id as id,
                         username as username,
                         email as email, 
                         password as password
                  FROM users 
                  WHERE username = ? OR email = ? 
                  LIMIT 1";
        
        $this->username = htmlspecialchars(strip_tags($this->username));
        $params = array($this->username, $this->username);
        $stmt = sqlsrv_query($this->conn, $query, $params);
        row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC);
        
        if($row && password_verify($this->password, $row['password'])) {
            $this->id = $row['id'];
            $this->username = $row['username'];
            $this->email = $row['email'];
            return true;
        }
        return false;
    }

    private function userExists() {
        $query = "SELECT id FROM users 
                  WHERE username = ? OR email = ? 
                  LIMIT 1";
        
        $params = array($this->username, $this->email);
        $stmt = sqlsrv_query($this->conn, $query, $params);
        
        return $stmt;
    }
}
?>