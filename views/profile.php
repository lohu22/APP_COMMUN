<?php
// Démarrage de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Connexion à la base de données
$host = 'localhost';
$dbname = 'dbtest1'; // Nom de la base de données
$username = 'root'; // Identifiant
$password = ''; // Mot de passe (pour XAMPP)

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Ne pas afficher de détails en production
    die("Erreur de connexion. Veuillez contacter l'administrateur.");
}

// Récupération des informations de l'utilisateur depuis la base
$user_email = $_SESSION['user_email'];

// Connexion à la base de données
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE mail = ?");
    $stmt->execute([$user_email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        die("Utilisateur introuvable.");
    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" type="text/css" href="">
</head>
<body>
<div class="profile-container">
    <!-- Section photo -->
    <div class="photo-section">
        <img id="profile-pic" src="uploads/<?php echo htmlspecialchars($user['photo'] ?? 'default.jpg'); ?>" alt="Photo de profil">
        <input type="file" id="photo-input" accept="image/*">
    </div>

    <!-- Formulaire d'édition -->
    <div class="profile-form">
        <h2>Gestion du Profil</h2>
        <form id="profile-form">
            <label>Nom</label>
            <input type="text" name="lastname" value="<?php echo htmlspecialchars($user['lastname'] ?? ''); ?>">

            <label>Prénom</label>
            <input type="text" name="firstname" value="<?php echo htmlspecialchars($user['firstname'] ?? ''); ?>">

            <label>Date de naissance</label>
            <input type="date" name="dob" value="<?php echo htmlspecialchars($user['dob'] ?? ''); ?>">

            <label>Genre</label>
            <select name="gender">
                <option value="Homme" <?php echo ($user['gender'] ?? '') === 'Homme' ? 'selected' : ''; ?>>Homme</option>
                <option value="Femme" <?php echo ($user['gender'] ?? '') === 'Femme' ? 'selected' : ''; ?>>Femme</option>
            </select>

            <label>Pays</label>
            <input type="text" name="country" value="<?php echo htmlspecialchars($user['country'] ?? ''); ?>">

            <label>Langue</label>
            <input type="text" name="language" value="<?php echo htmlspecialchars($user['language'] ?? ''); ?>">

            <label>Email</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>">

            <button type="submit" class="basic-button">Mettre à jour</button>
        </form>
    </div>
</div>

<script>
// Prévisualisation de la photo
document.getElementById('photo-input').addEventListener('change', function(e) {
    const reader = new FileReader();
    reader.onload = function(event) {
        document.getElementById('profile-pic').src = event.target.result;
    };
    reader.readAsDataURL(e.target.files[0]);
});

// Envoi des données via AJAX
document.getElementById('profile-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const photoFile = document.getElementById('photo-input').files[0];
    if (photoFile) formData.append('photo', photoFile);

    fetch('update_profile.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Profil mis à jour avec succès.');
            location.reload();
        } else {
            alert('Erreur : ' + data.message);
        }
    })
    .catch(error => console.error('Erreur AJAX:', error));
});
</script>
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

