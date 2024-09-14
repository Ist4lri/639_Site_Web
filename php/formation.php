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

// Récupérer les utilisateurs de la même spécialité (spe_id)
$usersStmt = $pdo->prepare("SELECT id, nom FROM utilisateurs WHERE spe_id = :spe_id");
$usersStmt->execute(['spe_id' => $currentUser['spe_id']]);
$users = $usersStmt->fetchAll(PDO::FETCH_ASSOC);

// Si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['id_utilisateur']) && !empty($_POST['id_utilisateur'])) {
        $id_utilisateur = $_POST['id_utilisateur'];

        // Si "valider" est cliqué, la formation est FS, sinon si "rejeter" est cliqué, elle devient FB
        if (isset($_POST['valider'])) {
            $formation = 'FS'; // La formation validée
        } elseif (isset($_POST['rejeter'])) {
            $formation = 'FB'; // La formation rejetée
        }

        if (isset($formation)) {
            // Vérifier si l'utilisateur a déjà une formation
            $formationStmt = $pdo->prepare("SELECT id FROM formation WHERE id_utilisateur = ?");
            $formationStmt->execute([$id_utilisateur]);
            $formationExists = $formationStmt->fetch();

            if ($formationExists) {
                // Si une formation existe, la mettre à jour
                $updateStmt = $pdo->prepare("UPDATE formation SET formation = ? WHERE id_utilisateur = ?");
                $updateStmt->execute([$formation, $id_utilisateur]);
                $message = $formation == 'FS' ? "Formation validée avec succès." : "Formation rejetée (FB).";
            } else {
                // Si aucune formation n'existe, en créer une nouvelle
                $insertStmt = $pdo->prepare("INSERT INTO formation (id_utilisateur, formation) VALUES (?, ?)");
                $insertStmt->execute([$id_utilisateur, $formation]);
                $message = $formation == 'FS' ? "Formation validée avec succès." : "Formation rejetée (FB).";
            }
        }
    } else {
        $message = "Erreur : utilisateur non sélectionné.";
    }
}

// Récupérer les demandes de spécialité en attente
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
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['accept']) && isset($_POST['demande_id'])) {
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
?>

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

    <form action="formation.php" method="post">
        <label for="id_utilisateur">Choisir un utilisateur :</label>
        <select name="id_utilisateur" id="id_utilisateur" required>
            <option value="">Sélectionnez un utilisateur</option>
            <?php foreach ($users as $user): ?>
                <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['nom']); ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit" name="valider" class="btn btn-primary">Valider la Formation Spécialisée (FS)</button>
        <button type="submit" name="rejeter" class="btn btn-danger">Rejeter la Formation (FB)</button>
    </form>
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
