<?php
// search_users.php

header('Content-Type: application/json');

// Connexion à la base de données
$host = 'localhost'; // Remplacez par vos informations
$dbname = 'dbtest1';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer le terme de recherche depuis la requête
    $term = isset($_GET['q']) ? trim($_GET['q']) : '';

    if (!empty($term)) {
        // Requête pour chercher les utilisateurs par prénom ou nom
        $stmt = $pdo->prepare("SELECT id_utilisateur, prenom, nom, nb_Point FROM utilisateur WHERE prenom LIKE :term OR nom LIKE :term");
        $stmt->execute(['term' => "%$term%"]);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($users);
    } else {
        echo json_encode([]); // Retourne un tableau vide si aucun terme n'est fourni
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Erreur de connexion : ' . $e->getMessage()]);
}
