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

// Récupérer les demandes de l'utilisateur
if ($faction) {
    $demandeStmt = $pdo->prepare("SELECT type_entretien, description, status, date_creation FROM demande_mechanicus WHERE id_utilisateur = ?");
    $demandeStmt->execute([$currentUser['id']]);
    $demandes = $demandeStmt->fetchAll(PDO::FETCH_ASSOC);
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
    <style>
        body{
            background-image: url('../src/assets/Bougie.png');
    background-repeat: no-repeat;
    background-position: center bottom;
    background-attachment: fixed;
        }
    </style>

<?php include 'headerm.php'; ?>

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
                    <option value="infrastructure">Infrastructure</option>
                    <option value="division">Demande Division</option>
                </select>

                <label for="request_description">Description de la demande :</label>
                <textarea id="request_description" name="request_description" rows="5" placeholder="Veuillez décrire en détail votre demande." required></textarea>

                <button type="submit" class="btn-request">Envoyer la demande</button>
            </form>
        </div>
        <!-- Affichage des demandes précédentes -->
        <?php if (!empty($demandes)): ?>
            <h2>Vos demandes précédentes</h2>
            <table border="1">
                <thead>
                    <tr>
                        <th>Type d'entretien</th>
                        <th>Description</th>
                        <th>Statut</th>
                        <th>Date de création</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($demandes as $demande): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($demande['type_entretien']); ?></td>
                            <td><?php echo htmlspecialchars($demande['description']); ?></td>
                            <td><?php echo htmlspecialchars($demande['status']); ?></td>
                            <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($demande['date_creation']))); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Aucune demande soumise pour l'instant.</p>
        <?php endif; ?>
    <?php endif; ?>
</div>

</body>
</html>
