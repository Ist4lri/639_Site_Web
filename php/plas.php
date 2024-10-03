<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plasma - Astra Militarum</title>
    <link rel="stylesheet" href="../css/spe.css"> <!-- Lien vers le fichier CSS -->
</head>
<body>
    <a href="../index.php">Acceuil</a>
    <h1>Plasma</h1>
    <a href="formation.php">Gestion Spécialité</a>
    <div class="container">
        <h2>Introduction</h2>
        <p class="content">
            Ce document a pour objectif de vous transmettre toutes les connaissances dont vous aurez besoin afin de mener à bien vos missions en tant que soldat lourd <span class="important">Plasma</span> de l’Astra-Militarum. Vous découvrirez les équipements, les types de mission et les protocoles d'engagement. L'échec peut entraîner de lourdes conséquences, allant de blessures sévères à la destruction de votre unité.
        </p>

        <h2>Présentation de la spécialisation</h2>
        <p class="content">
            La spécialisation <span class="important">Plasma</span> ne se résume pas à tirer une salve de plasma et espérer pour le mieux. Vous jouez un rôle clé dans l'élimination de cibles blindées et dans le soutien d’autres escouades. Vous devez maîtriser l'utilisation de votre arme plasma pour éviter toute surcharge et garantir le succès de vos opérations.
        </p>

        <h2>Protocole d’engagement</h2>
        <p class="content">
            Le soldat lourd Plasma respecte la hiérarchie et doit être attentif à la température de son arme ainsi qu'aux chances d'explosion en cas de surcharge. Vous êtes responsable de la neutralisation précise des objectifs tout en assurant la sécurité de vos camarades.
        </p>

        <h3>Protocole Global</h3>
        <ul>
            <li><span class="highlight">Protocole pré-engagement</span>: Vérifier la température de l’arme, utiliser une charge de refroidissement si nécessaire, obtenir les informations sur la cible.</li>
            <li><span class="highlight">Protocole d’engagement</span>: Ajuster la visée et effectuer un tir en fonction de la distance.</li>
            <li><span class="highlight">Protocole post-engagement</span>: Confirmer si la cible a été touchée, recharger l'arme et vérifier la température de celle-ci.</li>
        </ul>

        <h2>Protocole d’engagement</h2>
        <ul>
            <li>Vérifier la température et les chances d'explosion</li>
            <li>Obtenir l'état et l'azimut de la cible</li>
            <li>Effectuer un tir droit ou en cloche selon la distance</li>
            <li>Confirmer si la cible a été touchée ou endommagée</li>
            <li><span class="highlight">Réitérer</span> si la cible n'a pas été touchée.</li>
        </ul>

        <h2>Protocole post-engagement</h2>
        <ul>
            <li>Vérifier la température de l’arme</li>
            <li>Confirmer si la cible a été touchée ou détruite</li>
            <li>Utiliser une charge de refroidissement si nécessaire</li>
            <li>Si plusieurs cibles sont présentes, réitérer les protocoles jusqu'à la destruction de toutes les cibles.</li>
        </ul>

        <p class="content">
            Les surcharges de plasma peuvent engendrer des blessures de gravité différente : 
            <span class="yellow">faible</span>, <span class="orange">moyenne</span>, <span class="red">grave</span>, en fonction de la distance des unités présentes dans la zone de l’explosion.
        </p>

        <h3>Conclusion</h3>
        <p class="content">
            Le rôle du <span class="important">Plasma Gunner</span> est essentiel dans la destruction de véhicules blindés et d’unités sur le champ de bataille. Une bonne gestion de votre arme et des charges de plasma est cruciale pour garantir la sécurité de vos camarades et le succès des missions.
        </p>
    </div>
    <?php
session_start();
include 'db.php'; 


$idUtilisateur = $_SESSION['id_utilisateur'];
$stmt = $pdo->prepare("SELECT spe_id, gerance FROM utilisateurs WHERE id = :id");
$stmt->execute(['id' => $idUtilisateur]);
$utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

 $filePath = __DIR__ . '/pdf/plas.pdf';
    if (file_exists($filePath)) {
        echo "<p>Un fichier PDF Plasma est disponible : <a href='pdf/plas.pdf' target='_blank'>Afficher</a></p>";
    } else {
        echo "<p>Aucun fichier PDF disponible pour le moment.</p>";
    }

if ($utilisateur && $utilisateur['spe_id'] == 6 && in_array($utilisateur['gerance'], [1, 2])) {
    
    // Chemin vers le fichier vox.pdf
   
    
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
                $destinationPath = __DIR__ . '/pdf/vox.pdf';

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

    <!-- Affichage du gérant et sous-gérant pour Plasma (spe_id = 6) -->
    <div class="management">
        <?php
        include 'db.php'; // Connexion à la base de données

        // Requête pour récupérer le gérant et sous-gérant
        $stmt = $pdo->prepare("SELECT nom, gerance FROM utilisateurs WHERE spe_id = 6 AND gerance IN (1, 2)");
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

$stmt = $pdo->prepare("SELECT nom FROM utilisateurs WHERE spe_id = 6 AND gerance NOT IN (1, 2)");
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
