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

$isLoggedIn = isset($_SESSION['utilisateur']);
$userName = $isLoggedIn ? $_SESSION['nom_utilisateur'] : '';

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
        header("Location: profil_utilisateur.php");
        exit();
    }

$speStmt = $pdo->prepare("SELECT id, nom FROM spe WHERE total IS NOT NULL");
$speStmt->execute();
$specialites = $speStmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'demande_spe') {
    if (isset($_POST['specialite_id']) && !empty($_POST['specialite_id'])) {
        $specialite_id = $_POST['specialite_id'];

        $stmt = $pdo->prepare("INSERT INTO demande_spe (spe_id, utilisateur_id, demande) VALUES (:spe_id, :utilisateur_id, 'Attente')");
        $stmt->execute([
            ':spe_id' => $specialite_id,
            ':utilisateur_id' => $utilisateur['id'],
        ]);

        $message = "Votre demande de spécialité a été soumise avec succès.";
        header("Location: profil_utilisateur.php");
        exit();
    } else {
        $message = "Veuillez sélectionner une spécialité.";
    }
}


    
    if (isset($_POST['nouveau_nom']) && !empty($_POST['nouveau_nom'])) {
        $nouveauNom = $_POST['nouveau_nom'];
        $stmt = $pdo->prepare("UPDATE utilisateurs SET nom = :nouveau_nom WHERE id = :id");
        $stmt->execute([
            ':nouveau_nom' => $nouveauNom,
            ':id' => $utilisateur['id']
        ]);
        $message = "Votre nom a été mis à jour.";
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
        $_SESSION['utilisateur'] = $nouvelEmail;
        $message = "Votre email a été mis à jour.";
        header("Location: profil_utilisateur.php");
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'update_histoire') {
    if (!empty($_POST['histoire'])) {
        $nouvelleHistoire = $_POST['histoire'];

        // Mettre à jour l'histoire dans la base de données
        $stmt = $pdo->prepare("UPDATE utilisateurs SET histoire = :histoire WHERE id = :id");
        $stmt->execute([
            ':histoire' => $nouvelleHistoire,
            ':id' => $utilisateur['id']
        ]);

        // Message de succès
        $message = "Votre histoire a été mise à jour avec succès.";

        // Redirection pour éviter le resoumission du formulaire
        header("Location: profil_utilisateur.php");
        exit();
    } else {
        $message = "L'histoire ne peut pas être vide.";
    }
}

$pendingStmt = $pdo->prepare("SELECT * FROM demande WHERE id_utilisateurs = :id AND status = 'en attente'");
$pendingStmt->execute(['id' => $utilisateur['id']]);
$demandesEnAttente = $pendingStmt->fetchAll(PDO::FETCH_ASSOC);

$acceptedStmt = $pdo->prepare("SELECT * FROM demande WHERE id_utilisateurs = :id AND status = 'accepter'");
$acceptedStmt->execute(['id' => $utilisateur['id']]);
$demandesAcceptees = $acceptedStmt->fetchAll(PDO::FETCH_ASSOC);

$personnagesStmt = $pdo->prepare("SELECT id, nom, validation FROM personnages WHERE id_utilisateur = :id_utilisateur");
$personnagesStmt->execute(['id_utilisateur' => $utilisateur['id']]);
$personnages = $personnagesStmt->fetchAll(PDO::FETCH_ASSOC);

$excel_file_path = "../excel/planning_utilisateurs.xlsx";


?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Utilisateur</title>
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/profil.css">

</head>
<body class="head">

<header class="head">
    <div class="head-logo">
        <a href="../index.php">
            <img src="../src/assets/Logo.png" alt="Logo 639">
        </a>
    <span class="head-username">Bonjour, <?php echo htmlspecialchars($userName); ?></span>
    </div>
    <div class="head-logo2">
        <img src="../src/assets/TitreSite.png" alt="639 Régiment cadien"
    </div>
    <nav class="head-nav">
            <a href="perso.php">Créé vos personnages</a>
            <a href="officier.php">Officier</a>
            <a href="sous-officier.php">Sous-Officier</a>
            <a href="Dec.php">Déconnexion</a>
    </nav>
</header>
<body>

