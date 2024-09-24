<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_utilisateur'])) {
    $id_utilisateur = $_POST['id_utilisateur'];

    // Récupérer les informations médicales de l'utilisateur
    $stmt = $pdo->prepare("SELECT * FROM informations_medicales WHERE id_utilisateur = ?");
    $stmt->execute([$id_utilisateur]);
    $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_info'])) {
        $age = $_POST['age'];
        $taille = $_POST['taille'];
        $poids = $_POST['poids'];
        $problemes_medicaux = $_POST['problemes_medicaux'];

        // Mise à jour des informations médicales
        $stmt = $pdo->prepare("UPDATE informations_medicales SET age = ?, taille = ?, poids = ?, problemes_medicaux = ? WHERE id_utilisateur = ?");
        $stmt->execute([$age, $taille, $poids, $problemes_medicaux, $id_utilisateur]);

        $success_message = "Les informations médicales ont été mises à jour avec succès.";
        header("Location: medicae_info.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier les Informations Médicales</title>
    <link rel="stylesheet" href="../css/med.css">
</head>
<body>
    <div class="container">
        <h2>Modifier les Informations Médicales</h2>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>

        <form action="modif.php" method="post">
            <input type="hidden" name="id_utilisateur" value="<?php echo htmlspecialchars($id_utilisateur); ?>">
            
            <label for="age">Age:</label>
            <input type="number" id="age" name="age" value="<?php echo htmlspecialchars($userInfo['age'] ?? ''); ?>" required>
            
            <label for="taille">Taille (cm):</label>
            <input type="number" id="taille" name="taille" value="<?php echo htmlspecialchars($userInfo['taille'] ?? ''); ?>" required>

            <label for="poids">Poids (kg):</label>
            <input type="number" id="poids" name="poids" value="<?php echo htmlspecialchars($userInfo['poids'] ?? ''); ?>" required>

            <label for="problemes_medicaux">Problèmes médicaux:</label>
            <textarea id="problemes_medicaux" name="problemes_medicaux" rows="4" required><?php echo htmlspecialchars($userInfo['problemes_medicaux'] ?? ''); ?></textarea>

            <button type="submit" name="update_info" class="btn btn-success">Confirmer</button>
        </form>
    </div>
</body>
</html>
