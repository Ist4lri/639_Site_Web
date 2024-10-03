<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Médicae - Astra Militarum</title>
    <link rel="stylesheet" href="../css/spe.css"> <!-- Lien vers le fichier CSS -->
</head>
<body>
    <a href="../index.php">Acceuil</a>
    <h1>Médicae</h1>
    <a href="medicae_info.php">Gestion Patient</a><br>
    <a href="formation.php">Gestion Spécialité</a>
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

    <?php
session_start();
include 'db.php'; 


$idUtilisateur = $_SESSION['id_utilisateur'];
$stmt = $pdo->prepare("SELECT spe_id, gerance FROM utilisateurs WHERE id = :id");
$stmt->execute(['id' => $idUtilisateur]);
$utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);
 $filePath = __DIR__ . '/pdf/med.pdf';
    if (file_exists($filePath)) {
        echo "<p>Un fichier PDF Vox est disponible : <a href='pdf/med.pdf' target='_blank'>Afficher</a></p>";
    } else {
        echo "<p>Aucun fichier PDF disponible pour le moment.</p>";
    }

if ($utilisateur && $utilisateur['spe_id'] == 3 && in_array($utilisateur['gerance'], [1, 2])) {
    
   
    
    ?>
    <h2>Upload du fichier PDF</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="file">Sélectionner un fichier PDF :</label>
        <input type="file" name="file" id="file" accept="application/pdf" required>
        <button type="submit" name="upload">Uploader</button>
    </form>

    <?php
    if (isset($_POST['upload'])) {
        // Vérifier si un fichier a été envoyé
        if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
            // Récupérer les informations du fichier
            $fileTmpPath = $_FILES['file']['tmp_name'];
            $fileName = $_FILES['file']['name'];
            $fileSize = $_FILES['file']['size'];
            $fileType = $_FILES['file']['type'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            // Vérifier que le fichier est bien un PDF
            if ($fileExtension === 'pdf') {
                // Chemin d'enregistrement du fichier
                $destinationPath = __DIR__ . '/pdf/med.pdf';

                // Déplacer le fichier temporaire vers sa destination finale
                if (move_uploaded_file($fileTmpPath, $destinationPath)) {
                    echo "<p>Le fichier a été téléchargé avec succès.</p>";
                } else {
                    echo "<p>Une erreur est survenue lors de l'enregistrement du fichier.</p>";
                }
            } else {
                echo "<p>Erreur : Veuillez uploader uniquement des fichiers PDF.</p>";
            }
        } else {
            echo "<p>Erreur : Aucun fichier n'a été envoyé ou une erreur est survenue lors du téléchargement.</p>";
        }
    }
} 
?>

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
$stmt = $pdo->prepare("SELECT nom FROM utilisateurs WHERE spe_id = 3 AND gerance NOT IN (1, 2)");
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
