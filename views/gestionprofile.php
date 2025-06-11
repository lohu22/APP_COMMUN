<?php
require_once 'recuperation.php'; // Inclure la récupération



// Démarrage de la session
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}



// Vérifier si l'utilisateur est connecté
$isLoggedIn = isset($_SESSION['user_email']);
$isAdmin =  isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
?>

<!DOCTYPE html>
<html lang="fr">
    <style>
        .search-bar {
            width: 60%; 
            max-width: 600px;
            padding: 12px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .search-button {
            background-color:rgb(77, 7, 255); /* Jaune */
            color: black; /* Texte noir */
            border: none;
            border-radius: 5px;
            padding: 12px 24px;
            font-size: 16px;
            cursor: pointer;
        }

        .search-button:hover {
            background-color:rgb(50, 11, 247); /* Jaune plus foncé au survol */
        }
    </style>
    
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion de Profile</title>
    <link rel="stylesheet" type="text/css" href="">
</head>
<body>
<!-- Navbar jaune si utilisateur pas connecté ou simple utilisateur-->
<?php if ($isLoggedIn): ?>
    <nav>
        <ul class="nav">
            <li><a href="index.php?cible=menu">Menu</a></li>
            <li><a href="index.php?cible=groupe">Nos Capteurs</a></li>
            <li><a href="index.php?cible=gestion-u">Autres Capteurs</a></li>
            <li><a href="index.php?cible=faq">FAQ</a></li>
            <?php if ($isAdmin): ?>
                <li class="deroulant"><a href="#">Administration</a>
                    <ul class="sous">
                        <li><a href="index.php?cible=gestion_utilisateurs">Gestion des utilisateurs</a></li>
                        <li><a href="index.php?cible=gestion_capteurs">Gestion des capteurs</a></li>
                    </ul>
                </li>
            <?php endif; ?>
            <li class="deroulant"><a href="#">Profil</a>
                <ul class="sous">
                    <li><a href="index.php?cible=profile">Gestion du profil</a></li>
                    <li><a href="#" onclick="confirmLogout()">Déconnexion</a></li>
                </ul>
                </li>
        </ul>
    </nav>
<?php else: ?>
    <!-- Navbar si utilisateur pas connecté -->
    <nav>
        <ul class="nav">
            <li><a href="index.php?cible=menu">Menu</a></li>
            <li><a href="index.php?cible=faq">FAQ</a></li>
            <li><a href="index.php?cible=connexion">Connexion</a></li>
        </ul>
    </nav>
<?php endif; ?>

    <div class="image-container">
        <div class="overlayb">Page de Gestion des Profils</div>
    </div>
    <form method="GET" action="gestionprofile.php">
        <input type="text" name="search" placeholder="Rechercher un utilisateur..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
        style="width: 715px; padding: 8px;"
        >
        <button type="submit" style="width: 100px ;padding: 10px; font-size: 16px" class="search-button">Rechercher</button>
    </form>

    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Prenom</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Genre</th>
                <th>Date de naissance</th>
                <th>Pays</th>
                <th>Langue</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($utilisateur as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['id_utilisateur']) ?></td>
                    <td><?= htmlspecialchars(string:$user['prenom']) ?> </td>
                    <td><?= htmlspecialchars($user['nom']) ?></td>
                    <td><?= htmlspecialchars($user['mail']) ?></td>
                    <td><?= htmlspecialchars($user['genre']) ?></td>
                    <td><?= htmlspecialchars($user['date_naissance']) ?></td>
                    <td><?= htmlspecialchars($user['pays']) ?></td>
                    <td><?= htmlspecialchars($user['langue']) ?></td>
                    <td>
                            <!-- Formulaire de suppression -->
                            <form method="POST" action="supprimer_utilisateur.php" style="display:inline;">
                                <input type="hidden" name="id_utilisateur" value="<?= htmlspecialchars($user['id_utilisateur']) ?>">
                                <button type="submit" onclick="return confirm('Voulez-vous vraiment supprimer cet utilisateur ?');">Supprimer</button>
                            </form>
                        </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<!-- Deconnexion pop up -->
<script>
    function confirmLogout() {
        if (confirm("Êtes-vous sûr de vouloir vous déconnecter ?")) {
            window.location.href = "index.php?cible=deconnexion";
        }
        else {
            window.location.href = "index.php?cible=menu";
        }
    }
</script>
</body>
</html>