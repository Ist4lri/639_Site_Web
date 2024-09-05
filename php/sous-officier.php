<?php
session_start();
include 'db.php';


if (!isset($_SESSION['utilisateur'])) {
    header("Location: connection.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = :email");
$stmt->execute(['email' => $_SESSION['utilisateur']]);
$currentUser = $stmt->fetch();


$gradesAutorises = ['Sergent', 'Lieutenant', 'Capitaine', 'Commandant', 'Colonel', 'Général', 'Major'];
if (!in_array($currentUser['grade'], $gradesAutorises)) {
    echo "Accès refusé. Vous n'avez pas l'autorisation pour accéder à cette page.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_POST['user_id'];
    $nouvelleSpe = $_POST['nouvelle_spe'];

    if (!empty($nouvelleSpe)) {
        $stmt = $pdo->prepare("UPDATE utilisateurs SET spe_id = :nouvelle_spe WHERE id = :id");
        $stmt->execute(['nouvelle_spe' => $nouvelleSpe, 'id' => $userId]);
    }

    $message = "Les informations de l'utilisateur ont été mises à jour avec succès.";
}


$usersStmt = $pdo->query("SELECT u.id, u.nom, u.grade, s.nom AS specialite 
                          FROM utilisateurs u 
                          LEFT JOIN spe s ON u.spe_id = s.id "); 
$users = $usersStmt->fetchAll(PDO::FETCH_ASSOC);

$specialitesStmt = $pdo->query("SELECT id, nom FROM spe");
$specialites = $specialitesStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Spécialités</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Gestion des spécialités</h1>

    <?php if (isset($message)): ?>
        <p style="color: green;"><?php echo $message; ?></p>
    <?php endif; ?>

    <table border="1">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Grade actuel</th>
                <th>Spécialité actuelle</th>
                <th>Nouvelle spécialité</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['nom']); ?></td>
                    <td><?php echo htmlspecialchars($user['grade']); ?></td>
                    <td><?php echo htmlspecialchars($user['specialite']); ?></td>
                    <td>
                        <form action="sous-officier.php" method="post">
                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['id']); ?>">
                            <select name="nouvelle_spe">
                                <option value="">Sélectionnez une spécialité</option>
                                <?php foreach ($specialites as $spe): ?>
                                    <option value="<?php echo $spe['id']; ?>"><?php echo htmlspecialchars($spe['nom']); ?></option>
                                <?php endforeach; ?>
                            </select>
                    </td>
                    <td>
                            <button type="submit">Mettre à jour</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
