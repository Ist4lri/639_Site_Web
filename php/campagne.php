<?php
include 'db.php';

$query = "SELECT zeus, mappeur FROM utilisateurs WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$sql = "SELECT c.date, c.nom, c.missions, u_mappeur.nom AS mappeur, u_zeus1.nom AS zeus1, u_zeus2.nom AS zeus2, u_zeus3.nom AS zeus3
        FROM campagne c
        LEFT JOIN utilisateurs u_mappeur ON c.id_mappeur = u_mappeur.id
        LEFT JOIN utilisateurs u_zeus1 ON c.id_zeus = u_zeus1.id
        LEFT JOIN utilisateurs u_zeus2 ON c.id_zeus2 = u_zeus2.id
        LEFT JOIN utilisateurs u_zeus3 ON c.id_zeus3 = u_zeus3.id";

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if ($row['zeus'] != 1 && $row['mappeur'] != 1) {
        header("Location: /access_denied.php");
        exit();
    }
} else {
    header("Location: insubordination.php");
    exit();
}

$mappeur_query = "SELECT id, nom FROM utilisateurs WHERE mappeur = 1";
$mappeur_result = $conn->query($mappeur_query);

$zeus_query = "SELECT id, nom FROM utilisateurs WHERE zeus = 1 OR mappeur = 1";
$zeus_result = $conn->query($zeus_query);
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
        $sql = "SELECT c.date, c.nom, c.missions, u_mappeur.nom AS mappeur, u_zeus.nom AS zeus1
                FROM campagne c
                LEFT JOIN utilisateurs u_mappeur ON c.id_mappeur = u_mappeur.id
                LEFT JOIN utilisateurs u_zeus ON c.id_zeus = u_zeus.id";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['date']) . "</td>";
        echo "<td>" . htmlspecialchars($row['nom']) . "</td>";
        echo "<td>" . htmlspecialchars($row['missions']) . "</td>";
        echo "<td class='mappeur'>" . htmlspecialchars($row['mappeur']) . "</td>";
        echo "<td class='zeus'>" . htmlspecialchars($row['zeus1']) . "</td>";
        echo "<td class='zeus'>" . htmlspecialchars($row['zeus2']) . "</td>"; // Zeus 2
        echo "<td class='zeus'>" . htmlspecialchars($row['zeus3']) . "</td>"; // Zeus 3
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

    <label for="mappeur">Mappeur:</label>
    <select id="mappeur" name="mappeur" required>
        <option value="">Sélectionnez un mappeur</option>
        <?php
        if ($mappeur_result && $mappeur_result->num_rows > 0) {
            while ($row = $mappeur_result->fetch_assoc()) {
                echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['nom']) . "</option>";
            }
        }
        ?>
    </select><br><br>

    <label for="zeus1">Zeus 1:</label>
    <select id="zeus1" name="zeus1" required>
        <option value="">Sélectionnez un Zeus</option>
        <?php
        if ($zeus_result && $zeus_result->num_rows > 0) {
            while ($row = $zeus_result->fetch_assoc()) {
                echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['nom']) . "</option>";
            }
        }
        ?>
    </select><br><br>

    <label for="zeus2">Zeus 2:</label>
    <select id="zeus2" name="zeus2">
        <option value="">Sélectionnez un Zeus</option>
        <?php
        if ($zeus_result && $zeus_result->num_rows > 0) {
            $zeus_result->data_seek(0);
            while ($row = $zeus_result->fetch_assoc()) {
                echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['nom']) . "</option>";
            }
        }
        ?>
    </select><br><br>

    <label for="zeus3">Zeus 3:</label>
    <select id="zeus3" name="zeus3">
        <option value="">Sélectionnez un Zeus</option>
        <?php
        if ($zeus_result && $zeus_result->num_rows > 0) {
            $zeus_result->data_seek(0);
            while ($row = $zeus_result->fetch_assoc()) {
                echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['nom']) . "</option>";
            }
        }
        ?>
    </select><br><br>

    <button type="submit">Créer la campagne</button>
</form>

</body>
</html>

<?php
$conn->close();
?>
