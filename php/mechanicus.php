<?php
session_start();
include 'db.php';

if (!isset($_SESSION['utilisateur'])) {
    header("Location: connection.php");
    exit();
}

// Récupérer l'utilisateur et vérifier s'il est dans la faction Adeptus Mechanicus
$stmt = $pdo->prepare("SELECT id, nom FROM utilisateurs WHERE email = :email");
$stmt->execute(['email' => $_SESSION['utilisateur']]);
$currentUser = $stmt->fetch();

$factionStmt = $pdo->prepare("SELECT * FROM personnages WHERE id_utilisateur = :id_utilisateur AND faction = 'Adeptus Mechanicus' AND validation = 'Accepter'");
$factionStmt->execute(['id_utilisateur' => $currentUser['id']]);
$faction = $factionStmt->fetch();

$message = '';

// Traitement du formulaire de demande
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['request_type'])) {
    $requestType = $_POST['request_type'];

    // Insérer la demande dans la table demande_mechanicus
    $insertStmt = $pdo->prepare("INSERT INTO demande_mechanicus (id_utilisateur, type_entretien, description) VALUES (?, ?, ?)");
    $insertStmt->execute([$currentUser['id'], $requestType, 'Demande envoyée pour entretien ' . $requestType]);

    $message = "Votre demande a été soumise avec succès pour un entretien $requestType.";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adeptus Mechanicus</title>
    <link rel="stylesheet" href="../css/adeptus.css">
</head>
<body>

<?php if ($faction): ?>
    <div class="container cogitator">
        <h1>Bienvenue, membre de l'Adeptus Mechanicus</h1>

        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
       

        <div class="quotes">
            <p class="quote">"On dit qu'il est impossible pour un homme de devenir comme la Machine. Mais seul le plus petit des esprits s'efforce de comprendre ses limites."</p>
            <p class="quote">"Seule la chair vacille, la Machine, elle, n'est jamais corrompue."</p>
            <p class="quote">"La connaissance est le pouvoir, et le pouvoir est dangereux."</p>
            <p class="quote">"Et comme les armes bénies par l'Omnimessie vous servent, vous les servirez. Préservez-les de la honte de la défaite."</p>
        </div>
    </div>
<?php else: ?>
     <div class="actions">
            <h2>Demander un entretien spécial</h2>
            <form action="demande_mechanicus.php" method="post">
                <label for="request_type">Type d'entretien :</label>
                <select id="request_type" name="request_type" required>
                    <option value="arsenal">Arsenal</option>
                    <option value="medical">Médical</option>
                </select>
                <button type="submit" class="btn-request">Envoyer la demande</button>
            </form>
        </div>
<?php endif; ?>

</body>
</html>
