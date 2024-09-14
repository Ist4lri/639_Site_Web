<?php
session_start();
include 'db.php';

// Check if the user is an admin
if (!isset($_SESSION['utilisateur']) || $_SESSION['role'] !== 'admin') {
    header("Location: connection.php");
    exit();
}

// Fetch pending specialization requests
$stmt = $pdo->prepare("
    SELECT ds.id, u.nom as utilisateur_nom, s.nom as spe_nom
    FROM demande_spe ds
    JOIN utilisateurs u ON ds.utilisateur_id = u.id
    JOIN spe s ON ds.spe_id = s.id
    WHERE ds.demande = 'Attente'
");
$stmt->execute();
$demandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submissions for accepting requests
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['accept']) && isset($_POST['demande_id'])) {
        $demande_id = $_POST['demande_id'];

        // Fetch the demande details
        $demandeStmt = $pdo->prepare("
            SELECT ds.spe_id, ds.utilisateur_id
            FROM demande_spe ds
            WHERE ds.id = :id
        ");
        $demandeStmt->execute(['id' => $demande_id]);
        $demande = $demandeStmt->fetch(PDO::FETCH_ASSOC);

        if ($demande) {
            // Update the user's spe_id
            $updateStmt = $pdo->prepare("UPDATE utilisateurs SET spe_id = :spe_id WHERE id = :id");
            $updateStmt->execute(['spe_id' => $demande['spe_id'], 'id' => $demande['utilisateur_id']]);

            // Mark the request as accepted
            $updateDemandeStmt = $pdo->prepare("UPDATE demande_spe SET demande = 'Accepter' WHERE id = :id");
            $updateDemandeStmt->execute(['id' => $demande_id]);

            $message = "Spécialité acceptée avec succès.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des demandes de spécialités</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Demandes de spécialités en attente</h1>

    <?php if (isset($message)): ?>
        <p style="color: green;"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Utilisateur</th>
                <th>Spécialité demandée</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($demandes as $demande): ?>
                <tr>
                    <td><?php echo htmlspecialchars($demande['utilisateur_nom']); ?></td>
                    <td><?php echo htmlspecialchars($demande['spe_nom']); ?></td>
                    <td>
                        <form action="gestion_demandes.php" method="post">
                            <input type="hidden" name="demande_id" value="<?php echo $demande['id']; ?>">
                            <button type="submit" name="accept">Accepter</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
