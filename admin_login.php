<?php
session_start();
if (isset($_POST['login']) && isset($_POST['password'])) {
    // Identifiants admin en dur (à sécuriser en prod)
    $admin_user = 'admin';
    $admin_pass = 'admin123'; // Change ce mot de passe !

    if ($_POST['login'] === $admin_user && $_POST['password'] === $admin_pass) {
        $_SESSION['admin'] = true;
        header('Location: admin_dashboard.php');
        exit();
    } else {
        $error = "Identifiants incorrects";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion Admin</title>
</head>
<body>
    <h2>Connexion Administrateur</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="post">
        <label>Login : <input type="text" name="login"></label><br>
        <label>Mot de passe : <input type="password" name="password"></label><br>
        <button type="submit">Connexion</button>
    </form>
</body>
</html>