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

// Handle updates to medals and notes
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_POST['user_id'];

    $nouvelleNote = $_POST['note'];
    for ($i = 1; $i <= 5; $i++) {
        $medalField = "medaille_$i";
        if (isset($_POST[$medalField])) {
            $stmt = $pdo->prepare("UPDATE utilisateurs SET $medalField = 1 WHERE id = :id_utilisateur");
            $stmt->execute(['id_utilisateur' => $userId]);
        } else {
            $stmt = $pdo->prepare("UPDATE utilisateurs SET $medalField = 0 WHERE id = :id_utilisateur");
            $stmt->execute(['id_utilisateur' => $userId]);
        }
    }

    $stmt = $pdo->prepare("UPDATE utilisateurs SET note = :note WHERE id = :id_utilisateur");
    $stmt->execute([
        'note' => $nouvelleNote,
        'id_utilisateur' => $userId
    ]);

    $message = "Les médailles et la note ont été mises à jour avec succès.";
}

// Fetch users data
$sql = "SELECT id, nom, grade, medaille_1, medaille_2, medaille_3, medaille_4, medaille_5, note 
        FROM utilisateurs";
$stmt = $pdo->query($sql);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Officiers</title>
    <link rel="icon" type="image/x-icon" href="../src/assets/Logo_639th_2.ico">
    <link rel="stylesheet" href="../css/tab.css">
    <link rel="stylesheet" href="../css/header.css">
</head>

<header class="head">
    <div class="head-logo">
        <a href="../index.php">
            <img src="../src/assets/Logo.png" alt="Logo 639">
        </a>
    </div>
    <div class="head-logo2">
        <a href="../index.php">
        <img src="../src/assets/TitreSite.png" alt="639 Régiment cadien">
        </a>
    </div>
    <nav class="head-nav">
            <a href="profil_utilisateur.php">Profil</a>
            <a href="officier.php">Grades</a>
            <a href="demande.php">Demandes</a>
            <a href="Dec.php">Déconnexion</a>
    </nav>
</header>
<body>
    <h1>Gestion des Médailles et Note</h1>

    <?php if (isset($message)): ?>
        <p style="color: green;"><?php echo $message; ?></p>
    <?php endif; ?>

    <table border="1">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Grade</th>
                <th>Médaille 1</th>
                <th>Médaille 2</th>
                <th>Médaille 3</th>
                <th>Médaille 4</th>
                <th>Médaille 5</th>
                <th>Note</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <form action="medaille.php" method="post">
                        <td><?php echo htmlspecialchars($user['nom']); ?></td>
                        <td><?php echo htmlspecialchars($user['grade']); ?></td>

                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <td>
                                <input type="checkbox" name="medaille_<?php echo $i; ?>" value="1" <?php echo $user["medaille_$i"] ? 'checked' : ''; ?>>
                            </td>
                        <?php endfor; ?>

                        <td>
                            <input type="text" name="note" value="<?php echo htmlspecialchars($user['note']); ?>">
                        </td>

                        <td>
                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['id']); ?>">
                            <button type="submit">Mettre à jour</button>
                        </td>
                    </form>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
