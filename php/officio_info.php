<?php
session_start();
include 'db.php';

// Vérifiez si l'utilisateur est connecté et a les autorisations
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = :email");
$stmt->execute(['email' => $_SESSION['utilisateur']]);
$currentUser = $stmt->fetch();
$gradesAutorises = ['Lieutenant', 'Capitaine', 'Commandant', 'Colonel', 'Général', 'Major'];
if (!in_array($currentUser['grade'], $gradesAutorises)) {
    header("Location: insubordination.php");
    exit();
}

// Handle updates to grade, specialty, management, formation, and formation_hierarchique
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_POST['user_id'];
    $nouveauGrade = $_POST['nouveau_grade'];
    $nouvelleSpe = $_POST['nouvelle_spe'];
    $nouvelleFormation = $_POST['nouvelle_formation'];
    $nouvelleFormationHierarchique = $_POST['nouvelle_formation_hierarchique'];

    // Check if nouvelle_gerance exists before using it
    if (isset($_POST['nouvelle_gerance'])) {
        $nouvelleGerance = $_POST['nouvelle_gerance'];
        $stmt = $pdo->prepare("UPDATE utilisateurs SET gerance = :nouvelle_gerance WHERE id = :id");
        $stmt->execute(['nouvelle_gerance' => $nouvelleGerance, 'id' => $userId]);
    }

    if (!empty($nouveauGrade)) {
        $stmt = $pdo->prepare("UPDATE utilisateurs SET grade = :nouveau_grade WHERE id = :id");
        $stmt->execute(['nouveau_grade' => $nouveauGrade, 'id' => $userId]);
    }

    if (!empty($nouvelleSpe)) {
        $stmt = $pdo->prepare("UPDATE utilisateurs SET spe_id = :nouvelle_spe WHERE id = :id");
        $stmt->execute(['nouvelle_spe' => $nouvelleSpe, 'id' => $userId]);
    }

    // Check if a formation record exists for the user
    $formationExistsStmt = $pdo->prepare("SELECT id FROM formation WHERE id_utilisateur = :id_utilisateur");
    $formationExistsStmt->execute(['id_utilisateur' => $userId]);
    $formationExists = $formationExistsStmt->fetch();

    if ($formationExists) {
        // Update the formation if a record exists
        $stmt = $pdo->prepare("UPDATE formation SET formation = :nouvelle_formation, formation_hierarchique = :nouvelle_formation_hierarchique WHERE id_utilisateur = :id_utilisateur");
        $stmt->execute([
            'nouvelle_formation' => $nouvelleFormation,
            'nouvelle_formation_hierarchique' => $nouvelleFormationHierarchique,
            'id_utilisateur' => $userId
        ]);
    } else {
        // Insert a new formation record if none exists
        $stmt = $pdo->prepare("INSERT INTO formation (id_utilisateur, formation, formation_hierarchique) VALUES (:id_utilisateur, :nouvelle_formation, :nouvelle_formation_hierarchique)");
        $stmt->execute([
            'id_utilisateur' => $userId,
            'nouvelle_formation' => $nouvelleFormation,
            'nouvelle_formation_hierarchique' => $nouvelleFormationHierarchique
        ]);
    }

    $message = "Les informations de l'utilisateur ont été mises à jour avec succès.";
}

// Fetch users and formation data
$searchNom = isset($_GET['search_nom']) ? $_GET['search_nom'] : '';
$searchGrade = isset($_GET['search_grade']) ? $_GET['search_grade'] : '';
$searchSpe = isset($_GET['search_spe']) ? $_GET['search_spe'] : '';

$sql = "SELECT u.id, u.nom, u.grade, u.gerance, s.nom AS specialite, f.formation, f.formation_hierarchique 
        FROM utilisateurs u 
        LEFT JOIN spe s ON u.spe_id = s.id
        LEFT JOIN formation f ON u.id = f.id_utilisateur
        WHERE 1=1";

$params = [];
if (!empty($searchNom)) {
    $sql .= " AND u.nom LIKE :nom";
    $params[':nom'] = "%$searchNom%";
}
if (!empty($searchGrade)) {
    $sql .= " AND u.grade = :grade";
    $params[':grade'] = $searchGrade;
}
if (!empty($searchSpe)) {
    $sql .= " AND s.nom = :spe";
    $params[':spe'] = $searchSpe;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Specialties for the dropdown
$specialitesStmt = $pdo->query("SELECT id, nom FROM spe");
$specialites = $specialitesStmt->fetchAll(PDO::FETCH_ASSOC);

// Formation options for the dropdowns
$formationOptions = ['FB', 'FS', 'Aucune'];
$formationHierarchiqueOptions = ['FH1', 'FH1T', 'FH2', 'FH2T', 'FH3', 'FH3T', 'FH4', 'FH4T', 'FH5', 'FH5T', 'FH6', 'FH6T', 'Aucune'];

$isLoggedIn = isset($_SESSION['utilisateur']);
$userName = $isLoggedIn ? $_SESSION['nom_utilisateur'] : '';
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Officiers</title>
    <link rel="stylesheet" href="../css/tab.css">
</head>

<?php include 'headero.php' ?>
<body>
    <h1>Gestion des grades, spécialités et formations</h1>

    <?php if (isset($message)): ?>
        <p style="color: green;"><?php echo $message; ?></p>
    <?php endif; ?>

    <!-- Formulaire de recherche -->
    <form method="get" action="officio_info.php">
        <label for="search_nom">Nom:</label>
        <input type="text" id="search_nom" name="search_nom" value="<?php echo htmlspecialchars($searchNom); ?>">

        <label for="search_grade">Grade:</label>
        <select id="search_grade" name="search_grade">
            <option value="">Tous les grades</option>
            <option value="Conscrit" <?php if ($searchGrade == 'Conscrit') echo 'selected'; ?>>Conscrit</option>
            <option value="Garde" <?php if ($searchGrade == 'Garde') echo 'selected'; ?>>Garde</option>
            <option value="Garde-Vétéran" <?php if ($searchGrade == 'Garde-Vétéran') echo 'selected'; ?>>Garde-Vétéran</option>
            <option value="Caporal" <?php if ($searchGrade == 'Caporal') echo 'selected'; ?>>Caporal</option>
            <option value="Sergent" <?php if ($searchGrade == 'Sergent') echo 'selected'; ?>>Sergent</option>
            <option value="Lieutenant" <?php if ($searchGrade == 'Lieutenant') echo 'selected'; ?>>Lieutenant</option>
        </select>

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
                <th>Gérance Actuelle</th>
                <th>Formation actuelle</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['nom']); ?></td>
                    <td><?php echo htmlspecialchars($user['grade']); ?></td>
                    <td><?php echo !empty($user['specialite']) ? htmlspecialchars($user['specialite']) : 'Aucune'; ?></td>
                   <td>
                        <form action="officier.php" method="post">
                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['id']); ?>">
                            <select name="nouvelle_gerance">
                                <option value="0" <?php if (isset($user['gerance']) && $user['gerance'] == 0) echo 'selected'; ?>>0 - Aucun</option>
                                <option value="1" <?php if (isset($user['gerance']) && $user['gerance'] == 1) echo 'selected'; ?>>1 - Gérant</option>
                                <option value="2" <?php if (isset($user['gerance']) && $user['gerance'] == 2) echo 'selected'; ?>>2 - Sous-Gérant</option>
                            </select>
                                           
                    </form>
                    </td>
                    <td><?php echo htmlspecialchars(($user['formation'] ?? 'Aucune') . '/' . ($user['formation_hierarchique'] ?? 'Aucune')); ?></td>
 
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
