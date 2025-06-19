<?php
if (isset($_POST['places'])) {
    foreach ($_POST['places'] as $place) {
        list($rangee, $placeNum) = explode('-', $place);
        // Vérifier que la place n'est pas déjà réservée, puis insérer
        $stmt = $pdo->prepare("INSERT INTO reservation (id_utilisateur, id_seance, rangee, place) VALUES (?, ?, ?, ?)");
        $stmt->execute([$_SESSION['utilisateur']['id_utilisateur'], $id_seance, $rangee, $placeNum]);
    }
    header('Location: confirmation.php');
    exit();
}