<?php
session_start();
include 'db.php';

if (!isset($_SESSION['id_utilisateur'])) {
    die('Vous devez être connecté pour voir les personnages.');
}

$id_utilisateur = $_SESSION['id_utilisateur'];

$sqlLeader = "SELECT faction FROM personnages WHERE id_utilisateur = :id_utilisateur AND gerance = 1 LIMIT 1";
$stmtLeader = $pdo->prepare($sqlLeader);
$stmtLeader->execute(['id_utilisateur' => $id_utilisateur]);
$factionLeader = $stmtLeader->fetch(PDO::FETCH_ASSOC);

if ($factionLeader) {
    $faction = $factionLeader['faction'];
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
} else {
    die('Vous n\'êtes pas autorisé à voir ces informations.');
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Personnages</title>
    <style>
        body { font-family: Arial, sans-serif; }
        h2 { color: #4CAF50; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        table, th, td { border: 1px solid black; padding: 10px; text-align: center; }
        button { padding: 8px 12px; margin-right: 10px; }
        .pdf-btn { background-color: #4CAF50; color: white; }
        .accept-btn { background-color: #28a745; color: white; }
        .reject-btn { background-color: #dc3545; color: white; }
    </style>
</head>
<body>

<h1>Liste des Personnages de la Faction <?= htmlspecialchars($faction); ?></h1>

<?php if (isset($showReasonForm) && $showReasonForm && $characterIdForReason): ?>
    <h2>Raison pour rejeter le personnage</h2>
    <form action="validation_perso.php" method="POST">
        <input type="hidden" name="id" value="<?= $characterIdForReason; ?>">
        <textarea name="raison" rows="4" cols="50" placeholder="Entrez la raison pour rejeter ce personnage..." required></textarea><br><br>
        <button type="submit" name="reject_with_reason" class="reject-btn">Envoyer la Raison et Rejeter</button>
    </form>
<?php endif; ?>

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
                    <!-- PDF Button -->
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
