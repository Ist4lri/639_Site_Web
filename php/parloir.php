<?php
session_start();

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['id_utilisateur'])) {
    // Si l'utilisateur n'est pas connecté, redirigez-le vers la page de connexion
    header("Location: connection.php");
    exit();
}

// Inclure la connexion à la base de données
include 'db.php';

// Si l'utilisateur est connecté, vous pouvez afficher ses informations
$userId = $_SESSION['id_utilisateur'];
$nomUtilisateur = $_SESSION['nom_utilisateur'];

// Requête pour vérifier si l'utilisateur fait partie de l'Ecclesiarchie
$factionStmt = $pdo->prepare("SELECT * FROM personnages WHERE id_utilisateur = :id_utilisateur AND faction = 'Ecclesiarchie' AND validation = 'Accepter'");
$factionStmt->execute(['id_utilisateur' => $userId]); // Remplacer $currentUser['id'] par $userId
$faction = $factionStmt->fetch();

// Système de recherche par type d'entretien et nom (si applicable)
$searchQuery = "";
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $searchType = isset($_GET['search_type']) ? $_GET['search_type'] : '';
    $searchName = isset($_GET['search_name']) ? $_GET['search_name'] : '';

    // Filtrer par type d'entretien ou par nom
    $sql = "SELECT * FROM demande_eccle WHERE id_utilisateur = ?";
    $params = [$userId];

    if (!empty($searchType)) {
        $sql .= " AND type_entretien LIKE ?";
        $params[] = '%' . $searchType . '%';
    }

    if (!empty($searchName)) {
        $sql .= " AND description LIKE ?";
        $params[] = '%' . $searchName . '%';
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $demandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Récupérer toutes les demandes de l'utilisateur connecté s'il n'y a pas de recherche
    $stmt = $pdo->prepare("SELECT * FROM demande_eccle WHERE id_utilisateur = ?");
    $stmt->execute([$userId]);
    $demandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$personnageStmt = $pdo->prepare("SELECT nom FROM personnages WHERE id_utilisateur = :id_utilisateur AND faction = 'Ecclesiarchie' AND validation = 'Accepter' LIMIT 1");
$personnageStmt->execute(['id_utilisateur' => $userId]); // $userId est l'ID de l'utilisateur stocké dans la session
$personnage = $personnageStmt->fetch(PDO::FETCH_ASSOC);

if ($personnage) {
    $nomPersonnage = $personnage['nom'];
} else {
    $nomPersonnage = "Aucun personnage trouvé"; // Valeur par défaut si aucun personnage n'est trouvé
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parloir - Demandes</title>
    <link rel="stylesheet" href="../css/ecclesiarchie.css"> <!-- Lien vers votre fichier CSS -->
</head>
    <style>
@font-face {
    font-family: 'Inquisitor';
    src: url('../css/fonts/Inquisitor.otf') format('opentype');
    font-weight: normal;
    font-style: normal;
    
}
        body {
            background-image: url('../src/assets/Bougie.png');
            background-repeat: no-repeat;
            background-position: center bottom;
            background-attachment: fixed;
            background-size: cover;   
        }
    </style>

<header>
    <div class="head-logo2">
        <a href="../index.php">
            <img src="../src/assets/Banderole.png" alt="639 Régiment cadien">
        </a>
    </div>

    <nav class="head-nav">
        <?php if ($faction): ?>
            <!-- Si l'utilisateur fait partie de l'église -->
            <a href="eglise.php">Porte de l'église</a>
            <a href="../index.php">Acceuil</a>
        <?php else: ?>
            <!-- Si l'utilisateur n'est pas dans l'église -->
            <a href="profil_utilisateur.php">Profil</a>
            <a href="Dec.php">Déconnexion</a>
        <?php endif; ?>
    </nav>
</header>
<body>

<div class="container">
    <h2>Bienvenue, <?php echo htmlspecialchars($nompersonnage); ?> !</h2>
    <h3>Voici vos demandes d'entretien :</h3>

    <!-- Formulaire de recherche -->
    <form method="GET" action="parloir.php">
        <label for="search_type">Rechercher par type d'entretien :</label>
        <input type="text" name="search_type" id="search_type" placeholder="Type d'entretien" value="<?php echo htmlspecialchars($searchType ?? ''); ?>">

        <label for="search_name">Rechercher par nom (description) :</label>
        <input type="text" name="search_name" id="search_name" placeholder="Nom ou description" value="<?php echo htmlspecialchars($searchName ?? ''); ?>">

        <input type="submit" value="Rechercher">
    </form>

    <!-- Affichage des demandes dans un tableau -->
    <table>
        <thead>
            <tr>
                <th>Type d'entretien</th>
                <th>Description</th>
                <th>Status</th>
                <th>Date de création</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($demandes)): ?>
                <?php foreach ($demandes as $demande): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($demande['type_entretien']); ?></td>
                        <td><?php echo htmlspecialchars($demande['description']); ?></td>
                        <td><?php echo htmlspecialchars($demande['status']); ?></td>
                        <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($demande['date_creation']))); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">Aucune demande trouvée.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
