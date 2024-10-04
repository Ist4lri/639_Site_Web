<?php
session_start();
include 'db.php'; // Inclure la connexion à la base de données

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['demande'])) {
    $utilisateur_id = $_SESSION['user_id']; // Récupérer l'ID de l'utilisateur connecté
    $demande = trim($_POST['demande']);

    if (!empty($demande)) {
        // Insérer la demande dans la table 'dadmin'
        $sql = "INSERT INTO dadmin (utilisateur_id, demande) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$utilisateur_id, $demande]);

        echo "<p style='color:green;'>Votre demande a été envoyée avec succès !</p>";
    } else {
        echo "<p style='color:red;'>La demande ne peut pas être vide !</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page avec Footer Demande</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Main content */
        .content {
            flex: 1;
            padding: 20px;
        }

        /* Footer form styling */
        footer {
            background-color: #333;
            color: white;
            padding: 20px;
            text-align: center;
        }

        footer h2 {
            margin-bottom: 15px;
            font-size: 1.5em;
            color: #ff8800;
        }

        footer form {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 20px;
        }

        footer textarea {
            width: 80%;
            height: 100px;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: none;
            resize: none;
        }

        footer button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s ease;
        }

        footer button:hover {
            background-color: #218838;
        }

        footer p {
            margin-top: 10px;
            color: white;
        }
    </style>
</head>
<body>

<div class="content">
    <!-- Contenu principal de la page -->
</div>

<footer>
    <h2>Envoyer une demande à l'Administrateur</h2>
    <form method="POST" action="">
        <textarea name="demande" placeholder="Tapez votre demande ici..." required></textarea>
        <button type="submit">Envoyer la demande</button>
    </form>
</footer>

</body>
</html>
