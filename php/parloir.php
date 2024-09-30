<?php
session_start();
include 'db.php';

if (!isset($_SESSION['utilisateur'])) {
    header("Location: connection.php");
    exit();
}

$currentUser = $_SESSION['utilisateur'];

// Vérifier si un formulaire a été soumis pour créer une demande
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['request_type'], $_POST['request_description'])) {
    $requestType = $_POST['request_type'];
    $description = trim($_POST['request_description']);

    // Insérer la demande avec la description fournie par l'utilisateur
    $insertStmt = $pdo->prepare("INSERT INTO demande_eccle (id_utilisateur, type_entretien, description) VALUES (?, ?, ?)");
    $insertStmt->execute([$currentUser['id'], $requestType, $description]);

    $message = "Votre demande a été soumise avec succès pour un entretien $requestType.";
}

// Système de recherche par type d'entretien et nom de l'utilisateur
$searchType = isset($_GET['search_type']) ? trim($_GET['search_type']) : '';
$searchNom = isset($_GET['search_nom']) ? trim($_GET['search_nom']) : '';

// Construire la requête de base
$query = "SELECT u.nom, de.type_entretien, de.description, de.status, de.date_creation 
          FROM demande_eccle de 
          JOIN utilisateurs u ON de.id_utilisateur = u.id 
          WHERE u.id = ?";

// Ajouter des conditions de recherche si elles existent
$params = [$currentUser['id']];

if (!empty($searchType)) {
    $query .= " AND de.type_entretien LIKE ?";
    $params[] = '%' . $searchType . '%';
}

if (!empty($searchNom)) {
    $query .= " AND u.nom LIKE ?";
    $params[] = '%' . $searchNom . '%';
}

// Exécuter la requête
$demandeStmt = $pdo->prepare($query);
$demandeStmt->execute($params);
$demandes = $demandeStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parloir Ecclesiarchie</title>
    <link rel="stylesheet" href="../css/ecclesiarchie.css">
</head>
<body>
    <h1>Parloir Ecclesiarchie</h1>

    <!-- Formulaire de création de demande -->
    <form method="post" action="parloir.php">
        <label for="request_type">Type d'entretien :</label>
        <input type="text" id="request_type" name="request_type" required>

        <label for="request_description">Description :</label>
        <textarea id="request_description" name="request_description" required></textarea>

        <button type="submit">Soumettre la demande</button>
    </form>

    <?php if (isset($message)) : ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>

    <!-- Formulaire de recherche -->
    <form method="get" action="parloir.php">
        <label for="search_type">Rechercher par type d'entretien :</label>
        <input type="text" id="search_type" name="search_type" value="<?php echo htmlspecialchars($searchType); ?>">

        <label for="search_nom">Rechercher par nom d'utilisateur :</label>
        <input type="text" id="search_nom" name="search_nom" value="<?php echo htmlspecialchars($searchNom); ?>">

        <button type="submit">Rechercher</button>
    </form>

    <!-- Tableau des demandes -->
    <table border="1">
        <thead>
            <tr>
                <th>Utilisateur</th>
                <th>Type d'entretien</th>
                <th>Description</th>
                <th>Statut</th>
                <th>Date de création</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($demandes)) : ?>
                <tr>
                    <td colspan="5">Aucune demande trouvée.</td>
                </tr>
            <?php else : ?>
                <?php foreach ($demandes as $demande) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($demande['nom']); ?></td>
                        <td><?php echo htmlspecialchars($demande['type_entretien']); ?></td>
                        <td><?php echo htmlspecialchars($demande['description']); ?></td>
                        <td><?php echo htmlspecialchars($demande['status']); ?></td>
                        <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($demande['date_creation']))); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
