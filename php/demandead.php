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
    $sqlRequests = "SELECT d.id, u.nom AS utilisateur_nom, d.demande, d.status 
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
    <link rel="stylesheet" href="../css/form.css">
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
                    <td><?php echo htmlspecialchars($request['status']); ?></td>
                    <td>
                        <form method="POST" action="">
                            <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($request['id']); ?>">
                            <select name="status" required>
                                <option value="" disabled selected>Select Status</option>
                                <option value="Fait">Fait</option>
                                <option value="Refusé">Refusé</option>
                            </select>
                            <button type="submit" name="update_status">Update</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
