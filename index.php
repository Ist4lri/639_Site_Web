<?php
// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Activer l'affichage des erreurs pour déboguer
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    // Connexion à la base de données
    include 'php/db.php';
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Récupérer les spécialités avec les places occupées
try {
    $sqlSpecialties = "
        SELECT 
            s.nom, 
            s.ab,
            COUNT(u.id) AS places_occupees, 
            s.total
        FROM spe s 
        LEFT JOIN utilisateurs u ON s.id = u.spe_id 
        GROUP BY s.id, s.nom, s.ab, s.total
    ORDER BY s.id;
";
    $stmtSpecialties = $pdo->query($sqlSpecialties);
    $specialties = $stmtSpecialties->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur SQL spécialités : " . $e->getMessage());
}

// Récupérer les instructeurs (gérants)
try {
    $sqlInstructors = "
        SELECT 
            u.nom AS utilisateur_nom, 
            s.nom AS specialite_nom 
        FROM utilisateurs u 
        JOIN spe s ON u.spe_id = s.id 
        WHERE u.gerance = 1;
    ";
    $stmtInstructors = $pdo->query($sqlInstructors);
    $instructors = $stmtInstructors->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur SQL instructeurs : " . $e->getMessage());
}

// Requête pour récupérer les utilisateurs gradés (à partir du grade de Sergent)
$sqlGradedUsers = "
    SELECT nom, grade 
    FROM utilisateurs 
    WHERE grade IN ('Sergent', 'Adjudant', 'Lieutenant', 'Capitaine', 'Commandant', 'Colonel') 
    ORDER BY grade DESC";
$stmtGradedUsers = $pdo->query($sqlGradedUsers);
$gradedUsers = $stmtGradedUsers->fetchAll(PDO::FETCH_ASSOC);


$isLoggedIn = isset($_SESSION['utilisateur']);
$userName = $isLoggedIn ? $_SESSION['nom_utilisateur'] : '';
?>

