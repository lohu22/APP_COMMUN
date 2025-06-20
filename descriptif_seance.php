<?php
require_once 'Frontend/header.php';
require_once 'connexion_bdd.php';

$id_seance = isset($_GET['id_seance']) ? intval($_GET['id_seance']) : 0;
if (!$id_seance) { echo "Séance inconnue."; exit(); }

// Récupérer infos séance et salle
$stmt = $pdo->prepare("SELECT seance.*, salle.nom AS salle_nom, salle.nb_rangees, salle.nb_places_par_rangee FROM seance JOIN salle ON seance.id_salle = salle.id WHERE seance.id = ?");
$stmt->execute([$id_seance]);
$seance = $stmt->fetch();
if (!$seance) { echo "Séance inconnue."; exit(); }
$nb_rangees = $seance['nb_rangees'];
$nb_places_par_rangee = $seance['nb_places_par_rangee'];

$id_utilisateur = null;
if (isset($_SESSION['utilisateur']['id_utilisateur'])) {
    $id_utilisateur = $_SESSION['utilisateur']['id_utilisateur'];
}

// Récupérer les places déjà réservées
$stmt = $pdo->prepare("SELECT rangee, place FROM reservation WHERE id_seance = ?");
$stmt->execute([$id_seance]);
$reserved = [];
while ($row = $stmt->fetch()) {
    $reserved[$row['rangee']][$row['place']] = true;
}

// Vérifier si l'utilisateur a déjà réservé pour cette séance et récupérer sa place si oui
$deja_reserve = false;
$ma_reservation = null;
if ($id_utilisateur) {
    $stmt = $pdo->prepare("SELECT rangee, place FROM reservation WHERE id_seance = ? AND id_utilisateur = ?");
    $stmt->execute([$id_seance, $id_utilisateur]);
    $ma_reservation = $stmt->fetch();
    $deja_reserve = $ma_reservation ? true : false;
}

// Traitement de la réservation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place']) && !$deja_reserve) {
    list($rangee, $placeNum) = explode('-', $_POST['place']);
    // Vérifier si la place n'est pas déjà réservée
    if (empty($reserved[$rangee][$placeNum]) && $id_utilisateur) {
        $stmt = $pdo->prepare("INSERT INTO reservation (id_utilisateur, id_seance, rangee, place) VALUES (?, ?, ?, ?)");
        $stmt->execute([$id_utilisateur, $id_seance, $rangee, $placeNum]);
        $reserved[$rangee][$placeNum] = true;
        // Mettre à jour la variable pour affichage immédiat
        $ma_reservation = ['rangee' => $rangee, 'place' => $placeNum];
        $deja_reserve = true;
    }
}

// Calcul du nombre de places disponibles
$total_places = $nb_rangees * $nb_places_par_rangee;
$places_reservees = 0;
foreach ($reserved as $row) { $places_reservees += count($row); }
$places_disponibles = $total_places - $places_reservees;

