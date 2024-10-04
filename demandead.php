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

// Fetch all requests
try {
    $sqlRequests = "SELECT d.id, u.nom AS utilisateur_nom, d.demande, d.statut 
                    FROM dadmin d 
                    JOIN utilisateurs u ON d.utilisateur_id = u.id 
                    ORDER BY d.id;";
    $stmtRequests = $pdo->query($sqlRequests);
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
            // Optionally, you can redirect to the same page to see the updated status
            header("Location: demandead.php"); // Refresh the page
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
    <link rel="stylesheet" href="css/back.css"> 
</head>
<body>
    <h1>Demandes Administratives</h1>
    <a href="back.php">Back</a>
    <a href="zeusing.php">Zeus</a>
    <a href="index.php">Accueil</a>
<style>
    h1 {
    margin-top: 10px;
    font-size: 3rem;
    text-align: center;
    letter-spacing: 2px;
    color: #f1f1f1;
}
</style>
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
                    <td><?php echo htmlspecialchars($request['statut'] ?? 'Non défini'); ?></td> <!-- Default value -->
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
