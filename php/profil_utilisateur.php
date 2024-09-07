<?php
session_start();

if (!isset($_SESSION['utilisateur'])) {
    header("Location: connection.php");
    exit();
}

include 'db.php';

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

        $stmt = $pdo->prepare("INSERT INTO demande (id_utilisateurs, demande, status) VALUES (:id_utilisateurs, :demande, 'en attente')");
        $stmt->execute([
            ':id_utilisateurs' => $utilisateur['id'],
            ':demande' => $demandeText,
        ]);

        $message = "Votre demande a été soumise.";
    }
}

$pendingStmt = $pdo->prepare("SELECT * FROM demande WHERE id_utilisateurs = :id AND status = 'en attente'");
$pendingStmt->execute(['id' => $utilisateur['id']]);
$demandesEnAttente = $pendingStmt->fetchAll(PDO::FETCH_ASSOC);

$acceptedStmt = $pdo->prepare("SELECT * FROM demande WHERE id_utilisateurs = :id AND status = 'acceptée'");
$acceptedStmt->execute(['id' => $utilisateur['id']]);
$demandesAcceptees = $acceptedStmt->fetchAll(PDO::FETCH_ASSOC);

$excel_file_path = "../excel/planning_utilisateurs.xlsx";
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Utilisateur</title>
    <link rel="stylesheet" href="../css/profil.css">
    <link rel="stylesheet" href="../css/header.css">
</head>
<body>

<header class="head">
    <div class="head-logo">
        <a href="../index.php">
            <img src="../src/assets/Logo_639th_2.ico" alt="Logo 639">
        </a>
        <span class="head-username">Bonjour, <?php echo htmlspecialchars($utilisateur['nom']); ?></span>
    </div>
    <div class="head-title">
        <h1>639ème Régiment Cadien</h1>
    </div>
    <nav class="head-nav">
        <a href="officier.php">Officier</a>
        <a href="sous-officier.php">Sous-Officier</a>
        <a href="Dec.php">Déconnexion</a>
    </nav>
</header>

<div class="profile-container">
    <div class="current-info">
        <h3>Informations actuelles</h3>
        <p><strong>Nom :</strong> <?php echo htmlspecialchars($utilisateur['nom']); ?></p>
        <p><strong>Email :</strong> <?php echo htmlspecialchars($utilisateur['email']); ?></p>
        <p><strong>Grade :</strong> <?php echo htmlspecialchars($utilisateur['grade']); ?></p>
        <p><strong>Spécialité :</strong> <?php echo htmlspecialchars($utilisateur['specialite_nom']); ?></p>
    </div>

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
    </div>

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
</div>

</body>
</html>
