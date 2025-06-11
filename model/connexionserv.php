<?php
// Connexion à la base de données
$host = 'localhost';
$dbname = 'dbtest1'; // Nom de la base de données
$username = 'root'; // Identifiant
$password = ''; // Mot de passe (pour XAMPP)

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>

