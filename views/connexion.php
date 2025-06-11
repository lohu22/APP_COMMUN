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
    die("Erreur de connexion. Veuillez contacter l'administrateur.");
}

// Traitement du formulaire
$errorMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifiant = filter_input(INPUT_POST, 'identifiant', FILTER_VALIDATE_EMAIL);
    $motDePasse = filter_input(INPUT_POST, 'motdepasse', FILTER_SANITIZE_STRING);

    if ($identifiant && $motDePasse) {
        try {
            // Recherche de l'utilisateur dans la base de données
            $query = $db->prepare('SELECT * FROM utilisateur WHERE mail = :mail');
            $query->bindParam(':mail', $identifiant, PDO::PARAM_STR);
            $query->execute();
            $user = $query->fetch(PDO::FETCH_ASSOC);

            // Vérification du mot de passe
            if ($user && password_verify($motDePasse, $user['mot_de_passe'])) {
                // Verification que l'utilisateur est bien verifié son email
                if ($user['isVerified'] == 1) {
                    // Connexion réussie
                    $_SESSION['user_email'] = $user['mail'];
                    $_SESSION['user_name'] = htmlspecialchars($user['prenom'] . ' ' . $user['nom']);

                    // Message de confirmation et redirection
                    echo '<script>
                        alert("Vous êtes connecté !");
                        window.location.href = "index.php?cible=menu";
                    </script>';
                    exit;
                } else {
                    // Popup JavaScript pour compte non vérifié
                    echo '<script>
                        if (confirm("Votre compte n\'est pas encore vérifié. Veuillez vérifier votre email.")) {
                            window.location.href = "index.php?cible=connexion";
                        }
                    </script>';
                }
            } else {
                $errorMessage = "Identifiant ou mot de passe incorrect.";
            }
        } catch (Exception $e) {
            $errorMessage = "Une erreur est survenue. Veuillez réessayer.";
        }
    } else {
        $errorMessage = "Veuillez remplir tous les champs.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" type="text/css" href="">
</head>
<body>
    <!-- Navbar -->

    <nav>
<ul class="nav">
        <li><a href="index.php?cible=menu">Menu</a></li>
        <li><a href="index.php?cible=faq">FAQ</a></li>
        <li><a class="active" href="index.php?cible=connexion">Connexion</a></li>


</ul>
</nav>


    <!-- Formulaire de connexion -->
    <div class="login-container">
        <h2 class="login-title">Connexion</h2>
        <div class="login-box">
            <?php if (!empty($errorMessage)) : ?>
                <p class="error-message"><?= htmlspecialchars($errorMessage); ?></p>
            <?php endif; ?>
            <form action="index.php?cible=connexion" method="POST">
                <input type="email" name="identifiant" placeholder="Identifiant" class="input-field" required>
                <input type="password" name="motdepasse" placeholder="Mot de passe" class="input-field" required>
                <button type="submit" class="basic-button">Se connecter</button>
            </form>
            <p class="create-account">
                Pas encore de compte ? <a href="index.php?cible=inscription">Créer un compte</a>
            </p>
            <p class="create-account">
                <a href="index.php?cible=reset">Mot de passe oublié ?</a>
            </p>
        </div>
    </div>
</body>
</html>
