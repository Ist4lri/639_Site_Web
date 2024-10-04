<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

// Update request status
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $request_id = $_POST['request_id'];
    $new_status = $_POST['status'];

    if (!empty($request_id) && !empty($new_status)) {
        $sqlUpdate = "UPDATE dadmin SET status = ? WHERE id = ?";
        $stmtUpdate = $pdo->prepare($sqlUpdate);
        $stmtUpdate->execute([$new_status, $request_id]);
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Demandes Administratives</title>
    <link rel="stylesheet" href="css/back.css">
  <a href="back.php">Back</a>
    <a href="zeusing.php">Zeus</a>
    <a href="index.php">Acceuil</a>
</head>
<body>
    <h1>Demandes Administratives</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Utilisateur</th>
                <th>Demande</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($requests as $request): ?>
                <tr>
                    <td><?php echo htmlspecialchars($request['id']); ?></td>
                    <td><?php echo htmlspecialchars($request['utilisateur_nom']); ?></td>
                    <td><?php echo htmlspecialchars($request['demande']); ?></td>
                    <td><?php echo htmlspecialchars($request['statut']); ?></td>
                    <td>
    <form method="POST" action="" style="display:inline;">
        <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($request['id']); ?>">
        <button type="submit" name="update_status" value="Fait">Fait</button>
    </form>
    <form method="POST" action="" style="display:inline;">
        <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($request['id']); ?>">
        <button type="submit" name="update_status" value="Refusé">Refusé</button>
    </form>
</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
