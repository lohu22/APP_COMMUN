<?php
// 引入数据库配置
require_once __DIR__ . '/../Backend/models/Database.php';

// 获取最新的声音数据
function getLatestSound() {
    $db = (new Database())->getConnection();
    // 查询最新一条数据
    $sql = "SELECT * FROM sound_measurements ORDER BY timestamp DESC LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        return [
            'db_level' => $row['db_level'],
            'quality' => $row['quality'],
            'time' => $row['timestamp']
        ];
    } else {
        return null;
    }
}

$sound = getLatestSound();
if ($sound) {
    // 转换为法国时区并格式化
    $dt = new DateTime($sound['time']);
    $dt->setTimezone(new DateTimeZone('Europe/Paris'));
    $formattedTime = $dt->format('Y-m-d H:i:s T');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surveillance du niveau sonore - ShowPilot</title>
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
        .sound-value {
            font-size: 2.5rem;
            color: #009ffd;
            font-weight: bold;
        }
        .sound-quality {
            font-size: 1.5rem;
            color: #666;
            margin-top: 10px;
        }
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
    <div id="header"></div>
    <div class="container">
        <h1>Surveillance du niveau sonore</h1>
        <div class="data-box" id="sound-box">
            <h2>Dernière mesure sonore</h2>
            <?php if ($sound): ?>
                <div class="sound-value"><?php echo htmlspecialchars($sound['db_level']); ?> dB</div>
                <div class="sound-quality">Qualité : <?php echo htmlspecialchars($sound['quality']); ?></div>
                <div class="timestamp">Mis à jour à : <?php echo $formattedTime; ?></div>
            <?php else: ?>
                <div>Aucune donnée disponible</div>
            <?php endif; ?>
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
    // 刷新声音数据卡片的函数
    function refreshSoundBox() {
        fetch(window.location.href, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newBox = doc.getElementById('sound-box');
            if (newBox) {
                document.getElementById('sound-box').innerHTML = newBox.innerHTML;
            }
        })
        .catch(error => {
            document.getElementById('sound-box').innerHTML = '<div class="error">Erreur lors du rafraîchissement</div>';
        });
    }

    // 每2秒刷新一次数据
    setInterval(refreshSoundBox, 2000);
    </script>
</body>
</html> 