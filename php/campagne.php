<?php
// Connect to the database
include 'db.php'; // Make sure this includes your database connection code

// Fetching the data from the "campagne" table with joined mappers and zeus info
$sql = "SELECT c.date, c.nom, c.missions, u_mappeur.nom AS mappeur, u_zeus.nom AS zeus1
        FROM campagne c
        LEFT JOIN utilisateurs u_mappeur ON c.id_mappeur = u_mappeur.id
        LEFT JOIN utilisateurs u_zeus ON c.id_zeus = u_zeus.id";
$result = $pdo->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau des Campagnes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
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

<h2>Tableau des Campagnes</h2>

<table>
    <thead>
        <tr>
            <th>DATES</th>
            <th>CAMPAGNES</th>
            <th>MISSIONS</th>
            <th>MAPPEURS</th>
            <th>ZEUS 1</th>
            <th>ZEUS 2</th>
            <th>ZEUS 3</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result && $result->rowCount() > 0) {
            // Affichage des données ligne par ligne
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['date']) . "</td>";
                echo "<td>" . htmlspecialchars($row['nom']) . "</td>";
                echo "<td>" . htmlspecialchars($row['missions']) . "</td>";
                echo "<td class='mappeur'>" . htmlspecialchars($row['mappeur']) . "</td>";
                echo "<td class='zeus'>" . htmlspecialchars($row['zeus1']) . "</td>";
                echo "<td class='zeus'></td>"; // Placeholder for Zeus 2
                echo "<td class='zeus'></td>"; // Placeholder for Zeus 3
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='7'>Aucune campagne trouvée</td></tr>";
        }
        ?>
    </tbody>
</table>

<!-- Form for creating a new campaign -->
<h2>Créer une Nouvelle Campagne</h2>
<form action="create_c.php" method="post">
    <label for="date">Date:</label>
    <input type="date" id="date" name="date" required><br><br>

    <label for="nom">Nom de la campagne:</label>
    <input type="text" id="nom" name="nom" required><br><br>

    <label for="missions">Nombre de missions:</label>
    <input type="number" id="missions" name="missions" required><br><br>

    <label for="mappeur">Mappeur (ID utilisateur):</label>
    <input type="number" id="mappeur" name="mappeur"><br><br>

    <label for="zeus">Zeus 1 (ID utilisateur):</label>
    <input type="number" id="zeus" name="zeus"><br><br>

    <button type="submit">Créer la campagne</button>
</form>

</body>
</html>
