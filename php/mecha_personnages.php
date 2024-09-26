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

// Récupérer tous les personnages de la faction Adeptus Mechanicus
$mechaStmt = $pdo->prepare("SELECT nom, grade_mecha FROM personnages WHERE faction = 'Adeptus Mechanicus' AND validation = 'Accepter'");
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
      h1{
         letter-spacing: 4px;
        margin-top: 130px;
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
    </style>
  <?php include 'headerm.php'; ?>
</head>
<body>

    <h1>Membres de Adeptus Mechanicus</h1>

    <table>
        <tr>
            <th>Nom</th>
            <th>Grade Mecha</th>
        </tr>
        <?php foreach ($mechaPersonnages as $personnage): ?>
        <tr>
            <td><?php echo htmlspecialchars($personnage['nom']); ?></td>
            <td><?php echo htmlspecialchars($personnage['grade_mecha'] ? $personnage['grade_mecha'] : 'Aucun'); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

</body>
</html>
