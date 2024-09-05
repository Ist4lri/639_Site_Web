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
</head>
<body class="head">

<header class="head">
    <div class="head-logo">
        <a href="../index.php">
            <img src="../src/assets/Logo_639th_2.ico" alt="Logo 639">
        </a>
        <?php if ($isLoggedIn): ?>
            <span class="head-username">Bonjour, <?php echo htmlspecialchars($userName); ?></span>
        <?php endif; ?>
    </div>
    <div class="head-title">
        <h1>639</h1>
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

</body>
</html>
