<?php
session_start();
include 'db.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['utilisateur'])) {
    header("Location: connection.php");
    exit();
}

// Récupérer l'utilisateur actuel
$stmt = $pdo->prepare("SELECT id, spe_id FROM utilisateurs WHERE email = :email");
$stmt->execute(['email' => $_SESSION['utilisateur']]);
$currentUser = $stmt->fetch();

if ($currentUser['spe_id'] != 3) {
    header("Location: insubordination.php");
    exit();
}

// Récupérer les grades distincts pour le filtre
$gradesStmt = $pdo->query("SELECT DISTINCT grade FROM utilisateurs");
$grades = $gradesStmt->fetchAll(PDO::FETCH_ASSOC);

// Gestion de la recherche par nom
$searchQuery = isset($_POST['search_query']) ? trim($_POST['search_query']) : '';

// Récupérer les utilisateurs en fonction du grade sélectionné ou de la recherche par nom
$selectedGrade = isset($_POST['selected_grade']) ? $_POST['selected_grade'] : '';

if (!empty($searchQuery)) {
    $usersStmt = $pdo->prepare("SELECT u.id, u.nom, u.grade, s.nom AS specialite, im.id AS info_id, im.age, im.taille, im.poids, im.problemes_medicaux 
                                FROM utilisateurs u 
                                LEFT JOIN informations_medicales im ON u.id = im.id_utilisateur
                                LEFT JOIN spe s ON u.spe_id = s.id
                                WHERE u.nom LIKE ?");
    $usersStmt->execute(['%' . $searchQuery . '%']);
} elseif (!empty($selectedGrade)) {
    $usersStmt = $pdo->prepare("SELECT u.id, u.nom, u.grade, s.nom AS specialite, im.id AS info_id, im.age, im.taille, im.poids, im.problemes_medicaux 
                                FROM utilisateurs u 
                                LEFT JOIN informations_medicales im ON u.id = im.id_utilisateur
                                LEFT JOIN spe s ON u.spe_id = s.id
                                WHERE u.grade = ?");
    $usersStmt->execute([$selectedGrade]);
} else {
    // Par défaut, récupérer tous les utilisateurs
    $usersStmt = $pdo->query("SELECT u.id, u.nom, u.grade, s.nom AS specialite, im.id AS info_id, im.age, im.taille, im.poids, im.problemes_medicaux 
                              FROM utilisateurs u 
                              LEFT JOIN informations_medicales im ON u.id = im.id_utilisateur
                              LEFT JOIN spe s ON u.spe_id = s.id");
}

$users = $usersStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informations Médicales</title>
    <link rel="stylesheet" href="../css/med.css">
</head>
<?php include 'header.php'; ?>
<body>

<div class="container">
    <h2>Page d'Informations Médicales</h2>

    <!-- Filtrer par grade ou rechercher par nom -->
    <form method="post" action="">
        <label for="selected_grade">Choisir un grade :</label>
        <select name="selected_grade" id="selected_grade" class="form-control" style="width:200px; display:inline;">
            <option value="">Tous les grades</option>
            <?php foreach ($grades as $grade): ?>
                <option value="<?php echo htmlspecialchars($grade['grade']); ?>" 
                    <?php if ($grade['grade'] == $selectedGrade) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($grade['grade']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary">Filtrer</button>

        <label for="search_query" style="margin-left: 20px;">Rechercher par nom :</label>
        <input type="text" id="search_query" name="search_query" value="<?php echo htmlspecialchars($searchQuery); ?>" class="form-control" style="width:200px; display:inline;">
        <button type="submit" class="btn btn-primary">Rechercher</button>
    </form>

    <table class="table table-bordered" style="margin-top: 20px;">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Grade</th>
                <th>Spécialité</th>
                <th>Age</th>
                <th>Taille (cm)</th>
                <th>Poids (kg)</th>
                <th>Problèmes Médicaux</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo htmlspecialchars($user['nom']); ?></td>
                <td><?php echo htmlspecialchars($user['grade']); ?></td>
                <td><?php echo htmlspecialchars($user['specialite']); ?></td>
                <td><?php echo htmlspecialchars($user['age'] ?? 'N/A'); ?></td>
                <td><?php echo htmlspecialchars($user['taille'] ?? 'N/A'); ?></td>
                <td><?php echo htmlspecialchars($user['poids'] ?? 'N/A'); ?></td>
                <td><?php echo htmlspecialchars(substr($user['problemes_medicaux'] ?? 'N/A', 0, 30)) . (strlen($user['problemes_medicaux'] ?? '') > 30 ? '...' : ''); ?></td>
                <td>
                    <!-- Bouton pour rediriger vers modif.php -->
                    <form action="modif.php" method="post" style="display:inline;">
                        <input type="hidden" name="id_utilisateur" value="<?php echo $user['id']; ?>">
                        <button type="submit" name="edit" class="btn btn-modify">Modifier</button>
                    </form>

                    <!-- Bouton pour afficher le PDF -->
<form action="afficher_info.php" method="post" style="display:inline;" target="_blank">
    <input type="hidden" name="id_utilisateur" value="<?php echo htmlspecialchars($user['id']); ?>">
    <button type="submit" name="view_pdf" class="btn btn-view-pdf">PDF</button>
</form>

                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
