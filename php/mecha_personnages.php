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

// Récupérer le personnage Adeptus Mechanicus de l'utilisateur avec gérance = 1 et validation = 'Accepter'
$userPersonnageStmt = $pdo->prepare("SELECT id FROM personnages WHERE id_utilisateur = :id_utilisateur AND faction = 'Adeptus Mechanicus' AND validation = 'Accepter' AND gerance = 1");
$userPersonnageStmt->execute(['id_utilisateur' => $currentUser['id']]);
$userPersonnage = $userPersonnageStmt->fetch();

// Si un formulaire a été soumis pour changer le grade_mecha ou rejeter le personnage
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['personnage_id'])) {
    $newGradeMecha = $_POST['grade_mecha'];
    $personnageId = $_POST['personnage_id'];

    // Mettre à jour le grade_mecha
    $updateStmt = $pdo->prepare("UPDATE personnages SET grade_mecha = :grade_mecha WHERE id = :id");
    $updateStmt->execute(['grade_mecha' => $newGradeMecha, 'id' => $personnageId]);

    // Si l'utilisateur veut rejeter le personnage
    if (isset($_POST['reject'])) {
        $rejectStmt = $pdo->prepare("UPDATE personnages SET validation = 'Rejeter' WHERE id = :id");
        $rejectStmt->execute(['id' => $personnageId]);
    }

    header("Location: mecha_personnages.php");
    exit();
}

// Récupérer tous les personnages de la faction Adeptus Mechanicus
$mechaStmt = $pdo->prepare("SELECT id, nom, grade_mecha FROM personnages WHERE faction = 'Adeptus Mechanicus' AND validation = 'Accepter'");
$mechaStmt->execute();
$mechaPersonnages = $mechaStmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personnages Adeptus Mechanicus</title>
    <style>
        body {
            background-color: black;
            color: lime;
            background-image: url('../src/assets/Bougie.png');
            background-repeat: no-repeat;
            background-position: center bottom;
            background-attachment: fixed;
        }
        h1 {
            letter-spacing: 4px;
            margin-top: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            font-family: 'Courier New', Courier, monospace;
            border: 1px solid lime;
            padding: 10px;
            text-align: left;
            font-size: 18px;
            letter-spacing: 2px;
        }
        th {
            background-color: lime;
            color: black;
        }
        .action-form {
            margin-top: 20px;
            background-color: black;
            border: 1px solid lime;
            padding: 10px;
        }
        .submit-button {
            background-color: lime;
            color: black;
            border: none;
            padding: 10px;
            cursor: pointer;
        }
    </style>
    <?php include 'headerm.php'; ?>
</head>
<body>

    <h1>Membres de Adeptus Mechanicus</h1>

    <table>
        <tr>
            <th>Nom</th>
            <th>Grade Mecha</th>
            <?php if ($userPersonnage): ?>
            <th>Actions</th>
            <?php endif; ?>
        </tr>
        <?php foreach ($mechaPersonnages as $personnage): ?>
        <tr>
            <td><?php echo htmlspecialchars($personnage['nom']); ?></td>
            <td><?php echo htmlspecialchars($personnage['grade_mecha'] ? $personnage['grade_mecha'] : 'Aucun'); ?></td>
            <?php if ($userPersonnage): ?>
            <td>
                <form method="post" action="mecha_personnages.php" class="action-form">
                    <input type="hidden" name="personnage_id" value="<?php echo $personnage['id']; ?>">
                    <label for="grade_mecha">Changer Grade Mecha:</label>
                    <select name="grade_mecha" required>
                        <option value="TechnoPrêtre">TechnoPrêtre</option>
                        <option value="Magos">Magos</option>
                        <option value="Servitor">Servitor</option>
                    </select>
                    <br>
                    <button type="submit" class="submit-button">Mettre à jour</button>
                    <br><br>
                    <button type="submit" name="reject" class="submit-button" style="background-color: red; color: white;">Rejeter Personnage</button>
                </form>
            </td>
            <?php endif; ?>
        </tr>
        <?php endforeach; ?>
    </table>

</body>
</html>
