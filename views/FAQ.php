<?php
// -- Démarrage de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// -- Connexion à la base de données
$host = 'localhost';
$dbname = 'dbtest1'; // Nom de votre base de données
$username = 'root'; // Identifiant (par défaut pour XAMPP)
$password = ''; // Mot de passe (par défaut vide pour XAMPP)

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>

<?php
// -- Le code de la page faq
$query = "SELECT question, reponse FROM faq ORDER BY orderpriority ASC";
$stmt = $db->query($query);
$faqList = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ</title>
    <link rel="stylesheet" href="faq.css">
</head>

<body>
<!-- Navbar jaune -->
<ul class="nav">
    <li><a href="index.php?cilbe=menu">Menu</a></li>
    <li><a class="active" href="index.php?cilbe=faq">FAQ</a></li>
    <li><a href="index.php?cible=connexion">Connexion</a></li>
</ul>

<div class="main-content">
    <div class="header-content">
        <h1>Comment pouvons-nous vous aider?</h1>

        <!-- Barre de recherche -->
        <div class="search-container">
            <input type="text" id="search-bar" placeholder="Rechercher">
        </div>
    </div>
</div>

<!-- Questions FAQ-->
<div class="faq-container">
    <?php if (!empty($faqList)): ?>
        <?php foreach ($faqList as $faq): ?>
            <div class="faq-item">
                <div class="faq-question">
                    <span><?php echo htmlspecialchars($faq['question']); ?></span>
                    <span class="arrow">&#9660;</span>
                </div>
                <div class="faq-answer" style="display: none;">
                    <?php echo htmlspecialchars($faq['reponse']); ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Aucune question pour le moment.</p>
    <?php endif; ?>
</div>

<script>
    const arrows = document.querySelectorAll('.arrow');
    arrows.forEach(arrow => {
        arrow.addEventListener('click', (event) => {
            event.stopPropagation();
            const answer = arrow.parentElement.nextElementSibling;
            if (answer.style.display === "block") {
                answer.style.display = "none";
                arrow.innerHTML = "&#9660;";
            } else {
                answer.style.display = "block";
                arrow.innerHTML = "&#9650;";
            }
        });
    });
</script>

</body>
</html>
