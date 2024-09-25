<?php
session_start();
include 'db.php'; 

$stmt = $pdo->prepare("SELECT id, nom FROM utilisateurs WHERE email = :email");
$stmt->execute(['email' => $_SESSION['utilisateur']]);
$currentUser = $stmt->fetch(PDO::FETCH_ASSOC);

$searchCampaign = isset($_GET['search_campaign']) ? trim($_GET['search_campaign']) : '';
$searchUser = isset($_GET['search_user']) ? trim($_GET['search_user']) : '';

$message = '';

$sql = "SELECT c.date, c.nom, c.missions, 
               u_mappeur.nom AS mappeur, 
               u_zeus1.nom AS zeus1, 
               u_zeus2.nom AS zeus2, 
               u_zeus3.nom AS zeus3
        FROM campagne c
        LEFT JOIN utilisateurs u_mappeur ON c.id_mappeur = u_mappeur.id
        LEFT JOIN utilisateurs u_zeus1 ON c.id_zeus = u_zeus1.id
        LEFT JOIN utilisateurs u_zeus2 ON c.id_zeus2 = u_zeus2.id
        LEFT JOIN utilisateurs u_zeus3 ON c.id_zeus3 = u_zeus3.id
        WHERE 1 = 1";

$params = [];
if ($searchCampaign) {
    $sql .= " AND c.nom LIKE ?";
    $params[] = '%' . $searchCampaign . '%';
}
if ($searchUser) {
    $sql .= " AND (u_mappeur.nom LIKE ? OR u_zeus1.nom LIKE ? OR u_zeus2.nom LIKE ? OR u_zeus3.nom LIKE ?)";
    $params[] = '%' . $searchUser . '%';
    $params[] = '%' . $searchUser . '%';
    $params[] = '%' . $searchUser . '%';
    $params[] = '%' . $searchUser . '%';
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
        }

        form {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 20px;
            width: 300px; /* Main form width */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            margin-left: 0;
            float: left;
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
    </style>
</head>
<body>

<h2>Créer une Nouvelle Campagne</h2>

<!-- Main form for creating campaigns -->
<form action="create_c.php" method="post">
    <!-- Date and Mappeur on the same line -->
    <div class="form-group">
        <div>
            <label for="date">Date:</label>
            <input type="date" id="date" name="date" required>
        </div>
        <div>
            <label for="mappeur">Mappeur:</label>
            <select id="mappeur" name="mappeur" required>
                <option value="">Sélectionnez un mappeur</option>
                <?php
                if ($mappeur_result->rowCount() > 0) {
                    while ($row = $mappeur_result->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['nom']) . "</option>";
                    }
                }
                ?>
            </select>
        </div>
    </div>

    <!-- Nom de la campagne and Numéro de missions on the same line -->
    <div class="form-group">
        <div>
            <label for="nom">Nom de la campagne:</label>
            <input type="text" id="nom" name="nom" required>
        </div>
        <div>
            <label for="missions">Numéro de missions:</label>
            <input type="number" id="missions" name="missions" required>
        </div>
    </div>

    <!-- Zeus selectors all on the same line -->
    <div class="zeus-group">
        <div>
            <label for="zeus1">Zeus 1:</label>
            <select id="zeus1" name="zeus1">
                <option value="">Sélectionnez un Zeus</option>
                <?php
                if ($zeus_result->rowCount() > 0) {
                    while ($row = $zeus_result->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['nom']) . "</option>";
                    }
                }
                ?>
            </select>
        </div>
        <div>
            <label for="zeus2">Zeus 2:</label>
            <select id="zeus2" name="zeus2">
                <option value="">Sélectionnez un Zeus</option>
                <?php
                // Re-execute Zeus query for zeus2 and zeus3
                $zeus_result->execute();
                if ($zeus_result->rowCount() > 0) {
                    while ($row = $zeus_result->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['nom']) . "</option>";
                    }
                }
                ?>
            </select>
        </div>
        <div>
            <label for="zeus3">Zeus 3:</label>
            <select id="zeus3" name="zeus3">
                <option value="">Sélectionnez un Zeus</option>
                <?php
                // Re-execute Zeus query for zeus3
                $zeus_result->execute();
                if ($zeus_result->rowCount() > 0) {
                    while ($row = $zeus_result->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['nom']) . "</option>";
                    }
                }
                ?>
            </select>
        </div>
    </div>

    <button type="submit">Créer la campagne</button>
</form>


<form method="get" action="campagne.php" class="search-form">
    <label for="search_campaign">Rechercher par Nom de Campagne :</label>
    <input type="text" id="search_campaign" name="search_campaign" value="<?php echo htmlspecialchars($searchCampaign); ?>">

    <label for="search_user">Rechercher par Nom de Mappeur ou Zeus :</label>
    <input type="text" id="search_user" name="search_user" value="<?php echo htmlspecialchars($searchUser); ?>">

    <button type="submit">Rechercher</button>
</form>

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
        for ($i = 0; $i < count($result); $i++) {
            $row = $result[$i];
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['date']) . "</td>";
            echo "<td>" . htmlspecialchars($row['nom']) . "</td>";
            echo "<td>" . htmlspecialchars($row['missions']) . "</td>";
            echo "<td class='mappeur'>" . htmlspecialchars($row['mappeur']) . "</td>";
            echo "<td class='zeus'>" . htmlspecialchars($row['zeus1'] ?? 'Personne') . "</td>";
            echo "<td class='zeus'>" . htmlspecialchars($row['zeus2'] ?? 'Personne') . "</td>";
            echo "<td class='zeus'>" . htmlspecialchars($row['zeus3'] ?? 'Personne') . "</td>";
            echo "</tr>";
        }
        ?>
    </tbody>
</table>
</body>
</html>
