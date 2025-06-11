<?php

// Démarrage de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si l'utilisateur est connecté
$isLoggedIn = isset($_SESSION['user_email']);
$isAdmin = isset($_SESSION['user_email']);


?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" type="text/css" href="">
</head>
<body>

<!-- Navbar jaune si utilisateur pas connecté ou simple utilisateur-->
<?php if ($isLoggedIn): ?>
    <nav>
        <ul class="nav">
            <li><a href="index.php?cible=menu">Menu</a></li>
            <li><a href="index.php?cible=Nos_Capteurs">Nos Capteurs</a></li>
            <li><a href="index.php?cible=Autres_Capteurs">Autres Capteurs</a></li>
            <li><a href="index.php?cible=faq">FAQ</a></li>
            <?php if ($isAdmin): ?>
                <li class="deroulant"><a href="#">Administration</a>
                    <ul class="sous">
                        <li><a href="index.php?cible=gestion_utilisateurs">Gestion des utilisateurs</a></li>
                        <li><a href="index.php?cible=gestion_capteurs">Gestion des capteurs</a></li>

                    </ul>
                </li>
            <?php endif; ?>
            <li class="deroulant"><a href="#">Profil</a>
                <ul class="sous">
                    <li><a href="index.php?cible=profile">Gestion du profil</a></li>
                    <li><a href="#" onclick="confirmLogout()">Déconnexion</a></li>
                </ul>
                </li>
        </ul>
    </nav>
<?php else: ?>
    <!-- Navbar si utilisateur pas connecté -->
    <nav>
        <ul class="nav">
            <li><a href="index.php?cible=menu">Menu</a></li>
            <li><a href="index.php?cible=faq">FAQ</a></li>
            <li><a href="index.php?cible=connexion">Connexion</a></li>
        </ul>
    </nav>
<?php endif; ?>






<!-- Bouton retour en haut -->
<a href="#" id="scroll-to-top">↑</a>

<script>
    // Fonction pour détecter le scroll et afficher/cacher le bouton
    window.onscroll = function() {
        var scrollToTopButton = document.getElementById("scroll-to-top");
        if (document.body.scrollTop > 50 || document.documentElement.scrollTop > 50) {
            scrollToTopButton.style.display = "block"; // Affiche le bouton si l'utilisateur a scrollé vers le bas
        } else {
            scrollToTopButton.style.display = "none"; // Cache le bouton si l'utilisateur est en haut
        }
    };

    // Fonction pour remonter en douceur
    document.getElementById('scroll-to-top').addEventListener('click', function(e) {
        e.preventDefault();
        window.scrollTo({
            top: 0,
            behavior: 'smooth' // Scroll fluide
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
    const dropdown = document.querySelector('.dropdown');
    const dropdownContent = dropdown.querySelector('.dropdown-content');

    dropdown.addEventListener('click', (e) => {
        e.preventDefault();
        dropdownContent.classList.toggle('active');
    });

    document.addEventListener('click', (e) => {
        if (!dropdown.contains(e.target)) {
            dropdownContent.classList.remove('active');
        }
    });
});
</script>

<!-- Deconnexion pop up -->
<script>
    function confirmLogout() {
        if (confirm("Êtes-vous sûr de vouloir vous déconnecter ?")) {
            window.location.href = "index.php?cible=deconnexion";
        }
        else {
            window.location.href = "index.php?cible=menu";
        }
    }
</script>


<section class="contact" id="contact">
    <h2 class="animate-title">Contactez-nous</h2>
    <p class="animate-text">Pour toute question ou demande, contactez-nous dès maintenant.</p>
    <form action="#" method="POST">
        <input type="text" placeholder="Votre nom" required>
        <input type="email" placeholder="Votre email" required>
        <textarea placeholder="Votre message" required></textarea>
        <button type="submit" class="cta-btn animate-btn">Envoyer</button>
    </form>
</section>

<footer>
    <p>&copy; 2025 Capteurs | Tous droits réservés</p>
</footer>



</body>
</html>

