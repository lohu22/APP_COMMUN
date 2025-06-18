<?php
// 引入数据库配置
require_once __DIR__ . '/../Backend/models/Database.php';
// 获取最新的ecran OLED状态
function getLatestEcranState() {
    $db = (new Database())->getConnection();
    $sql = "SELECT * FROM ecran_oled ORDER BY ctid DESC LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        return [
            'state' => $row['state'],
            'time' => $row['recorded_at']
        ];
    } else {
        return null;
    }
}
$ecran = getLatestEcranState();
if ($ecran) {
    $dtEcran = new DateTime($ecran['time']);
    $dtEcran->setTimezone(new DateTimeZone('Europe/Paris'));
    $formattedEcranTime = $dtEcran->format('Y-m-d H:i:s T');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surveillance de l'écran OLED - ShowPilot</title>
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
        .navbar {
            background-color: var(--color-primary);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 999;
        }
        .logo img { height: 50px; width: auto; vertical-align: middle; }
        .nav-links { list-style: none; display: flex; gap: 1.5rem; }
        .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        .nav-links a:hover { color: var(--color-accent); }
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
        .luminosity-value {
            font-size: 2.5rem;
            color: #009ffd;
            font-weight: bold;
        }
        .success { color: #008000; }
        .error { color: #ff0000; }
        .timestamp {
            color: #666;
            font-size: 0.9em;
        }
        footer {
            background-color: var(--color-primary);
            color: white;
            text-align: center;
            padding: 1.5rem;
            margin-top: 4rem;
            flex-shrink: 0;
        }
        footer a {
            color: var(--color-accent);
            text-decoration: none;
        }
        footer a:hover { text-decoration: underline; }
        @media (max-width: 768px) {
            .nav-links { flex-direction: column; gap: 1rem; }
            .container { margin: 1rem; padding: 15px; }
        }
    </style>
</head>
<body>
<header>
    <nav class="navbar">
        <div class="logo">
            <img src="logo_showpilot_transparent_white.png" alt="ShowPilot Logo">
        </div>
        <ul class="nav-links">
            <li><a href="/Frontend/index.html">Accueil</a></li>
            <li><a href="/Frontend/dht11_monitor.php">Capteurs</a></li>
            <li><a href="/Frontend/login.html">Connexion</a></li>
        </ul>
    </nav>
</header>
<div class="container">
    <h1>Surveillance de l'écran OLED</h1>
    <!-- Carte pour l'état de l'écran OLED (ecran_oled) -->
    <div class="data-box" id="ecran-box">
        <h2>État de l'écran OLED (backdrop scène)</h2>
        <p style="margin-bottom:10px;">
            <!-- Description de la fonction G8A écran OLED -->
            Cet écran OLED gère dynamiquement les backdrops de la scène, permettant d'adapter l'ambiance visuelle sur scène.
        </p>
        <?php
        if ($ecran) {
            $etat = ($ecran['state']) ? 'Allumé' : 'Éteint';
            $etatClass = ($ecran['state']) ? 'success' : 'error';
            echo "<div class='luminosity-value $etatClass'>" . htmlspecialchars($etat) . "</div>";
            echo "<div class='timestamp'>Mis à jour à : $formattedEcranTime</div>";
        } else {
            echo "<div>Aucune donnée disponible</div>";
        }
        ?>
        <!--
            Cette carte affiche l'état actuel de l'écran OLED (allumé/éteint) et l'horodatage correspondant.
            Elle met en avant la fonction de gestion dynamique des backdrops de la scène.
        -->
    </div>
</div>
<footer>
    <p>&copy; 2025 ShowPilot - Projet Commun ISEP</p>
    <p><a href="#">Mentions légales</a> | <a href="#">Contact</a></p>
</footer>
<!-- Rafraîchissement automatique de la carte de l'écran OLED toutes les 2 secondes -->
<script>
// Fonction pour rafraîchir la carte de l'écran OLED via AJAX
function refreshEcranBox() {
    // 只请求当前页面，但只获取数据卡片部分
    fetch(window.location.href, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.text())
    .then(html => {
        // 解析返回的HTML，提取#ecran-box内容
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newBox = doc.getElementById('ecran-box');
        if (newBox) {
            document.getElementById('ecran-box').innerHTML = newBox.innerHTML;
        }
    })
    .catch(error => {
        // Afficher une erreur en cas d'échec
        document.getElementById('ecran-box').innerHTML = '<div class="error">Erreur lors du rafraîchissement</div>';
    });
}
// Toutes les 2 secondes, rafraîchir la carte
setInterval(refreshEcranBox, 2000);
</script>
</body>
</html> 