<?php
session_start();
include 'db.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['utilisateur'])) {
    header("Location: connection.php");
    exit();
}

// Récupérer l'utilisateur actuel
$stmt = $pdo->prepare("SELECT id, spe_id, gerance FROM utilisateurs WHERE email = :email");
$stmt->execute(['email' => $_SESSION['utilisateur']]);
$currentUser = $stmt->fetch();

// Vérifier si l'utilisateur a la bonne spécialité et gerance (gérance 1 ou 2)
if (!in_array($currentUser['gerance'], [1, 2])) {
    header("Location: insubordination.php");
    exit();
}


$usersStmt = $pdo->prepare("SELECT id, nom FROM utilisateurs WHERE spe_id = :spe_id");
$usersStmt->execute(['spe_id' => $currentUser['spe_id']]);
$users = $usersStmt->fetchAll(PDO::FETCH_ASSOC);


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_utilisateur'])) {
    $id_utilisateur = $_POST['id_utilisateur'];

    if (isset($_POST['valider'])) {
        $formation = 'FS'; 
    } elseif (isset($_POST['rejeter'])) {
        $formation = 'FB'; 
    } elseif (isset($_POST['virer'])) {
        $formation = 'VIRER'; 
    }

    if (isset($formation)) {
        if ($formation === 'VIRER') {
            $updateStmt = $pdo->prepare("UPDATE utilisateurs SET spe_id = 9 WHERE id = ?");
            $updateStmt->execute([$id_utilisateur]);
            $message = "Utilisateur viré vers Fusilier.";
        } else {
            $formationStmt = $pdo->prepare("SELECT id FROM formation WHERE id_utilisateur = ?");
            $formationStmt->execute([$id_utilisateur]);
            $formationExists = $formationStmt->fetch();

            if ($formationExists) {
                $updateStmt = $pdo->prepare("UPDATE formation SET formation = ? WHERE id_utilisateur = ?");
                $updateStmt->execute([$formation, $id_utilisateur]);
                $message = $formation == 'FS' ? "Formation validée avec succès." : "Formation rejetée (FB).";
            } else {
                $insertStmt = $pdo->prepare("INSERT INTO formation (id_utilisateur, formation) VALUES (?, ?)");
                $insertStmt->execute([$id_utilisateur, $formation]);
                $message = $formation == 'FS' ? "Formation validée avec succès." : "Formation rejetée (FB).";
            }
        }
    }
}

$stmt = $pdo->prepare("
    SELECT ds.id, u.nom as utilisateur_nom, s.nom as spe_nom
    FROM demande_spe ds
    JOIN utilisateurs u ON ds.utilisateur_id = u.id
    JOIN spe s ON ds.spe_id = s.id
    WHERE ds.demande = 'Attente'
");
$stmt->execute();
$demandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<?php include 'header.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validation de Formation</title>
    <link rel="stylesheet" href="../css/med.css">
    
</head>
<body>

<div class="container">
    <h2>Validation de Formation Spécialisée (FS)</h2>

    <?php if (isset($message)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>


    <h3>Utilisateurs de la même spécialité</h3>
    <table>
        <thead>
            <tr>
                <th>Utilisateur</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['nom']); ?></td>
                    <td>
                        <form action="formation.php" method="post">
                            <input type="hidden" name="id_utilisateur" value="<?php echo $user['id']; ?>">
                            <button type="submit" name="valider" class="btn btn-primary">FS</button>
                            <button type="submit" name="rejeter" class="btn btn-danger">FB</button>
                            <button type="submit" name="virer" class="btn btn-warning">VIRER</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

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
                    <form action="formation.php" method="post">
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
