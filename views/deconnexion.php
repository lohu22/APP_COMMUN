<?php

// Démarrer la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Supprimer toutes les variables de session
session_unset();

// Détruire la session
session_destroy();



// Rediriger l'utilisateur vers la page de connexion
header("Location: index.php?cible=connexion");
exit;
?>
