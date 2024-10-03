<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marksman - Astra Militarum</title>
    <link rel="stylesheet" href="../css/spe.css"> <!-- Lien vers le fichier CSS -->
</head>
<body>
    <a href="../index.php">Acceuil</a>
    <h1>Marksman</h1>
    <a href="formation.php">Gestion Spécialité</a>
    <div class="container">
        <h2>Introduction</h2>
        <p class="content">
            Ce document a pour objectif de vous transmettre toutes les connaissances dont vous aurez besoin afin de mener à bien vos missions en tant que tireur de précision <span class="important">Marksman</span> de l’Astra-Militarum. Ce document aborde les équipements, les types de mission et les protocoles d'engagement. Une précision absolue est essentielle, l'échec pouvant avoir des conséquences désastreuses.
        </p>

        <h2>Présentation de la spécialisation</h2>
        <p class="content">
            La spécialisation <span class="important">Marksman</span> ne se limite pas à tirer à longue distance. Vous jouez un rôle clé dans l'élimination précise de cibles importantes sur le champ de bataille, tout en fournissant un soutien tactique à vos camarades. La précision et la maîtrise des facteurs environnementaux sont la clé de votre réussite.
        </p>

        <h2>Protocole d’engagement</h2>
        <p class="content">
            Le marksman doit respecter la hiérarchie et se préparer méticuleusement avant d'engager une cible. Vous devez savoir utiliser votre équipement avec précision et tenir compte des facteurs tels que le vent, la distance et la gravité avant de tirer.
        </p>

        <h3>Protocole Global</h3>
        <ul>
            <li><span class="highlight">Protocole pré-engagement</span>: Se placer confortablement, vérifier son arme et équipement, et obtenir toutes les informations sur la cible.</li>
            <li><span class="highlight">Protocole d’engagement</span>: Régler la distance et l’organe de visée, puis effectuer un tir précis.</li>
            <li><span class="highlight">Protocole post-engagement</span>: Confirmer si la cible a été touchée, recharger et se préparer pour le prochain tir.</li>
        </ul>

        <h2>Protocole d’engagement</h2>
        <ul>
            <li>Se placer confortablement et préparer son arme</li>
            <li>Analyser l'état de la cible (Unité/Spécial)</li>
            <li>Obtenir l'azimut et la distance de la cible</li>
            <li>Régler la distance d'engagement et l'organe de visée</li>
            <li>Effectuer un tir droit ou en cloche selon la distance</li>
            <li>Confirmer si la cible a été touchée et/ou éliminée</li>
            <li><span class="highlight">Réitérer</span> si la cible n'a pas été touchée.</li>
        </ul>

        <h2>Protocole post-engagement</h2>
        <ul>
            <li>Confirmer si la cible a été touchée</li>
            <li>Recharger l'arme si nécessaire</li>
            <li>Si plusieurs cibles sont présentes, réitérer les protocoles jusqu'à l'élimination complète des cibles.</li>
        </ul>

        <h3>Conclusion</h3>
        <p class="content">
            Le rôle du <span class="important">Marksman</span> est essentiel dans l'élimination de cibles prioritaires et la protection des escouades alliées. Votre capacité à faire des tirs précis dans des conditions extrêmes est cruciale pour le succès des missions.
        </p>
    </div>

    <?php
session_start();
include 'db.php'; 


$idUtilisateur = $_SESSION['id_utilisateur'];
$stmt = $pdo->prepare("SELECT spe_id, gerance FROM utilisateurs WHERE id = :id");
$stmt->execute(['id' => $idUtilisateur]);
$utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

 $filePath = __DIR__ . '/pdf/mark.pdf';
    if (file_exists($filePath)) {
        echo "<p>Un fichier PDF Marksman est disponible : <a href='pdf/mark.pdf' target='_blank'>Afficher</a></p>";
    } else {
        echo "<p>Aucun fichier PDF disponible pour le moment.</p>";
    }

if ($utilisateur && $utilisateur['spe_id'] == 5 && in_array($utilisateur['gerance'], [1, 2])) {
    
    // Chemin vers le fichier mark.pdf
   
    
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
                $destinationPath = __DIR__ . '/pdf/mark.pdf';

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

    <!-- Affichage du gérant et sous-gérant pour Marksman (spe_id = 5) -->
    <div class="management">
        <?php
        include 'db.php'; // Connexion à la base de données

        // Requête pour récupérer le gérant et sous-gérant
        $stmt = $pdo->prepare("SELECT nom, gerance FROM utilisateurs WHERE spe_id = 5 AND gerance IN (1, 2)");
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
$stmt = $pdo->prepare("SELECT nom FROM utilisateurs WHERE spe_id = 5 AND gerance NOT IN (1, 2)");
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
