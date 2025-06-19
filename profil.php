<?php
session_start();
if (!isset($_SESSION['utilisateur'])) {
    header('Location: connexion.php');
    exit();
}
require_once 'connexion_bdd.php'; // adapte le chemin si besoin

$user = $_SESSION['utilisateur'];
$id = $user['id_utilisateur'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $prenom = htmlspecialchars($_POST['prenom']);
    $nom = htmlspecialchars($_POST['nom']);
    $mail = filter_var($_POST['mail'], FILTER_SANITIZE_EMAIL);

    // Gestion du mot de passe
    $password = $_POST['password'];
    $updatePassword = !empty($password);

    // Gestion de la photo
    $photoPath = $user['photo'] ?? 'Frontend/default-avatar.png';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['photo']['tmp_name'];
        $fileName = uniqid() . '_' . basename($_FILES['photo']['name']);
        $uploadDir = 'uploads/photos/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $photoPath = $uploadDir . $fileName; // chemin relatif à la racine du projet
        move_uploaded_file($tmpName, $photoPath);
    }

    try {
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if ($updatePassword) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE utilisateur SET prenom=?, nom=?, mail=?, mot_de_passe=?, photo=? WHERE id_utilisateur=?");
            $stmt->execute([$prenom, $nom, $mail, $hashed_password, $photoPath, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE utilisateur SET prenom=?, nom=?, mail=?, photo=? WHERE id_utilisateur=?");
            $stmt->execute([$prenom, $nom, $mail, $photoPath, $id]);
        }
        // Mettre à jour la session
        $_SESSION['utilisateur']['prenom'] = $prenom;
        $_SESSION['utilisateur']['nom'] = $nom;
        $_SESSION['utilisateur']['mail'] = $mail;
        $_SESSION['utilisateur']['photo'] = $photoPath;
        $success = "Profil mis à jour avec succès.";
    } catch (PDOException $e) {
        $error = "Erreur lors de la mise à jour : " . $e->getMessage();
    }
}
$user = $_SESSION['utilisateur'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Mon Profil</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #74ebd5 0%, #ACB6E5 100%);
      margin: 0;
      min-height: 100vh;
    }
    .profile-container {
      background: white;
      max-width: 400px;
      margin: 100px auto 0 auto;
      border-radius: 20px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.12);
      padding: 40px 30px;
      text-align: center;
    }
    .profile-pic {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid #2980b9;
      background: #fff;
      margin-bottom: 20px;
    }
    h2 {
      margin: 10px 0 5px 0;
      font-size: 1.5rem;
      color: #2980b9;
    }
    .profile-info {
      font-size: 1.1rem;
      color: #2c3e50;
      margin-bottom: 10px;
    }
    .logout-btn, .update-btn {
      margin-top: 25px;
      padding: 12px 30px;
      background: linear-gradient(90deg, #2980b9 0%, #6dd5fa 100%);
      color: #fff;
      font-weight: 700;
      font-size: 1rem;
      border: none;
      border-radius: 12px;
      cursor: pointer;
      transition: background 0.3s, transform 0.2s;
      margin-bottom: 10px;
    }
    .logout-btn:hover, .update-btn:hover {
      background: linear-gradient(90deg, #1c5d8f 0%, #2980b9 100%);
      transform: translateY(-2px) scale(1.03);
    }
    .profile-form label {
      display: block;
      margin-top: 10px;
      font-weight: 600;
      text-align: left;
    }
    .profile-form input[type="text"],
    .profile-form input[type="email"],
    .profile-form input[type="password"] {
      width: 100%;
      padding: 10px;
      border: 1.5px solid #ccc;
      border-radius: 8px;
      margin-top: 5px;
      font-size: 1rem;
      margin-bottom: 5px;
    }
    .profile-form input[type="file"] {
      margin-top: 10px;
      margin-bottom: 10px;
    }
    .success-message {
      color: green;
      margin-bottom: 10px;
    }
    .error-message {
      color: red;
      margin-bottom: 10px;
    }
  </style>
</head>
<body>
  <?php include 'Frontend/header.php'; ?>
  <div class="profile-container">
    <?php if (!empty($success)): ?>
      <div class="success-message"><?= $success ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
      <div class="error-message"><?= $error ?></div>
    <?php endif; ?>
    <form class="profile-form" method="post" enctype="multipart/form-data">
        <img 
             src="<?= !empty($user['photo']) ? '/APP_COMMUN/' . ltrim($user['photo'], '/\\') : '/APP_COMMUN/Frontend/default-avatar.png' ?>" 
             alt="Photo de profil" class="profile-pic">
      <div>
        <label for="photo">Changer la photo :</label>
        <input type="file" name="photo" id="photo" accept="image/*">
      </div>
      <label for="prenom">Prénom :</label>
      <input type="text" name="prenom" id="prenom" value="<?= htmlspecialchars($user['prenom']) ?>" required>
      <label for="nom">Nom :</label>
      <input type="text" name="nom" id="nom" value="<?= htmlspecialchars($user['nom']) ?>" required>
      <label for="mail">Email :</label>
      <input type="email" name="mail" id="mail" value="<?= htmlspecialchars($user['mail']) ?>" required>
      <label for="password">Nouveau mot de passe :</label>
      <input type="password" name="password" id="password" placeholder="Laisser vide pour ne pas changer">
      <button type="submit" class="update-btn" name="update">Enregistrer les modifications</button>
    </form>
    <form method="post" action="logout.php">
      <button type="submit" class="logout-btn">Se déconnecter</button>
    </form>
  </div>
</body>
</html>