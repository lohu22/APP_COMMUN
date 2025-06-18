<?php
// Contrôleur pour obtenir les dernières données de température et d'humidité
// Controller to get latest temperature and humidity data
require_once '../models/Database.php';
require_once '../models/SensorData.php';

header('Content-Type: application/json');

try {
    // Créer une instance de la base de données
    // Create database instance
    $database = new Database();
    $db = $database->getConnection();

    // Créer une instance de SensorData
    // Create SensorData instance
    $sensorData = new SensorData($db);
    
    // Lire la dernière entrée
    // Read the latest entry
    $stmt = $sensorData->read(1);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        // 兼容字段名大小写
        $temp = isset($result['TEMP']) ? $result['TEMP'] : (isset($result['temp']) ? $result['temp'] : null);
        $hum = isset($result['HUM']) ? $result['HUM'] : (isset($result['hum']) ? $result['hum'] : null);

        echo json_encode([
            'success' => true,
            'temp' => $temp,
            'hum' => $hum
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Aucune donnée trouvée'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur: ' . $e->getMessage()
    ]);
}
?> 