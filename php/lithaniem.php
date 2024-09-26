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

// Si un formulaire a été soumis pour ajouter une lithanie
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $faction) {
    $newLithanie = $_POST['lithanie'];
    $insertStmt = $pdo->prepare("INSERT INTO lithaniem (text) VALUES (:text)");
    $insertStmt->execute(['text' => $newLithanie]);
    header("Location: lithaniem.php");
    exit();
}

// Récupérer et afficher les litanies
$lithaniesStmt = $pdo->query("SELECT * FROM lithaniem");
$lithanies = $lithaniesStmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Litanies</title>
    <style>
        body {
            background-color: black;
            color: lime;
            font-family: 'Courier New', Courier, monospace;
            background-image: url('../src/assets/Bougie.png');
    background-repeat: no-repeat;
    background-position: center bottom;
    background-attachment: fixed;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td {
            border: 1px solid lime;
            padding: 10px;
        }
        .terminal-form {
            background-color: black;
            color: lime;
            padding: 20px;
            border: 2px solid lime;
            width: 50%;
            margin: 20px auto;
            text-align: center;
        }
        .terminal-input {
            background-color: black;
            color: lime;
            border: 1px solid lime;
            padding: 10px;
            width: 80%;
            margin-bottom: 10px;
        }
        .terminal-button {
            background-color: black;
            color: lime;
            border: 2px solid lime;
            padding: 10px;
            cursor: pointer;
        }
        .terminal-button:hover {
            background-color: lime;
            color: black;
        }
    </style>
</head>
<body>

    <h1>Litanies</h1>

    <table>
        <?php foreach ($lithanies as $lithanie): ?>
        <tr>
            <td><?php echo htmlspecialchars($lithanie['text']); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <?php if ($faction): ?>
    <div class="terminal-form">
        <h2>Ajouter une nouvelle lithanie</h2>
        <form method="post" action="lithaniem.php">
            <textarea class="terminal-input" name="lithanie" rows="5" placeholder="Écris ta lithanie ici..." required></textarea>
            <br>
            <button class="terminal-button" type="submit">Envoyer</button>
        </form>
    </div>
    <?php endif; ?>

</body>
</html>
