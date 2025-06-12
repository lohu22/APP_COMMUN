<?php
// Contrôleur pour gérer les données des capteurs
// Controller to manage sensor data
require_once '../models/Database.php';
require_once '../models/SensorData.php';

class SensorController {
    private $db;
    private $sensorData;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->sensorData = new SensorData($this->db);
    }

    // Enregistrer les nouvelles données
    // Save new data
    public function saveData($humidity, $temperature) {
        $this->sensorData->humidity = $humidity;
        $this->sensorData->temperature = $temperature;
        $this->sensorData->timestamp = date('Y-m-d H:i:s');

        if($this->sensorData->create()) {
            return json_encode(array("message" => "Données enregistrées avec succès."));
        } else {
            return json_encode(array("message" => "Impossible d'enregistrer les données."));
        }
    }

    // Récupérer toutes les données
    // Get all data
    public function getAllData() {
        $stmt = $this->sensorData->read();
        $num = $stmt->rowCount();

        if($num > 0) {
            $data_arr = array();
            $data_arr["records"] = array();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $data_item = array(
                    "id" => $id,
                    "humidity" => $HUM,
                    "temperature" => $TEMP,
                    "timestamp" => $Temps
                );
                array_push($data_arr["records"], $data_item);
            }
            return json_encode($data_arr);
        } else {
            return json_encode(array("message" => "Aucune donnée trouvée."));
        }
    }

    // Méthode statique pour traiter et enregistrer les données brutes du port série
    // 静态方法：处理并保存串口原始数据
    public static function saveRawSerialData($raw_data) {
        // Correspondance du format de données
        if (preg_match('/Hum\s*:\s*(\d+)\s*%\s*\|\s*Temp\s*:\s*(\d+\.?\d*)\s*C/', $raw_data, $matches)) {
            $humidity = floatval($matches[1]);
            $temperature = floatval($matches[2]);
            $timestamp = date('Y-m-d H:i:s');

            // Connexion à la base de données
            require_once __DIR__ . '/../models/Database.php';
            require_once __DIR__ . '/../models/SensorData.php';
            $database = new Database();
            $db = $database->getConnection();
            $sensorData = new SensorData($db);
            $sensorData->humidity = $humidity;
            $sensorData->temperature = $temperature;
            $sensorData->timestamp = $timestamp;
            if ($sensorData->create()) {
                return [
                    'success' => true,
                    'message' => 'Données enregistrées avec succès !',
                    'humidity' => $humidity,
                    'temperature' => $temperature
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Échec de l\'enregistrement des données !'
                ];
            }
        } else {
            return [
                'success' => false,
                'message' => 'Format de données invalide',
                'received' => $raw_data
            ];
        }
    }
}
?> 