<?php
session_start();
require 'model/connexionserv.php';

$cible = isset($_GET['cible']) ? $_GET['cible'] : 'menu';

switch ($cible) {
    case 'connexion':
        include 'views/connexion.php';
        break;
    case 'menu':
        include 'views/menu.php';
        break;
    case 'inscription':
        include 'views/inscription.php';
        break;
    case 'profile':
        include 'views/profile.php';
        break;
    case 'Nos_Capteurs':
        include 'views/capteurs.php';
        break;
    case 'Autres_Capteurs':
        include 'views/groupe.php';
        break;
    case 'deconnexion':
        include 'views/deconnexion.php';
        break;
    case 'reset':
        include 'views/reset.php';
        break;
    case 'password_reset':
        include 'views/password_reset.php';
        break;
    case 'faq':
        include 'views/FAQ.php';
        break;
    case 'gestion_utilisateurs':
        include 'views/gestionprofile.php';
        break;
    case 'gestion_capteurs':
        include 'views/gestion_capteurs.php';
        break;
    case 'password_reset':
        include 'views/password_reset.php';
        break;
    // Ajouter d'autres cas pour "annonce", "faq", etc.
    default:
        include 'views/menu.php';
        break;
    
}
?>
