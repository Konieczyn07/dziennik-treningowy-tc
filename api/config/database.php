<?php

class Database {
	public $username = getenv("DATABASE_USERNAME");
	public $password = getenv("DATABASE_PASSWORD");
	public $conn;
	
	public function getConn() {
		$this->conn = null;
		

		try {
			$this->conn = new PDO("sqlsrv:server = tcp:dzienniktreningowy-sqldb.database.windows.net,1433; Database = dzienniktreningowy-db", $this->username, $this->password);
			$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		catch (PDOException $e) {
			print("Error connecting to SQL Server.");
			die(print_r($e));
		}

		return $this->conn;
	}
}

?>