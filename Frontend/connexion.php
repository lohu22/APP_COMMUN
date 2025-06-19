<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../connexion_bdd.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mail = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // Identifiants admin (à adapter selon ta base ou à sécuriser)
    $admin_email = 'admin@admin.com';
    $admin_password = 'admin123'; // Mot de passe en clair pour l'exemple

    // Si l'admin se connecte
    if ($mail === $admin_email && $password === $admin_password) {
        $_SESSION['admin'] = true;
        header('Location: ../admin_dashboard.php');
        exit();
    }

    try {
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE mail = ?");
        $stmt->execute([$mail]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['mot_de_passe'])) {
            $_SESSION['utilisateur'] = [
                'id_utilisateur' => $user['id_utilisateur'],
                'prenom' => $user['prenom'],
                'nom' => $user['nom'],
                'mail' => $user['mail'],
                'photo' => $user['photo'] ?? null
            ];
            header('Location: index.php'); // adapte si besoin
            exit();
        } else {
            $error = "Identifiants incorrects.";
        }
    } catch (PDOException $e) {
        $error = "Erreur lors de la connexion : " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Connexion - APP</title>
  <style>
   body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background: linear-gradient(135deg, #74ebd5 0%, #ACB6E5 100%);
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  margin: 0;
  color: #2c3e50;
}
.container {
  background: white;
  padding: 40px 50px;
  border-radius: 20px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.12);
  width: 400px;
  max-width: 95vw;
  text-align: center;
}
h2 {
  margin-bottom: 25px;
  font-weight: 700;
}
form {
  display: flex;
  flex-direction: column;
  gap: 15px;
  text-align: left;
}
label {
  font-weight: 600;
  margin-bottom: 5px;
  display: block;
}
input {
  width: 100%;
  padding: 12px 15px;
  border: 1.8px solid #ccc;
  border-radius: 12px;
  font-size: 1rem;
  transition: border-color 0.3s;
}
input:focus {
  border-color: #2980b9;
  outline: none;
}
button.btn-connexion {
  padding: 15px;
  background-color: #2980b9;
  color: white;
  font-weight: 700;
  font-size: 1.1rem;
  border: none;
  border-radius: 15px;
  cursor: pointer;
  transition: background-color 0.3s ease;
  margin-top: 10px;
  margin-bottom: 10px;
  width: 100%;
}
button.btn-connexion:hover {
  background-color: #1c5d8f;
}
.error {
  color: red;
  margin-bottom: 15px;
  text-align: center;
}

/* Nouveau bloc pour le bas du formulaire */
.form-bottom {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  margin-bottom: 5px;
}
.side-text {
  font-size: 1rem;
  color: #2c3e50;
  font-weight: 500;
  margin: 0;
  padding: 0;
  white-space: nowrap;
}
.btn-inscription {
  background: none;
  border: none;
  color: #2980b9;
  font-size: 1rem;
  font-weight: 600;
  text-decoration: underline;
  cursor: pointer;
  padding: 0;
  margin: 0;
  transition: color 0.2s;
  display: inline;
  line-height: 1;
  vertical-align: middle;
}
.btn-inscription:hover, .btn-inscription:focus {
  color: #1c5d8f;
  text-decoration: underline;
}
    
  </style>
</head>
<body>
  <div id="header"></div>
  <div class="container">
    <h2>Connexion</h2>
    <?php if (!empty($error)): ?>
      <div class="error"><?= $error ?></div>
    <?php endif; ?>
    <form method="POST">
  <label for="email">Adresse e-mail</label>
  <input type="email" id="email" name="email" placeholder="exemple@mail.com" required />

  <label for="password">Mot de passe</label>
  <input type="password" id="password" name="password" placeholder="Votre mot de passe" required minlength="6" />

  <button type="submit" class="btn-connexion">Se connecter</button>
  <div class="form-bottom">
    <span class="side-text">Pas de compte&nbsp;?</span>
    <a href="/APP_COMMUN/Frontend/inscription.php" class="btn-inscription">Inscription</a>
  </div>
</form>
  </div>
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      fetch("/APP_COMMUN/Frontend/header.php")
        .then(response => response.text())
        .then(data => {
          document.getElementById("header").innerHTML = data;
        })
        .catch(error => {
          console.error("Erreur lors du chargement du header :", error);
        });
    });
  </script>
</body>
</html>