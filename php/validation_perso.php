<?php
session_start();
include 'db.php';

if (!isset($_SESSION['id_utilisateur'])) {
    die('Vous devez être connecté pour voir les personnages.');
}

$id_utilisateur = $_SESSION['id_utilisateur'];

//gerance = 1
$sqlLeader = "SELECT faction FROM personnages WHERE id_utilisateur = :id_utilisateur AND gerance = 1 LIMIT 1";
$stmtLeader = $pdo->prepare($sqlLeader);
$stmtLeader->execute(['id_utilisateur' => $id_utilisateur]);
$factionLeader = $stmtLeader->fetch(PDO::FETCH_ASSOC);

if (!$factionLeader) {
    die('Vous n\'êtes pas autorisé à voir ces informations.');
}

$faction = $factionLeader['faction'];

//acceptation et de rejet
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['action'])) {
    $id = $_POST['id'];
    $action = $_POST['action'];

    if ($action == 'Accepter') {
        $sql = "UPDATE personnages SET validation = 'Accepter' WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

    } elseif ($action == 'Rejeter' && isset($_POST['raison'])) {
        $raison = $_POST['raison'];
        $sql = "UPDATE personnages SET validation = 'Rejeter', raison = :raison WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id, 'raison' => $raison]);
    }

    header('Location: validation_perso.php');
    exit;
}

// en attente
$sqlPending = "SELECT * FROM personnages WHERE validation = 'Attente' AND faction = :faction ORDER BY id DESC";
$stmtPending = $pdo->prepare($sqlPending);
$stmtPending->execute(['faction' => $faction]);
$pendingCharacters = $stmtPending->fetchAll(PDO::FETCH_ASSOC);

$sqlAccepted = "SELECT * FROM personnages WHERE validation = 'Accepter' AND faction = :faction ORDER BY id DESC";
$stmtAccepted = $pdo->prepare($sqlAccepted);
$stmtAccepted->execute(['faction' => $faction]);
$acceptedCharacters = $stmtAccepted->fetchAll(PDO::FETCH_ASSOC);

$sqlRejected = "SELECT * FROM personnages WHERE validation = 'Rejeter' AND faction = :faction ORDER BY id DESC";
$stmtRejected = $pdo->prepare($sqlRejected);
$stmtRejected->execute(['faction' => $faction]);
$rejectedCharacters = $stmtRejected->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'header.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="../css/tab.css">
</head>
    <style>
        h2 { padding-top: 100px;}
        button { padding: 8px 12px; margin-right: 10px; }
        .pdf-btn { background-color: #4CAF50; color: white; }
        .accept-btn { background-color: #28a745; color: white; }
        .reject-btn { background-color: #dc3545; color: white; }
    </style>
</head>
<body>


<h2>Personnages en Attente</h2>
<?php if (count($pendingCharacters) > 0): ?>
    <table>
        <tr>
            <th>Nom</th>
            <th>Faction</th>
            <th>Histoire</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($pendingCharacters as $character): ?>
            <tr>
                <td><?= htmlspecialchars($character['nom']); ?></td>
                <td><?= htmlspecialchars($character['faction']); ?></td>
                <td><?= htmlspecialchars(substr($character['histoire'], 0, 50)); ?>...</td>
                <td>
                    <!--PDF-->
                    <a href="affiche_perso.php?id=<?= $character['id']; ?>" target="_blank">
                        <button class="pdf-btn">PDF</button>
                    </a>
                    <form action="validation_perso.php" method="POST" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $character['id']; ?>">
                        <input type="hidden" name="action" value="Accepter">
                        <button type="submit" class="accept-btn">Accepter</button>
                    </form>
                    <form action="validation_perso.php" method="POST" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $character['id']; ?>">
                        <input type="hidden" name="action" value="Rejeter">
                        <textarea name="raison" rows="2" cols="30" placeholder="Raison du rejet" required></textarea>
                        <button type="submit" class="reject-btn">Rejeter</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>Aucun personnage en attente.</p>
<?php endif; ?>

<h2>Personnages Acceptés</h2>
<?php if (count($acceptedCharacters) > 0): ?>
    <table>
        <tr>
            <th>Nom</th>
            <th>Faction</th>
            <th>Histoire</th>
        </tr>
        <?php foreach ($acceptedCharacters as $character): ?>
            <tr>
                <td><?= htmlspecialchars($character['nom']); ?></td>
                <td><?= htmlspecialchars($character['faction']); ?></td>
                <td><?= htmlspecialchars(substr($character['histoire'], 0, 50)); ?>...</td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>Aucun personnage accepté.</p>
<?php endif; ?>


<h2>Personnages Rejetés</h2>
<?php if (count($rejectedCharacters) > 0): ?>
    <table>
        <tr>
            <th>Nom</th>
            <th>Faction</th>
            <th>Histoire</th>
            <th>Raison du Rejet</th>
        </tr>
        <?php foreach ($rejectedCharacters as $character): ?>
            <tr>
                <td><?= htmlspecialchars($character['nom']); ?></td>
                <td><?= htmlspecialchars($character['faction']); ?></td>
                <td><?= htmlspecialchars(substr($character['histoire'], 0, 50)); ?>...</td>
                <td><?= htmlspecialchars($character['raison']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>Aucun personnage rejeté.</p>
<?php endif; ?>

</body>
</html>
