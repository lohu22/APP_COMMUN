<?php
// 引入数据库配置
require_once __DIR__ . '/../Backend/models/Database.php';

// 获取最新的count数据
function getLatestCount() {
    $db = (new Database())->getConnection();
    $sql = "SELECT count, timestamp FROM ir_distance_data ORDER BY ctid DESC LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        return [
            'count' => $row['count'],
            'time' => $row['timestamp']
        ];
    } else {
        return null;
    }
}

$countData = getLatestCount();
if ($countData) {
    $formattedTime = $countData['time'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compteur de Personnes - ShowPilot</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --color-primary: #0a0a23;
            --color-accent: #5ce1e6;
            --color-bg-light: #f9f9fb;
            --color-card: #ffffff;
            --color-text: #222;
            --font-main: 'Montserrat', sans-serif;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; }
        body {
            font-family: var(--font-main);
            background-color: var(--color-bg-light);
            color: var(--color-text);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .container {
            max-width: 800px;
            margin: 2rem auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            flex: 1 0 auto;
        }
        .data-box {
            margin: 10px 0;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: #f8f9fa;
        }
        .count-value {
            font-size: 3.5rem;
            color: #009ffd;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
        }
        .timestamp {
            color: #666;
            font-size: 0.9em;
            text-align: center;
        }
    </style>
</head>
<body>
    <div id="header"></div>
    <div class="container">
        <h1>Compteur de Personnes</h1>
        <div class="data-box" id="count-box">
            <h2>Nombre des personnes passées</h2>
            <p style="margin-bottom:10px;">
                <!-- Description du compteur de personnes -->
                Ce compteur affiche le nombre total de personnes détectées par le capteur.
            </p>
            <?php
            if ($countData) {
                echo "<div class='count-value'>" . htmlspecialchars($countData['count']) . "</div>";
                echo "<div class='timestamp'>Dernière mise à jour : $formattedTime</div>";
            } else {
                echo "<div>Aucune donnée disponible</div>";
            }
            ?>
        </div>
    </div>
    <div id="footer"></div>
    <script>
    // 动态加载header和footer
    document.addEventListener("DOMContentLoaded", function() {
        fetch("/APP_COMMUN/Frontend/header.php")
            .then(response => response.text())
            .then(data => {
                document.getElementById("header").innerHTML = data;
            });
        fetch("/APP_COMMUN/Frontend/footer.html")
            .then(response => response.text())
            .then(data => {
                document.getElementById("footer").innerHTML = data;
            });
    });

    // 实时更新count数据
    function refreshCountBox() {
        fetch(window.location.href, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newBox = doc.getElementById('count-box');
            if (newBox) {
                document.getElementById('count-box').innerHTML = newBox.innerHTML;
            }
        })
        .catch(error => {
            document.getElementById('count-box').innerHTML = '<div class="error">Erreur lors du rafraîchissement</div>';
        });
    }

    // 每2秒更新一次数据
    setInterval(refreshCountBox, 2000);
    </script>
</body>
</html> 