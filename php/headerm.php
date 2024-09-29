<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$stmt = $pdo->prepare("SELECT id, nom FROM utilisateurs WHERE email = :email");
$stmt->execute(['email' => $_SESSION['utilisateur']]);
$currentUser = $stmt->fetch();

$factionStmt = $pdo->prepare("SELECT * FROM personnages WHERE id_utilisateur = :id_utilisateur AND faction = 'Adeptus Mechanicus' AND validation = 'Accepter'");
$factionStmt->execute(['id_utilisateur' => $currentUser['id']]);
$faction = $factionStmt->fetch();
?>

<header>
    <div class="head-logo2">
        <a href="../index.php">
            <img src="../src/assets/TitreSite.png" alt="639 Régiment cadien">
        </a>
    </div>

    <nav class="head-nav">
        <?php if ($faction): ?>
            <!-- Si l'utilisateur fait partie de l'Adeptus Mechanicus -->
            <a href="lithaniem.php">Lithanies </a>
            <a href="mecha_personnages.php">Membres du Méchanicus </a>
            <a href="demande_mechanicus.php">Demandes</a>
        <?php else: ?>
            <!-- Si l'utilisateur n'est pas dans l'Adeptus Mechanicus -->
            <a href="lithaniem.php">Lithanies </a>
            <a href="mecha_personnages.php">Membres du Méchanicus </a>
            <a href="profil_utilisateur.php">Profil</a>
            <a href="Dec.php">Déconnexion</a>
        <?php endif; ?>
    </nav>
</header>

<style>
    @font-face {
    font-family: 'Inquisitor';
    src: url('../css/fonts/Inquisitor.otf') format('opentype');
    font-weight: normal;
    font-style: normal;
    
}

body {
    background-color: #121212;
    color: #00ff66;
    font-family: 'Inquisitor', Serif;
}
    
header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #333;
    padding: 10px 20px;
    margin-bottom: 15px;
    border: solid 3px #00ff66;
    font-family: 'Inquisitor', Serif;
}

.head-logo2 img {
    height: 60px;
}

.head-nav a {
    font-family: 'Inquisitor', Serif;
    color: #00ff66;
    margin: 0 15px;
    text-decoration: none;
    display: inline-block; 
    line-height: 30px; 
    padding: 0 10px;
    font-size:  2rem; 
    transition: color 0.3s ease, background-color 0.3s ease; 
}


.head-nav a:hover {
    color: #ff6600;
    background-color: rgba(0, 255, 102, 0.2); 
    border-radius: 5px; 
}</style>
