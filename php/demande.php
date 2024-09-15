<?php
session_start();

if (!isset($_SESSION['utilisateur'])) {
    header("Location: connection.php");
    exit();
}

include 'db.php';

$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = :email");
$stmt->execute(['email' => $_SESSION['utilisateur']]);
$utilisateur = $stmt->fetch();

$stmt = $pdo->prepare("SELECT u.*, s.nom AS specialite_nom FROM utilisateurs u LEFT JOIN spe s ON u.spe_id = s.id WHERE u.email = :email");
$stmt->execute(['email' => $_SESSION['utilisateur']]);
$utilisateur = $stmt->fetch();

if (!$utilisateur) {
    echo "Erreur: Utilisateur introuvable.";
    exit();
}

$GradeAutorise = ['Lieutenant', 'Capitaine', 'Major', 'Colonel', 'General'];

if (!in_array($utilisateur['grade'], $GradeAutorise)) {
    header("Location: unauthorized.php");
    exit();
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $id_demande = $_POST['id_demande'];
    $action = $_POST['action'];

    if ($action == "accepter") {
        $stmt = $pdo->prepare("UPDATE demande SET status = 'accepter' WHERE id = ?");
        $stmt->execute([$id_demande]);
        $message = "Demande acceptée avec succès.";
    } elseif ($action == "rejeter") {
        $stmt = $pdo->prepare("UPDATE demande SET status = 'rejeter' WHERE id = ?");
        $stmt->execute([$id_demande]);
        $message = "Demande rejetée avec succès.";
    }
}

$pendingStmt = $pdo->query("SELECT d.id, u.nom AS utilisateur, d.demande, d.status 
                            FROM demande d 
                            JOIN utilisateurs u ON d.id_utilisateurs = u.id
                            WHERE d.status = 'en attente'");
$pendingDemandes = $pendingStmt->fetchAll(PDO::FETCH_ASSOC);

$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';
$statusCondition = !empty($statusFilter) ? "WHERE d.status = :status" : '';

$query = "SELECT d.id, u.nom AS utilisateur, d.demande, d.status 
          FROM demande d 
          JOIN utilisateurs u ON d.id_utilisateurs = u.id 
          $statusCondition";

$stmt = $pdo->prepare($query);
if (!empty($statusFilter)) {
    $stmt->execute(['status' => $statusFilter]);
} else {
    $stmt->execute();
}
$demandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Demandes</title>
    <link rel="stylesheet" href="../css/med.css">
    <link rel="stylesheet" href="../css/header.css">
</head>

    <header class="head">
    <div class="head-logo">
        <a href="../index.php">
            <img src="../src/assets/Logo.png" alt="Logo 639">
        </a>
    </div>
    <div class="head-logo2">
        <a href="../index.php">
        <img src="../src/assets/TitreSite.png" alt="639 Régiment cadien">
        </a>
    </div>
    <nav class="head-nav">
            <a href="profil_utilisateur.php">Profil</a>
            <a href="officier.php">Officier</a>
            <a href="Dec.php">Déconnexion</a>
    </nav>
</header>
    
<body>

<div class="container">
    <h2>Gestion des Demandes</h2>

    <?php if (isset($message)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <h3>Demandes en attente</h3>
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
            <?php foreach ($pendingDemandes as $demande): ?>
            <tr>
                <td><?php echo htmlspecialchars($demande['utilisateur']); ?></td>
                <td><?php echo htmlspecialchars($demande['demande']); ?></td>
                <td><?php echo htmlspecialchars($demande['status'] ?? 'en attente'); ?></td>
                <td>
                    <form action="demande.php" method="post" style="display:inline;">
                        <input type="hidden" name="id_demande" value="<?php echo $demande['id']; ?>">
                        <button type="submit" name="action" value="accepter" class="btn btn-success">Accepter</button>
                        <button type="submit" name="action" value="rejeter" class="btn btn-danger">Rejeter</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <form method="GET" action="demande.php">
        <label for="status">Rechercher par statut :</label>
        <select name="status" id="status">
            <option value="">Tous les statuts</option>
            <option value="en attente" <?php if ($statusFilter == 'en attente') echo 'selected'; ?>>En attente</option>
            <option value="accepter" <?php if ($statusFilter == 'accepter') echo 'selected'; ?>>Acceptée</option>
            <option value="rejeter" <?php if ($statusFilter == 'rejeter') echo 'selected'; ?>>Rejetée</option>
        </select>
        <button type="submit" class="btn btn-primary">Rechercher</button>
    </form>

    <h3>Toutes les Demandes</h3>
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
                <td><?php echo htmlspecialchars($demande['status'] ?? 'en attente'); ?></td>
                <td>
                    <?php if ($demande['status'] !== 'accepter' && $demande['status'] !== 'rejeter'): ?>
                    <form action="demande.php" method="post" style="display:inline;">
                        <input type="hidden" name="id_demande" value="<?php echo $demande['id']; ?>">
                        <button type="submit" name="action" value="accepter" class="btn btn-success">Accepter</button>
                        <button type="submit" name="action" value="rejeter" class="btn btn-danger">Rejeter</button>
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
