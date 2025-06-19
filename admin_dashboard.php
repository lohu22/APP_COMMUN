<?php
session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: Frontend/connexion.php');
    exit();
}
require_once 'connexion_bdd.php';

// Création du dossier uploads/photos si besoin
$upload_dir = __DIR__ . '/uploads/photos/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Suppression séance
if (isset($_GET['delete_seance'])) {
    $id = intval($_GET['delete_seance']);
    $pdo->prepare("DELETE FROM reservation WHERE id_seance = ?")->execute([$id]);
    $pdo->prepare("DELETE FROM seance WHERE id = ?")->execute([$id]);
    $message = "Séance supprimée.";
}

// Suppression utilisateur
if (isset($_GET['delete_user'])) {
    $id = intval($_GET['delete_user']);
    $pdo->prepare("DELETE FROM reservation WHERE id_utilisateur = ?")->execute([$id]);
    $pdo->prepare("DELETE FROM utilisateur WHERE id_utilisateur = ?")->execute([$id]);
    $message = "Utilisateur supprimé.";
}

// Suppression réservation
if (isset($_GET['delete_reservation'])) {
    $id = intval($_GET['delete_reservation']);
    $pdo->prepare("DELETE FROM reservation WHERE id = ?")->execute([$id]);
    $message = "Réservation supprimée.";
}

// Ajout séance avec upload d'affiche
if (
    isset($_POST['film'], $_POST['date_heure'], $_POST['id_salle'])
) {
    $photo_path = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== 4 && $_FILES['photo']['error'] === 0) {
        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($ext, $allowed)) {
            $filename = uniqid('affiche_', true) . '.' . $ext;
            $dest = $upload_dir . $filename;
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $dest)) {
                $photo_path = 'uploads/photos/' . $filename;
            }
        }
    }
    $stmt = $pdo->prepare("INSERT INTO seance (film, date_heure, id_salle, photo) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_POST['film'], $_POST['date_heure'], $_POST['id_salle'], $photo_path]);
    $message = "Séance ajoutée !";
}

