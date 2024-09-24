<?php
// Activer l'affichage des erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupérer les données du formulaire
    $date = $_POST['date'];
    $nom = $_POST['nom'];
    $missions = $_POST['missions'];
    $id_mappeur = $_POST['mappeur'];
    $id_zeus1 = $_POST['zeus1'];
    $id_zeus2 = $_POST['zeus2'];
    $id_zeus3 = $_POST['zeus3'];


    
    try {
        // Requête d'insertion dans la table campagne
        $stmt = $pdo->prepare("INSERT INTO campagne (date, nom, missions, id_mappeur, id_zeus, id_zeus2, id_zeus3) 
                               VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$date, $nom, $missions, $id_mappeur, $id_zeus1, $id_zeus2, $id_zeus3]);

        // Redirection ou message de confirmation
        header('Location: campagne.php');
        exit();
    } catch (PDOException $e) {
        // Afficher l'erreur SQL pour débogage
        echo "Erreur SQL : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer une Nouvelle Campagne</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        a {
            position: absolute;
            right: 10px;
            top: 40px;
        }

        a.zeus {
            position: absolute;
            right: 90px;
            top: 40px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid black;
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: green;
            color: white;
        }

        td {
            height: 40px;
        }

        .zeus {
            background-color: orange;
        }

        .mappeur {
            color: blue;
        }
    </style>
</head>
<body>
<a href="record.php" class="zeus">Compteur</a>
<a href="../index.php">Accueil</a>

<h2>Créer une Nouvelle Campagne</h2>
<form action="create_c.php" method="post">
    <label for="date">Date:</label>
    <input type="date" id="date" name="date" required><br><br>

    <label for="nom">Nom de la campagne:</label>
    <input type="text" id="nom" name="nom" required><br><br>

    <label for="missions">Nombre de missions:</label>
    <input type="number" id="missions" name="missions" required><br><br>

    <label for="mappeur">Mappeur:</label>
    <select id="mappeur" name="mappeur" required>
        <option value="">Sélectionnez un mappeur</option>
        <?php
        $mappeur_query = "SELECT id, nom FROM utilisateurs WHERE mappeur = 1";
        $mappeur_result = $pdo->query($mappeur_query);
        if ($mappeur_result->rowCount() > 0) {
            while ($row = $mappeur_result->fetch(PDO::FETCH_ASSOC)) {
                echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['nom']) . "</option>";
            }
        }
        ?>
    </select><br><br>

    <label for="zeus1">Zeus 1:</label>
    <select id="zeus1" name="zeus1" required>
        <option value="">Sélectionnez un Zeus</option>
        <?php
        $zeus_query = "SELECT id, nom FROM utilisateurs WHERE zeus = 1";
        $zeus_result = $pdo->query($zeus_query);
        if ($zeus_result->rowCount() > 0) {
            while ($row = $zeus_result->fetch(PDO::FETCH_ASSOC)) {
                echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['nom']) . "</option>";
            }
        }
        ?>
    </select><br><br>

    <label for="zeus2">Zeus 2:</label>
    <select id="zeus2" name="zeus2">
        <option value="">Sélectionnez un Zeus (facultatif)</option>
        <?php
        $zeus_result->execute(); // Réexécuter la requête pour zeus2 et zeus3
        if ($zeus_result->rowCount() > 0) {
            while ($row = $zeus_result->fetch(PDO::FETCH_ASSOC)) {
                echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['nom']) . "</option>";
            }
        }
        ?>
    </select><br><br>

    <label for="zeus3">Zeus 3:</label>
    <select id="zeus3" name="zeus3">
        <option value="">Sélectionnez un Zeus (facultatif)</option>
        <?php
        $zeus_result->execute(); // Réexécuter la requête pour zeus3
        if ($zeus_result->rowCount() > 0) {
            while ($row = $zeus_result->fetch(PDO::FETCH_ASSOC)) {
                echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['nom']) . "</option>";
            }
        }
        ?>
    </select><br><br>

    <button type="submit">Créer la campagne</button>
</form>
</body>
</html>
