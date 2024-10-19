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

// Si le bouton "Accepter" ou "Rejeter" est pressé pour une demande de spécialité
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['demande_id'])) {
    $demande_id = $_POST['demande_id'];

    // Récupérer l'utilisateur de la demande
    $getUserStmt = $pdo->prepare("SELECT utilisateur_id FROM demande_spe WHERE id = ?");
    $getUserStmt->execute([$demande_id]);
    $userFromDemand = $getUserStmt->fetch();

    if ($userFromDemand) {
        // Récupérer l'utilisateur actuel (doit être fait après la soumission du formulaire)
        $stmt = $pdo->prepare("SELECT id, spe_id, gerance FROM utilisateurs WHERE email = :email");
        $stmt->execute(['email' => $_SESSION['utilisateur']]);
        $currentUser = $stmt->fetch();

        if (!$currentUser) {
            echo "Erreur : utilisateur non trouvé.";
            exit();
        }

        // Si le bouton "Accepter" est appuyé
        if (isset($_POST['accept'])) {
            // Mettre à jour la demande pour indiquer qu'elle est acceptée
            $updateDemandStmt = $pdo->prepare("UPDATE demande_spe SET demande = 'Accepter' WHERE id = ?");
            $updateDemandStmt->execute([$demande_id]);

            $updateFormationStmt = $pdo->prepare("UPDATE formation SET formation = 'FB' WHERE id_utilisateur = ?");
            $updateFormationStmt->execute([$userFromDemand['utilisateur_id']]);

            // Mettre à jour l'utilisateur pour changer la spécialité
            $updateUserSpeStmt = $pdo->prepare("UPDATE utilisateurs SET spe_id = ? WHERE id = ?");
            $updateUserSpeStmt->execute([$currentUser['spe_id'], $userFromDemand['utilisateur_id']]);

            $message = "Demande acceptée avec succès et spécialité mise à jour.";

        // Si le bouton "Rejeter" est appuyé
        } elseif (isset($_POST['reject'])) {
            // Mettre à jour la demande pour indiquer qu'elle est rejetée
            $updateDemandStmt = $pdo->prepare("UPDATE demande_spe SET demande = 'Rejeter' WHERE id = ?");
            $updateDemandStmt->execute([$demande_id]);
           
            $updateFormationStmt = $pdo->prepare("UPDATE formation SET formation = 'FB' WHERE id_utilisateur = ?");
            $updateFormationStmt->execute([$userFromDemand['utilisateur_id']]);

            // Mettre à jour l'utilisateur avec formation = 'FB' et spe_id = 9
            $updateUserSpeStmt = $pdo->prepare("UPDATE utilisateurs SET  spe_id = 9 WHERE id = ?");
            $updateUserSpeStmt->execute([$userFromDemand['utilisateur_id']]);

            $message = "Demande rejetée avec succès et l'utilisateur a été transféré vers la spécialité 'Fusilier' (spe_id = 9).";
        }

    } else {
        $message = "Erreur lors de la récupération de l'utilisateur de la demande.";
    }
}


// Récupérer l'utilisateur actuel
$stmt = $pdo->prepare("SELECT id, spe_id, gerance FROM utilisateurs WHERE email = :email");
$stmt->execute(['email' => $_SESSION['utilisateur']]);
$currentUser = $stmt->fetch();

// Vérifier si l'utilisateur a la bonne spécialité et gérance (gérance 1 ou 2)
if (!in_array($currentUser['gerance'], [1, 2])) {
    header("Location: insubordination.php");
    exit();
}

// Récupérer les utilisateurs ayant la même spécialité et leur formation (FS ou FB)
$usersStmt = $pdo->prepare("
    SELECT u.id, u.nom, f.formation
    FROM utilisateurs u
    LEFT JOIN formation f ON u.id = f.id_utilisateur
    WHERE u.spe_id = :spe_id
");
$usersStmt->execute(['spe_id' => $currentUser['spe_id']]);
$users = $usersStmt->fetchAll(PDO::FETCH_ASSOC);

// Si une demande est soumise (formation ou rejet)
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
            $updateStmt = $pdo->prepare("UPDATE utilisateurs SET spe_id = 9, gerance = 0 WHERE id = ?");
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


// Récupérer les demandes de spécialité de la même spécialité que l'utilisateur
$stmt = $pdo->prepare("
    SELECT ds.id, u.nom as utilisateur_nom, s.nom as spe_nom
    FROM demande_spe ds
    JOIN utilisateurs u ON ds.utilisateur_id = u.id
    JOIN spe s ON ds.spe_id = s.id
    WHERE ds.demande = 'Attente' AND ds.spe_id = :spe_id
");
$stmt->execute(['spe_id' => $currentUser['spe_id']]);
$demandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<?php include 'header.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validation de Formation</title>
    <link rel="icon" type="image/x-icon" href="../src/assets/Logo_639th_2.ico">
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
                <th>Formation</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['nom']); ?></td>
                    <td><?php echo htmlspecialchars($user['formation'] ?? 'Aucune'); ?></td>
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
                        <button type="submit" name="accept" class="btn btn-success">Accepter</button>
                        <button type="submit" name="reject" class="btn btn-reject">Rejeter</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