<!-- HTML rest of the code follows -->


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>639ème Régiment Cadien</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/header.css">
    <style>
        .table-section h3 {
            cursor: pointer;
            color: #4CAF50;
        }

        .table-section table {
            width: 30%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table, th, td {
            border: 1px solid black;
            padding: 10px;
            text-align: center;
        }
    </style>
</head>
<body>

<header class="head">
    <div class="head-logo">
        <a href="index.php">
            <img src="src/assets/Logo.png" alt="Logo 639">
        </a>
        <?php if ($isLoggedIn): ?>
            <span class="head-username">Bonjour, <?php echo htmlspecialchars($userName); ?></span>
        <?php endif; ?>
    </div>
    <div class="head-logo2">
        <img src="src/assets/TitreSite.png" alt="639 Régiment cadien">
    </div>
    <nav class="head-nav">
        <?php if ($isLoggedIn): ?>
            <a href="php/profil_utilisateur.php">Profil</a>
            <a href="php/officier.php">Officier</a>
            <a href="php/sous-officier.php">Sous-Officier</a>
            <a href="php/Dec.php">Déconnexion</a>
        <?php else: ?>
            <a href="php/connection.php">Connexion</a>
            <a href="php/ins.php">Inscription</a>
        <?php endif; ?>
    </nav>
</header>

<div class="intro_content">
    <?php if ($isLoggedIn): ?>
        <h1>Bienvenue dans la Garde Impériale, soldat du 639ème Régiment Cadien.</h1>
        <p>Nous espérons de vous une efficacité et une assiduité exemplaire. 
            Les Cadiens, originaires de Cadia, sont parmi les régiments les plus disciplinés et les plus redoutables de l'Imperium. 
            Leur monde natal, situé dans le secteur Cadien à proximité de l'Œil de la Terreur, a fait d'eux des combattants acharnés, 
            habitués à résister aux forces chaotiques depuis leur plus jeune âge.</p>
        <p>Faites honneur à vos familles et à la mémoire de Cadia. Montrez à l'Imperium que Cadia tient toujours, à travers vous.</p>
        <h1> Pour l'Empereur, pour Cadia ! </h1>
        <!-- Tableaux interactifs pour les utilisateurs connectés -->
        <div class="table-section">
            <!-- Tableau des gradés -->
            <h3 onclick="toggleTable('graded-users')">Afficher les gradés</h3>
            <table id="graded-users" style="display:none;">
                <tr>
                    <th>Nom</th>
                    <th>Grade</th>
                </tr>
                <?php foreach ($gradedUsers as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['nom']) ?></td>
                        <td><?= htmlspecialchars($user['grade']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <div class="table-section">
            <!-- Tableau des places restantes dans les spécialités -->
            <h3 onclick="toggleTable('remaining-places')">Afficher les places restantes dans les spécialités</h3>
            <table id="remaining-places" style="display:none;">
                <tr>
                    <th>Spécialité</th>
                    <th>Places restantes</th>
                </tr>
                <?php 
                foreach ($specialties as $specialty): ?>
                    <?php
                if (isset($specialty['id']) && $specialty['id'] > 8) {
                    $specialty['id'] = 8; // Cap the value to 8
                }
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($specialty['nom']) ?></td>
                        <td><?=($specialty['places_occupees']) . '/' . $specialty['total'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <div class="table-section">
            <!-- Tableau des instructeurs (gérants) -->
            <h3 onclick="toggleTable('instructors')">Afficher les instructeurs</h3>
            <table id="instructors" style="display:none;">
                <tr>
                    <th>Nom</th>
                    <th>Spécialité</th>
                </tr>
                <?php foreach ($instructors as $instructor): ?>
                    <tr>
                    <td><?= htmlspecialchars($instructor['utilisateur_nom']) ?></td>
                        <td><?= htmlspecialchars($instructor['specialite_nom']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

    <?php else: ?>
        <!-- Contenu pour les visiteurs non connectés -->
        <h1>Le 639th régiment</h1>
        <h3>Une communauté A l&apos;écoute et présente.</h3>
        <p>
            Ici, au régiment, on est environ une soixantaine a se battre pour l&apos;empereur ! Lorsque tu nous rejoindras, tu pourras trouver des personnes présentes
            pour te guider et répondre à toutes tes questions. ...
        </p>
        <h3>Un serveur français Milsim</h3>
        <p>
            Nous sommes une team Française <img src="src/assets/drapeau_fr.png" alt="Drapeau Français"/> pratiquant le Milsim
            semi-sérieux. ...
        </p>
        <h3>Comment nous rejoindre ?</h3>
        <p>
            Pour nous rejoindre, rien de plus simple : Il suffit de rejoindre notre serveur discord ! ...
        </p>
        <h4>Alors ? prêt à nous rejoindre ?</h4>
        <h5><a href="https://discord.gg/HUwHpEZBZx" target="_blank" rel="noopener noreferrer">Enrole toi aujourd&apos;hui !</a></h5>
    <?php endif; ?>
</div>


<div class="explain_container">
    <h3>Nos spécialités</h3>
    <div class="specialties">
        <div class="specialty">
            <a href="php/mg.php">
                <img src="src/assets/BoutonMGunner.png" alt="Machine Gunner">
            </a>
        </div>
        <div class="specialty">
            <a href="php/at.php">
                <img src="src/assets/BoutonATank.png" alt="Anti-Tank">
            </a>
        </div>
        <div class="specialty">
            <a href="php/med.php">
                <img src="src/assets/BoutonMedicae.png" alt="Médicae">
            </a>
        </div>
        <div class="specialty">
            <a href="php/vox.php">
                <img src="src/assets/BoutonVOperateur.png" alt="Vox Opérateur">
            </a>
        </div>
        <div class="specialty">
            <a href="php/mark.php">
                <img src="src/assets/BoutonMarksman.png" alt="Marksman">
            </a>
        </div>
        <div class="specialty">
            <a href="php/plas.php">
                <img src="src/assets/BoutonPlasma.png" alt="Plasma">
            </a>
        </div>
        <div class="specialty">
            <a href="php/kboom.php">
                <img src="src/assets/BoutonBreacher.png" alt="Breacher">
            </a>
        </div>
        <div class="specialty">
            <a href="php/etl.php">
                <img src="src/assets/BoutonETLourd.png" alt="Equipier de Tir Lourd">
            </a>
        </div>
    </div>

    <div class="eff"><a href="php/effectif.php">Nos Effectifs</a></div>
</div>

<script>
function toggleTable(tableId) {
    const table = document.getElementById(tableId);
    table.style.display = table.style.display === "none" ? "table" : "none";
}
</script>

</body>
</html>
