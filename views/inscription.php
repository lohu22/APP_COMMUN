<?php

// Démarrage de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}



// Connexion à la base de données
$host = 'localhost';
$dbname = 'dbtest1'; // Nom de la base de données
$username = 'root'; // Identifiant
$password = ''; // Mot de passe (pour XAMPP)

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Ne pas afficher de détails en production
    die("Erreur de connexion. Veuillez contacter l'administrateur.");
}


// Initialisation des variables
$errorMessage = '';
$successMessage = '';

require_once 'PHPmailer/mailer.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération et validation des données
    $prenom = filter_input(INPUT_POST, 'prenom', FILTER_SANITIZE_STRING);
    $nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    $confirmPassword = filter_input(INPUT_POST, 'confirm_password', FILTER_SANITIZE_STRING);


    // Validation des champs obligatoires
    if (!$prenom || !$nom || !$email || !$password || !$confirmPassword) {
        $errorMessage = "Veuillez remplir tous les champs obligatoires.";
    } elseif ($password !== $confirmPassword) {
        $errorMessage = "Les mots de passe ne correspondent pas.";
    } else {
        try {
            // Vérifier si l'utilisateur existe déjà
            $checkQuery = $db->prepare('SELECT COUNT(*) FROM utilisateur WHERE mail = :email');
            $checkQuery->bindParam(':email', $email, PDO::PARAM_STR);
            $checkQuery->execute();

            if ($checkQuery->fetchColumn() > 0) {
                $errorMessage = "Un utilisateur avec cet email existe déjà.";
            } else {
                // Hachage du mot de passe
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // Génération du token de vérification qui n'est pas deja utilisé dans la base de données
                // car nous effacons le token de vérification après la vérification de l'email

                
                do {
                    $verrificationToken = bin2hex(random_bytes(16));
                    $tokenQuery = $db->prepare('SELECT COUNT(*) FROM utilisateur WHERE verrification_token = :token');
                    $tokenQuery->bindParam(':token', $verrificationToken, PDO::PARAM_STR);
                    $tokenQuery->execute();
                } while ($tokenQuery->fetchColumn() > 0);

                // Préparer la requête d'insertion
                $query = $db->prepare(
                    'INSERT INTO utilisateur (prenom, nom, mail, mot_de_passe, verrification_token, isVerified) 
                     VALUES (:prenom, :nom, :email, :password, :verrification_token, 0)'
                );

                // Lier les paramètres
                $query->bindParam(':prenom', $prenom, PDO::PARAM_STR);
                $query->bindParam(':nom', $nom, PDO::PARAM_STR);
                $query->bindParam(':email', $email, PDO::PARAM_STR);
                $query->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
                $query->bindParam(':verrification_token', $verrificationToken, PDO::PARAM_STR);

                // Exécuter la requête
                $query->execute();

                // Envoyer l'email de confirmation
                $mailSent = smtpmailer(
                    $email,
                    'mokistarmed@gmail.com',
                    'MKD',
                    'Email Validation de compte',
                    "Bonjour $prenom, veuillez confirmer votre inscription en cliquant sur ce lien : 
                    <a href='localhost/BeeHome/verify.php?token=$verrificationToken'>Confirmer mon email</a>"
                );

                // Utiliser la réponse pour afficher un message
                if (strpos($mailSent, 'Erreur') !== false) {
                    $errorMessage = $mailSent;
                } else {
                    $successMessage = $mailSent;
                }
            }
        } catch (Exception $e) {
            $errorMessage = "Une erreur est survenue : " . $e->getMessage();
        }
    }
}

// Affichage des messages
if ($errorMessage) {
    echo "<p style='color: red;'>$errorMessage</p>";
}

if ($successMessage) {
    echo "<p style='color: green;'>$successMessage</p>";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="">
</head>
<body>
    <div class="register-container">
        <h2>S'inscrire</h2>
        <?php if ($errorMessage): ?>
            <p class="error-message"><?= htmlspecialchars($errorMessage); ?></p>
        <?php elseif ($successMessage): ?>
            <p class="success-message"><?= htmlspecialchars($successMessage); ?></p>
        <?php endif; ?>
        <form action="index.php?cible=inscription" method="POST" enctype="multipart/form-data" class="register-form" onsubmit = "return validateForm()">
            <label for="prenom">Prénom :</label>
            <input type="text" name="prenom" id="prenom" class="input-field" required>

            <label for="nom">Nom :</label>
            <input type="text" name="nom" id="nom" class="input-field" required>

            <label for="email">Email :</label>
            <input type="email" name="email" id="mail" class="input-field" required>

            <label for="password">Mot de passe :</label>
            <input type="password" name="password" id="mot_de_passe" class="input-field" required>

            <label for="confirm_password">Confirmer le mot de passe :</label>
            <input type="password" name="confirm_password" id="confirm_password" class="input-field" required>

            <button type="submit" class="basic-button">S'inscrire</button>
        </form>
    </div>
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
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    </script>
    <script>
        function validateForm() {
            const password = document.getElementById("password").value;
            const confirmPassword = document.getElementById("confirm_password").value;

            if (password !== confirmPassword) {
                alert("Les mots de passe ne correspondent pas !");
                return false;
            }
            return true;
        }
    </script>
    <script>
        //Fonction pour verifier si l'email est valide
        //Et Fonction pour verifier si le mot de passe est valide
        function validateForm() {
            var email = document.getElementById("email").value;
            var password = document.getElementById("password").value;
            var error = document.getElementById("error");
            var text;

            error.style.padding = "10px";

            //Vérification de l'email tel que : /\S+@\S+\.\S+/
            // ce qui signifie que l'adresse email doit contenir un @ et un point dans cet ordre
            // et met en rouge la bordure de l'input si l'email n'est pas valide et reviens le curseur sur l'input et met en vert si l'email est valide

            if (email.match(/\S+@\S+\.\S+/)) {
                text = "Adresse email valide";
                error.innerHTML = text;
                error.style.color = "green";
            } else {
                text = "Adresse email non valide";
                error.innerHTML = text;
                error.style.color = "red";
                return false;
            }

            //Vérification du mot de passe tel que : /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,20}$/
            // ce qui signifie que le mot de passe doit contenir au moins un chiffre, une lettre minuscule, une lettre majuscule et doit être compris entre 6 et 20 caractères
            // et met en rouge la bordure de l'input si le mot de passe n'est pas valide et reviens le curseur sur l'input et met en vert si le mot de passe est valide (et c'est la bordure de l'input qui chage de couleur par le surlignage)
            // et met le pop up de l'erreur devant l'input en question (juste en dessous)
            
            if (password.match(/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,20}$/)) {
                text = "Mot de passe valide";
                error.innerHTML = text;
                error.style.color = "green";
                return true;
            } else {
                text = "Mot de passe non valide veuillez respecter les consignes de 6 à 20 caractères, une lettre majuscule, une lettre minuscule et un chiffre";
                error.innerHTML = text;
                error.style.color = "red";
                return false;
            }
        }

    </script>
</body>
</html>