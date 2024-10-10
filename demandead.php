<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the database connection
try {
    include 'php/db.php';
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Default search criteria
$search_statut = $_GET['statut'] ?? '';
$search_utilisateur = $_GET['utilisateur'] ?? '';

// Fetch requests with search criteria
try {
    $sqlRequests = "SELECT d.id, u.nom AS utilisateur_nom, d.demande, d.statut 
                    FROM dadmin d 
                    JOIN utilisateurs u ON d.utilisateur_id = u.id 
                    WHERE 1=1";

    // Apply filters
    if (!empty($search_statut)) {
        $sqlRequests .= " AND d.statut = :statut";
    }
    if (!empty($search_utilisateur)) {
        $sqlRequests .= " AND u.nom LIKE :utilisateur";
    }
    $sqlRequests .= " ORDER BY d.id;";

    $stmtRequests = $pdo->prepare($sqlRequests);

    // Bind search parameters
    if (!empty($search_statut)) {
        $stmtRequests->bindValue(':statut', $search_statut);
    }
    if (!empty($search_utilisateur)) {
        $stmtRequests->bindValue(':utilisateur', "%{$search_utilisateur}%");
    }

    $stmtRequests->execute();
    $requests = $stmtRequests->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur SQL demandes : " . $e->getMessage());
}

// Update request statut
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id'], $_POST['update_statut'])) {
    $request_id = $_POST['request_id'];
    $new_statut = $_POST['update_statut']; // Use the button value directly

    if (!empty($request_id) && !empty($new_statut)) {
        try {
            $sqlUpdate = "UPDATE dadmin SET statut = ? WHERE id = ?";
            $stmtUpdate = $pdo->prepare($sqlUpdate);
            $stmtUpdate->execute([$new_statut, $request_id]);

            // Redirect with query params to retain search filters
            header("Location: demandead.php?statut=" . urlencode($search_statut) . "&utilisateur=" . urlencode($search_utilisateur));
            exit;
        } catch (PDOException $e) {
            die("Erreur lors de la mise à jour du statut : " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Demandes Administratives</title>
    <link rel="icon" type="image/x-icon" href="src/assets/Logo_639th_2.ico">
    <link rel="stylesheet" href="css/back.css"> 
</head>
<body>
    <h1>Demandes Administratives</h1>
    <a href="back.php">Back</a>
    <a href="zeusing.php">Zeus</a>
    <a href="index.php">Accueil</a>

    <!-- Search Form -->
    <form method="GET" action="demandead.php">
        <label for="statut">Statut:</label>
        <select id="statut" name="statut">
            <option value="" <?php if ($search_statut == '') echo 'selected'; ?>>Tous</option>
            <option value="Fait" <?php if ($search_statut == 'Fait') echo 'selected'; ?>>Fait</option>
            <option value="Refusé" <?php if ($search_statut == 'Refusé') echo 'selected'; ?>>Refusé</option>
            <!-- Add more statut options as needed -->
        </select>

        <label for="utilisateur">Utilisateur:</label>
        <input type="text" id="utilisateur" name="utilisateur" value="<?php echo htmlspecialchars($search_utilisateur); ?>" placeholder="Nom utilisateur">

        <button type="submit">Rechercher</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Utilisateur</th>
                <th>Demande</th>
                <th>Statut</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($requests as $request): ?>
                <tr>
                    <td><?php echo htmlspecialchars($request['id']); ?></td>
                    <td><?php echo htmlspecialchars($request['utilisateur_nom']); ?></td>
                    <td><?php echo htmlspecialchars($request['demande']); ?></td>
                    <td><?php echo htmlspecialchars($request['statut'] ?? 'Non défini'); ?></td>
                    <td>
                        <form method="POST" action="" style="display:inline;">
                            <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($request['id']); ?>">
                            <button type="submit" name="update_statut" value="Fait">Fait</button>
                        </form>
                        <form method="POST" action="" style="display:inline;">
                            <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($request['id']); ?>">
                            <button type="submit" name="update_statut" value="Refusé">Refusé</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
