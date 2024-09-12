<?php
session_start();
include 'db.php';

// Initialize a variable to store the success message
$success_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form inputs
    $nom = $_POST['nom'];
    $faction = $_POST['faction'];
    $histoire = $_POST['histoire'];
    $id_utilisateur = $_SESSION['id_utilisateur'];  // The user's ID should be in the session

    // Check if the required inputs are not empty
    if (!empty($nom) && !empty($faction) && !empty($histoire)) {
        // Insert the character into the database
        $sql = "INSERT INTO personnages (nom, id_utilisateur, gerance, faction, histoire, validation) 
                VALUES (:nom, :id_utilisateur, 0, :faction, :histoire, 'Attente')";
        $stmt = $pdo->prepare($sql);
        
        try {
            // Execute the insert statement
            $stmt->execute([
                'nom' => $nom,
                'id_utilisateur' => $id_utilisateur,
                'faction' => $faction,
                'histoire' => $histoire
            ]);

            // Set the success message
            $success_message = "Votre personnage a bien été envoyé.";
        } catch (PDOException $e) {
            // If there's an error inserting, display a user-friendly message
            echo "Erreur lors de la création du personnage: " . $e->getMessage();
        }
    } else {
        // Handle cases where the form is incomplete
        echo "Tous les champs sont obligatoires.";
    }
}
?>

<?php include 'header.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/perso.css"> 
    <title>Création de Personnage</title>
</head>
<body>

<h2>Création de Personnage</h2>

<?php if ($success_message): ?>
    <div style="color: green; font-weight: bold;">
        <?php echo $success_message; ?>
    </div>
<?php else: ?>
    <form action="perso.php" method="POST">
        <label for="nom">Nom du personnage:</label>
        <input type="text" id="nom" name="nom" required><br>

        <label for="faction">Faction:</label>
        <select id="faction" name="faction" required>
            <option value="Officio Prefectus">Officio Prefectus</option>
            <option value="Adeptus Mechanicus">Adeptus Mechanicus</option>
            <option value="Ecclésiarchie">Ecclésiarchie</option>
            <option value="Inquisition">Inquisition</option>
            <option value="Psyker">Psyker</option>
            <option value="Abhumains">Abhumains</option>
        </select><br>

        <label for="histoire">Histoire:</label><br>
        <textarea id="histoire" name="histoire" rows="10" cols="50" required></textarea><br>

        <input type="submit" value="Envoyer">
    </form>
<?php endif; ?>

</body>
</html>
