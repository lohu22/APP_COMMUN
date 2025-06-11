<?php

// Démarrer la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Connexion à la base de données
$host = 'localhost';
$dbname = 'bdtest1';
$username = 'root';
$password = '';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion. Veuillez réessayer plus tard.");
}

$error = $success = '';

require_once 'PHPmailer/mailer.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

    if ($email) {
        // Vérifier si l'email existe et que le compte est vérifié
        $query = $db->prepare('SELECT * FROM utilisateur WHERE mail = :mail AND isVerified = 1');
        $query->execute([':mail' => $email]);
        $user = $query->fetch();

        if ($user) {
            // Générer un token unique
            // Génération du token de réinitialisation unique
            do {
                $resetToken = bin2hex(random_bytes(16));
                $tokenQuery = $db->prepare('SELECT COUNT(*) FROM utilisateur WHERE verrification_token = :token');
                $tokenQuery->bindParam(':token', $resetToken, PDO::PARAM_STR);
                $tokenQuery->execute();
            } while ($tokenQuery->fetchColumn() > 0);

            // Mise à jour du token dans la base de données pour cet utilisateur
            $updateTokenQuery = $db->prepare('UPDATE utilisateur SET verrification_token = :token WHERE mail = :email');
            $updateTokenQuery->bindParam(':token', $resetToken, PDO::PARAM_STR);
            $updateTokenQuery->bindParam(':email', $email, PDO::PARAM_STR);
            $updateTokenQuery->execute();


            // Envoi de l'email
            $resetLink = "localhost/Capteurs/password_reset.php?token=$resetToken";
            $mailSent =  smtpmailer(
                $email,
                'servicecapteurs@gmail.com',
                'Capteurs',
                'Email Validation de compte',
                "Si vous n'avez pas demandé de réinitialisation de mot de passe, veuillez ignorer cet email. Réinitialisation du mot de passe. Cliquez ici pour réinitialiser votre mot de passe, <a href=$resetLink>cliquez ici</a>."
            );

            // Utiliser la réponse pour afficher un message
            if (strpos($mailSent, 'Erreur') !== false) {
                $errorMessage = $mailSent;
            } else {
                $successMessage = "Un email vous a été envoyé avec les instructions pour réinitialiser votre mot de passe.";
            }


            


            $success = "Un email vous a été envoyé avec les instructions pour réinitialiser votre mot de passe.";
        } else {
            $error = "Email introuvable ou compte non vérifié.";
        }
    } else {
        $error = "Veuillez saisir une adresse email valide.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mot de passe oublié</title>
    <link rel="stylesheet" href="template.css">
</head>
<body>
    <div class="login-container">
        <h2 class="login-title">Mot de passe oublié</h2>
        <div class="login-box">
            <?php if ($error): ?>
                <p class="error-message"><?= htmlspecialchars($error); ?></p>
            <?php elseif ($success): ?>
                <p class="success-message"><?= htmlspecialchars($success); ?></p>
            <?php endif; ?>
            <form action="index.php?cible=reset" method="POST">
                <input type="email" name="email" placeholder="Votre email" class="input-field" required>
                <button type="submit" class="basic-button">Continuer</button>
            </form>
        </div>
    </div>
</body>
</html>
