<?php
// ====== 可配置部分 ======
$portName = 'COM8'; // 串口号
$baudRate = 9600;
$bits = 8;
$stopBit = 1;
$readDurationSeconds = 2; // 读取时长（秒）
// ========================

// Fonction pour lire les données du port série avec DIO (参考recevoir.php)
function readSerialDataDIO($portName, $baudRate, $bits, $stopBit, $readDurationSeconds) {
    // 配置串口参数（Windows下用exec mode命令）
    $output = array();
    exec("mode {$portName} baud={$baudRate} data={$bits} stop={$stopBit} parity=n xon=off", $output);
    // 打开串口
    $fd = @dio_open("\\\\.\\{$portName}", O_RDWR);
    if (!$fd) {
        return ['error' => 'Impossible d\'ouvrir le port série avec DIO ' . $portName];
    }
    // 读取数据
    $data = '';
    $endTime = time() + $readDurationSeconds;
    while (time() < $endTime) {
        $chunk = dio_read($fd, 256);
        if ($chunk) $data .= $chunk;
        usleep(100000); // 每100ms检查一次
    }
    dio_close($fd);
    return ['data' => $data, 'timestamp' => date('Y-m-d H:i:s')];
}

// Fonction pour générer le contenu HTML
function generateSensorDataHTML() {
    global $portName, $baudRate, $bits, $stopBit, $readDurationSeconds;
    
    $result = readSerialDataDIO($portName, $baudRate, $bits, $stopBit, $readDurationSeconds);
    
    ob_start(); // 开始输出缓冲
    ?>
    <div class="data-box">
        <h2>Données récentes</h2>
        <?php if (isset($result['error'])): ?>
            <p class="error">Erreur : <?php echo htmlspecialchars($result['error']); ?></p>
        <?php else: ?>
            <?php
            $data = $result['data'];
            if (preg_match('/Hum : (\d+) %/', $data, $humidity)) {
                echo "<p>Humidité : {$humidity[1]}%</p>";
            }
            if (preg_match('/Temp : ([\d.]+) C/', $data, $temperature)) {
                echo "<p>Température : {$temperature[1]}°C</p>";
            }
            if (strpos($data, 'Read date failed') !== false) {
                echo "<p class='error'>Échec de la lecture</p>";
            }
            if (strpos($data, 'Checksum wrong') !== false) {
                echo "<p class='error'>Erreur de somme de contrôle</p>";
            }
            
            require_once __DIR__ . '/../controllers/SensorController.php';
            $resultSave = SensorController::saveRawSerialData($data);
            if ($resultSave['success']) {
                echo "<p class='success'>Enregistrement DB : " . htmlspecialchars($resultSave['message']) . "</p>";
            } else {
                echo "<p class='error'>Enregistrement DB : " . htmlspecialchars($resultSave['message']) . "</p>";
            }
            ?>
            <p class="timestamp">Horodatage : <?php echo $result['timestamp']; ?></p>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean(); // 返回缓冲的内容
}

// 如果是AJAX请求，只返回数据部分
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    echo generateSensorDataHTML();
    exit;
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DHT11 传感器监控</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f0f0f0;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .data-box {
            margin: 10px 0;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .error {
            color: #ff0000;
        }
        .success {
            color: #008000;
        }
        .timestamp {
            color: #666;
            font-size: 0.9em;
        }
        .refresh-btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .refresh-btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Surveillance du capteur DHT11</h1>
        
        <div id="sensor-container">
            <?php echo generateSensorDataHTML(); ?>
        </div>

        <button class="refresh-btn" onclick="refreshData()">Rafraîchir</button>
    </div>

    <script>
        // Fonction pour rafraîchir les données
        function refreshData() {
            fetch(window.location.href, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                document.getElementById('sensor-container').innerHTML = html;
            })
            .catch(error => {
                console.error('Erreur lors du rafraîchissement:', error);
                document.getElementById('sensor-container').innerHTML = '<div class="data-box"><p class="error">Erreur lors du rafraîchissement des données</p></div>';
            });
        }

        // Rafraîchissement automatique toutes les 2 secondes
        setInterval(refreshData, 2000);
    </script>
</body>
</html>
