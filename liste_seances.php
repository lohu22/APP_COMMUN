<?php
require_once 'Frontend/header.php';
require_once 'connexion_bdd.php';
$stmt = $pdo->query("SELECT seance.id, seance.film, seance.date_heure, salle.nom AS salle_nom FROM seance JOIN salle ON seance.id_salle = salle.id ORDER BY seance.date_heure");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Séances</title>
  <style>
    body {
      background: linear-gradient(120deg, #e0eafc 0%, #cfdef3 100%);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 0;
      min-height: 100vh;
    }
    .seances-container {
      max-width: 700px;
      margin: 60px auto 0 auto;
      background: #fff;
      border-radius: 18px;
      box-shadow: 0 8px 32px rgba(44, 62, 80, 0.12);
      padding: 36px 32px 28px 32px;
    }
    .seances-title {
      text-align: center;
      color: #2980b9;
      font-size: 2rem;
      margin-bottom: 28px;
      letter-spacing: 1px;
    }
    .seances-list {
      list-style: none;
      padding: 0;
      margin: 0;
    }
    .seance-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: linear-gradient(90deg, #f0f6fa 0%, #eaf6fb 100%);
      border-radius: 10px;
      margin-bottom: 18px;
      padding: 18px 22px;
      box-shadow: 0 2px 8px rgba(41,128,185,0.07);
      transition: box-shadow 0.2s;
    }
    .seance-item:hover {
      box-shadow: 0 6px 18px rgba(41,128,185,0.13);
    }
    .seance-info {
      display: flex;
      flex-direction: column;
      gap: 4px;
    }
    .seance-film {
      font-size: 1.15rem;
      font-weight: 600;
      color: #2c3e50;
    }
    .seance-details {
      font-size: 0.98rem;
      color: #2980b9;
    }
    .seance-btn {
      background: linear-gradient(90deg, #2980b9 0%, #6dd5fa 100%);
      color: #fff;
      border: none;
      border-radius: 8px;
      padding: 10px 22px;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.2s, transform 0.15s;
      text-decoration: none;
      outline: none;
      box-shadow: 0 2px 8px rgba(41,128,185,0.10);
    }
    .seance-btn:hover, .seance-btn:focus {
      background: linear-gradient(90deg, #1c5d8f 0%, #2980b9 100%);
      transform: translateY(-2px) scale(1.04);
    }
    @media (max-width: 600px) {
      .seances-container {
        padding: 18px 6px;
      }
      .seance-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
        padding: 14px 10px;
      }
    }
  </style>
</head>
<body>
  <div class="seances-container">
    <h2 class="seances-title">Séances disponibles</h2>
    <ul class="seances-list">
      <?php while ($row = $stmt->fetch()): ?>
        <li class="seance-item">
          <div class="seance-info">
            <span class="seance-film"><?= htmlspecialchars($row['film']) ?></span>
            <span class="seance-details"><?= htmlspecialchars($row['date_heure']) ?> - Salle <?= htmlspecialchars($row['salle_nom']) ?></span>
          </div>
            <a href="descriptif_seance.php?id_seance=<?= $row['id'] ?>" class="seance-btn">Réserver</a>
        </li>
      <?php endwhile; ?>
    </ul>
  </div>
</body>