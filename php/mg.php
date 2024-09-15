<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Machine Gunner - Astra Militarum</title>
    <link rel="stylesheet" href="../css/spe.css"> <!-- Lien vers le fichier CSS -->
</head>
<body>
    <a href="../index.php">Acceuil</a>
    <h1>Machine Gunner</h1>
    <a href="formation.php">Gestion Spécialité</a>
    <div class="container">
        <h2>Introduction</h2>
        <p class="content">
            La spécialisation <span class="important">Machine Gunner</span> ne se résume pas à tirer dans le tas comme un Ogryn. Votre rôle comprend la neutralisation d’objectif mais aussi la préparation au tir de suppression, l'élimination massive d’ennemis et le soutien aux autres escouades. Vous êtes à une place stratégique dans une escouade ou une section entière.
        </p>

        <h2>Présentation de la spécialisation</h2>
        <p class="content">
            Le <span class="important">Machine Gunner</span> assure la couverture, la suppression, l’élimination massive et aussi un soutien moral et physique à ses camarades sur le champ de bataille. Vous jouez un rôle clé dans l’efficacité d’une unité lourde et devez savoir vous positionner pour maximiser votre impact sur le terrain.
        </p>

        <h2>Protocole d’engagement</h2>
        <p class="content">
            Le soldat lourd respecte la hiérarchie et l’équipement n’est porté qu’en mission ou entraînement. Vous devez assurer la suppression efficace et protéger vos camarades dans le feu de l’action.
        </p>

        <h3>Protocole Global</h3>
        <ul>
            <li><span class="highlight">Protocole pré-engagement</span>: Vérifier son chargeur, obtenir les informations de la cible, se positionner correctement.</li>
            <li><span class="highlight">Protocole d’engagement</span>: Stabiliser l’arme, effectuer des tirs de suppression, vérifier le chargeur.</li>
            <li><span class="highlight">Protocole post-engagement</span>: Recharger, désenrayer si besoin, enclencher la sécurité de l’arme.</li>
        </ul>

        <h2>Protocole de transmission</h2>
        <ul>
            <li>Utiliser l'organe de visée et stabiliser l'arme</li>
            <li>Effectuer des salves de tirs pour neutraliser les cibles</li>
            <li>Vérifier et recharger régulièrement pour éviter les imprévus</li>
            <li>Appliquer ces protocoles jusqu'à l'élimination totale des menaces.</li>
        </ul>

        <h3>Conclusion</h3>
        <p class="content">
            Le <span class="important">Machine Gunner</span> joue un rôle crucial dans la suppression d’ennemis et la protection de ses camarades sur le champ de bataille. Vous devez maîtriser vos outils et vos techniques pour être efficace.
        </p>
    </div>

    <!-- Affichage du gérant et sous-gérant pour Machine Gunner (spe_id = 1) -->
    <div class="management">
        <?php
        include 'db.php'; // Connexion à la base de données

        // Requête pour récupérer le gérant et sous-gérant
        $stmt = $pdo->prepare("SELECT nom, gerance FROM utilisateurs WHERE spe_id = 1 AND gerance IN (1, 2)");
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
        ?>
    </div>
</body>
</html>
