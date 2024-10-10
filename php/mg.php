<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Machine Gunner - Astra Militarum</title>
    <link rel="icon" type="image/x-icon" href="../src/assets/Logo_639th_2.ico">
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

    <?php
session_start();
include 'db.php'; 


$idUtilisateur = $_SESSION['id_utilisateur'];
$stmt = $pdo->prepare("SELECT spe_id, gerance FROM utilisateurs WHERE id = :id");
$stmt->execute(['id' => $idUtilisateur]);
$utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

$filePath = __DIR__ . '/pdf/mg.pdf';
    if (file_exists($filePath)) {
        echo "<p>Un fichier PDF MG est disponible : <a href='pdf/mg.pdf' target='_blank'>Afficher</a></p>";
    } else {
        echo "<p>Aucun fichier PDF disponible pour le moment.</p>";
    }

if ($utilisateur && $utilisateur['spe_id'] == 1 && in_array($utilisateur['gerance'], [1, 2])) {
    
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
                $destinationPath = __DIR__ . '/pdf/mg.pdf';

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
$stmt = $pdo->prepare("SELECT nom FROM utilisateurs WHERE spe_id = 1 AND gerance NOT IN (1, 2)");
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
