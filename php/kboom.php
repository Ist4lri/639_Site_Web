<?php
session_start();

// Vérifier si l'utilisateur est connecté (id_utilisateur est défini)
if (!isset($_SESSION['id_utilisateur'])) {
    echo "Erreur : vous devez être connecté pour accéder à cette page.";
    exit; // Arrêter l'exécution du script si l'utilisateur n'est pas connecté
}

// Récupérer l'ID de l'utilisateur connecté
$idUtilisateur = $_SESSION['id_utilisateur'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Breacher - Astra Militarum</title>
    <link rel="icon" type="image/x-icon" href="../src/assets/Logo_639th_2.ico">
    <link rel="stylesheet" href="../css/spe.css"> <!-- Lien vers le fichier CSS -->
</head>
<body>
    <a href="../index.php">Acceuil</a>
    <h1>Breacher</h1>
    <a href="formation.php">Gestion Spécialité</a>
    <div class="container">
        <h2>Introduction</h2>
        <p class="content">
            Ce document a pour objectif  de vous transmettre toutes les connaissances dont vous aurez besoin afin de mener à bien vos missions en tant que <span class="important">Breacher</span> de l’Astra-Militarum. Il aborde tous les points théoriques de la spécialisation de <span class="important">Breacher</span>. Vous y trouverez une présentation de votre équipement, des différents types de mission que vous aurez à effectuer au sein de l’Imperium. Avant de rejoindre cette spécialité, il faut bien prendre en compte que vous n’avez pas le droit à l’erreur, l’échec peut entraîner, au mieux, de lourdes blessures et au pire la perte de toutes les unités présentent sur le terrain. 
        </p>
        <h2>Présentation de la spécialisation</h2>
        <p class="content">
            Le <span class="important">Breacher</span> ne se résume pas à simplement à foncer tête baisser ou juste poser une bombe. Votre rôle comprend évidemment la neutralisation de cible mais englobe aussi toute la préparation annexe au combat en milieu clos, la percée de lignes ennemies et à la neutralisation d’objectif avec des moyens explosifs, comprenant le déminage/désamorçage.
            En effet, avant de traiter un objectif il y a différentes actions à faire (cf. Protocole d’engagement). Il est important de noter qu’être une unité <span class="important">Breacher</span>, vous êtes à une place importante dans une escouade ou une section entière.
            Vous assurez la sécurité de votre unité afin de progresser au milieu des lignes ennemis ainsi que la sécurisation de chaque bâtiment, par la même occasion vous vous occupez de la neutralisation d’objectif et le désamorçage, vous assurez aussi un soutien moral et physique à vos camarades sur le champ de bataille.
        </p>
        <h2>Présentation des explosifs</h2>
        <p class="content">
            En tant que <span class="important">Breacher</span> vous allez rencontrer différents types de bombes. Il faut bien évidemment suivre les protocoles pour tous les types de bombes. On peut les classer en catégories :
            </br>- <span class="highlight">Les EM (Explosifs Militaires) </span>: la charge explosive de ce type est stable et le système de mise à feu complexe. Ce type d’explosif est dangereux de par sa portée qui est estimée entre moyenne et grande. Ce type d’explosif est utilisé principalement par des groupes armés conventionnels.
            </br>- <span class="highlight">Les EID (Explosifs Industriels) </span>: ce sont des explosifs conçus pour l’usage civil (minage, démolition, etc …). La charge explosive est stable et le système de mise à feu est facile à cerner. Sa portée est estimée entre faible et moyenne. Ce type d’explosif est principalement par des groupes rebelles.
            </br>- <span class="highlight">Les EEI (Engin Explosif Improvisé) </span>: ce type de charge est la plus dangereuse de par son fonctionnement totalement aléatoire. La charge explosive est particulièrement instable et son système de mise à feu l’est tout autant. Sa portée est inconnue. Il faut manier ces explosifs avec énormément de précaution, voire même d’opter pour la destruction contrôlée plutôt que de tenter de les désamorcer. Ce type d’explosif est principalement utilisé par des groupes terroristes.
        </p>

        <h2>Protocole d’engagement</h2>
        <p class="content">
            Le soldat <span class="important">Breacher</span> respecte la hiérarchie qu’importe la spécialisation/ affectation de son supérieur.
            L’équipement du soldat <span class="important">Breacher</span> n’est porté qu’une fois affecté lors d'entraînement ou de départ sur le combat, un soldat <span class="important">Breacher</span> non affecté est considéré comme infanterie ou unité de garde, ainsi en combat il lui est accordé le port d’un fusil d’assaut ainsi que de son matériel.
            Lors de l’utilisation de son matériel, l’unité doit faire attention au périmètre qui l'entoure. L’unité doit utiliser son matériel correctement lors de l’application d’une charge explosive ou bien le désamorçage d’une mine ou d’un explosif puissant. Connaître son matériel et savoir l’utiliser au bon moment. Il doit aussi savoir sécuriser un bâtiment rapidement.
            Ainsi, le soldat <span class="important">Breacher</span> doit respecter ces règles d’intervention présente dans le protocole. 
        </p>

        <h3>Protocole Global - Désamorçage de Bombes</h3>
        <ul>
            <li><span class="highlight">Protocole pré-désamorçage</span>: Sécurisation de la zone et de l’objectif, etablissement d’un périmètre de sécurité et mise en place de l’équipe de déminage.</li>
            <li><span class="highlight">Protocole désamorçage</span>: Annonce radio du début de la procédure de désamorçage, identification de l’engin explosif, identification du système de mise à feu, préparation du matériel en conséquence, vérification du périmètre de sécurité, procédure de désamorçage, vérification de la réussite de la procédure.</li>
            <li><span class="highlight">Protocole post-engagement</span>: Annonce radio de confirmation de désamorçage, rangement du matériel, evacuation de la zone.</li>
        </ul>

        <h3>Protocole Global - Amorçage de Bombes</h3>
        <ul>
            <li><span class="highlight">Protocole pré-amorçage</span>: Reception de l'ordre de pose d'un explosif par le gradé direct, prise de conaissance du lieu à traiter, etablissement d'un périmètre de sécurité, choix de l'explosif.</li>
            <li><span class="highlight">Protocole amorçage</span>: Prise de position des lieu à traiter, positionnement de l'explosif, amorçage de l'explosif, prise de distance de sécurité, vérification que tous les hommes respectent les mesures de sécurités, information au gradé que l'explosif est prêt, attente de l'ordre de mise à feu, mises à feu.</li>
            <li><span class="highlight">Protocole post-amorçage</span>: Prise de confirmation de l'élimination de l'objectif, (Si non, rebelote du protocole total), information de l'élimination de l'objectif au gradé, autorisation aux autres hommes de reprendre les positions.</li>
        </ul>

        <h3>Protocole Global - Sécuristaion de batiments</h3>
        <ul>
            <li><span class="highlight">Protocole pré-insertion</span>: Approche du bâtiment, sécurisation des fenêtres extérieurs, etablissement d’un périmètre de sécurité, Mise en place de l’équipe pour entrer.</li>
            <li><span class="highlight">Protocole insertion</span>: Vérification de son équipement avant insertion, insertion en bâtiment avec décompte/signe, insertion et prise de position, verrouillage de l’escalier (par une unité), insertion dans les autres pièces et sécurisation du RDC, monter dans l’étage suivant, insertion dans les pièces de l’étage, si autre escalier montant/descendant, réitérer protocole insertion.</li>
            <li><span class="highlight">Protocole post-insertion</span>: Annonce radio de confirmation de sécurisation de bâtiment, préparation pour réitérer, protocole pré-insertion et protocole d’insertion, evacuation de la zone.</li>
        </ul>

        <h3>Conclusion</h3>
        <p class="content">
            Le <span class="important">Breacher</span> joue un rôle crucial dans la pose, et le désamorçage de bombe, tout comme le nettoyage de batiments.
        </p>
    </div>

    <?php
session_start();
include 'db.php'; 


$idUtilisateur = $_SESSION['id_utilisateur'];
$stmt = $pdo->prepare("SELECT spe_id, gerance FROM utilisateurs WHERE id = :id");
$stmt->execute(['id' => $idUtilisateur]);
$utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);
$filePath = __DIR__ . '/pdf/bre.pdf';
    if (file_exists($filePath)) {
        echo "<p>Un fichier PDF Breacher est disponible : <a href='pdf/bre.pdf' target='_blank'>Afficher</a></p>";
    } else {
        echo "<p>Aucun fichier PDF disponible pour le moment.</p>";
    }

if ($utilisateur && $utilisateur['spe_id'] == 7 && in_array($utilisateur['gerance'], [1, 2])) {
    
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
                $destinationPath = __DIR__ . '/pdf/bre.pdf';

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

    <!-- Affichage du gérant et sous-gérant pour Machine Gunner (spe_id = 7) -->
    <div class="management">
        <?php
        include 'db.php'; // Connexion à la base de données

        // Requête pour récupérer le gérant et sous-gérant
        $stmt = $pdo->prepare("SELECT nom, gerance FROM utilisateurs WHERE spe_id = 7 AND gerance IN (1, 2)");
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

$stmt = $pdo->prepare("SELECT nom FROM utilisateurs WHERE spe_id = 7 AND gerance NOT IN (1, 2)");
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