<div class="profile-container">
    <div class="current-info">
        <h3>Informations actuelles</h3>
        <p><strong>Nom :</strong> <?php echo htmlspecialchars($utilisateur['nom']); ?></p>
        <p><strong>Email :</strong> <?php echo htmlspecialchars($utilisateur['email']); ?></p>
        <p><strong>Grade :</strong> <?php echo htmlspecialchars($utilisateur['grade']); ?></p>
        <p><strong>Spécialité :</strong> <?php echo htmlspecialchars($utilisateur['specialite_nom']); ?></p>
        <button class="btn" onclick="toggleHistoireForm()">Modifier Histoire</button>
<a href="affiche_u.php?id=<?php echo $utilisateur['id']; ?>" target="_blank">
    <button class="btn">PDF</button>
</a>

<div id="histoire-form" style="display:none; margin-top: 20px;">
    <form action="profil_utilisateur.php" method="post">
        <label for="histoire">Modifier votre histoire :</label>
        <textarea id="histoire" name="histoire" rows="4" style="width: 100%;" required><?php echo htmlspecialchars($utilisateur['histoire'] ?? ''); ?></textarea>
        <br>
        <input type="hidden" name="action" value="update_histoire">
        <input type="submit" value="Mettre à jour l'histoire" class="btn">
    </form>
</div>
    </div>

    <div class="update-form">
        <h3>Soumettre une demande</h3>
        <?php if ($message): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form action="profil_utilisateur.php" method="post">
            <div>
                <label for="demande">Votre demande :</label>
                <textarea id="demande" name="demande" rows="4" required> Nom de l'arme:
Raison: 
                </textarea>

            </div>
            <div>
                <input type="submit" value="Soumettre la demande">
            </div>
        </form>

        <form action="profil_utilisateur.php" method="post">
            <div>
                <label for="nouveau_nom">Nouveau nom :</label>
                <input type="text" id="nouveau_nom" name="nouveau_nom" value="<?php echo htmlspecialchars($utilisateur['nom']); ?>" required>
            </div>
            <div>
                <input type="submit" value="Mettre à jour le nom">
            </div>
        </form>

        <form action="profil_utilisateur.php" method="post">
            <div>
                <label for="nouvel_email">Nouvel email :</label>
                <input type="email" id="nouvel_email" name="nouvel_email" value="<?php echo htmlspecialchars($utilisateur['email']); ?>" required>
            </div>
            <div>
                <input type="submit" value="Mettre à jour l'email">
            </div>
        </form>
        <div class="specialite-request-form">
    <?php if ($message): ?>
        <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form action="profil_utilisateur.php" method="post">
        <label for="specialite_id">Choisissez une spécialité :</label>
        <select name="specialite_id" id="specialite_id" required>
            <option value="">-- Sélectionnez une spécialité --</option>
            <option value="1">Machine Gunner</option>
            <option value="2">Anti-Tank</option>
            <option value="3">Medicae</option>
            <option value="4">Vox Operator</option>
            <option value="5">Marksman</option>
            <option value="6">Plasma</option>
            <option value="7">Breacher</option>
            <option value="8">ETL</option>
            <option value="9">Fusilier</option>
        </select>
        <input type="hidden" name="action" value="demande_spe">
        <div style="margin-top: 20px;">
            <input type="submit" value="Soumettre la demande" class="btn">
        </div>
    </form>
</div>

</div>
</div>

<div class="excel-download">
    <?php if (file_exists($excel_file_path)): ?>
        <p><a href="<?php echo $excel_file_path; ?>" download>Télécharger le planning des utilisateurs (Excel)</a></p>
    <?php else: ?>
        <p>Aucun fichier Excel disponible.</p>
    <?php endif; ?>
</div>

<!-- Afficher les personnages de l'utilisateur -->
<div class="personnages-section">
    <h3>Vos personnages</h3>
    <ul>
        <?php foreach ($personnages as $perso): ?>
            <li>
                <?php 
            $nom = htmlspecialchars($perso['nom'] ?? 'Nom non disponible'); 
            $validation = htmlspecialchars($perso['validation'] ?? '{Non validé}');
            echo "$nom  {'$validation'}";
        ?>
                <a href="affiche_perso.php?id=<?= $perso['id']; ?>" target="_blank">
                    <button class="btn">Afficher</button>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
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

    <script>
    function toggleHistoireForm() {
        var form = document.getElementById("histoire-form");
        if (form.style.display === "none" || form.style.display === "") {
            form.style.display = "block"; // Affiche le formulaire
        } else {
            form.style.display = "none"; // Masque le formulaire
        }
    }
</script>

    

</body>
</html>
