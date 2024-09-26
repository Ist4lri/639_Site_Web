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
            <a href="demande_mechanicus.php">Demandes</a>
        <?php else: ?>
            <!-- Si l'utilisateur n'est pas dans l'Adeptus Mechanicus -->
            <a href="lithaniem.php">Lithanies </a>
            <a href="profil_utilisateur.php">Profil</a>
            <a href="Dec.php">Déconnexion</a>
        <?php endif; ?>
    </nav>
</header>
