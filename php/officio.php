<?php
session_start();
include 'db.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['utilisateur'])) {
    header("Location: connection.php");
    exit();
}

// Récupération de l'utilisateur actuel
$stmt = $pdo->prepare("SELECT id, nom, spe_id FROM utilisateurs WHERE email = :email");
$stmt->execute(['email' => $_SESSION['utilisateur']]);
$currentUser = $stmt->fetch();

if (!$currentUser) {
    echo "Utilisateur non trouvé.";
    exit();
}

$message = '';

// Si un utilisateur souhaite envoyer une plainte
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['plainte']) && !empty($_POST['plainte'])) {
    $plainteText = trim($_POST['plainte']);

    if (!empty($plainteText)) {
        $stmt = $pdo->prepare("INSERT INTO plaintes (id_utilisateur, plainte, status, date_creation) VALUES (?, ?, 'Attente', NOW())");
        $stmt->execute([$currentUser['id'], $plainteText]);
        $message = "Votre plainte a été soumise avec succès.";
        header("Location: officio.php");
        exit();
    } else {
        $message = "La plainte ne peut pas être vide.";
    }
}

// Vérification si l'utilisateur est dans la faction "Officio Prefectus"
$factionStmt = $pdo->prepare("SELECT * FROM personnages WHERE id_utilisateur = :id_utilisateur AND faction = 'Officio Prefectus' AND validation = 'Accepter'");
$factionStmt->execute(['id_utilisateur' => $currentUser['id']]);
$faction = $factionStmt->fetch();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Officio Prefectus</title>
    <link rel="stylesheet" href="../css/officio.css">
</head>
     <style>
        .complaint-form-container {
            margin-top: 120px; /* Espace de 120px par rapport au haut de la page */
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #1c1c1e; /* Darker background color to match the style */
            padding: 30px;
            border-radius: 10px;
            max-width: 80%;
            margin-left: auto;
            margin-right: auto;
        }

        /* Style for the textarea */
        textarea {
            width: 100%;
            max-width: 1200px;
            height: 150px;
            padding: 15px;
            margin-top: 20px;
            font-size: 16px;
            border: 2px solid #0056b3; /* Border color to match the button hover state */
            border-radius: 5px;
            background-color: #2c2c2e; /* Darker background for textarea */
            color: white; /* White text for better visibility */
        }

        /* Button styling */
        .btn-primary {
            margin-top: 20px;
            padding: 12px 25px;
            background-color: #007bff; /* Primary button color */
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3; /* Hover effect for button */
        }

        /* Body background */
        body {
            background-color: #0f0f10; /* Dark background for the whole page */
            color: #fff; /* White text for contrast */
            font-family: 'Arial', sans-serif;
        }
    </style>
<body>
<?php if ($faction): ?>
        <!-- Si l'utilisateur est dans la faction "Officio Prefectus" -->
        <?php include 'headero.php'; ?>    
    <h1 class="title">Bienvenue, membre de l'Officio Prefectus,</h1>
    <h2 class="title">
        Vous avez franchi les portes de l'un des ordres les plus respectés et redoutés de l'Imperium.<br>
        En rejoignant nos rangs, vous devenez une pièce essentielle de la machine de l'Empereur,<br>
        une sentinelle vigilante contre le chaos et l'hérésie.<br>
        Ici, sous l'étendard de l'ordre et de la discipline,<br>
        nous veillons à ce que l'autorité impériale soit respectée, à chaque instant, dans chaque secteur.<br>
        Votre dévouement à la loi impériale et votre loyauté inébranlable envers le Trône d'Or seront votre guide.<br>
        Qu'aucune faiblesse ne ternisse votre âme et que la lumière de l'Empereur vous éclaire dans chaque décision.<br>
        À partir de cet instant, vous êtes plus qu'un soldat, vous êtes un gardien du futur de l'humanité.
    </h2>

    
    <?php else: ?>
        <!-- Si l'utilisateur n'est pas dans la faction "Officio Prefectus" -->
        <?php include 'header.php'; ?>
        <div class="complaint-form-container">
    <h3>Souhaitez-vous envoyer une plainte ?</h3>
    <form action="officio.php" method="post">
        <textarea name="plainte" required placeholder="Votre plainte"></textarea>
        <button type="submit" class="btn-primary">Envoyer la plainte</button>
    </form>
</div>
    <?php endif; ?>

</body>
</html>
