<?php
session_start();
include 'db.php';

if (!isset($_SESSION['utilisateur'])) {
    header("Location: connection.php");
    exit();
}

// Récupérer toutes les demandes Mechanicus avec possibilité de recherche par nom, date et statut
$searchNom = isset($_GET['search_nom']) ? trim($_GET['search_nom']) : '';
$searchDate = isset($_GET['search_date']) ? trim($_GET['search_date']) : '';
$searchStatus = isset($_GET['search_status']) ? trim($_GET['search_status']) : '';

// Construire la requête de base
$query = "SELECT dm.id, u.nom AS utilisateur, dm.type_entretien, dm.description, dm.status, dm.date_creation 
          FROM demande_mechanicus dm 
          JOIN utilisateurs u ON dm.id_utilisateur = u.id 
          WHERE 1=1";

// Ajouter des conditions de recherche si elles existent
$params = [];

if (!empty($searchNom)) {
    $query .= " AND u.nom LIKE ?";
    $params[] = '%' . $searchNom . '%';
}

if (!empty($searchDate)) {
    $query .= " AND DATE(dm.date_creation) = ?";
    $params[] = $searchDate;
}

if (!empty($searchStatus)) {
    $query .= " AND dm.status = ?";
    $params[] = $searchStatus;
}

// Préparer et exécuter la requête
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$demandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Gérer les actions Accepter et Rejeter
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && isset($_POST['demande_id'])) {
    $action = $_POST['action'];
    $demandeId = $_POST['demande_id'];

    if ($action == 'accepter') {
        $updateStmt = $pdo->prepare("UPDATE demande_mechanicus SET status = 'Acceptée' WHERE id = ?");
        $updateStmt->execute([$demandeId]);
    } elseif ($action == 'rejeter') {
        $updateStmt = $pdo->prepare("UPDATE demande_mechanicus SET status = 'Rejetée' WHERE id = ?");
        $updateStmt->execute([$demandeId]);
    }

    // Rediriger pour éviter le re-post
    header("Location: demandes_mechanicus.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Demandes Mechanicus</title>
    <link rel="stylesheet" href="../css/adeptus.css">
</head>
<body>
    <h1>Gestion des Demandes Mechanicus</h1>

    <!-- Formulaire de recherche -->
    <form method="get" action="demandes_mechanicus.php">
        <label for="search_nom">Rechercher par nom :</label>
        <input type="text" id="search_nom" name="search_nom" value="<?php echo htmlspecialchars($searchNom); ?>">

        <label for="search_date">Rechercher par date :</label>
        <input type="date" id="search_date" name="search_date" value="<?php echo htmlspecialchars($searchDate); ?>">

        <label for="search_status">Rechercher par statut :</label>
        <select id="search_status" name="search_status">
            <option value="">Tous</option>
            <option value="En attente" <?php if ($searchStatus == 'En attente') echo 'selected'; ?>>En attente</option>
            <option value="Acceptée" <?php if ($searchStatus == 'Acceptée') echo 'selected'; ?>>Acceptée</option>
            <option value="Rejetée" <?php if ($searchStatus == 'Rejetée') echo 'selected'; ?>>Rejetée</option>
        </select>

        <button type="submit">Rechercher</button>
    </form>

    <!-- Table des demandes -->
    <table border="1">
        <thead>
            <tr>
                <th>Utilisateur</th>
                <th>Type d'entretien</th>
                <th>Description</th>
                <th>Statut</th>
                <th>Date de création</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($demandes)): ?>
                <tr>
                    <td colspan="6">Aucune demande trouvée.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($demandes as $demande): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($demande['utilisateur']); ?></td>
                        <td><?php echo htmlspecialchars($demande['type_entretien']); ?></td>
                        <td><?php echo htmlspecialchars($demande['description']); ?></td>
                        <td><?php echo htmlspecialchars($demande['status']); ?></td>
                        <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($demande['date_creation']))); ?></td>
                        <td>
                            <?php if ($demande['status'] == 'en attente'): ?>
                                <form method="post" action="demandes_mechanicus.php" style="display:inline;">
                                    <input type="hidden" name="demande_id" value="<?php echo $demande['id']; ?>">
                                    <button type="submit" name="action" value="acceptee">Accepter</button>
                                    <button type="submit" name="action" value="rejetee" class="danger">Rejeter</button>
                                </form>
                            <?php else: ?>
                                <?php echo htmlspecialchars($demande['status']); ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
