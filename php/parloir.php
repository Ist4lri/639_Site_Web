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

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parloir - Demandes</title>
    <link rel="stylesheet" href="../css/style.css"> <!-- Lien vers votre fichier CSS -->
</head>
<body>

<div class="container">
    <h2>Bienvenue, <?php echo htmlspecialchars($nomUtilisateur); ?> !</h2>
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
