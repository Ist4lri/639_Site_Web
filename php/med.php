<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Médicae - Astra Militarum</title>
    <link rel="stylesheet" href="../css/spe.css"> <!-- Lien vers le fichier CSS -->
</head>
<body>
    <h1>Médicae</h1>
    <a href="medicae_info.php">Gestion Patient</a>
    <div class="container">
        <h2>Introduction</h2>
        <p class="content">
            Ce document a pour objectif de vous transmettre toutes les connaissances dont vous aurez besoin afin de mener à bien vos missions en tant que soldat <span class="important">Médicae</span> de l’Astra-Militarum. Vous découvrirez les équipements, les protocoles de soin et les situations critiques où vos décisions sauveront des vies.
        </p>

        <h2>Présentation de la spécialisation</h2>
        <p class="content">
            La spécialisation <span class="important">Médicae</span> ne se limite pas à poser un bandage. Vous êtes responsable du soin de vos camarades, de la sélection des unités importantes, et de la prise de décisions médicales vitales sur le terrain. Vous jouez un rôle essentiel au sein de votre escouade, assurant le maintien de leur santé physique et mentale.
        </p>

        <h2>Protocole d’engagement</h2>
        <p class="content">
            Le soldat médicae respecte la hiérarchie et doit savoir utiliser son équipement médical dans des conditions de combat stressantes. Votre priorité est le soin des Officiers et Sous-Officiers.
        </p>

        <h3>Protocole Global</h3>
        <ul>
            <li><span class="highlight">Protocole pré-traitement</span>: Préparer le matériel, sécuriser la victime, analyser son état.</li>
            <li><span class="highlight">Protocole de traitement</span>: Soigner les blessures, stabiliser l'état, et réanimer si nécessaire.</li>
            <li><span class="highlight">Protocole post-traitement</span>: Injecter les médicaments nécessaires, surveiller l'état de la victime, et fournir un rapport médical.</li>
        </ul>

        <h2>Protocole de traitement</h2>
        <ul>
            <li>Analyser l’état de la victime</li>
            <li>Appliquer des garrots sur les membres qui saignent</li>
            <li>Soigner les blessures à la tête et au torse</li>
            <li>Vérifier le pouls et commencer une <strong>RCP</strong> si nécessaire</li>
            <li>Bander les membres blessés</li>
            <li><span class="highlight">Réitérer</span> jusqu'à stabilisation complète</li>
        </ul>

        <h2>Protocole post-traitement</h2>
        <ul>
            <li>Retirer les garrots</li>
            <li>Analyser l'état général de la victime</li>
            <li>Injecter <span class="yellow">Morphine</span> si pouls rapide ou douleur <span class="yellow">faible</span>, <span class="orange">moyenne</span>, ou <span class="red">intense</span></li>
            <li>Injecter <span class="yellow">Epinephrine</span> si pouls faible</li>
            <li>Si perte de sang <span class="yellow">faible</span>, injecter une poche de sang de <span class="yellow">250 ml</span></li>
            <li>Si perte de sang <span class="orange">moyenne</span>, injecter une poche de sang de <span class="orange">500 ml</span></li>
            <li>Si perte de sang <span class="red">grande</span>, injecter une poche de sang de <span class="red">1000 ml</span></li>
            <li>Analyser la victime et appliquer une fiche de soins : <strong>Aucune urgence</strong>, <strong>Peut attendre</strong>, <strong>Urgent</strong>, <strong>Décédé</strong></li>
        </ul>

        <h3>Conclusion</h3>
        <p class="content">
            Le <span class="important">Médicae</span> assure la survie de ses camarades dans les situations les plus critiques. Vos compétences en soins sur le champ de bataille sont essentielles pour maintenir l'intégrité de vos unités et garantir le succès des missions.
        </p>
    </div>

    <!-- Affichage du gérant et sous-gérant pour Médicae (spe_id = 2) -->
    <div class="management">
        <?php
        include 'db.php'; // Connexion à la base de données

        // Requête pour récupérer le gérant et sous-gérant
        $stmt = $pdo->prepare("SELECT nom, gerance FROM utilisateurs WHERE spe_id = 3 AND gerance IN (1, 2)");
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
