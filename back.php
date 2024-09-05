<?php
session_start();
include 'php/db.php';

if (!isset($_SESSION['utilisateur']) || $_SESSION['role'] !== 'admin') {
    header("Location: php/connection.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_POST['user_id'];
    $action = $_POST['action'];

    if (!empty($userId) && !empty($action)) {
        switch ($action) {
            case 'valider':
                $sql = "UPDATE utilisateurs SET confirmation = 1 WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$userId]);
                $message = "Utilisateur validé avec succès.";
                break;
            case 'bannir':
                $sql = "UPDATE utilisateurs SET banni = 1 WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$userId]);
                $message = "Utilisateur banni avec succès.";
                break;
            case 'debannir':
                $sql = "UPDATE utilisateurs SET banni = 0 WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$userId]);
                $message = "Utilisateur débanni avec succès.";
                break;
            case 'changer_grade':
                $nouveauGrade = $_POST['grade'];
                $sql = "UPDATE utilisateurs SET grade = ? WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$nouveauGrade, $userId]);
                $message = "Grade de l'utilisateur modifié avec succès.";
                break;
            case 'changer_role':
                $nouveauRole = $_POST['role'];
                $sql = "UPDATE utilisateurs SET role = ? WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$nouveauRole, $userId]);
                $message = "Rôle de l'utilisateur modifié avec succès.";
                break;
        }
    } else {
        $message = "Tous les champs sont obligatoires.";
    }
}

// Récupérer les paramètres de recherche
$searchNom = isset($_GET['search_nom']) ? $_GET['search_nom'] : '';
$searchConfirmation = isset($_GET['search_confirmation']) ? $_GET['search_confirmation'] : '';
$searchBanni = isset($_GET['search_banni']) ? $_GET['search_banni'] : '';

$sql = "SELECT id, nom, email, confirmation, banni, grade, role FROM utilisateurs WHERE 1=1";

if (!empty($searchNom)) {
    $sql .= " AND nom LIKE :nom";
}

if ($searchConfirmation !== '') {
    $sql .= " AND confirmation = :confirmation";
}

if ($searchBanni !== '') {
    $sql .= " AND banni = :banni";
}

$stmt = $pdo->prepare($sql);

if (!empty($searchNom)) {
    $stmt->bindValue(':nom', '%' . $searchNom . '%');
}

if ($searchConfirmation !== '') {
    $stmt->bindValue(':confirmation', $searchConfirmation, PDO::PARAM_INT);
}

if ($searchBanni !== '') {
    $stmt->bindValue(':banni', $searchBanni, PDO::PARAM_INT);
}

$stmt->execute();
$utilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration</title>
    <link rel="stylesheet" href="css/tab.css">
    <a href="gerer_demandes.php">Demandes</a>
</head>
<body>
    <h2>Gestion des utilisateurs</h2>

    <?php if (isset($message)): ?>
        <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="get" action="back.php">
        <label for="search_nom">Nom:</label>
        <input type="text" id="search_nom" name="search_nom" value="<?php echo isset($_GET['search_nom']) ? htmlspecialchars($_GET['search_nom']) : ''; ?>">

        <label for="search_confirmation">Confirmation:</label>
        <select id="search_confirmation" name="search_confirmation">
            <option value="">Tous</option>
            <option value="1" <?php echo (isset($_GET['search_confirmation']) && $_GET['search_confirmation'] == '1') ? 'selected' : ''; ?>>Oui</option>
            <option value="0" <?php echo (isset($_GET['search_confirmation']) && $_GET['search_confirmation'] == '0') ? 'selected' : ''; ?>>Non</option>
        </select>

        <label for="search_banni">Banni:</label>
        <select id="search_banni" name="search_banni">
            <option value="">Tous</option>
            <option value="1" <?php echo (isset($_GET['search_banni']) && $_GET['search_banni'] == '1') ? 'selected' : ''; ?>>Oui</option>
            <option value="0" <?php echo (isset($_GET['search_banni']) && $_GET['search_banni'] == '0') ? 'selected' : ''; ?>>Non</option>
        </select>

        <input type="submit" value="Rechercher">
    </form>

    <br>

    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Confirmation</th>
                <th>Banni</th>
                <th>Grade</th>
                <th>Rôle</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($utilisateurs as $utilisateur): ?>
                <tr>
                    <td><?php echo htmlspecialchars($utilisateur['id']); ?></td>
                    <td><?php echo htmlspecialchars($utilisateur['nom']); ?></td>
                    <td><?php echo htmlspecialchars($utilisateur['email']); ?></td>
                    <td><?php echo $utilisateur['confirmation'] ? 'Oui' : 'Non'; ?></td>
                    <td><?php echo $utilisateur['banni'] ? 'Oui' : 'Non'; ?></td>
                    <td><?php echo htmlspecialchars($utilisateur['grade']); ?></td>
                    <td><?php echo htmlspecialchars($utilisateur['role']); ?></td>
                    <td>
                        <form action="back.php" method="post" style="display:inline;">
                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($utilisateur['id']); ?>">
                            <input type="hidden" name="action" value="valider">
                            <input type="submit" value="Valider">
                        </form>
                        <form action="back.php" method="post" style="display:inline;">
                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($utilisateur['id']); ?>">
                            <input type="hidden" name="action" value="bannir">
                            <input type="submit" value="Bannir" class="danger">
                        </form>
                        <?php if ($utilisateur['banni']): ?>
                        <form action="back.php" method="post" style="display:inline;">
                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($utilisateur['id']); ?>">
                            <input type="hidden" name="action" value="debannir">
                            <input type="submit" value="Débannir">
                        </form>
                        <?php endif; ?>
                        <form action="back.php" method="post" style="display:inline;">
                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($utilisateur['id']); ?>">
                            <input type="hidden" name="action" value="changer_grade">
                            <select name="grade">
                                <option value="Civil" <?php if ($utilisateur['grade'] == 'Civil') echo 'selected'; ?>>Civil</option>
                                <option value="Garde" <?php if ($utilisateur['grade'] == 'Garde') echo 'selected'; ?>>Garde</option>
                                <option value="Garde-Vétéran" <?php if ($utilisateur['grade'] == 'Garde-Vétéran') echo 'selected'; ?>>Garde-Vétéran</option>
                                <option value="Caporal" <?php if ($utilisateur['grade'] == 'Caporal') echo 'selected'; ?>>Caporal</option>
                                <option value="Sergent" <?php if ($utilisateur['grade'] == 'Sergent') echo 'selected'; ?>>Sergent</option>
                                <option value="Lieutenant" <?php if ($utilisateur['grade'] == 'Lieutenant') echo 'selected'; ?>>Lieutenant</option>
                                <option value="Capitaine" <?php if ($utilisateur['grade'] == 'Capitaine') echo 'selected'; ?>>Capitaine</option>
                                <option value="Commandant" <?php if ($utilisateur['grade'] == 'Commandant') echo 'selected'; ?>>Commandant</option>
                                <option value="Colonel" <?php if ($utilisateur['grade'] == 'Colonel') echo 'selected'; ?>>Colonel</option>
                                <option value="Général" <?php if ($utilisateur['grade'] == 'Général') echo 'selected'; ?>>Général</option>
                                <option value="Major" <?php if ($utilisateur['grade'] == 'Major') echo 'selected'; ?>>Major</option>
                            </select>
                            <input type="submit" value="Changer Grade">
                        </form>
                        <form action="back.php" method="post" style="display:inline;">
                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($utilisateur['id']); ?>">
                            <input type="hidden" name="action" value="changer_role">
                            <select name="role">
                                <option value="utilisateur" <?php if ($utilisateur['role'] == 'utilisateur') echo 'selected'; ?>>Utilisateur</option>
                                <option value="admin" <?php if ($utilisateur['role'] == 'admin') echo 'selected'; ?>>Admin</option>
                            </select>
                            <input type="submit" value="Changer Rôle">
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
