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
    <title>639</title>
    <link rel="stylesheet" href="../css/header.css">
</head>
<body class="head">

<header class="head">
    <div class="head-logo">
        <a href="../index.php">
            <img src="../src/assets/Logo.png" alt="Logo 639">
        </a>
        <?php if ($isLoggedIn): ?>
            <span class="head-username">Bonjour, <?php echo htmlspecialchars($userName); ?></span>
        <?php endif; ?>
    </div>
    <div class="head-logo2">
         <a href="../index.php">
        <img src="../src/assets/TitreSite.png" alt="639 Régiment cadien">
        </a>
    </div>
    <nav class="head-nav">
        <?php if ($isLoggedIn): ?>
            <a href="profil_utilisateur.php">Profil</a>
            <a href="officier.php">Officier</a>
            <a href="sous-officier.php">Sous-Officier</a>
            <a href="Dec.php">Déconnexion</a>
        <?php else: ?>
            <a href="connection.php">Connexion</a>
            <a href="ins.php">Inscription</a>
        <?php endif; ?>
    </nav>
</header>

</body>
</html>
