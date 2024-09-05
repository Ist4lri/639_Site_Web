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

    if (!empty($nom) && !empty($email) && !empty($mot_de_passe)) {
        // Hachage du mot de passe
        $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_BCRYPT);
        
        // Insertion dans la base de données
        $sql = "INSERT INTO utilisateurs (nom, email, mot_de_passe) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);

        if ($stmt->execute([$nom, $email, $mot_de_passe_hash])) {
            $_SESSION['utilisateur'] = $email;
            header("Location: index.php");
            exit;
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
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="container">
    <h2>Page d'Inscription</h2>
    
    <?php if (isset($error_message)): ?>
        <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>
    
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
</div>

</body>
</html>

<?php
}
?>