// Récupérer les séances, salles, utilisateurs, réservations
$seances = $pdo->query("SELECT seance.*, salle.nom AS salle_nom FROM seance JOIN salle ON seance.id_salle = salle.id ORDER BY date_heure DESC")->fetchAll();
$salles = $pdo->query("SELECT * FROM salle")->fetchAll();
$utilisateurs = $pdo->query("SELECT * FROM utilisateur ORDER BY nom, prenom")->fetchAll();
$reservations = $pdo->query(
    "SELECT 
        reservation.id, 
        utilisateur.nom, 
        utilisateur.prenom, 
        utilisateur.mail, 
        seance.film, 
        seance.date_heure, 
        salle.nom AS salle_nom, 
        reservation.rangee, 
        reservation.place, 
        reservation.date_reservation
     FROM reservation
     JOIN utilisateur ON reservation.id_utilisateur = utilisateur.id_utilisateur
     JOIN seance ON reservation.id_seance = seance.id
     JOIN salle ON seance.id_salle = salle.id
     ORDER BY reservation.date_reservation DESC"
)->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin - Gestion des séances</title>
    <style>
        body {
            background: linear-gradient(120deg, #e0eafc 0%, #cfdef3 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }
        .container {
            max-width: 1050px;
            margin: 40px auto;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(44, 62, 80, 0.12);
            padding: 36px 32px 28px 32px;
        }
        h2 {
            text-align: center;
            color: #2980b9;
            font-size: 2.2rem;
            margin-bottom: 18px;
        }
        a.logout {
            color: #e74c3c;
            text-decoration: none;
            font-weight: bold;
            float: right;
            margin-top: -40px;
            margin-bottom: 20px;
            background: #fbeee6;
            padding: 7px 18px;
            border-radius: 8px;
            transition: background 0.2s;
        }
        a.logout:hover {
            background: #e74c3c;
            color: #fff;
        }
        h3 {
            color: #2980b9;
            margin-top: 32px;
            margin-bottom: 12px;
        }
        form {
            background: #f7fbff;
            border-radius: 12px;
            padding: 18px 22px 12px 22px;
            box-shadow: 0 2px 8px rgba(41,128,185,0.07);
            margin-bottom: 30px;
        }
        label {
            font-weight: 600;
            color: #34495e;
            margin-bottom: 8px;
            display: block;
        }
        input[type="text"], input[type="datetime-local"], select, input[type="file"] {
            width: 100%;
            padding: 10px 12px;
            border: 1.5px solid #b3e0ff;
            border-radius: 8px;
            margin-bottom: 16px;
            font-size: 1rem;
            background: #fafdff;
            transition: border-color 0.2s;
        }
        input[type="text"]:focus, input[type="datetime-local"]:focus, select:focus, input[type="file"]:focus {
            border-color: #2980b9;
            outline: none;
        }
        button[type="submit"], .btn-suppr {
            background: linear-gradient(90deg, #2980b9 0%, #6dd5fa 100%);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 8px 18px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s, transform 0.15s;
            margin-top: 8px;
        }
        button[type="submit"]:hover, .btn-suppr:hover {
            background: linear-gradient(90deg, #1c5d8f 0%, #2980b9 100%);
            transform: translateY(-2px) scale(1.04);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 18px;
            background: #fafdff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(41,128,185,0.07);
        }
        th, td {
            padding: 12px 10px;
            text-align: center;
        }
        th {
            background: #2980b9;
            color: #fff;
            font-weight: 700;
            font-size: 1.08rem;
        }
        tr:nth-child(even) {
            background: #eaf6fb;
        }
        tr:nth-child(odd) {
            background: #fafdff;
        }
        td {
            color: #34495e;
        }
        .success-message {
            color: #27ae60;
            background: #eafbe7;
            border: 1.5px solid #b3e0ff;
            border-radius: 8px;
            padding: 10px 18px;
            margin-bottom: 18px;
            text-align: center;
            font-weight: 600;
        }
        .btn-suppr {
            background: #e74c3c;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 7px 14px;
            font-size: 0.98rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s, transform 0.15s;
        }
        .btn-suppr:hover {
            background: #c0392b;
            transform: scale(1.07);
        }
        .affiche-img {
            width: 60px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #b3e0ff;
            background: #eee;
        }
        @media (max-width: 700px) {
            .container { padding: 10px 2vw; }
            table, th, td { font-size: 0.97rem; }
            a.logout { float: none; display: block; margin: 0 auto 18px auto; text-align: center; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Tableau de bord administrateur</h2>
        <a href="admin_logout.php" class="logout">Déconnexion</a>
        <?php if (isset($message)) echo "<div class='success-message'>$message</div>"; ?>

        <h3>Ajouter une séance</h3>
        <form method="post" enctype="multipart/form-data">
            <label>Film : <input type="text" name="film" required></label>
            <label>Date et heure : <input type="datetime-local" name="date_heure" required></label>
            <label>Salle :
                <select name="id_salle" required>
                    <?php foreach ($salles as $salle): ?>
                        <option value="<?= $salle['id'] ?>"><?= htmlspecialchars($salle['nom']) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>Affiche (jpg, png, webp, gif) :
                <input type="file" name="photo" accept="image/*">
            </label>
            <button type="submit">Ajouter</button>
        </form>

        <h3>Liste des séances</h3>
        <table>
            <tr>
                <th>ID</th><th>Film</th><th>Date/Heure</th><th>Salle</th><th>Affiche</th><th>Action</th>
            </tr>
            <?php foreach ($seances as $seance): ?>
                <tr>
                    <td><?= $seance['id'] ?></td>
                    <td><?= htmlspecialchars($seance['film']) ?></td>
                    <td><?= $seance['date_heure'] ?></td>
                    <td><?= htmlspecialchars($seance['salle_nom']) ?></td>
                    <td>
                        <?php if (!empty($seance['photo'])): ?>
                            <img src="<?= htmlspecialchars($seance['photo']) ?>" class="affiche-img" alt="Affiche">
                        <?php else: ?>
                            <span style="color:#aaa;">Aucune</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="?delete_seance=<?= $seance['id'] ?>" class="btn-suppr" onclick="return confirm('Supprimer cette séance ?');">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <h3>Liste des utilisateurs</h3>
        <table>
            <tr>
                <th>ID</th><th>Nom</th><th>Prénom</th><th>Email</th><th>Action</th>
            </tr>
            <?php foreach ($utilisateurs as $user): ?>
                <tr>
                    <td><?= $user['id_utilisateur'] ?></td>
                    <td><?= htmlspecialchars($user['nom']) ?></td>
                    <td><?= htmlspecialchars($user['prenom']) ?></td>
                    <td><?= htmlspecialchars($user['mail']) ?></td>
                    <td>
                        <a href="?delete_user=<?= $user['id_utilisateur'] ?>" class="btn-suppr" onclick="return confirm('Supprimer cet utilisateur ?');">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <h3>Liste des réservations</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>Utilisateur</th>
                <th>Email</th>
                <th>Film</th>
                <th>Date/Heure</th>
                <th>Salle</th>
                <th>Rangée</th>
                <th>Place</th>
                <th>Date réservation</th>
                <th>Action</th>
            </tr>
            <?php foreach ($reservations as $resa): ?>
                <tr>
                    <td><?= $resa['id'] ?></td>
                    <td><?= htmlspecialchars($resa['prenom'] . ' ' . $resa['nom']) ?></td>
                    <td><?= htmlspecialchars($resa['mail']) ?></td>
                    <td><?= htmlspecialchars($resa['film']) ?></td>
                    <td><?= htmlspecialchars($resa['date_heure']) ?></td>
                    <td><?= htmlspecialchars($resa['salle_nom']) ?></td>
                    <td><?= htmlspecialchars($resa['rangee']) ?></td>
                    <td><?= htmlspecialchars($resa['place']) ?></td>
                    <td><?= htmlspecialchars($resa['date_reservation']) ?></td>
                    <td>
                        <a href="?delete_reservation=<?= $resa['id'] ?>" class="btn-suppr" onclick="return confirm('Supprimer cette réservation ?');">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>