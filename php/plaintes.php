<?php
session_start();
include 'db.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['utilisateur'])) {
    header("Location: connection.php");
    exit();
}

// Récupération de l'utilisateur actuel
$stmt = $pdo->prepare("SELECT id, nom FROM utilisateurs WHERE email = :email");
$stmt->execute(['email' => $_SESSION['utilisateur']]);
$currentUser = $stmt->fetch();

if (!$currentUser) {
    echo "Utilisateur non trouvé.";
    exit();
}

$message = '';

// Gestion de la soumission du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['plainte']) && !empty($_POST['plainte'])) {
    $plainteText = trim($_POST['plainte']);

    if (!empty($plainteText)) {
        $stmt = $pdo->prepare("INSERT INTO plaintes (id_utilisateur, plainte, status) VALUES (?, ?, 'Attente')");
        $stmt->execute([$currentUser['id'], $plainteText]);
        $message = "Votre plainte a été soumise avec succès.";
        // Redirection pour éviter le renvoi de la plainte lors du rafraîchissement de la page
        header("Location: plaintes.php");
        exit();
    } else {
        $message = "La plainte ne peut pas être vide.";
    }
}

// Récupération des plaintes soumises par l'utilisateur
$stmt = $pdo->prepare("SELECT plainte, status, date_creation FROM plaintes WHERE id_utilisateur = :id_utilisateur ORDER BY date_creation DESC");
$stmt->execute(['id_utilisateur' => $currentUser['id']]);
$plaintes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Plaintes</title>
    <link rel="stylesheet" href="../css/med.css">
</head>
<body>

<div class="container">
    <h2>Soumettre une plainte</h2>

    <?php if (!empty($message)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form action="plaintes.php" method="post">
        <label for="plainte">Votre plainte :</label>
        <textarea id="plainte" name="plainte" rows="4" style="width: 100%;" required></textarea>
        <br>
        <button type="submit" class="btn btn-primary">Envoyer la plainte</button>
    </form>

    <h3>Vos plaintes</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Plainte</th>
                <th>Status</th>
                <th>Date de soumission</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($plaintes)): ?>
                <tr>
                    <td colspan="3">Aucune plainte soumise pour le moment.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($plaintes as $plainte): ?>
                <tr>
                    <td><?php echo htmlspecialchars($plainte['plainte']); ?></td>
                    <td><?php echo htmlspecialchars($plainte['status']); ?></td>
                    <td><?php echo htmlspecialchars($plainte['date_creation']); ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
