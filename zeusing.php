<?php
session_start();
include 'db.php';

if (!isset($_SESSION['utilisateur'])) {
    header("Location: connection.php");
    exit();
}

if ($_SESSION['role'] !== 'admin') {
    echo "Vous n'avez pas l'autorisation d'accéder à cette page.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $userId = (int)$_POST['id'];
    $mappeur = isset($_POST['mappeur']) ? 1 : 0;
    $zeus = isset($_POST['zeus']) ? 1 : 0;

    $stmt = $pdo->prepare("UPDATE utilisateurs SET mappeur = ?, zeus = ? WHERE id = ?");
    $stmt->execute([$mappeur, $zeus, $userId]);

    header("Location: zeusing.php");
    exit();
}

$stmt = $pdo->query("SELECT id, nom, mappeur, zeus FROM utilisateurs");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Zeus et Mappeurs</title>
    <link rel="stylesheet" href="css/tab.css">
  
</head>
<body>

<h2>Gestion des Zeus et Mappeurs</h2>

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
    </tbody>
</table>

</body>
</html>
