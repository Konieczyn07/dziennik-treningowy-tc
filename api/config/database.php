<?php

class Database {
	public $username = getenv("DATABASE_USERNAME");
	public $password = getenv("DATABASE_PASSWORD");
	
	public function getConn() {
		try{
			$connectionInfo = array("UID" => $this->username, "pwd" => $this->password, "Database" => "dzienniktreningowy-db", "LoginTimeout" => 30, "Encrypt" => 1, "TrustServerCertificate" => 0);
			$serverName = "tcp:dzienniktreningowy-sqldb.database.windows.net,1433";
			$conn = sqlsrv_connect($serverName, $connectionInfo);

			return $conn;
		}catch(error){
		
			$this->username = "rootadmin";
			$this->password = "=fX^anHD~W4a2#.D3ZP1";

			$connectionInfo = array("UID" => $this->username, "pwd" => $this->password, "Database" => "dzienniktreningowy-db", "LoginTimeout" => 30, "Encrypt" => 1, "TrustServerCertificate" => 0);
			$serverName = "tcp:dzienniktreningowy-sqldb.database.windows.net,1433";
			$conn = sqlsrv_connect($serverName, $connectionInfo);

			return $conn;
		}
	}
}

?>