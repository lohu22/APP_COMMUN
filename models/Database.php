<?php
// Configuration de la base de données PostgreSQL
// PostgreSQL database configuration
class Database {
    private $host = "app.garageisep.com";
    private $port = "5408";
    private $db_name = "app_db";
    private $username = "app_user";
    private $password = "appg8";
    public $conn;

    // Obtenir la connexion à la base de données
    // Get database connection
    public function getConnection() {
        $this->conn = null;

        try {
            // Check if PostgreSQL PDO driver is available
            if (!extension_loaded('pdo_pgsql')) {
                throw new Exception("L'extension PDO PostgreSQL n'est pas installée");
            }

            $dsn = "pgsql:host=" . $this->host . 
                   ";port=" . $this->port . 
                   ";dbname=" . $this->db_name . 
                   ";user=" . $this->username . 
                   ";password=" . $this->password;

            $this->conn = new PDO($dsn);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Test the connection
            $this->conn->query('SELECT 1');
            
        } catch(PDOException $e) {
            echo "Erreur de connexion PDO: " . $e->getMessage() . "\n";
            echo "DSN: pgsql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name . "\n";
            error_log("Database Connection Error: " . $e->getMessage());
            throw $e;
        } catch(Exception $e) {
            echo "Erreur: " . $e->getMessage() . "\n";
            error_log("General Error: " . $e->getMessage());
            throw $e;
        }

        return $this->conn;
    }
}
?> 