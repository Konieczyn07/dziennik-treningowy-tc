<?php

class Database {
	public $username;
	public $password;
	
	public function __construct() {
        $this->username = getenv("DATABASE_USERNAME");
        $this->password = getenv("DATABASE_PASSWORD");
    }

	public function getConn() {
		

		$connectionInfo = array("UID" => $this->$username, "pwd" => $this->$password, "Database" => "dzienniktreningowy-db", "LoginTimeout" => 30, "Encrypt" => 1, "TrustServerCertificate" => 0);
		$serverName = "tcp:dzienniktreningowy-sqldb.database.windows.net,1433";
		$conn = sqlsrv_connect($serverName, $connectionInfo);

		return $conn;
	}
}

?>