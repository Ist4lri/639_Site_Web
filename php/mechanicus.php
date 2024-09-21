<?php
session_start();
include 'db.php';

if (!isset($_SESSION['utilisateur'])) {
    header("Location: connection.php");
    exit();
}

// Récupérer l'utilisateur actuel
$stmt = $pdo->prepare("SELECT id, nom FROM utilisateurs WHERE email = :email");
$stmt->execute(['email' => $_SESSION['utilisateur']]);
$currentUser = $stmt->fetch();

// Vérifier si l'utilisateur fait partie de la faction Adeptus Mechanicus
$factionStmt = $pdo->prepare("SELECT * FROM personnages WHERE id_utilisateur = :id_utilisateur AND faction = 'Adeptus Mechanicus' AND validation = 'Accepter'");
$factionStmt->execute(['id_utilisateur' => $currentUser['id']]);
$faction = $factionStmt->fetch();

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['request_type'], $_POST['request_description'])) {
    $requestType = $_POST['request_type'];
    $description = trim($_POST['request_description']);  // Récupérer la description

    // Insérer la demande avec la description fournie par l'utilisateur
    $insertStmt = $pdo->prepare("INSERT INTO demande_mechanicus (id_utilisateur, type_entretien, description) VALUES (?, ?, ?)");
    $insertStmt->execute([$currentUser['id'], $requestType, $description]);

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

<!-- Header avec condition Adeptus Mechanicus ou non -->
<header>
    <div class="head-logo2">
        <a href="../index.php">
            <img src="../src/assets/TitreSite.png" alt="639 Régiment cadien">
        </a>
    </div>

    <nav class="head-nav">
        <?php if ($faction): ?>
            <!-- Si l'utilisateur fait partie de l'Adeptus Mechanicus -->
            <a href="demande_mechanicus.php">Demandes</a>
        <?php else: ?>
            <!-- Si l'utilisateur n'est pas dans l'Adeptus Mechanicus -->
            <a href="profil_utilisateur.php">Profil</a>
            <a href="Dec.php">Déconnexion</a>
        <?php endif; ?>
    </nav>
</header>

<div class="container">
    <?php if ($faction): ?>
        <h1>Bienvenue, membre de l'Adeptus Mechanicus</h1>

        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <div class="quotes">
            <p class="quote quote-1">"On dit qu'il est impossible pour un homme de devenir comme la Machine. Mais seul le plus petit des esprits s'efforce de comprendre ses limites."</p>
            <p class="quote quote-2">"Seule la chair vacille, la Machine, elle, n'est jamais corrompue."</p>
            <p class="quote quote-3">"La connaissance est le pouvoir, et le pouvoir est dangereux."</p>
            <p class="quote quote-4">"Et comme les armes bénies par l'Omnimessie vous servent, vous les servirez. Préservez-les de la honte de la défaite."</p>
        </div>
    <?php else: ?>
        <div class="actions">
            <h2>Demander un entretien spécial</h2>
            <form action="mechanicus.php" method="post">
                <label for="request_type">Type d'entretien :</label>
                <select id="request_type" name="request_type" required>
                    <option value="arsenal">Arsenal</option>
                    <option value="medical">Médical</option>
                </select>

                <label for="request_description">Description de la demande :</label>
                <textarea id="request_description" name="request_description" rows="5" placeholder="Veuillez décrire en détail votre demande." required></textarea>

                <button type="submit" class="btn-request">Envoyer la demande</button>
            </form>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
