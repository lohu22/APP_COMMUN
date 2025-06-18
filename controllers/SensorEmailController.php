<?php
// Contrôleur pour l'envoi des données des capteurs par email
// Controller for sending sensor data via email
require_once '../models/Database.php';
require_once '../models/SensorData.php';

class SensorEmailController {
    private $db;
    private $sensorData;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->sensorData = new SensorData($this->db);
    }

    // Récupérer les dernières données des capteurs
    // Get latest sensor data
    private function getLatestSensorData($limit = 10) {
        $stmt = $this->sensorData->read($limit);
        $data = array();
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // 添加调试日志
            error_log("Raw data from DB: " . print_r($row, true));
            
            // 检查列名是否存在
            $humidity = isset($row['hum']) ? $row['hum'] : (isset($row['HUM']) ? $row['HUM'] : 0);
            $temperature = isset($row['temp']) ? $row['temp'] : (isset($row['TEMP']) ? $row['TEMP'] : 0);
            $timestamp = isset($row['temps']) ? $row['temps'] : (isset($row['Temps']) ? $row['Temps'] : date('Y-m-d H:i:s'));
            
            $data[] = array(
                "humidity" => floatval($humidity),
                "temperature" => floatval($temperature),
                "timestamp" => $timestamp
            );
            // 添加调试日志
            error_log("Processed data: " . print_r(end($data), true));
        }
        return $data;
    }

    // Envoyer les données par email
    // Send data via email
    public function sendSensorDataEmail($email, $limit = 10) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => 'Adresse email invalide'
            ];
        }

        $sensorData = $this->getLatestSensorData($limit);
        // 添加调试日志
        error_log("Data to be sent in email: " . print_r($sensorData, true));
        
        if (empty($sensorData)) {
            return [
                'success' => false,
                'message' => 'Aucune donnée de capteur disponible'
            ];
        }

        // Préparer le contenu de l'email
        $subject = "Données du capteur DHT11 - " . date('Y-m-d H:i:s');
        
        $message = "Bonjour,\n\n";
        $message .= "Voici les dernières données du capteur DHT11 :\n\n";
        
        foreach ($sensorData as $data) {
            // 删除调试信息
            $message .= sprintf(
                "Date : %s\nHumidité : %.1f%%\nTempérature : %.1f°C\n\n",
                $data['timestamp'],
                $data['humidity'],
                $data['temperature']
            );
        }
        
        $message .= "Cordialement,\nSystème de surveillance DHT11";
        
        $headers = "From: L2164753957@gmail.com";

        // Envoyer l'email
        if (mail($email, $subject, $message, $headers)) {
            return [
                'success' => true,
                'message' => "Les données ont été envoyées avec succès à {$email}",
                'data_count' => count($sensorData)
            ];
        } else {
            $error = error_get_last();
            return [
                'success' => false,
                'message' => "Erreur lors de l'envoi de l'email : " . 
                    ($error ? $error['message'] : 'Erreur inconnue')
            ];
        }
    }
}

// Point d'entrée pour les requêtes AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    if (!isset($_POST['email']) || empty($_POST['email'])) {
        echo json_encode([
            'success' => false,
            'message' => 'L\'adresse email est requise'
        ]);
        exit;
    }

    $email = $_POST['email'];
    $limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 10;
    
    $controller = new SensorEmailController();
    $result = $controller->sendSensorDataEmail($email, $limit);
    
    echo json_encode($result);
}
?> 