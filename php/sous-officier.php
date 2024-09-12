<?php
session_start();
include 'db.php';

$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = :email");
$stmt->execute(['email' => $_SESSION['utilisateur']]);
$currentUser = $stmt->fetch();
// Vérification du grade autorisé ou admin
$gradesAutorises = ['Sergent', 'Lieutenant', 'Capitaine', 'Commandant', 'Colonel', 'Général', 'Major'];
if (!in_array($currentUser['grade'], $gradesAutorises)) {
    header("Location: insubordination.php");
    exit();
}

// Mettre à jour la spécialité et la gérance
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_POST['user_id'];
    $nouvelleSpe = $_POST['nouvelle_spe'];
    $nouvelleGerance = $_POST['nouvelle_gerance'];

    if (!empty($nouvelleSpe)) {
        $stmt = $pdo->prepare("UPDATE utilisateurs SET spe_id = :nouvelle_spe, gerance = :nouvelle_gerance WHERE id = :id");
        $stmt->execute(['nouvelle_spe' => $nouvelleSpe, 'nouvelle_gerance' => $nouvelleGerance, 'id' => $userId]);
        $message = "Les informations de l'utilisateur ont été mises à jour avec succès.";
    }
}

// Rechercher par nom ou spécialité
$searchNom = isset($_GET['search_nom']) ? $_GET['search_nom'] : '';
$searchSpe = isset($_GET['search_spe']) ? $_GET['search_spe'] : '';

$sql = "SELECT u.id, u.nom, u.grade, u.gerance, s.nom AS specialite 
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

<?php include 'header.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Spécialités et Gérances</title>
    <link rel="stylesheet" href="../css/tab.css">
    <link rel="stylesheet" href="../css/header.css">
</head>

<header class="head">
    <div class="head-logo">
        <a href="../index.php">
            <img src="../src/assets/Logo.png" alt="Logo 639">
        </a>
        <?php if ($isLoggedIn): ?>
            <span class="head-username">Bonjour, <?php echo htmlspecialchars($userName); ?></span>
        <?php endif; ?>
    </div>
    <div class="head-logo2">
        <img src="../src/assets/TitreSite.png" alt="639 Régiment cadien"
    </div>
    <nav class="head-nav">
            <a href="profil_utilisateur.php">Profil</a>
            <a href="officier.php">Officier</a>
            <a href="sous-officier.php">Sous-Officier</a>
            <a href="Dec.php">Déconnexion</a>
    </nav>
</header>

<body>
    <h1>Gestion des spécialités et gérances</h1>

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
                <th>Gérance actuelle</th>
                <th>Nouvelle gérance</th>
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
                    <td><?php echo htmlspecialchars($user['gerance'] === null ? 'Aucune' : $user['gerance']); ?></td>
                    <td>
                        <select name="nouvelle_gerance">
                            <option value="0" <?php if ($user['gerance'] === 0) echo 'selected'; ?>>0 - Aucun</option>
                            <option value="1" <?php if ($user['gerance'] == 1) echo 'selected'; ?>>1 - Gérant</option>
                            <option value="2" <?php if ($user['gerance'] == 2) echo 'selected'; ?>>2 - Sous-Gérant</option>
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
