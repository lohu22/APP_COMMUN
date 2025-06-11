<?php
// Démarrage de la session
session_start();

// Connexion à la base de données
$host = 'localhost';
$dbname = 'bdlocal';
$username = 'root';
$password = '';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion.");
}

// Vérification du token
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $query = $db->prepare('SELECT id_utilisateur FROM utilisateur WHERE verrification_token = :token AND isVerified = 0');
    $query->bindParam(':token', $token, PDO::PARAM_STR);
    $query->execute();

    if ($query->rowCount() > 0) {
        // Mise à jour du statut de vérification
        $updateQuery = $db->prepare('UPDATE utilisateur SET isVerified = 1, verrification_token = NULL WHERE verrification_token = :token');
        $updateQuery->bindParam(':token', $token, PDO::PARAM_STR);
        $updateQuery->execute();

        echo "Votre email a été vérifié avec succès. Vous pouvez maintenant vous connecter.";
    } else {
        echo "Lien de vérification invalide ou déjà utilisé.";
    }
} else {
    echo "Aucun token fourni.";
}
?>
