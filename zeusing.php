<?php
session_start();
include 'php/db.php';

if (!isset($_SESSION['utilisateur'])) {
    header("Location: connection.php");
    exit();
}

if ($_SESSION['role'] !== 'admin') {
    echo "Vous n'avez pas l'autorisation d'accéder à cette page.";
    exit();
}

// Handle search query
$searchQuery = '';
if (isset($_GET['search'])) {
    $searchQuery = trim($_GET['search']);
}

// Handle form submission for updating mappeur and zeus
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_id'])) {
    $userId = (int)$_POST['user_id'];
    $mappeur = isset($_POST['mappeur']) ? 1 : 0;
    $zeus = isset($_POST['zeus']) ? 1 : 0;

    $stmt = $pdo->prepare("UPDATE utilisateurs SET mappeur = ?, zeus = ? WHERE id = ?");
    $stmt->execute([$mappeur, $zeus, $userId]);

    header("Location: zeusing.php");
    exit();
}

// Fetch users based on search query
if ($searchQuery) {
    $stmt = $pdo->prepare("SELECT id, nom, mappeur, zeus FROM utilisateurs WHERE nom LIKE ?");
    $stmt->execute(['%' . $searchQuery . '%']);
} else {
    $stmt = $pdo->query("SELECT id, nom, mappeur, zeus FROM utilisateurs");
}
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Zeus et Mappeurs</title>
    <link rel="stylesheet" href="css/tab.css">
    <a href="back.php">Back</a>
    <a href="index.php">Acceuil</a>
</head>
<body>

<h2>Gestion des Zeus et Mappeurs</h2>

<!-- Search Form -->
<form method="GET" action="zeusing.php">
    <input type="text" name="search" placeholder="Rechercher par nom" value="<?php echo htmlspecialchars($searchQuery); ?>">
    <button type="submit">Rechercher</button>
</form>

<table>
    <thead>
        <tr>
            <th>Utilisateur</th>
            <th>Mappeur</th>
            <th>Zeus</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($users) > 0): ?>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo htmlspecialchars($user['nom']); ?></td>
                <td>
                    <form method="POST" action="zeusing.php">
                        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['id']); ?>">
                        <input type="checkbox" name="mappeur" value="1" <?php if ($user['mappeur'] == 1) echo 'checked'; ?>>
                </td>
                <td>
                        <input type="checkbox" name="zeus" value="1" <?php if ($user['zeus'] == 1) echo 'checked'; ?>>
                </td>
                <td>
                        <button type="submit" class="btn-submit">Mettre à jour</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4">Aucun utilisateur trouvé.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
