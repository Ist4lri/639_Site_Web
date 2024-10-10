<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anti-Tank - Astra Militarum</title>
    <link rel="icon" type="image/x-icon" href="../src/assets/Logo_639th_2.ico">
    <link rel="stylesheet" href="../css/spe.css"> <!-- Lien vers le fichier CSS -->
</head>
<body>
    <a href="../index.php">Acceuil</a>
    <h1>Anti-Tank</h1>
    <a href="formation.php">Gestion Spécialité</a>
    <div class="container">
        <h2>Introduction</h2>
        <p class="content">
            Ce document a pour objectif de vous transmettre toutes les connaissances dont vous aurez besoin afin de mener à bien vos missions en tant que soldat lourd <span class="important">Anti-Tank</span> de l’Astra-Militarum. Ce document aborde les équipements, les types de mission et les protocoles d'engagement. Votre spécialisation exige une précision sans faille, l'échec pouvant entraîner des conséquences désastreuses pour vos unités.
        </p>

        <h2>Présentation de la spécialisation</h2>
        <p class="content">
            La spécialisation <span class="important">Anti-Tank</span> ne se limite pas à tirer une rocket dans le tas comme un Ogryn. Vous jouez un rôle clé dans la destruction de véhicules blindés et de cibles sur le champ de bataille, assurant également un soutien moral et physique à vos camarades.
        </p>

        <h2>Protocole d’engagement</h2>
        <p class="content">
            Le soldat lourd respecte la hiérarchie et doit savoir utiliser son lanceur avec prudence. Le souffle de la roquette ("BackBlast") peut provoquer des blessures si des unités se trouvent à proximité. Il est crucial d’annoncer vos tirs et de vérifier les arrières avant de tirer.
        </p>

        <h3>Protocole Global</h3>
        <ul>
            <li><span class="highlight">Protocole pré-engagement</span>: Préparer le lanceur, obtenir l'azimut et la distance de la cible, et se positionner.</li>
            <li><span class="highlight">Protocole d’engagement</span>: Annoncer "BackBlast Clear", effectuer un tir après trois confirmations.</li>
            <li><span class="highlight">Protocole post-engagement</span>: Confirmer si la cible a été touchée, recharger le lanceur si nécessaire.</li>
        </ul>

        <h2>Protocole d’engagement</h2>
        <ul>
            <li>Sortir le lanceur et vérifier la roquette</li>
            <li>Analyser l'état de la cible (véhicule/blindé)</li>
            <li>Obtenir l'azimut et la distance de la cible</li>
            <li>Utiliser l’organe de visée et réévaluer la distance</li>
            <li>Annoncer "BackBlast Clear ?" pour vérifier la sécurité arrière</li>
            <li>Effectuer les trois affirmations de tir : "Feu, Feu, Feu", puis tirer au troisième "Feu"</li>
            <li><span class="highlight">Réitérer</span> si la cible n'a pas été touchée.</li>
        </ul>

        <h2>Protocole post-engagement</h2>
        <ul>
            <li>Confirmer si la cible a été touchée</li>
            <li>Recharger le lanceur si nécessaire</li>
            <li>Ranger le lanceur après engagement</li>
            <li>Si plusieurs véhicules sont présents, réitérer les protocoles jusqu’à la destruction de toutes les cibles.</li>
        </ul>

        <h3>Conclusion</h3>
        <p class="content">
            Le rôle du <span class="important">soldat Anti-Tank</span> est essentiel dans la destruction des véhicules ennemis et la protection des escouades alliées. Vous devez maîtriser votre équipement et vos tactiques pour maximiser votre efficacité sur le champ de bataille.
        </p>
    </div>
<?php
session_start();
include 'db.php'; 


$idUtilisateur = $_SESSION['id_utilisateur'];
$stmt = $pdo->prepare("SELECT spe_id, gerance FROM utilisateurs WHERE id = :id");
$stmt->execute(['id' => $idUtilisateur]);
$utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

$filePath = __DIR__ . '/pdf/at.pdf';
    if (file_exists($filePath)) {
        echo "<p>Un fichier PDF AT est disponible : <a href='pdf/at.pdf' target='_blank'>Afficher</a></p>";
    } else {
        echo "<p>Aucun fichier PDF disponible pour le moment.</p>";
    }
    

if ($utilisateur && $utilisateur['spe_id'] == 2 && in_array($utilisateur['gerance'], [1, 2])) {
    
    // Chemin vers le fichier at.pdf
    
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
                $destinationPath = __DIR__ . '/pdf/at.pdf';

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
    <!-- Affichage du gérant et sous-gérant pour Anti-Tank (spe_id = 2) -->
    <div class="management">
        <?php
        include 'db.php'; // Connexion à la base de données

        // Requête pour récupérer le gérant et sous-gérant
        $stmt = $pdo->prepare("SELECT nom, gerance FROM utilisateurs WHERE spe_id = 2 AND gerance IN (1, 2)");
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

$stmt = $pdo->prepare("SELECT nom FROM utilisateurs WHERE spe_id = 2 AND gerance NOT IN (1, 2)");
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
