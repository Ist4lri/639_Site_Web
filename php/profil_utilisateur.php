<?php
session_start();

if (!isset($_SESSION['utilisateur'])) {
    header("Location: connection.php");
    exit();
}

include 'db.php';

$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = :email");
$stmt->execute(['email' => $_SESSION['utilisateur']]);
$utilisateur = $stmt->fetch();

$stmt = $pdo->prepare("SELECT u.*, s.nom AS specialite_nom FROM utilisateurs u LEFT JOIN spe s ON u.spe_id = s.id WHERE u.email = :email");
$stmt->execute(['email' => $_SESSION['utilisateur']]);
$utilisateur = $stmt->fetch();

if (!$utilisateur) {
    echo "Erreur: Utilisateur introuvable.";
    exit();
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['demande']) && !empty($_POST['demande'])) {
        $demandeText = $_POST['demande'];
        
        // Insertion de la demande dans la table demande
        $stmt = $pdo->prepare("INSERT INTO demande (id_utilisateurs, demande, status) VALUES (:id_utilisateurs, :demande, 'en attente')");
        $stmt->execute([
            ':id_utilisateurs' => $utilisateur['id'],
            ':demande' => $demandeText,
        ]);
        
        $message = "Votre demande a été soumise.";
        
        // Redirect to the same page to avoid form resubmission
        header("Location: profil_utilisateur.php");
        exit();
    }
    
    if (isset($_POST['nouveau_nom']) && !empty($_POST['nouveau_nom'])) {
        $nouveauNom = $_POST['nouveau_nom'];
        $stmt = $pdo->prepare("UPDATE utilisateurs SET nom = :nouveau_nom WHERE id = :id");
        $stmt->execute([
            ':nouveau_nom' => $nouveauNom,
            ':id' => $utilisateur['id']
        ]);
        $message = "Votre nom a été mis à jour.";
        
        // Redirect to the same page to avoid form resubmission
        header("Location: profil_utilisateur.php");
        exit();
    }
    
    if (isset($_POST['nouvel_email']) && !empty($_POST['nouvel_email'])) {
        $nouvelEmail = $_POST['nouvel_email'];
        $stmt = $pdo->prepare("UPDATE utilisateurs SET email = :nouvel_email WHERE id = :id");
        $stmt->execute([
            ':nouvel_email' => $nouvelEmail,
            ':id' => $utilisateur['id']
        ]);
        $_SESSION['utilisateur'] = $nouvelEmail; // Mettre à jour l'email dans la session
        $message = "Votre email a été mis à jour.";
        
        // Redirect to the same page to avoid form resubmission
        header("Location: profil_utilisateur.php");
        exit();
    }
}

// Récupérer les demandes de l'utilisateur en attente et acceptées
$pendingStmt = $pdo->prepare("SELECT * FROM demande WHERE id_utilisateurs = :id AND status = 'en attente'");
$pendingStmt->execute(['id' => $utilisateur['id']]);
$demandesEnAttente = $pendingStmt->fetchAll(PDO::FETCH_ASSOC);

$acceptedStmt = $pdo->prepare("SELECT * FROM demande WHERE id_utilisateurs = :id AND status = 'acceptée'");
$acceptedStmt->execute(['id' => $utilisateur['id']]);
$demandesAcceptees = $acceptedStmt->fetchAll(PDO::FETCH_ASSOC);

$excel_file_path = "../excel/planning_utilisateurs.xlsx";

?>
<?php include 'header.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Utilisateur</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Styles pour la page */
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
        input[type="password"],
        textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        select {
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
        <p><strong>Nom :</strong> <?php echo htmlspecialchars($utilisateur['nom']); ?></p>
        <p><strong>Email :</strong> <?php echo htmlspecialchars($utilisateur['email']); ?></p>
        <p><strong>Grade :</strong> <?php echo htmlspecialchars($utilisateur['grade']); ?></p>
        <p><strong>Spécialité :</strong> <?php echo htmlspecialchars($utilisateur['specialite_nom']); ?></p>
    </div>

    <!-- Formulaire de mise à jour -->
    <div class="update-form">
        <h3>Soumettre une demande</h3>
        <?php if ($message): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form action="profil_utilisateur.php" method="post">
            <div>
                <label for="demande">Votre demande :</label>
                <textarea id="demande" name="demande" rows="4" required></textarea>
            </div>
            <div>
                <input type="submit" value="Soumettre la demande">
            </div>
        </form>

        <!-- Formulaire de changement de nom -->
        <form action="profil_utilisateur.php" method="post">
            <div>
                <label for="nouveau_nom">Nouveau nom :</label>
                <input type="text" id="nouveau_nom" name="nouveau_nom" value="<?php echo htmlspecialchars($utilisateur['nom']); ?>" required>
            </div>
            <div>
                <input type="submit" value="Mettre à jour le nom">
            </div>
        </form>

        <!-- Formulaire de changement d'email -->
        <form action="profil_utilisateur.php" method="post">
            <div>
                <label for="nouvel_email">Nouvel email :</label>
                <input type="email" id="nouvel_email" name="nouvel_email" value="<?php echo htmlspecialchars($utilisateur['email']); ?>" required>
            </div>
            <div>
                <input type="submit" value="Mettre à jour l'email">
            </div>
        </form>
    </div>
</div>

<!-- Lien pour télécharger le fichier Excel -->
<div class="excel-download">
    <?php if (file_exists($excel_file_path)): ?>
        <p><a href="<?php echo $excel_file_path; ?>" download>Télécharger le planning des utilisateurs (Excel)</a></p>
    <?php else: ?>
        <p>Aucun fichier Excel disponible.</p>
    <?php endif; ?>
</div>

<div class="demandes-section">
    <h3>Demandes en attente</h3>
    <ul>
        <?php foreach ($demandesEnAttente as $demande): ?>
            <li><?php echo htmlspecialchars($demande['demande']); ?> (En attente)</li>
        <?php endforeach; ?>
    </ul>

    <h3>Demandes acceptées</h3>
    <ul>
        <?php foreach ($demandesAcceptees as $demande): ?>
            <li><?php echo htmlspecialchars($demande['demande']); ?> (Acceptée)</li>
        <?php endforeach; ?>
    </ul>
</div>

</body>
</html>
