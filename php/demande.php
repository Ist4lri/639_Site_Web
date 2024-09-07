<?php
session_start();
include 'db.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['utilisateur'])) {
    header("Location: connection.php");
    exit();
}

$stmt = $pdo->prepare("SELECT id, grade FROM utilisateurs WHERE email = :email");
$stmt->execute(['email' => $_SESSION['utilisateur']]);
$currentUser = $stmt->fetch();

$GradeAutorise = ['Lieutenant', 'Capitaine', 'Major', 'Colonel', 'General'];

if (!in_array($currentUser['grade'], $GradeAutorise)) {
    header("Location: unauthorized.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $id_demande = $_POST['id_demande'];
    $action = $_POST['action'];

    if ($action == "accept") {
        $stmt = $pdo->prepare("UPDATE demande SET status = 'Accepted' WHERE id = ?");
        $stmt->execute([$id_demande]);
        $message = "Demande acceptée avec succès.";
    } elseif ($action == "reject") {
        $stmt = $pdo->prepare("UPDATE demande SET status = 'Rejected' WHERE id = ?");
        $stmt->execute([$id_demande]);
        $message = "Demande rejetée avec succès.";
    }
}

$stmt = $pdo->query("SELECT d.id, u.nom AS utilisateur, d.demande, d.status 
                     FROM demande d 
                     JOIN utilisateurs u ON d.id_utilisateurs = u.id");
$demandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Demandes</title>
    <link rel="stylesheet" href="../css/med.css">
</head>
<body>

<div class="container">
    <h2>Gestion des Demandes</h2>

    <?php if (isset($message)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Utilisateur</th>
                <th>Demande</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($demandes as $demande): ?>
            <tr>
                <td><?php echo htmlspecialchars($demande['utilisateur']); ?></td>
                <td><?php echo htmlspecialchars($demande['demande']); ?></td>
                <td><?php echo htmlspecialchars($demande['status'] ?? 'En attente'); ?></td>
                <td>
                    <?php if ($demande['status'] !== 'Accepted' && $demande['status'] !== 'Rejected'): ?>
                    <form action="demandes.php" method="post" style="display:inline;">
                        <input type="hidden" name="id_demande" value="<?php echo $demande['id']; ?>">
                        <button type="submit" name="action" value="accept" class="btn btn-success">Accepter</button>
                        <button type="submit" name="action" value="reject" class="btn btn-danger">Rejeter</button>
                    </form>
                    <?php else: ?>
                        <?php echo htmlspecialchars($demande['status']); ?>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
