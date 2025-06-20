<?php
require_once '../connexion_bdd.php';
require_once 'header.php';

// Récupérer les 6 prochaines séances (annonces)
$stmt = $pdo->query("SELECT film, date_heure, photo FROM seance WHERE date_heure >= NOW() ORDER BY date_heure ASC LIMIT 6");
$annonces = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Regrouper les annonces par 3 pour le carrousel (3 par slide)
$slides = array_chunk($annonces, 3);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ShowPilot - Accueil</title>
  <link rel="stylesheet" href="style.css">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --color-primary: #0a0a23;
      --color-accent: #5ce1e6;
      --color-bg-light: #f9f9fb;
      --color-card: #ffffff;
      --color-text: #222;
      --font-main: 'Montserrat', sans-serif;
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    html, body { width: 100%; margin: 0; padding: 0; }
    body {
      font-family: var(--font-main);
      background-color: var(--color-bg-light);
      color: var(--color-text);
      line-height: 1.6;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    main { flex: 1; display: flex; flex-direction: column; }

    /* Décalage de la rubrique confort vers le bas */
    .comfort-method-section {
      max-width: 1100px;
      margin: 120px auto 2.5rem auto; /* Augmente la marge haute */
      padding: 2rem 2rem;
      background: var(--color-card);
      border-radius: 14px;
      box-shadow: 0 2px 12px rgba(0,0,0,0.06);
      display: flex;
      align-items: center;
      gap: 2.5rem;
    }
    .comfort-method-img {
      flex-shrink: 0;
      width: 220px;
      height: 220px;
      border-radius: 12px;
      object-fit: cover;
      box-shadow: 0 2px 12px rgba(41,128,185,0.10);
      background: #eaf6fb;
    }
    .comfort-method-text {
      flex: 1;
      text-align: left;
    }
    .comfort-method-text h3 {
      color: #2980b9;
      font-size: 1.5rem;
      margin-bottom: 0.7rem;
      text-align: center;
      width: 100%;
      display: block;
    }
    .comfort-method-text p {
      font-size: 1.13rem;
      color: #222;
      margin-bottom: 0.5rem;
    }

    .carousel-section {
      max-width: 1100px;
      margin: 0 auto 3rem;
      padding: 2rem 1rem;
      background: var(--color-card);
      border-radius: 12px;
      box-shadow: 0 2px 12px rgba(0,0,0,0.05);
      text-align: center;
    }
    .carousel-section h2 {
      font-size: 1.7rem;
      margin-bottom: 1.2rem;
      color: var(--color-primary);
    }
    .carousel-container {
      position: relative;
      overflow: hidden;
      width: 100%;
      max-width: 900px;
      margin: 0 auto;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .carousel-btn {
      background: var(--color-accent);
      border: none;
      color: #fff;
      font-size: 2rem;
      border-radius: 50%;
      width: 48px;
      height: 48px;
      cursor: pointer;
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      z-index: 2;
      opacity: 0.9;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 2px 8px rgba(41,128,185,0.12);
      transition: background 0.2s, opacity 0.2s;
    }
    .carousel-btn svg {
      width: 1.5em;
      height: 1.5em;
      display: block;
    }
    .carousel-btn.prev { left: 0; }
    .carousel-btn.next { right: 0; }
    .carousel-btn:hover { background: #009ffd; opacity: 1; }
    .carousel-track {
      display: flex;
      transition: transform 0.5s cubic-bezier(.77,0,.18,1);
      width: 100%;
    }
    .carousel-slide {
      display: flex;
      gap: 32px;
      min-width: 100%;
      justify-content: center;
      align-items: stretch;
    }
    .carousel-item {
      background: #eaf6fb;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(41,128,185,0.07);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: flex-start;
      min-width: 180px;
      max-width: 200px;
      width: 180px;
      margin: 0 8px;
      padding: 1rem 1rem 1.2rem 1rem;
      font-size: 1.08rem;
      color: var(--color-primary);
      transition: box-shadow 0.2s;
    }
    .carousel-item img {
      width: 100%;
      height: 220px;
      object-fit: cover;
      border-radius: 6px;
      margin-bottom: 0.7rem;
      box-shadow: 0 2px 8px rgba(41,128,185,0.10);
      background: #ddd;
    }
    .carousel-item .annonce-titre {
      font-weight: 600;
      font-size: 1.08rem;
      margin-bottom: 0.3rem;
      color: #2980b9;
      text-align: center;
    }
    .carousel-item .annonce-date {
      font-size: 0.98rem;
      color: #222;
      text-align: center;
    }
    @media (max-width: 900px) {
      .comfort-method-section {
        flex-direction: column;
        align-items: flex-start;
        padding: 1.2rem 0.5rem;
        gap: 1.2rem;
        margin-top: 60px;
      }
      .comfort-method-img {
        width: 100%;
        max-width: 320px;
        height: 160px;
        margin: 0 auto;
      }
      .comfort-method-text { text-align: left; }
      .carousel-slide { gap: 12px; }
      .carousel-item { min-width: 140px; max-width: 150px; width: 140px; font-size: 0.98rem; }
      .carousel-item img { height: 120px; }
    }
    @media (max-width: 600px) {
      .carousel-section { padding: 1rem 0.2rem; }
      .carousel-slide { gap: 6px; }
      .carousel-item { min-width: 110px; max-width: 120px; width: 110px; font-size: 0.9rem; }
      .carousel-item img { height: 80px; }
    }
    footer {
      background-color: var(--color-primary);
      color: white;
      text-align: center;
      padding: 1.5rem;
      margin-top: auto;
    }
    footer a {
      color: var(--color-accent);
      text-decoration: none;
    }
    footer a:hover {
      text-decoration: underline;
    }
    .navbar, .hero, footer { width: 100%; }
  </style>
</head>
<body>
  <div id="header"></div>
  <main>
    <!-- Comfort method section -->
    <section class="comfort-method-section">
      <img class="comfort-method-img" src="/APP_COMMUN/assets/capteurs_confort.jpg" alt="Capteurs de confort">
      <div class="comfort-method-text">
        <h3>Le confort, c’est (presque) de la magie !</h3>
        <p>
          Chez ShowPilot, on ne laisse rien au hasard pour le bien-être de nos spectateurs. Grâce à nos capteurs malins, la salle devient un véritable espace intelligent : la température, la lumière et même l’air sont surveillés en temps réel. 
        </p>
        <p>
          Résultat ? Fini les salles trop chaudes ou trop froides, adieu les ambiances étouffantes ! Notre site affiche en direct toutes les infos utiles, pour que chacun profite du spectacle dans les meilleures conditions. 
        </p>
        <p>
          Installez-vous, détendez-vous… on s’occupe du confort, vous n’avez plus qu’à savourer le moment !
        </p>
      </div>
    </section>
    <!-- End comfort method section -->

    <!-- Carousel section dynamique -->
    <section class="carousel-section">
      <h2>Prochaines séances</h2>
      <div class="carousel-container">
        <button class="carousel-btn prev" aria-label="Précédent">
          <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="12" fill="none"/><polyline points="15 6 9 12 15 18" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
        <div class="carousel-track">
          <?php foreach ($slides as $slide): ?>
            <div class="carousel-slide">
              <?php foreach ($slide as $annonce): ?>
                <div class="carousel-item">
                  <img src="/APP_COMMUN/<?= ltrim(htmlspecialchars($annonce['photo']), '/') ?>" alt="<?= htmlspecialchars($annonce['film']) ?>">
                  <div class="annonce-titre"><?= htmlspecialchars($annonce['film']) ?></div>
                  <div class="annonce-date"><?= date('d/m/Y H:i', strtotime($annonce['date_heure'])) ?></div>
                </div>
              <?php endforeach; ?>
              <?php
                // Pour avoir toujours 3 affiches par slide
                for ($i = count($slide); $i < 3; $i++) {
                  echo '<div class="carousel-item" style="background:transparent;box-shadow:none;"></div>';
                }
              ?>
            </div>
          <?php endforeach; ?>
        </div>
        <button class="carousel-btn next" aria-label="Suivant">
          <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="12" fill="none"/><polyline points="9 6 15 12 9 18" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
      </div>
    </section>
    <!-- End carousel section -->

  </main>
  <div id="footer"></div>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      fetch("/APP_COMMUN/Frontend/header.php")
        .then(response => response.text())
        .then(data => {
          document.getElementById("header").innerHTML = data;
        })
        .catch(error => {
          console.error('Error loading header:', error);
        });

      fetch("/APP_COMMUN/Frontend/footer.html")
        .then(response => response.text())
        .then(data => {
          document.getElementById("footer").innerHTML = data;
        })
        .catch(error => {
          console.error('Error loading footer:', error);
        });

      // Carousel JS multi-affiche
      const track = document.querySelector('.carousel-track');
      const slides = document.querySelectorAll('.carousel-slide');
      const prevBtn = document.querySelector('.carousel-btn.prev');
      const nextBtn = document.querySelector('.carousel-btn.next');
      let index = 0;
      let autoScroll;

      function showSlide(i) {
        if (!track) return;
        index = (i + slides.length) % slides.length;
        track.style.transform = `translateX(-${index * 100}%)`;
      }

      function nextSlide() { showSlide(index + 1); }
      function prevSlide() { showSlide(index - 1); }

      if (nextBtn && prevBtn) {
        nextBtn.addEventListener('click', () => { nextSlide(); resetAuto(); });
        prevBtn.addEventListener('click', () => { prevSlide(); resetAuto(); });
      }

      function auto() {
        autoScroll = setInterval(nextSlide, 4000);
      }
      function resetAuto() {
        clearInterval(autoScroll);
        auto();
      }
      auto();

      // Responsive fix: reset to first slide on resize
      window.addEventListener('resize', () => showSlide(index));
    });
  </script>
</body>
</html>