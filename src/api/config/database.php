<?php
class Database {
    private $host;
    private $port;
    private $db_name;
    private $username;
    private $password;
    private $ssl_mode;
    private $ssl_ca_path;
    public $conn;

    public function __construct() {
        $this->host = getenv('DB_HOST') ?: 'localhost';
        $this->port = getenv('DB_PORT') ?: '5432';
        $this->db_name = getenv('DB_DATABASE') ?: 'training_journal';
        $this->username = getenv('DB_USERNAME') ?: 'postgres';
        $this->password = getenv('DB_PASSWORD') ?: '';
        $this->ssl_mode = getenv('DB_SSL_MODE') ?: 'require';
        $this->ssl_ca_path = getenv('DB_SSL_CA') ?: '/etc/ssl/certs/BaltimoreCyberTrustRoot.crt.pem';
    }

    public function getConnection() {
        $this->conn = null;
        
        $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->db_name};";
        
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
			];
        
        if ($this->ssl_mode !== 'disable') {
            $dsn .= "sslmode={$this->ssl_mode}";
            if ($this->ssl_ca_path && file_exists($this->ssl_ca_path)) {
                $dsn .= "&sslrootcert={$this->ssl_ca_path}";
            }
        }
        
        try {
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            
            $this->conn->exec("SET client_encoding TO 'UTF8'");
            $this->conn->exec("SET timezone = 'UTC'");
            
        } catch(PDOException $e) {
            error_log("PostgreSQL connection failed: " . $e->getMessage());
            throw $e;
        }
        
        return $this->conn;
    }
    
    public function testConnection() {
        try {
            $conn = $this->getConnection();
            $stmt = $conn->query("SELECT version()");
            $version = $stmt->fetchColumn();
            return ['status' => 'success', 'version' => $version];
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
?>