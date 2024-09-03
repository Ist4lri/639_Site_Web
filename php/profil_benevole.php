<?php
session_start();

if (!isset($_SESSION['utilisateur'])) {
    header("Location: connection.php");
    exit();
}

// Connexion à la base de données
$host = '51.210.180.94';
$dbname = 'nmw2';
$username = 'root';
$password = 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}


$stmt = $pdo->prepare("SELECT * FROM benevoles WHERE email = :email");
$stmt->execute(['email' => $_SESSION['utilisateur']]);
$benevole = $stmt->fetch();

if (!$benevole) {
    echo "Erreur: Bénévole introuvable.";
    exit();
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $telephone = $_POST['telephone'];
    $adresse = $_POST['adresse'];

    $stmt = $pdo->prepare("UPDATE benevoles SET nom = :nom, prenom = :prenom, telephone = :telephone, adresse = :adresse WHERE email = :email");
    $stmt->execute([
        ':nom' => $nom,
        ':prenom' => $prenom,
        ':telephone' => $telephone,
        ':adresse' => $adresse,
        ':email' => $_SESSION['utilisateur']
    ]);
}

$excel_file_path = "../excel/planning_benevoles.xlsx";

?>
<?php include 'header.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Bénévole</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .profile-container {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin-top: 20px;
        }

        .profile-container .current-info,
        .profile-container .update-form {
            flex: 1;
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .current-info h3,
        .update-form h3 {
            margin-bottom: 15px;
            color: #333;
            font-size: 1.5rem;
            text-align: center;
        }

        .current-info p {
            margin-bottom: 10px;
            font-size: 14px;
            color: #555;
        }

        label {
            display: block;
            text-align: left;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
            font-size: 14px;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="password"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        input[type="submit"] {
            background-color: #28a745;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }

        input[type="submit"]:hover {
            background-color: #218838;
        }

        .excel-download {
            text-align: center;
            margin-top: 20px;
        }

        .excel-download a {
            text-decoration: none;
            color: #007bff;
            font-size: 16px;
        }

        .excel-download a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="profile-container">
    <!-- Informations actuelles -->
    <div class="current-info">
        <h3>Informations actuelles</h3>
        <p><strong>Nom :</strong> <?php echo htmlspecialchars($benevole['nom']); ?></p>
        <p><strong>Prénom :</strong> <?php echo htmlspecialchars($benevole['prenom']); ?></p>
        <p><strong>Téléphone :</strong> <?php echo htmlspecialchars($benevole['telephone']); ?></p>
        <p><strong>Adresse :</strong> <?php echo htmlspecialchars($benevole['adresse']); ?></p>
    </div>

    <!-- Formulaire de mise à jour -->
    <div class="update-form">
        <h3>Mettre à jour les informations</h3>
        <form action="profil_benevole.php" method="post">
            <div>
                <label for="nom">Nom :</label>
                <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($benevole['nom']); ?>" required>
            </div>
            <div>
                <label for="prenom">Prénom :</label>
                <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($benevole['prenom']); ?>" required>
            </div>
            <div>
                <label for="telephone">Téléphone :</label>
                <input type="text" id="telephone" name="telephone" value="<?php echo htmlspecialchars($benevole['telephone']); ?>" required>
            </div>
            <div>
                <label for="adresse">Adresse :</label>
                <input type="text" id="adresse" name="adresse" value="<?php echo htmlspecialchars($benevole['adresse']); ?>" required>
            </div>
            <div>
                <input type="submit" value="Mettre à jour">
            </div>
        </form>
    </div>
</div>

<!-- Lien pour télécharger le fichier Excel -->
<div class="excel-download">
    <?php if (file_exists($excel_file_path)): ?>
        <p><a href="<?php echo $excel_file_path; ?>" download>Télécharger le planning des bénévoles (Excel)</a></p>
    <?php else: ?>
        <p>Aucun fichier Excel disponible.</p>
    <?php endif; ?>
</div>

</body>
</html>
