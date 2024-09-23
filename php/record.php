<?php
session_start();
include 'db.php'; 

$sqlMappeur = "SELECT u.nom, COUNT(c.id_mappeur) AS mappeur_count
               FROM utilisateurs u
               JOIN campagne c ON u.id = c.id_mappeur
               GROUP BY u.nom";

$sqlZeus = "SELECT u.nom, COUNT(c.id_zeus) AS zeus_count
            FROM utilisateurs u
            JOIN campagne c ON u.id = c.id_zeus
            GROUP BY u.nom";

$mappeurResults = $pdo->query($sqlMappeur);
$zeusResults = $pdo->query($sqlZeus);

$searchCampaign = isset($_GET['search_campaign']) ? trim($_GET['search_campaign']) : '';
$searchUser = isset($_GET['search_user']) ? trim($_GET['search_user']) : '';

$records = [];

while ($row = $mappeurResults->fetch(PDO::FETCH_ASSOC)) {
    $records[$row['nom']]['mappeur_count'] = $row['mappeur_count'];
}

while ($row = $zeusResults->fetch(PDO::FETCH_ASSOC)) {
    if (isset($records[$row['nom']])) {
        $records[$row['nom']]['zeus_count'] = $row['zeus_count'];
    } else {
        $records[$row['nom']]['zeus_count'] = $row['zeus_count'];
    }
}

foreach ($records as &$record) {
    if (!isset($record['mappeur_count'])) {
        $record['mappeur_count'] = 0;
    }
    if (!isset($record['zeus_count'])) {
        $record['zeus_count'] = 0;
    }
}

$params = [];
if ($searchCampaign) {
    $sql .= " AND c.nom LIKE ?";
    $params[] = '%' . $searchCampaign . '%';
}
if ($searchUser) {
    $sql .= " AND (u_mappeur.nom LIKE ? OR u_zeus.nom LIKE ?)";
    $params[] = '%' . $searchUser . '%';
    $params[] = '%' . $searchUser . '%';
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Record des Campagnes</title>
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

      a{
        position: absolute;
        right: 10px;
        top: 40px;
        }
        a.zeus{
        position: absolute;
        right: 90px;
        top: 40px;
        }
    </style>
</head>
<body>
 <form method="get" action="record.php">
  
    <label for="search_user">Rechercher par Nom de Mappeur ou Zeus :</label>
    <input type="text" id="search_user" name="search_user" value="<?php echo htmlspecialchars($searchUser); ?>">

    <button type="submit">Rechercher</button>
</form>
  
<h2>Participation </h2>

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
