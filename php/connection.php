<?php
session_start();
$_SESSION['utilisateur'] = [
    'id' => $user['id'], // L'ID utilisateur de la base de données
    'nom' => $user['nom'],
    'email' => $user['email']

include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $mot_de_passe = $_POST['password'];

    if (!empty($email) && !empty($mot_de_passe)) {
        $sql = "SELECT * FROM utilisateurs WHERE email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {
            if ($user['confirmation'] == 1 && $user['banni'] == 0) {
                $_SESSION['id_utilisateur'] = $user['id'];  
                $_SESSION['utilisateur'] = $user['email'];
                $_SESSION['nom_utilisateur'] = $user['nom'];
                $_SESSION['role'] = $user['role'];
                
                
                header("Location: ../index.php");
                exit;
            } else {
                $error_message = "Votre compte n'a pas encore été validé, ou est banni";
            }
        } else {
            $error_message = "Email ou mot de passe incorrect.";
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
    <title>Connexion</title>
    <link rel="stylesheet" href="../css/form.css">
</head>
<body>

<div class="container">
    <h2>Page de Connexion</h2>
    
    <?php if (isset($error_message)): ?>
        <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>
    
    <form action="connection.php" method="post">
        <label for="email">Email :</label>
        <input type="email" id="email" name="email" required>
        
        <label for="password">Mot de passe :</label>
        <input type="password" id="password" name="password" required>
        
        <input type="submit" value="Se connecter">
    </form>
    
    <p>Vous n'avez pas encore de compte ? <a href="ins.php">Inscrivez-vous ici</a></p>
</div>

</body>
</html>

<?php
}
?>
