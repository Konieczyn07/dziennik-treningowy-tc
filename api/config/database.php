<?php

class Database {
	public $host = "localhost";
	public $db_name = "training_journal";
	public $username = "root";
	public $password = "";
	public $conn;
	
	public function getConn() {
		$this->conn = null;
		
		try{
			$this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
			$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}catch(PDOException $e){
			echo "Błąd połączenia: " . $e->getMessage();
		}
		return $this->conn;
	}
}

?>