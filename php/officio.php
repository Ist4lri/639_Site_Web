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

$factionStmt = $pdo->prepare("SELECT * FROM personnages WHERE id_utilisateur = :id_utilisateur AND faction = 'Officio Prefectus' AND validation = 'Accepter'");
$factionStmt->execute(['id_utilisateur' => $currentUser['id']]);
$faction = $factionStmt->fetch();

$stmt = $pdo->prepare("SELECT plainte, status, date_creation FROM plaintes WHERE id_utilisateur = :id_utilisateur ORDER BY date_creation DESC");
$stmt->execute(['id_utilisateur' => $currentUser['id']]);
$plaintes = $stmt->fetchAll(PDO::FETCH_ASSOC);

 $plaintesStmt = $pdo->query("SELECT p.id, u.nom AS utilisateur, p.plainte, p.status, p.date_creation 
                                            FROM plaintes p 
                                            JOIN utilisateurs u ON p.id_utilisateur = u.id
                                            WHERE p.status = 'Attente'");
                $plaintes = $plaintesStmt->fetchAll(PDO::FETCH_ASSOC);


$pendingStmt = $pdo->query("SELECT d.id, u.nom AS utilisateur, d.demande, d.status 
                            FROM demande d 
                            JOIN utilisateurs u ON d.id_utilisateurs = u.id
                            WHERE d.status = 'en attente'");
$pendingDemandes = $pendingStmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT id, nom FROM utilisateurs");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);



?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Officio Prefectus</title>
    <link rel="stylesheet" href="../css/officio.css">
</head>
<body>

<div class="container">
    <?php if (!empty($message)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <?php if ($faction): ?>
        <!-- Titles for Tabs -->
        <h2 class="tab-title" onclick="showTabContent('members')">Membres de l'Officio Prefectus</h2>
        <h2 class="tab-title" onclick="showTabContent('plaintes')">Gestion des Plaintes</h2>
        <h2 class="tab-title" onclick="showTabContent('demandes')">Gestion des Demandes</h2>

        <!-- Members Section -->
        <div class="tab-content" id="members" style="display: none;">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['nom']); ?></td>
                        <td>
                            <form action="afficher_info.php" method="post" style="display:inline;" target="_blank">
                                <input type="hidden" name="id_utilisateur" value="<?php echo htmlspecialchars($user['id']); ?>">
                                <button type="submit" name="view_pdf" class="btn btn-view-pdf">PDF</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Plaintes Section -->
        <div class="tab-content" id="plaintes" style="display: none;">
            <h2>Gestion des Plaintes</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Utilisateur</th>
                        <th>Plainte</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($plaintes as $plainte): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($plainte['utilisateur']); ?></td>
                        <td><?php echo htmlspecialchars($plainte['plainte']); ?></td>
                        <td><?php echo htmlspecialchars($plainte['status'] ?? 'En attente'); ?></td>
                        <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($plainte['date_creation']))); ?></td>
                        <td>
                            <form action="officio.php" method="post" style="display:inline;">
                                <input type="hidden" name="id_plainte" value="<?php echo $plainte['id']; ?>">
                                <button type="submit" name="action" value="lu" class="btn btn-success">Marquer comme lu</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Demandes Section -->
        <div class="tab-content" id="demandes" style="display: none;">
            <h2>Gestion des Demandes</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Utilisateur</th>
                        <th>Demande</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pendingDemandes as $demande): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($demande['utilisateur']); ?></td>
                        <td><?php echo htmlspecialchars($demande['demande']); ?></td>
                        <td><?php echo htmlspecialchars($demande['status'] ?? 'en attente'); ?></td>
                        <td>
                            <form action="demande.php" method="post" style="display:inline;">
                                <input type="hidden" name="id_demande" value="<?php echo $demande['id']; ?>">
                                <button type="submit" name="action" value="accepter" class="btn btn-success">Accepter</button>
                                <button type="submit" name="action" value="rejeter" class="btn btn-danger">Rejeter</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <!-- Si l'utilisateur n'est pas dans la faction "Officio Prefectus" -->
        <h3>Souhaitez-vous envoyer une plainte ?</h3>
        <form action="officio.php" method="post">
            <textarea name="plainte" required placeholder="Votre plainte"></textarea>
            <button type="submit" class="btn btn-primary">Envoyer la plainte</button>
        </form>
    <?php endif; ?>
</div>

<script>
function showTabContent(tabId) {
    var tabContents = document.getElementsByClassName('tab-content');
    for (var i = 0; i < tabContents.length; i++) {
        tabContents[i].style.display = 'none';
    }

    // Affiche le contenu de l'onglet sélectionné
    document.getElementById(tabId).style.display = 'block';
}
</script>

</body>
</html>

