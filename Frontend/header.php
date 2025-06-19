<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Charger la photo de profil depuis la base de données pour garantir l'affichage le plus à jour
$photoPath = '/APP_COMMUN/Frontend/default-avatar.png';
if (isset($_SESSION['utilisateur']['id_utilisateur'])) {
    require_once __DIR__ . '/../connexion_bdd.php';
    $stmt = $pdo->prepare("SELECT photo FROM utilisateur WHERE id_utilisateur = ?");
    $stmt->execute([$_SESSION['utilisateur']['id_utilisateur']]);
    $userPhoto = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!empty($userPhoto['photo'])) {
        $photoPath = '/APP_COMMUN/' . ltrim($userPhoto['photo'], '/\\');
    }
}
?>
<header id="header" class="navbar">
  <a href="/APP_COMMUN/Frontend/index.html" class="logo-link">
    <img src="/APP_COMMUN/Frontend/logo.png" alt="SHOW PILOT" style="height: 150px; width: auto;">
  </a>
  <nav>
    <ul>
      <div class="nav-links">
        <li>
          <a href="/APP_COMMUN/Frontend/capteurs.html" class="btn-connexion">Capteurs</a>
        </li>
        <?php if (isset($_SESSION['utilisateur'])): ?>
          <li>
            <a href="/APP_COMMUN/profil.php" class="profile-link" title="Profil">
              <img 
                 src="<?= $photoPath ?>" 
                 alt="Profil" class="profile-pic">
            </a>  
          </li>
        <?php else: ?>
          <li>
            <a href="/APP_COMMUN/Frontend/connexion.php" class="btn-connexion">Connexion</a>
          </li>
        <?php endif; ?>
      </div>
    </ul>
  </nav>  
</header>
<script>
  document.addEventListener("DOMContentLoaded", () => {
    let lastScrollTop = 0;
    const header = document.getElementById('header');
    header.style.top = '0px';
    window.addEventListener('scroll', () => {
      let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
      if(scrollTop > lastScrollTop + 10) {
        header.style.top = '-70px';
      } else if(scrollTop < lastScrollTop) {
        header.style.top = '0px';
      }
      lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
    });
  });
</script>
<style>
body {
  margin: 0;
  font-family: 'Segoe UI', sans-serif;
}
html, body {
  width: 100%;
  margin: 0;
  padding: 0;
}
.navbar {
  background: linear-gradient(90deg, #cbeaee 0%, #b3e0ff 50%, #99cef1 100%);
  color: white;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.5rem 2rem;
  position: fixed; 
  top: 0;
  left: 0;
  right: 0;
  width: 100%;
  height: 70px;
  box-sizing: border-box;
  z-index: 9999;
  transition: top 0.3s ease;
}
.logo-link {
  display: flex;
  align-items: center;
}
.navbar nav ul {
  list-style: none;
  display: flex;
  gap: 2rem;
  margin: 0;
  padding: 0;
}
.nav-links {
  display: flex;
  align-items: center;
  gap: 18px;
}
.nav-links a {
  text-decoration: none;
  color: #2c3e50;
  font-weight: 500;
  padding: 8px 18px;
  border-radius: 8px;
  transition: background 0.2s, color 0.2s;
}
.nav-links a:hover {
  background: #f0f6fa;
  color: #2980b9;
}
.nav-links a,
.nav-links a:hover,
.nav-links a:focus {
  text-decoration: none;
}
.btn-connexion {
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(90deg, #2980b9 0%, #6dd5fa 100%);
  color: #fff !important;
  font-weight: 700;
  font-size: 1.08rem;
  border: none;
  border-radius: 12px;
  cursor: pointer;
  box-shadow: 0 4px 16px rgba(41,128,185,0.10);
  transition: background 0.3s, transform 0.2s;
  padding: 12px 28px;
  margin-left: 10px;
  text-align: center;
  outline: none;
  user-select: none;
}
.btn-connexion:hover, .btn-connexion:focus {
  background: linear-gradient(90deg, #1c5d8f 0%, #2980b9 100%);
  transform: translateY(-2px) scale(1.03);
  color: #fff !important;
}
.profile-link {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0;
  margin-left: 10px;
}
.profile-pic {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #2980b9;
  background: #fff;
  box-shadow: 0 2px 8px rgba(41,128,185,0.10);
  transition: border-color 0.2s;
}
.profile-pic:hover, .profile-pic:focus {
  border-color: #1c5d8f;
}
</style>