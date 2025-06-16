<?php
// ====== Partie configurable ======
$portName = isset($_POST['port_name']) ? $_POST['port_name'] : 'COM8'; // Récupérer le port série depuis le formulaire
$baudRate = isset($_POST['baud_rate']) ? (int)$_POST['baud_rate'] : 9600; // Récupérer le débit en bauds depuis le formulaire
$bits = 8;
$stopBit = 1;
$readDurationSeconds = 2; // Durée de lecture (en secondes)
// ========================

// Fonction pour lire les données du port série avec DIO (basé sur recevoir.php)
function readSerialDataDIO($portName, $baudRate, $bits, $stopBit, $readDurationSeconds) {
    // Configuration des paramètres du port série (commande mode pour Windows)
    $output = array();
    exec("mode {$portName} baud={$baudRate} data={$bits} stop={$stopBit} parity=n xon=off", $output);
    // Ouvrir le port série
    $fd = @dio_open("\\\\.\\{$portName}", O_RDWR);
    if (!$fd) {
        return ['error' => 'Impossible d\'ouvrir le port série avec DIO ' . $portName];
    }
    // Lire les données
    $data = '';
    $endTime = time() + $readDurationSeconds;
    while (time() < $endTime) {
        $chunk = dio_read($fd, 256);
        if ($chunk) $data .= $chunk;
        usleep(100000); // Vérifier toutes les 100ms
    }
    dio_close($fd);
    return ['data' => $data, 'timestamp' => date('Y-m-d H:i:s')];
}

// Fonction pour générer le contenu HTML
function generateSensorDataHTML() {
    global $portName, $baudRate, $bits, $stopBit, $readDurationSeconds;
    
    $result = readSerialDataDIO($portName, $baudRate, $bits, $stopBit, $readDurationSeconds);
    
    ob_start(); // Démarrer la mise en tampon de sortie
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
            
            require_once __DIR__ . '/../Backend/controllers/SensorController.php';
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
    return ob_get_clean(); // Retourner le contenu du tampon
}

// Si c'est une requête AJAX, ne retourner que la partie données
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    echo generateSensorDataHTML();
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surveillance du capteur DHT11</title>
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
        .config-form {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        .config-form select, .config-form input {
            padding: 8px;
            margin: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .config-form button {
            background-color: #007bff;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .config-form button:hover {
            background-color: #0056b3;
        }
        .result-message {
            margin-top: 10px;
            padding: 10px;
            border-radius: 4px;
        }
        .result-message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .result-message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .result-message.pending {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Surveillance du capteur DHT11</h1>
        
        <form class="config-form" method="post">
            <div>
                <label for="port_name">Port COM :</label>
                <select name="port_name" id="port_name">
                    <?php
                    // Générer les options de port COM (COM1-COM20)
                    for ($i = 1; $i <= 20; $i++) {
                        $selected = ($portName === "COM$i") ? 'selected' : '';
                        echo "<option value=\"COM$i\" $selected>COM$i</option>";
                    }
                    ?>
                </select>
            </div>
            <div>
                <label for="baud_rate">Débit en bauds :</label>
                <select name="baud_rate" id="baud_rate">
                    <?php
                    $baudRates = [9600, 19200, 38400, 57600, 115200];
                    foreach ($baudRates as $rate) {
                        $selected = ($baudRate === $rate) ? 'selected' : '';
                        echo "<option value=\"$rate\" $selected>$rate</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit">Appliquer les paramètres</button>
        </form>

        <div class="config-form">
            <h3>Envoyer les données par email</h3>
            <form id="email-form" onsubmit="sendSensorData(event)">
                <div>
                    <label for="email">Adresse email：</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div>
                    <label for="data-limit">Nombre de données：</label>
                    <select id="data-limit" name="limit">
                        <option value="5">5 enregistrements</option>
                        <option value="10" selected>10 enregistrements</option>
                        <option value="20">20 enregistrements</option>
                        <option value="50">50 enregistrements</option>
                    </select>
                </div>
                <button type="submit">Envoyer les données</button>
            </form>
            <div id="email-result" class="result-message"></div>
        </div>

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

        // Fonction pour envoyer les données par email
        function sendSensorData(event) {
            event.preventDefault();
            const email = document.getElementById('email').value;
            const limit = document.getElementById('data-limit').value;
            const resultDiv = document.getElementById('email-result');
            
            resultDiv.innerHTML = 'Envoi en cours...';
            resultDiv.className = 'result-message pending';
            
            fetch('/Backend/controllers/SensorEmailController.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `email=${encodeURIComponent(email)}&limit=${limit}`
            })
            .then(response => response.json())
            .then(data => {
                resultDiv.innerHTML = data.message;
                resultDiv.className = `result-message ${data.success ? 'success' : 'error'}`;
            })
            .catch(error => {
                resultDiv.innerHTML = 'Échec de l\'envoi : ' + error.message;
                resultDiv.className = 'result-message error';
            });
        }
    </script>
</body>
</html>
