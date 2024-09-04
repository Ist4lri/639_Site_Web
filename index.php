<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db.php';

$isLoggedIn = isset($_SESSION['utilisateur']);
$userName = $isLoggedIn ? $_SESSION['nom_utilisateur'] : ''; // Récupérer le nom de l'utilisateur depuis la session
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>639ème Régiment Cadien</title>
    <link rel="stylesheet" href="css/style.css">
    
</head>
<body>

<header class="head">
    <div class="head-logo">
        <a href="index.php">
            <img src="src/assets/Logo_639th_2.ico" alt="Logo 639">
        </a>
        <?php if ($isLoggedIn): ?>
            <span class="head-username">Bonjour, <?php echo htmlspecialchars($userName); ?></span>
        <?php endif; ?>
    </div>
    <div class="head-title">
        <h1>639ème Régiment Cadien</h1>
    </div>
    <nav class="head-nav">
        <?php if ($isLoggedIn): ?>
            <a href="php/profil_utilisateur.php">Profil</a>
            <a href="php/Dec.php">Déconnexion</a>
        <?php else: ?>
            <a href="php/connection.php">Connexion</a>
            <a href="php/ins.php">Inscription</a>
        <?php endif; ?>
    </nav>
</header>

<div class="container">
    <h2>Bienvenue sur le site du 639ème régiment cadien.</h2>

    
    <h3>Nos spécialités</h3>
    <div class="specialties">
        <div class="specialty"><a href="#">Machine Gunner</a></div>
        <div class="specialty"><a href="#">Anti-Tank</a></div>
        <div class="specialty"><a href="#">Médicae</a></div>
        <div class="specialty"><a href="#">Vox Opérateur</a></div>
        <div class="specialty"><a href="#">Marksman</a></div>
        <div class="specialty"><a href="#">Plasma</a></div>
        <div class="specialty"><a href="#">Breacher</a></div>
        <div class="specialty"><a href="#">Equipier de Tir Lourd</a></div>
    </div>
</div>

<div class="eff"><a href="php/effectif.php">Nos Effectif</a></div>

</body>
</html>
