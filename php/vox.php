<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vox Operator - Astra Militarum</title>
    <link rel="icon" type="image/x-icon" href="../src/assets/Logo_639th_2.ico">
    <link rel="stylesheet" href="../css/spe.css"> <!-- Inclusion du fichier CSS -->
</head>
<body>
    <a href="../index.php">Acceuil</a>
    <h1>Vox Operator</h1>
    <a href="formation.php">Gestion Spécialité</a>
    <div class="container">
        <h2>Introduction</h2>
        <p class="content">
            Ce document a pour objectif de vous transmettre toutes les connaissances dont vous aurez besoin afin de mener à bien vos missions en tant que <span class="important">Vox Operator</span> de l’Astra-Militarum.
        </p>

        <h2>Présentation de la spécialisation</h2>
        <p class="content">
            La spécialisation <span class="important">Vox Operator</span> ne se résume pas à simplement dire quelques mots en radio. Votre rôle comprend évidemment la neutralisation de cibles mais englobe aussi toute la préparation liée aux transmissions d’information et d’ordre aux sections, escouades ainsi qu’aux divisions aériennes et blindées. Vous êtes à une place stratégique dans une escouade ou une section entière.
        </p>

        <h2>Protocole d’engagement</h2>
        <p class="content">
            Le soldat vox respecte la hiérarchie qu’importe la spécialisation ou l’affectation de son supérieur. L’équipement n’est porté qu’une fois affecté lors d'entraînements ou au départ pour le combat. 
        </p>

        <h3>Protocole Global</h3>
        <ul>
            <li><span class="highlight">Protocole pré-transmission</span>: Préparer son matériel, ajuster les fréquences, confirmer les présences.</li>
            <li><span class="highlight">Protocole de transmission</span>: Transmettre les informations, recevoir et retranscrire les ordres.</li>
            <li><span class="highlight">Protocole post-transmission</span>: Annoncer la fin de transmission, entretenir le matériel.</li>
        </ul>

        <h2>Protocole de transmission</h2>
        <ul>
            <li>Transmettre les informations de l’<span class="important">État-Major</span></li>
            <li>Informer son chef de section</li>
            <li>Recevoir les ordres du chef de section</li>
            <li>Transmettre les ordres à l’escouade</li>
            <li>Informer l’<span class="important">État-Major</span> des résultats</li>
            <li>Réitérer jusqu’à la fin des opérations</li>
        </ul>

        <h3>Conclusion</h3>
        <p class="content">
            Être <span class="important">Vox Operator</span> signifie assurer la transmission vitale des ordres sur le champ de bataille. Vous jouez un rôle crucial pour la réussite des missions de l’Astra Militarum.
        </p>
    </div>

<?php
session_start();
include 'db.php'; 


$idUtilisateur = $_SESSION['id_utilisateur'];
$stmt = $pdo->prepare("SELECT spe_id, gerance FROM utilisateurs WHERE id = :id");
$stmt->execute(['id' => $idUtilisateur]);
$utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

 $filePath = __DIR__ . '/pdf/vox.pdf';
    if (file_exists($filePath)) {
        echo "<p>Un fichier PDF Vox est disponible : <a href='pdf/vox.pdf' target='_blank'>Afficher</a></p>";
    } else {
        echo "<p>Aucun fichier PDF disponible pour le moment.</p>";
    }

if ($utilisateur && $utilisateur['spe_id'] == 4 && in_array($utilisateur['gerance'], [1, 2])) {
       
    
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



    <!-- Affichage du gérant et sous-gérant -->
    <div class="management">
        <?php
        include 'db.php'; // Connexion à la base de données

        // Requête pour récupérer le gérant et sous-gérant
        $stmt = $pdo->prepare("SELECT nom, gerance FROM utilisateurs WHERE spe_id = 4 AND gerance IN (1, 2)");
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
        }

$stmt = $pdo->prepare("SELECT nom FROM utilisateurs WHERE spe_id = 4 AND gerance NOT IN (1, 2)");
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
</html>

    <?php include 'footer.php'; ?>
