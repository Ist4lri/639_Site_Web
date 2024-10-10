<?php
session_start();
include 'db.php'; 

// Requête pour compter les occurrences en tant que mappeur
$sqlMappeur = "SELECT u.nom, COUNT(c.id_mappeur) AS mappeur_count
               FROM utilisateurs u
               LEFT JOIN campagne c ON u.id = c.id_mappeur
               GROUP BY u.nom";

// Requête pour compter les occurrences en tant que zeus
$sqlZeus = "SELECT u.nom, 
                   COUNT(c.id_zeus) AS zeus_count, 
                   COUNT(c.id_zeus2) AS zeus2_count, 
                   COUNT(c.id_zeus3) AS zeus3_count
            FROM utilisateurs u
            LEFT JOIN campagne c 
            ON u.id = c.id_zeus OR u.id = c.id_zeus2 OR u.id = c.id_zeus3
            GROUP BY u.nom";

$mappeurResults = $pdo->query($sqlMappeur);
$zeusResults = $pdo->query($sqlZeus);

$searchCampaign = isset($_GET['search_campaign']) ? trim($_GET['search_campaign']) : '';
$searchUser = isset($_GET['search_user']) ? trim($_GET['search_user']) : '';

// Initialisation des enregistrements
$records = [];

// Remplir les enregistrements avec les résultats mappeur
while ($row = $mappeurResults->fetch(PDO::FETCH_ASSOC)) {
    $records[$row['nom']]['mappeur_count'] = $row['mappeur_count'];
}

// Remplir les enregistrements avec les résultats zeus
while ($row = $zeusResults->fetch(PDO::FETCH_ASSOC)) {
    if (isset($records[$row['nom']])) {
        $records[$row['nom']]['zeus_count'] = $row['zeus_count'];
    } else {
        $records[$row['nom']]['zeus_count'] = $row['zeus_count'];
    }
}

// Initialiser les valeurs manquantes à 0
foreach ($records as &$record) {
    if (!isset($record['mappeur_count'])) {
        $record['mappeur_count'] = 0;
    }
    if (!isset($record['zeus_count'])) {
        $record['zeus_count'] = 0;
    }
}

// Filtrage par utilisateur ou campagne si des critères de recherche sont fournis
if ($searchUser) {
    $records = array_filter($records, function ($key) use ($searchUser) {
        return stripos($key, $searchUser) !== false;
    }, ARRAY_FILTER_USE_KEY);
}

// Garder uniquement les utilisateurs ayant mappeur_count > 0 ou zeus_count > 0
$records = array_filter($records, function ($record) {
    return $record['mappeur_count'] > 0 || $record['zeus_count'] > 0;
});
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Record des Campagnes</title>
  <link rel="icon" type="image/x-icon" href="../src/assets/Logo_639th_2.ico">
    <style>
       body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
        }

        .top-links {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        .top-links a {
            margin-left: 20px;
            color: #ff8800;
            text-decoration: none;
            font-weight: bold;
        }

        form {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .form-group {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .form-group label {
            font-weight: bold;
            margin-right: 10px;
            color: #555;
        }

        .form-group input, .form-group select {
            width: calc(50% - 10px);
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .zeus-group {
            display: flex;
            justify-content: space-between;
        }

        .zeus-group select {
            width: calc(33% - 10px);
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        button[type="submit"] {
            background-color: #28a745;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 1em;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button[type="submit"]:hover {
            background-color: #218838;
        }

        h2 {
            color: #333;
            font-size: 1.5em;
            margin-bottom: 20px;
            text-align: left;
        }

        table {
            width: 95%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,td {
            border: 1px solid black;
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: green;
            color: white;
        }

        .mappeur {
            color: blue;
        }

    </style>
</head>
<body>
<div class="top-links">
  <a href="campagne.php" class="zeus"> Campagne </a>
  <a href="../index.php"> Acceuil </a>
</div>

<form method="get" action="record.php">
    <label for="search_user">Rechercher par Nom de Mappeur ou Zeus :</label>
    <input type="text" id="search_user" name="search_user" value="<?php echo htmlspecialchars($searchUser); ?>">
    <button type="submit">Rechercher</button>
</form>

<h2>Participation</h2>

<table>
    <thead>
        <tr>
            <th>Nom</th>
            <th>Nombre de fois Mappeur</th>
            <th>Nombre de fois Zeus</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if (!empty($records)) {
            foreach ($records as $nom => $data) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($nom) . "</td>";
                echo "<td>" . htmlspecialchars($data['mappeur_count']) . "</td>";
                echo "<td>" . htmlspecialchars($data['zeus_count']) . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='3'>Aucun enregistrement trouvé.</td></tr>";
        }
        ?>
    </tbody>
</table>

</body>
</html>
