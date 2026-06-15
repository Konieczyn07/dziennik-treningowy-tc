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
        $query = "SELECT TOP 1 id, username, email, password
                  FROM users
                  WHERE username = ?";
        
        $this->username = htmlspecialchars(strip_tags($this->username));
        $params = array($this->username);
        $stmt = sqlsrv_query($this->conn, $query, $params);
        
        if ($stmt === false) {
            return false;
        }
        $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC);
        
        if($row && password_verify($this->password, $row['password'])) {
            $this->id = $row['id'];
            $this->username = $row['username'];
            $this->email = $row['email'];

            if(session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['user_id'] = $this->id;
            $_SESSION['username'] = $this->username;
            $_SESSION['email'] = $this->email;
            $_SESSION['logged_in'] = true;
            
            return true;
        }
        return false;
    }

    private function userExists() {
        $query = "SELECT id FROM users 
                  WHERE username = ? OR email = ?";
        
        $params = array($this->username, $this->email);
        $stmt = sqlsrv_query($this->conn, $query, $params);
        if ($stmt === false) {
            return false;
        }
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        return !empty($row);
    }
}
?>