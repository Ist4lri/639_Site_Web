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
    $zeus1 = $_POST['zeus1'] ?? null;
    $zeus2 = $_POST['zeus2'] ?? null;
    $zeus3 = $_POST['zeus3'] ?? null;

    $stmt = $pdo->prepare("UPDATE campagne SET id_zeus = ?, id_zeus2 = ?, id_zeus3 = ? WHERE id = ?");
    $stmt->execute([$zeus1, $zeus2, $zeus3, $id_campagne]);

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
</head>
<body>
    <h2>Modifier les Zeus de la campagne : <?php echo htmlspecialchars($campagne['nom']); ?></h2>

    <form action="" method="post">
        <label for="zeus1">Zeus 1:</label>
        <select id="zeus1" name="zeus1" required>
            <option value="">Sélectionnez un Zeus</option>
            <?php foreach ($zeus_list as $zeus): ?>
                <option value="<?php echo $zeus['id']; ?>" <?php echo $zeus['id'] == $campagne['id_zeus'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($zeus['nom']); ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="zeus2">Zeus 2:</label>
        <select id="zeus2" name="zeus2">
            <option value="">Sélectionnez un Zeus</option>
            <?php foreach ($zeus_list as $zeus): ?>
                <option value="<?php echo $zeus['id']; ?>" <?php echo $zeus['id'] == $campagne['id_zeus2'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($zeus['nom']); ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="zeus3">Zeus 3:</label>
        <select id="zeus3" name="zeus3">
            <option value="">Sélectionnez un Zeus</option>
            <?php foreach ($zeus_list as $zeus): ?>
                <option value="<?php echo $zeus['id']; ?>" <?php echo $zeus['id'] == $campagne['id_zeus3'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($zeus['nom']); ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <button type="submit">Mettre à jour</button>
    </form>
</body>
</html>
