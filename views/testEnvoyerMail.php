<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <script src="/views/testEnvoyerMail.js"></script>
    <head>
        <title>Envoyer un code de vérification</title>
    </head>

    <form method="post" action="../controllers/verification_code.php">
        <input type="email" name="email" placeholder="Entrez votre email">
        <button type="button" onclick="sendVerificationCode()">Envoyer le code</button>
        <p id="code-msg"></p>
    </form>
    <body>

</body>
</html>

