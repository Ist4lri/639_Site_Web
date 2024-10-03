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

    <?php
session_start();
include 'db.php'; 


$idUtilisateur = $_SESSION['id_utilisateur'];
$stmt = $pdo->prepare("SELECT spe_id, gerance FROM utilisateurs WHERE id = :id");
$stmt->execute(['id' => $idUtilisateur]);
$utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

// Chemin vers le fichier etl.pdf
    $filePath = __DIR__ . '/pdf/etl.pdf';
    if (file_exists($filePath)) {
        echo "<p>Un fichier PDF ETL est disponible : <a href='pdf/etl.pdf' target='_blank'>Afficher</a></p>";
    } else {
        echo "<p>Aucun fichier PDF disponible pour le moment.</p>";
    }

if ($utilisateur && $utilisateur['spe_id'] == 8 && in_array($utilisateur['gerance'], [1, 2])) {
    
    
    
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
                $destinationPath = __DIR__ . '/pdf/etl.pdf';

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
