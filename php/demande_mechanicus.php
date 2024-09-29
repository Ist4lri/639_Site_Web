<?php
session_start();
include 'db.php';

if (!isset($_SESSION['utilisateur'])) {
    header("Location: connection.php");
    exit();
}

// Gérer les actions Accepter et Rejeter
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && isset($_POST['demande_id'])) {
    $action = $_POST['action'];
    $demandeId = $_POST['demande_id'];

    // Ajuster les actions en fonction du statut de la BDD
    if ($action == 'Accepter') {
        $updateStmt = $pdo->prepare("UPDATE demande_mechanicus SET status = 'acceptee' WHERE id = ?");
        $updateStmt->execute([$demandeId]);
    } elseif ($action == 'Rejeter') {
        $updateStmt = $pdo->prepare("UPDATE demande_mechanicus SET status = 'rejete' WHERE id = ?");
        $updateStmt->execute([$demandeId]);
    }

    // Rediriger pour éviter le re-post
    header("Location: demande_mechanicus.php");
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
    // Transformer les labels d'interface utilisateur en statuts pour la BDD
    if ($searchStatus == 'En attente') {
        $searchStatus = 'en attente';
    } elseif ($searchStatus == 'Accepter') {
        $searchStatus = 'acceptee';
    } elseif ($searchStatus == 'Rejeter') {
        $searchStatus = 'rejete';
    }
    $query .= " AND dm.status = ?";
    $params[] = $searchStatus;
}

// Préparer et exécuter la requête
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$demandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Demandes Mechanicus</title>
    <link rel="stylesheet" href="../css/adeptus.css">
</head>
<?php include 'headerm.php'; ?>

    
<body>
    <h1>Gestion des Demandes Mechanicus</h1>

    <!-- Formulaire de recherche -->
    <form method="get" action="demande_mechanicus.php">
        <label for="search_nom">Rechercher par nom :</label>
        <input type="text" id="search_nom" name="search_nom" value="<?php echo htmlspecialchars($searchNom); ?>">

        <label for="search_date">Rechercher par date :</label>
        <input type="date" id="search_date" name="search_date" value="<?php echo htmlspecialchars($searchDate); ?>">

        <label for="search_status">Rechercher par statut :</label>
        <select id="search_status" name="search_status">
            <option value="">Tous</option>
            <option value="En attente" <?php if ($searchStatus == 'en attente') echo 'selected'; ?>>En attente</option>
            <option value="Accepter" <?php if ($searchStatus == 'acceptee') echo 'selected'; ?>>Accepter</option>
            <option value="Rejeter" <?php if ($searchStatus == 'rejete') echo 'selected'; ?>>Rejeter</option>
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
                        <td>
                            <!-- Afficher les labels corrects en fonction du statut dans la BDD -->
                            <?php 
                                if ($demande['status'] == 'en attente') {
                                    echo 'En attente';
                                } elseif ($demande['status'] == 'acceptee') {
                                    echo 'Accepter';
                                } elseif ($demande['status'] == 'rejete') {
                                    echo 'Rejeter';
                                }
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($demande['date_creation']))); ?></td>
                        <td>
                            <?php if ($demande['status'] == 'en attente'): ?>
                                <form method="post" action="demande_mechanicus.php" style="display:inline;">
                                    <input type="hidden" name="demande_id" value="<?php echo $demande['id']; ?>">
                                    <button type="submit" name="action" value="Accepter">Accepter</button>
                                    <button type="submit" name="action" value="Rejeter" class="danger">Rejeter</button>
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
