<?php
session_start();
include 'db.php'; 

$stmt = $pdo->prepare("SELECT id, nom FROM utilisateurs WHERE email = :email");
$stmt->execute(['email' => $_SESSION['utilisateur']]);
$currentUser = $stmt->fetch(PDO::FETCH_ASSOC);

$factionStmt = $pdo->prepare("SELECT * FROM personnages WHERE id_utilisateur = :id_utilisateur AND faction = 'Adeptus Mechanicus' AND validation = 'Accepter'");
$factionStmt->execute(['id_utilisateur' => $currentUser['id']]);
$faction = $factionStmt->fetch(PDO::FETCH_ASSOC);

$searchCampaign = isset($_GET['search_campaign']) ? trim($_GET['search_campaign']) : '';
$searchUser = isset($_GET['search_user']) ? trim($_GET['search_user']) : '';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['request_type'], $_POST['request_description'])) {
    $requestType = $_POST['request_type'];
    $description = trim($_POST['request_description']);

    $insertStmt = $pdo->prepare("INSERT INTO demande_mechanicus (id_utilisateur, type_entretien, description) VALUES (?, ?, ?)");
    $insertStmt->execute([$currentUser['id'], $requestType, $description]);

    $message = "Votre demande a été soumise avec succès pour un entretien $requestType.";
}

$sql = "SELECT c.date, c.nom, c.missions, u_mappeur.nom AS mappeur, u_zeus.nom AS zeus1
        FROM campagne c
        LEFT JOIN utilisateurs u_mappeur ON c.id_mappeur = u_mappeur.id
        LEFT JOIN utilisateurs u_zeus ON c.id_zeus = u_zeus.id";

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

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
<a href=record.php class=zeus> Compteur </a>
<a href=../index.php> Acceuil </a>
    
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

    <form action="create_c.php" method="post">


    <label for="zeus2">Zeus 2:</label>
    <select id="zeus2" name="zeus2">
        <option value="">Sélectionnez un Zeus (facultatif)</option>
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

    <label for="zeus3">Zeus 3:</label>
    <select id="zeus3" name="zeus3">
        <option value="">Sélectionnez un Zeus (facultatif)</option>
        <?php
        $zeus_result->execute(); // Exécuter à nouveau pour réutiliser le résultat
        if ($zeus_result->rowCount() > 0) {
            while ($row = $zeus_result->fetch(PDO::FETCH_ASSOC)) {
                echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['nom']) . "</option>";
            }
        }
        ?>
    </select><br><br>

    <button type="submit">Créer la campagne</button>
</form>


    <form method="get" action="campagne.php">
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
