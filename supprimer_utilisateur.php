<?php
require_once 'connexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_utilisateur'])) {
    $id = (int)$_POST['id_utilisateur']; // Assurez-vous que l'ID est un entier

    try {
        $stmt = $pdo->prepare("DELETE FROM utilisateur WHERE id_utilisateur = :id_utilisateur");
        $stmt->execute(['id_utilisateur' => $id]);

        // Redirigez vers la page principale après suppression
        header("Location: gestionprofile.php");
        echo "Utilisateur supprimé";
        exit;
    } catch (PDOException $e) {
        die("Erreur lors de la suppression de l'utilisateur : " . $e->getMessage());
    }
}
?>
