<?php
session_start();
include 'db.php';

// Récupérer l'ID de la campagne à partir des paramètres URL
$id_campagne = $_GET['id'] ?? null;

if (!$id_campagne) {
    die('ID de campagne non spécifié.');
}

// Récupérer les informations de la campagne
$stmt = $pdo->prepare("SELECT * FROM campagne WHERE id = ?");
$stmt->execute([$id_campagne]);
$campagne = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$campagne) {
    die('Campagne non trouvée.');
}

// Récupérer les utilisateurs Zeus
$zeus_query = "SELECT id, nom FROM utilisateurs WHERE zeus = 1";
$zeus_result = $pdo->query($zeus_query);
$zeus_list = $zeus_result->fetchAll(PDO::FETCH_ASSOC);

// Traitement de la mise à jour
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $date = $_POST['date'] ?? null;
    $nom = $_POST['nom'] ?? null;
    $missions = $_POST['missions'] ?? null;
    $zeus1 = !empty($_POST['zeus1']) ? $_POST['zeus1'] : null;
    $zeus2 = !empty($_POST['zeus2']) ? $_POST['zeus2'] : null;
    $zeus3 = !empty($_POST['zeus3']) ? $_POST['zeus3'] : null;

    $stmt = $pdo->prepare("UPDATE campagne SET date = ?, nom = ?, missions = ?, id_zeus = ?, id_zeus2 = ?, id_zeus3 = ? WHERE id = ?");
    $stmt->execute([$date, $nom, $missions, $zeus1, $zeus2, $zeus3, $id_campagne]);

    header('Location: campagne.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier les Zeus de la Campagne</title>
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
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
            color: #555;
            display: block;
            margin: 10px 0 5px;
        }

        input[type="text"], input[type="date"], input[type="number"], select {
            width: calc(100% - 10px);
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 15px;
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
        }
    </style>
</head>
<body>
    <h2>Modifier la campagne : <?php echo htmlspecialchars($campagne['nom']); ?></h2>

    <form action="" method="post">
        <label for="date">Date:</label>
        <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($campagne['date']); ?>" required>

        <label for="nom">Nom de la campagne:</label>
        <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($campagne['nom']); ?>" required>

        <label for="missions">Numéro de missions:</label>
        <input type="number" id="missions" name="missions" value="<?php echo htmlspecialchars($campagne['missions']); ?>" required>

        <label for="zeus1">Zeus 1:</label>
        <select id="zeus1" name="zeus1">
            <option value="">Sélectionnez un Zeus</option>
            <?php foreach ($zeus_list as $zeus): ?>
                <option value="<?php echo $zeus['id']; ?>" <?php echo $zeus['id'] == $campagne['id_zeus'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($zeus['nom']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="zeus2">Zeus 2:</label>
        <select id="zeus2" name="zeus2">
            <option value="">Sélectionnez un Zeus</option>
            <?php foreach ($zeus_list as $zeus): ?>
                <option value="<?php echo $zeus['id']; ?>" <?php echo $zeus['id'] == $campagne['id_zeus2'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($zeus['nom']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="zeus3">Zeus 3:</label>
        <select id="zeus3" name="zeus3">
            <option value="">Sélectionnez un Zeus</option>
            <?php foreach ($zeus_list as $zeus): ?>
                <option value="<?php echo $zeus['id']; ?>" <?php echo $zeus['id'] == $campagne['id_zeus3'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($zeus['nom']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Mettre à jour</button>
    </form>
</body>
</html>
