<?php
require_once 'connexion.php';

$search = $_GET['search'] ?? ''; // Récupérer le terme de recherche depuis l'URL

try {
    if (!empty($search)) {
        // Requête SQL avec recherche
        $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE nom LIKE :search OR mail LIKE :search");
        $stmt->execute(['search' => "%$search%"]);
    } else {
        // Requête SQL sans filtre
        $stmt = $pdo->query("SELECT * FROM utilisateur");
    }
    $utilisateur = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur lors de la récupération des utilisateurs : " . $e->getMessage());
}
?>
