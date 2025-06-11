<?php
session_start();

// Connexion à la base
$host = 'localhost';
$dbname = 'dbtest1';
$username = 'root';
$password = '';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion.");
}

$error = $success = '';
$token = $_GET['token'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($password === $confirmPassword) {
        // Vérification du token
        $query = $db->prepare('SELECT * FROM utilisateur WHERE verrification_token = :token');
        $query->execute([':token' => $token]);
        $user = $query->fetch();

        if ($user) {
            // Mise à jour du mot de passe
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $update = $db->prepare('UPDATE utilisateur SET mot_de_passe = :password, verrification_token = NULL WHERE verrification_token = :token');
            $update->execute([':password' => $hashedPassword, ':token' => $token]);
            $success = "Votre mot de passe a été réinitialisé avec succès.";
        } else {
            $error = "Lien invalide ou expiré.";
        }
    } else {
        $error = "Les mots de passe ne correspondent pas.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réinitialisation</title>
    <link rel="stylesheet" href="">
</head>
<body>
    <div class="login-container">
        <h2 class="login-title">Réinitialisation du mot de passe</h2>
        <div class="register-conteiner">
            <?php if ($error): ?>
                <p class="error-message"><?= htmlspecialchars($error); ?></p>
            <?php elseif ($success): ?>
                <p class="success-message"><?= htmlspecialchars($success); ?></p>
            <?php endif; ?>
            <form method="POST", class="register-form">
                <input type="password" name="password" placeholder="Nouveau mot de passe" class="input-field" required>
                <input type="password" name="confirm_password" placeholder="Confirmer mot de passe" class="input-field" required>
                <button type="submit" class="basic-button">Confirmer</button>
            </form>
        </div>
    </div>
</body>
</html>
