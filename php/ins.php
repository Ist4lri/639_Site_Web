<?php
session_start();
include 'db.php';

// Activer l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $mot_de_passe = $_POST['password'];
    $spe_id = 9; // Setting spe_id to 9, you can modify this based on your requirements.

    if (!empty($nom) && !empty($email) && !empty($mot_de_passe) && !empty($spe_id)) {
        // Hachage du mot de passe
        $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_BCRYPT);
        
        // Insertion dans la base de données avec spe_id
        $sql = "INSERT INTO utilisateurs (nom, email, mot_de_passe, spe_id) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);

        if ($stmt->execute([$nom, $email, $mot_de_passe_hash, $spe_id])) {
            // Message de confirmation
            $success_message = "Votre inscription a bien été prise en compte. Patientez, votre validation est effectuée tous les jours.";
        } else {
            $error_message = "Une erreur est survenue lors de l'inscription.";
        }
    } else {
        $error_message = "Tous les champs sont obligatoires.";
    }
}

if (isset($_SESSION['utilisateur'])) {
    echo "Vous êtes déjà connecté en tant que " . htmlspecialchars($_SESSION['utilisateur']) . ".";
    echo '<br><a href="Dec.php">Se déconnecter</a>';
} else {
?>
<?php include 'header.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="../css/form.css">
</head>
<body>

<div class="container">
    <h2>Page d'Inscription</h2>
    
    <?php if (isset($error_message)): ?>
        <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>
    
    <?php if (isset($success_message)): ?>
        <div class="success-message"><?php echo htmlspecialchars($success_message); ?></div>
        <p>Veuillez patienter, votre compte sera validé sous peu.</p>
    <?php else: ?>
        <form action="ins.php" method="post">
            <label for="nom">Matricule CIV-Num Nom :</label>
            <input type="text" id="nom" name="nom" required>
            
            <label for="email">Email :</label>
            <input type="email" id="email" name="email" required>
            
            <label for="password">Mot de passe :</label>
            <input type="password" id="password" name="password" required>
            
            <input type="submit" value="S'inscrire">
        </form>
        
        <p>Vous avez déjà un compte ? <a href="connection.php">Connectez-vous ici</a></p>
    <?php endif; ?>
</div>

</body>
</html>

<?php
}
?>
