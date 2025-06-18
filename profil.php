<?php

session_start();
if (!isset($_SESSION['utilisateur'])) {
    header('Location: connexion.php');
    exit();
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
    .logout-btn {
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
    }
    .logout-btn:hover {
      background: linear-gradient(90deg, #1c5d8f 0%, #2980b9 100%);
      transform: translateY(-2px) scale(1.03);
    }
  </style>
</head>
<body>
  <?php include 'Frontend/header.php'; ?>
  <div class="profile-container">
    <img 
      src="<?= !empty($user['photo']) ? htmlspecialchars($user['photo']) : 'Frontend/default-avatar.png' ?>" 
      alt="Photo de profil" class="profile-pic">
    <h2><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></h2>
    <div class="profile-info"><?= htmlspecialchars($user['mail']) ?></div>
    <form method="post" action="logout.php">
      <button type="submit" class="logout-btn">Se déconnecter</button>
    </form>
  </div>
</body>
</html>