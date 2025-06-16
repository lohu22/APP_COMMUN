<?php
// Modèle pour les données des capteurs
// Model for sensor data
class SensorData {
    // Propriétés de la base de données
    // Database properties
    private $conn;
    private $table_name = "sensor_data";

    // Propriétés de l'objet
    // Object properties
    public $id;
    public $humidity;
    public $temperature;
    public $timestamp;

    // Constructeur avec connexion à la base de données
    // Constructor with database connection
    public function __construct($db) {
        $this->conn = $db;
    }

    // Créer une nouvelle entrée de données
    // Create new data entry
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                (HUM, TEMP, Temps)
                VALUES
                (:humidity, :temperature, :timestamp)";

        $stmt = $this->conn->prepare($query);

        // Nettoyer les données
        // Sanitize data
        $this->humidity = htmlspecialchars(strip_tags($this->humidity));
        $this->temperature = htmlspecialchars(strip_tags($this->temperature));
        $this->timestamp = htmlspecialchars(strip_tags($this->timestamp));

        // Lier les valeurs
        // Bind values
        $stmt->bindParam(":humidity", $this->humidity);
        $stmt->bindParam(":temperature", $this->temperature);
        $stmt->bindParam(":timestamp", $this->timestamp);

        // Exécuter la requête
        // Execute query
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Lire les données avec limite
    // Read data with limit
    public function read($limit = null) {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY Temps DESC";
        if ($limit !== null && is_numeric($limit)) {
            $query .= " LIMIT " . (int)$limit;
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
?> 