<?php
session_start();
include 'db.php';

// Vérification du rôle : Seuls les admins peuvent accéder au site
if ($currentUser['role'] !== 'admin') {
    header("Location: insubordination.php"); // Rediriger vers une page d'insubordination
    exit();
}

// Vérification du grade autorisé (uniquement pour les rôles admin)
$gradesAutorises = ['Sergent', 'Lieutenant', 'Capitaine', 'Commandant', 'Colonel', 'Général', 'Major'];
if (!in_array($currentUser['grade'], $gradesAutorises)) {
    header("Location: insubordination.php");
    exit();
}

// Mettre à jour la spécialité
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_POST['user_id'];
    $nouvelleSpe = $_POST['nouvelle_spe'];

    if (!empty($nouvelleSpe)) {
        $stmt = $pdo->prepare("UPDATE utilisateurs SET spe_id = :nouvelle_spe WHERE id = :id");
        $stmt->execute(['nouvelle_spe' => $nouvelleSpe, 'id' => $userId]);
        $message = "Les informations de l'utilisateur ont été mises à jour avec succès.";
    }
}

// Rechercher par nom ou spécialité
$searchNom = isset($_GET['search_nom']) ? $_GET['search_nom'] : '';
$searchSpe = isset($_GET['search_spe']) ? $_GET['search_spe'] : '';

$sql = "SELECT u.id, u.nom, u.grade, s.nom AS specialite 
        FROM utilisateurs u 
        LEFT JOIN spe s ON u.spe_id = s.id
        WHERE 1=1";

$params = [];
if (!empty($searchNom)) {
    $sql .= " AND u.nom LIKE :nom";
    $params[':nom'] = "%$searchNom%";
}
if (!empty($searchSpe)) {
    $sql .= " AND s.nom = :spe";
    $params[':spe'] = $searchSpe;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les spécialités
$specialitesStmt = $pdo->query("SELECT id, nom FROM spe");
$specialites = $specialitesStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Spécialités</title>
    <link rel="stylesheet" href="../css/tab.css">
</head>
<body>
    <h1>Gestion des spécialités</h1>

    <?php if (isset($message)): ?>
        <p style="color: green;"><?php echo $message; ?></p>
    <?php endif; ?>

    <!-- Formulaire de recherche -->
    <form method="get" action="sous-officier.php">
        <label for="search_nom">Nom:</label>
        <input type="text" id="search_nom" name="search_nom" value="<?php echo htmlspecialchars($searchNom); ?>">

        <label for="search_spe">Spécialité:</label>
        <select id="search_spe" name="search_spe">
            <option value="">Toutes les spécialités</option>
            <?php foreach ($specialites as $spe): ?>
                <option value="<?php echo htmlspecialchars($spe['nom']); ?>" <?php if ($searchSpe == $spe['nom']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($spe['nom']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <input type="submit" value="Rechercher">
    </form>

    <table border="1">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Grade actuel</th>
                <th>Spécialité actuelle</th>
                <th>Nouvelle spécialité</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['nom']); ?></td>
                    <td><?php echo htmlspecialchars($user['grade']); ?></td>
                    <td><?php echo !empty($user['specialite']) ? htmlspecialchars($user['specialite']) : 'Aucune'; ?></td>
                    <td>
                        <form action="sous-officier.php" method="post">
                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['id']); ?>">
                            <select name="nouvelle_spe">
                                <option value="">Sélectionnez une spécialité</option>
                                <?php foreach ($specialites as $spe): ?>
                                    <option value="<?php echo htmlspecialchars($spe['id']); ?>"><?php echo htmlspecialchars($spe['nom']); ?></option>
                                <?php endforeach; ?>
                            </select>
                    </td>
                    <td>
                        <button type="submit">Mettre à jour</button>
                    </td>
                        </form>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
