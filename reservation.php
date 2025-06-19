<?php
require_once 'connexion_bdd.php';
session_start();
if (!isset($_SESSION['utilisateur'])) {
    header('Location: connexion.php');
    exit();
}
$id_seance = isset($_GET['id_seance']) ? intval($_GET['id_seance']) : 0;
if (!$id_seance) { echo "Séance inconnue."; exit(); }

// Récupérer infos séance et salle
$stmt = $pdo->prepare("SELECT seance.*, salle.nom AS salle_nom, salle.nb_rangees, salle.nb_places_par_rangee FROM seance JOIN salle ON seance.id_salle = salle.id WHERE seance.id = ?");
$stmt->execute([$id_seance]);
$seance = $stmt->fetch();
if (!$seance) { echo "Séance inconnue."; exit(); }
$nb_rangees = $seance['nb_rangees'];
$nb_places_par_rangee = $seance['nb_places_par_rangee'];

// Récupérer les places déjà réservées
$stmt = $pdo->prepare("SELECT rangee, place FROM reservation WHERE id_seance = ?");
$stmt->execute([$id_seance]);
$reserved = [];
while ($row = $stmt->fetch()) {
    $reserved[$row['rangee']][$row['place']] = true;
}

// Traitement de la réservation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['places'])) {
    foreach ($_POST['places'] as $place) {
        list($rangee, $placeNum) = explode('-', $place);
        // Vérifier que la place n'est pas déjà réservée
        if (empty($reserved[$rangee][$placeNum])) {
            $stmt = $pdo->prepare("INSERT INTO reservation (id_utilisateur, id_seance, rangee, place) VALUES (?, ?, ?, ?)");
            $stmt->execute([$_SESSION['utilisateur']['id_utilisateur'], $id_seance, $rangee, $placeNum]);
            $reserved[$rangee][$placeNum] = true; // Marquer comme réservée pour l'affichage
        }
    }
    $success = "Réservation effectuée !";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Réservation - <?= htmlspecialchars($seance['film']) ?></title>
  <style>
    .salle-schema { display: flex; flex-direction: column; gap: 8px; margin-bottom: 20px;}
    .rangee { display: flex; gap: 6px; }
    .place { padding: 4px 8px; border: 1px solid #ccc; border-radius: 4px; }
    .place.reserved { background: #e74c3c; color: #fff; }
    .place input[type="checkbox"] { margin-right: 4px; }
  </style>
</head>
<body>
  <h2><?= htmlspecialchars($seance['film']) ?> - <?= htmlspecialchars($seance['date_heure']) ?> (Salle <?= htmlspecialchars($seance['salle_nom']) ?>)</h2>
  <?php if (!empty($success)): ?>
    <div style="color:green;"><?= $success ?></div>
  <?php endif; ?>
  <form method="post">
    <div class="salle-schema">
      <?php for ($r = 1; $r <= $nb_rangees; $r++): ?>
        <div class="rangee">
          <?php for ($p = 1; $p <= $nb_places_par_rangee; $p++): 
            $isReserved = isset($reserved[$r][$p]);
          ?>
            <label class="place<?= $isReserved ? ' reserved' : '' ?>">
              <input type="checkbox" name="places[]" value="<?= $r ?>-<?= $p ?>" <?= $isReserved ? 'disabled' : '' ?>>
              R<?= $r ?>-P<?= $p ?>
            </label>
          <?php endfor; ?>
        </div>
      <?php endfor; ?>
    </div>
    <button type="submit">Réserver</button>
  </form>
  <p><a href="seances.php">Retour aux séances</a></p>
</body>
</html>