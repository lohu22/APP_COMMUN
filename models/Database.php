<?php
// Configuration de la base de données
// Database configuration
class Database {
    private $host = "localhost";
    private $db_name = "sensor_db";
    private $username = "root";
    private $password = "root";
    public $conn;

    // Obtenir la connexion à la base de données
    // Get database connection
    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Erreur de connexion: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?> 