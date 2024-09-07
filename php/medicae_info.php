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

if ($currentUser['spe_id'] != 2) {
    header("Location: unauthorized.php");
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
    $usersStmt = $pdo->prepare("SELECT u.id, u.nom, u.grade, s.nom AS specialite, im.id AS info_id, im.taille, im.poids, im.problemes_medicaux 
                                FROM utilisateurs u 
                                LEFT JOIN informations_medicales im ON u.id = im.id_utilisateur
                                LEFT JOIN spe s ON u.spe_id = s.id
                                WHERE u.nom LIKE ?");
    $usersStmt->execute(['%' . $searchQuery . '%']);
} elseif (!empty($selectedGrade)) {
    $usersStmt = $pdo->prepare("SELECT u.id, u.nom, u.grade, s.nom AS specialite, im.id AS info_id, im.taille, im.poids, im.problemes_medicaux 
                                FROM utilisateurs u 
                                LEFT JOIN informations_medicales im ON u.id = im.id_utilisateur
                                LEFT JOIN spe s ON u.spe_id = s.id
                                WHERE u.grade = ?");
    $usersStmt->execute([$selectedGrade]);
} else {
    // Par défaut, récupérer tous les utilisateurs
    $usersStmt = $pdo->query("SELECT u.id, u.nom, u.grade, s.nom AS specialite, im.id AS info_id, im.taille, im.poids, im.problemes_medicaux 
                              FROM utilisateurs u 
                              LEFT JOIN informations_medicales im ON u.id = im.id_utilisateur
                              LEFT JOIN spe s ON u.spe_id = s.id");
}

$users = $usersStmt->fetchAll(PDO::FETCH_ASSOC);

// Traitement de la modification ou création des informations médicales
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_info'])) {
    $id_utilisateur = $_POST['id_utilisateur'];
    $taille = $_POST['taille'];
    $poids = $_POST['poids'];
    $problemes_medicaux = $_POST['problemes_medicaux'];

    if (!empty($id_utilisateur) && !empty($taille) && !empty($poids) && !empty($problemes_medicaux)) {
        // Vérifier si des informations médicales existent déjà pour cet utilisateur
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM informations_medicales WHERE id_utilisateur = ?");
        $stmt->execute([$id_utilisateur]);
        $infoExists = $stmt->fetchColumn();

        if ($infoExists) {
            // Si des informations existent, mise à jour
            $stmt = $pdo->prepare("UPDATE informations_medicales 
                                   SET taille = ?, poids = ?, problemes_medicaux = ? 
                                   WHERE id_utilisateur = ?");
            $stmt->execute([$taille, $poids, $problemes_medicaux, $id_utilisateur]);
            $success_message = "Les informations médicales ont été mises à jour avec succès.";
        } else {
            // Si aucune information n'existe, création d'un nouvel enregistrement
            $stmt = $pdo->prepare("INSERT INTO informations_medicales (id_utilisateur, taille, poids, problemes_medicaux) 
                                   VALUES (?, ?, ?, ?)");
            $stmt->execute([$id_utilisateur, $taille, $poids, $problemes_medicaux]);
            $success_message = "Les informations médicales ont été créées avec succès.";
        }

        // Redirection pour éviter la soumission multiple
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        $error_message = "Tous les champs sont obligatoires pour modifier ou créer des informations.";
    }
}

// Traitement pour afficher le formulaire de modification
$showEditForm = false;
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit'])) {
    $id_utilisateur = $_POST['id_utilisateur'];
    $stmt = $pdo->prepare("SELECT * FROM informations_medicales WHERE id_utilisateur = ?");
    $stmt->execute([$id_utilisateur]);
    $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    $showEditForm = true;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informations Médicales</title>
    <link rel="stylesheet" href="../css/med.css">

</head>
<body>

<div class="container">
    <h2>Page d'Informations Médicales</h2>

    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <?php if (isset($success_message)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
    <?php endif; ?>

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
                <td><?php echo htmlspecialchars($user['taille'] ?? 'N/A'); ?></td>
                <td><?php echo htmlspecialchars($user['poids'] ?? 'N/A'); ?></td>
                <td><?php echo htmlspecialchars($user['problemes_medicaux'] ?? 'N/A'); ?></td>
                <td>
                    <!-- Bouton pour afficher le formulaire de modification -->
                    <form action="" method="post" style="display:inline;">
                        <input type="hidden" name="id_utilisateur" value="<?php echo $user['id']; ?>">
                        <button type="submit" name="edit" class="btn btn-modify">Modifier</button>
                    </form>

                    <!-- Bouton pour afficher le PDF -->
                    <form action="afficher_info.php" method="post" style="display:inline;">
                        <input type="hidden" name="id_utilisateur" value="<?php echo $user['id']; ?>">
                        <button type="submit" name="view_pdf" class="btn btn-view-pdf">PDF</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Formulaire de modification, affiché uniquement si le bouton "Modifier" a été cliqué -->
    <?php if ($showEditForm): ?>
        <h3>Modifier les informations médicales de <?php echo htmlspecialchars($user['nom']); ?></h3>
        <form action="" method="post">
            <input type="hidden" name="id_utilisateur" value="<?php echo htmlspecialchars($id_utilisateur); ?>">
            
            <label for="taille">Taille (cm):</label>
            <input type="number" id="taille" name="taille" value="<?php echo htmlspecialchars($userInfo['taille'] ?? ''); ?>" required>

            <label for="poids">Poids (kg):</label>
            <input type="number" id="poids" name="poids" value="<?php echo htmlspecialchars($userInfo['poids'] ?? ''); ?>" required>

            <label for="problemes_medicaux">Problèmes médicaux:</label>
            <textarea id="problemes_medicaux" name="problemes_medicaux" rows="4" required><?php echo htmlspecialchars($userInfo['problemes_medicaux'] ?? ''); ?></textarea>

            <button type="submit" name="update_info" class="btn btn-success">Confirmer</button>
        </form>
    <?php endif; ?>
</div>

</body>
</html>
