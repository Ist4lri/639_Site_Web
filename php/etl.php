<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Équipier de Tir Lourd - Astra Militarum</title>
    <link rel="stylesheet" href="../css/spe.css"> <!-- Lien vers le fichier CSS -->
</head>
<body>
    <a href="../index.php">Acceuil</a>
    <h1>Équipier de Tir Lourd</h1>
    <a href="formation.php">Gestion Spécialité</a>
    <div class="container">
        <h2>Introduction</h2>
        <p class="content">
            Ce document a pour objectif de vous transmettre toutes les connaissances dont vous aurez besoin afin de mener à bien vos missions en tant qu'<span class="important">Équipier de Tir Lourd</span> de l’Astra-Militarum. Vous découvrirez les équipements, les types de mission et les protocoles d'engagement qui sont cruciaux pour votre spécialité.
        </p>

        <h2>Présentation de la spécialisation</h2>
        <p class="content">
            La spécialisation <span class="important">Équipier de Tir Lourd</span> ne se résume pas à poser une tourelle et tirer. Vous jouez un rôle clé dans la mise en place de défenses et de soutien à distance, en assurant la sécurité et la protection de vos camarades avec l'installation de tourelles, de mortiers et de canons anti-véhicule.
        </p>

        <h2>Protocole d’engagement</h2>
        <p class="content">
            Le soldat d’équipier de tir doit respecter la hiérarchie et se préparer méticuleusement avant d'engager une mission. Vous êtes responsable de la mise en place de défenses et d'assurer que vos tourelles et équipements fonctionnent correctement pendant l'engagement.
        </p>

        <h3>Protocole Global</h3>
        <ul>
            <li><span class="highlight">Protocole pré-intervention</span>: Préparer le matériel, ajuster les fréquences radio, tester les communications, préparer le terrain.</li>
            <li><span class="highlight">Protocole d’intervention</span>: Mettre en place les défenses, poser les tourelles, informer l'État-Major, et commencer la défense.</li>
            <li><span class="highlight">Protocole post-intervention</span>: Analyser les tourelles, les recharger et les réparer, puis transmettre les rapports de mission.</li>
        </ul>

        <h2>Protocole d’intervention</h2>
        <ul>
            <li>Préparer le terrain et les équipements</li>
            <li>Poser les bâtiments de commandement et les tourelles de défense</li>
            <li>Informer l'État-Major de la situation</li>
            <li>Recevoir les ordres et les appliquer sur le terrain</li>
            <li><span class="highlight">Réitérer</span> en fonction des besoins de la mission</li>
        </ul>

        <h2>Protocole post-intervention</h2>
        <ul>
            <li>Analyser l’état des tourelles et des défenses</li>
            <li>Recharger les tourelles et réparer si nécessaire</li>
            <li>Retranscrire les rapports de mission à l'État-Major</li>
            <li>Ranger les tourelles et le matériel à la fin de l’opération</li>
        </ul>

        <h3>Conclusion</h3>
        <p class="content">
            Le rôle de l'<span class="important">Équipier de Tir Lourd</span> est essentiel dans la mise en place de défenses solides et le soutien à distance lors des engagements. Votre capacité à organiser et maintenir vos équipements en état de marche est cruciale pour le succès des opérations.
        </p>
    </div>

    <!-- Affichage du gérant et sous-gérant pour Équipier de Tir Lourd (spe_id = 8) -->
    <div class="management">
        <?php
        include 'db.php'; // Connexion à la base de données

        // Requête pour récupérer le gérant et sous-gérant
        $stmt = $pdo->prepare("SELECT nom, gerance FROM utilisateurs WHERE spe_id = 8 AND gerance IN (1, 2)");
        $stmt->execute();
        $managers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $gerant = null;
        $sousGerant = null;

        // Vérification des gérants et sous-gérants
        foreach ($managers as $manager) {
            if ($manager['gerance'] == 1) {
                $gerant = $manager['nom'];
            } elseif ($manager['gerance'] == 2) {
                $sousGerant = $manager['nom'];
            }
        }

        // Affichage des résultats
        if ($gerant || $sousGerant) {
            echo "<div class='manager-box'>";
            if ($gerant) {
                echo "<p><strong>Gérant :</strong> " . htmlspecialchars($gerant) . "</p>";
            }
            if ($sousGerant) {
                echo "<p><strong>Sous-Gérant :</strong> " . htmlspecialchars($sousGerant) . "</p>";
            }
            echo "</div>";
        } else {
            echo "<p>Aucun gérant/sous-gérant disponible.</p>";
        }

$stmt = $pdo->prepare("SELECT nom FROM utilisateurs WHERE spe_id = 8 AND gerance NOT IN (1, 2)");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);


if (!empty($users)) {
    echo "<div class='users-box'>";
    echo "<h3>Membres de la spécialité :</h3>";
    echo "<ul>";
    foreach ($users as $user) {
        echo "<li>" . htmlspecialchars($user['nom']) . "</li>";
    }
    echo "</ul>";
    echo "</div>";
} else {
    echo "<p>Aucun utilisateur trouvé pour cette spécialité.</p>";
}
        ?>
    </div>
</body>
    <?php include 'footer.php'; ?>
</html>