// Pour convertir 1->A, 2->B, etc.
function lettre_rangee($num) {
    return chr(64 + intval($num));
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($seance['film']) ?> - Détail de la séance</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <style>
    body {
      background: linear-gradient(120deg, #e0eafc 0%, #cfdef3 100%);
      margin: 0;
      padding: 0;
      min-height: 100vh;
    }
    .main-title {
      text-align: center;
      color: #2980b9;
      font-size: 2.3rem;
      font-weight: bold;
      letter-spacing: 1px;
      margin-top: 80px;
      margin-bottom: 18px;
    }
    .descriptif-container {
      max-width: 1000px;
      margin: 0 auto 40px auto;
      background: #fff;
      border-radius: 18px;
      box-shadow: 0 8px 32px rgba(44, 62, 80, 0.12);
      padding: 36px 32px 28px 32px;
      display: flex;
      gap: 40px;
      align-items: flex-start;
    }
    .descriptif-photo-wrap {
      flex-shrink: 0;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 18px;
      width: 240px;
      min-width: 240px;
      max-width: 240px;
    }
    .descriptif-photo {
      width: 220px;
      height: 320px;
      object-fit: cover;
      border-radius: 16px;
      box-shadow: 0 6px 32px rgba(41,128,185,0.18);
      background: #eee;
      border: 3px solid #b3e0ff;
      transition: transform 0.2s;
      display: block;
      margin-bottom: 10px;
    }
    .descriptif-photo:hover {
      transform: scale(1.04) rotate(-2deg);
      box-shadow: 0 12px 40px rgba(41,128,185,0.22);
    }
    .descriptif-details {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 10px;
      margin-bottom: 18px;
      font-size: 1.13rem;
    }
    .detail-date, .detail-salle {
      background: #eaf6fb;
      color: #2980b9;
      padding: 8px 18px;
      border-radius: 8px;
      font-weight: 600;
      box-shadow: 0 2px 8px rgba(41,128,185,0.07);
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 1.08rem;
      letter-spacing: 0.5px;
    }
    .detail-date i, .detail-salle i {
      color: #2980b9;
      font-size: 1.1em;
    }
    .cinema-room-block {
      flex: 1;
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    .places-dispo {
      font-size: 1.15rem;
      color: #27ae60;
      font-weight: bold;
      margin-bottom: 18px;
      background: #eafbe7;
      padding: 8px 18px;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(39, 174, 96, 0.07);
      display: inline-block;
    }
    .already-reserved-msg {
      color: #e74c3c;
      background: #fbeee6;
      border: 1.5px solid #e74c3c;
      border-radius: 8px;
      padding: 12px 18px;
      margin: 18px 0;
      text-align: center;
      font-weight: 600;
      font-size: 1.1rem;
    }
    .ticket-cinema {
      margin: 18px auto 0 auto;
      background: #fafdff;
      border: 2px dashed #2980b9;
      border-radius: 12px;
      padding: 18px 32px;
      max-width: 320px;
      text-align: center;
      font-size: 1.15rem;
      color: #2980b9;
      font-weight: 600;
      box-shadow: 0 2px 8px rgba(41,128,185,0.10);
    }
    .ticket-row, .ticket-seat {
      margin: 8px 0;
      font-size: 1.1rem;
    }
    .ticket-label {
      font-weight: 700;
      margin-right: 8px;
    }
    .ticket-value {
      font-weight: 600;
      color: #1c5d8f;
    }
    /* 3D Cinema Room */
    .screen-wrap {
      width: 120px;
      margin: 0 auto 18px auto;
      position: relative;
      display: flex;
      flex-direction: column;
      align-items: center;
      perspective: 700px;
    }
    .screen {
      width: 100%;
      height: 18px;
      background: linear-gradient(90deg, #b3e0ff 0%, #99cef1 100%);
      border-radius: 0 0 40px 40px / 0 0 20px 20px;
      text-align: center;
      color: #2980b9;
      font-weight: bold;
      line-height: 18px;
      letter-spacing: 2px;
      box-shadow: 0 8px 32px rgba(41,128,185,0.18);
      border-bottom: 2px solid #2980b9;
      margin-bottom: 2px;
      font-size: 0.95rem;
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
      transform: rotateX(18deg) scaleY(1.1);
    }
    .screen-label {
      font-size: 0.95rem;
      color: #2980b9;
      font-weight: 600;
      letter-spacing: 2px;
      pointer-events: none;
      user-select: none;
    }
    .salle-schema {
      display: flex;
      flex-direction: column;
      gap: 13px;
      align-items: center;
      margin-bottom: 30px;
      perspective: 700px;
      width: 100%;
      max-width: 600px;
    }
    .rangee {
      display: flex;
      gap: 13px;
      justify-content: center;
      width: 100%;
      align-items: center;
      position: relative;
    }
    .rangee-label {
      font-weight: bold;
      color: #fff;
      background: #2980b9;
      border-radius: 8px;
      padding: 0.3em 0.8em;
      margin-right: 10px;
      font-size: 1.1rem;
      box-shadow: 0 2px 8px rgba(41,128,185,0.10);
      align-self: center;
      min-width: 36px;
      text-align: center;
      letter-spacing: 1px;
      transform: translateZ(10px);
      flex-shrink: 0;
      height: 38px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .place {
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 2px;
      position: relative;
      flex: 1 1 0;
      min-width: 0;
    }
    .place input[type="radio"] {
      display: none;
    }
    .place-label {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 100%;
      max-width: 44px;
      min-width: 32px;
      height: 38px;
      border-radius: 50%;
      background: linear-gradient(135deg, #e0eafc 0%, #b3e0ff 100%);
      border: 2px solid #2980b9;
      color: #2980b9;
      font-weight: bold;
      font-size: 1rem;
      text-align: center;
      line-height: 34px;
      cursor: pointer;
      transition: 
        background 0.2s, 
        border-color 0.2s, 
        color 0.2s, 
        transform 0.15s, 
        box-shadow 0.2s;
      box-shadow: 0 4px 16px rgba(41,128,185,0.13);
      user-select: none;
      transform: rotateX(12deg) scaleY(1.05);
      position: relative;
      outline: none;
    }
    .place input[type="radio"]:checked + .place-label {
      background: linear-gradient(135deg, #6dd5fa 0%, #2980b9 100%);
      color: #fff;
      border-color: #1c5d8f;
      transform: scale(1.12) rotateX(0deg);
      box-shadow: 0 8px 24px rgba(41,128,185,0.22);
    }
    .place input[type="radio"]:checked + .place-label::after {
      content: '\f00c';
      font-family: "Font Awesome 6 Free";
      font-weight: 900;
      color: #fff;
      font-size: 1.1em;
      position: absolute;
      right: 6px;
      top: 6px;
      display: block;
    }
    .place-label:active {
      filter: brightness(0.95);
    }
    .place-label:hover {
      background: linear-gradient(135deg, #b3e0ff 0%, #6dd5fa 100%);
      color: #222;
      border-color: #2980b9;
      z-index: 2;
    }
    .place.reserved .place-label {
      background: #e74c3c;
      color: #fff;
      border-color: #c0392b;
      cursor: not-allowed;
      opacity: 0.7;
      text-decoration: line-through;
      box-shadow: 0 2px 8px rgba(231,76,60,0.13);
      pointer-events: none;
    }
    .place-label,
    .place input[type="radio"]:checked + .place-label {
      transition: 
        background 0.2s, 
        border-color 0.2s, 
        color 0.2s, 
        transform 0.15s, 
        box-shadow 0.2s;
    }
    .btn-center {
      display: flex;
      justify-content: center;
      width: 100%;
    }
    .reserver-btn {
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
      margin-top: 10px;
    }
    .reserver-btn:hover, .reserver-btn:focus {
      background: linear-gradient(90deg, #1c5d8f 0%, #2980b9 100%);
      transform: translateY(-2px) scale(1.04);
    }
    .btn-retour {
      display: flex;
      justify-content: center;
      width: 100%;
      margin-top: 18px;
    }
    @media (max-width: 900px) {
      .descriptif-container { flex-direction: column; align-items: center; }
      .descriptif-photo-wrap, .cinema-room-block { width: 100%; min-width: 0; max-width: 100%; }
      .screen-wrap { width: 100%; max-width: 120px; }
      .screen { width: 100%; }
      .salle-schema { max-width: 98vw; }
    }
  </style>
</head>
<body>
  <div class="main-title"><?= htmlspecialchars($seance['film']) ?></div>
  <div class="descriptif-container">
    <div class="descriptif-photo-wrap">
      <img class="descriptif-photo" src="<?= !empty($seance['photo']) ? htmlspecialchars($seance['photo']) : 'https://via.placeholder.com/220x320?text=Affiche' ?>" alt="Affiche du film">
      <div class="descriptif-details">
        <span class="detail-date">
          <i class="fa-regular fa-clock"></i>
          <?= htmlspecialchars($seance['date_heure']) ?>
        </span>
        <span class="detail-salle">
          <i class="fa-solid fa-chair"></i>
          Salle <?= htmlspecialchars($seance['salle_nom']) ?>
        </span>
        <div class="btn-retour">
          <a href="liste_seances.php" class="reserver-btn">Retour aux séances</a>
        </div>
      </div>
    </div>
    <div class="cinema-room-block">
      <div class="places-dispo">
        <i class="fa-solid fa-ticket"></i>
        <?= $places_disponibles ?> place<?= $places_disponibles > 1 ? 's' : '' ?> disponible<?= $places_disponibles > 1 ? 's' : '' ?>
      </div>
      <?php if ($deja_reserve && $ma_reservation): ?>
        <div class="already-reserved-msg">
          Vous avez déjà réservé une place pour cette séance.
        </div>
        <div class="ticket-cinema">
          <div class="ticket-row"><span class="ticket-label">Rangée</span><span class="ticket-value"><?= lettre_rangee($ma_reservation['rangee']) ?></span></div>
          <div class="ticket-seat"><span class="ticket-label">Place</span><span class="ticket-value"><?= htmlspecialchars($ma_reservation['place']) ?></span></div>
        </div>
      <?php else: ?>
      <form method="post">
        <div class="screen-wrap">
          <div class="screen">
            <span class="screen-label">ÉCRAN</span>
          </div>
        </div>
        <div class="salle-schema">
          <?php for ($r = 1; $r <= $nb_rangees; $r++): ?>
            <div class="rangee">
              <span class="rangee-label"><?= lettre_rangee($r) ?></span>
              <?php for ($p = 1; $p <= $nb_places_par_rangee; $p++):
                $isReserved = isset($reserved[$r][$p]);
                $placeId = "place-{$r}-{$p}"; ?>
                <div class="place<?= $isReserved ? ' reserved' : '' ?>">
                  <input type="radio" name="place" value="<?= $r ?>-<?= $p ?>" id="<?= $placeId ?>" <?= $isReserved ? 'disabled' : '' ?>>
                  <label class="place-label" for="<?= $placeId ?>">
                    <?= $p ?>
                  </label>
                </div>
              <?php endfor; ?>
            </div>
          <?php endfor; ?>
        </div>
        <div class="btn-center">
          <button type="submit" class="reserver-btn">Réserver</button>
        </div>
      </form>
      <?php endif; ?>
      <?php
      // Affichage du ticket cinéma si une place vient d'être réservée (en POST)
      if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place']) && $ma_reservation && !$deja_reserve) {
          list($rangee, $placeNum) = explode('-', $_POST['place']);
          echo '<div class="ticket-cinema">';
          echo '<div class="ticket-row"><span class="ticket-label">Rangée</span><span class="ticket-value">' . lettre_rangee($rangee) . '</span></div>';
          echo '<div class="ticket-seat"><span class="ticket-label">Place</span><span class="ticket-value">' . htmlspecialchars($placeNum) . '</span></div>';
          echo '</div>';
      }
      ?>
    </div>
  </div>
</body>
</html>