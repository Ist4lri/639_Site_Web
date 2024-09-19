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

// Filtrage pour la tab "Members"
$searchUser = isset($_GET['search_user']) ? trim($_GET['search_user']) : '';
$userQuery = "SELECT id, nom FROM utilisateurs";
if (!empty($searchUser)) {
    $userQuery .= " WHERE nom LIKE ?";
    $stmt = $pdo->prepare($userQuery);
    $stmt->execute(['%' . $searchUser . '%']);
} else {
    $stmt = $pdo->query($userQuery);
}
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Filtrage pour la tab "Plaintes"
$searchPlainteUser = isset($_GET['search_plainte_user']) ? trim($_GET['search_plainte_user']) : '';
$searchPlainteStatus = isset($_GET['search_plainte_status']) ? trim($_GET['search_plainte_status']) : '';
$plainteQuery = "SELECT p.id, u.nom AS utilisateur, p.plainte, p.status, p.date_creation FROM plaintes p JOIN utilisateurs u ON p.id_utilisateur = u.id WHERE 1=1";
$plainteParams = [];

if (!empty($searchPlainteUser)) {
    $plainteQuery .= " AND u.nom LIKE ?";
    $plainteParams[] = '%' . $searchPlainteUser . '%';
}

if (!empty($searchPlainteStatus)) {
    $plainteQuery .= " AND p.status = ?";
    $plainteParams[] = $searchPlainteStatus;
}

$plaintesStmt = $pdo->prepare($plainteQuery);
$plaintesStmt->execute($plainteParams);
$plaintes = $plaintesStmt->fetchAll(PDO::FETCH_ASSOC);

// Filtrage pour la tab "Demandes"
$searchDemandeUser = isset($_GET['search_demande_user']) ? trim($_GET['search_demande_user']) : '';
$searchDemandeStatus = isset($_GET['search_demande_status']) ? trim($_GET['search_demande_status']) : '';
$demandeQuery = "SELECT d.id, u.nom AS utilisateur, d.demande, d.status FROM demande d JOIN utilisateurs u ON d.id_utilisateurs = u.id WHERE 1=1";
$demandeParams = [];

if (!empty($searchDemandeUser)) {
    $demandeQuery .= " AND u.nom LIKE ?";
    $demandeParams[] = '%' . $searchDemandeUser . '%';
}

if (!empty($searchDemandeStatus)) {
    $demandeQuery .= " AND d.status = ?";
    $demandeParams[] = $searchDemandeStatus;
}

$pendingStmt = $pdo->prepare($demandeQuery);
$pendingStmt->execute($demandeParams);
$pendingDemandes = $pendingStmt->fetchAll(PDO::FETCH_ASSOC);

// Action sur "Accepter" ou "Rejeter" les demandes
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && in_array($_POST['action'], ['accepter', 'rejeter'])) {
    $id_demande = $_POST['id_demande'];
    $action = $_POST['action'] == 'accepter' ? 'Accepter' : 'Rejeter';

    $updateStmt = $pdo->prepare("UPDATE demande SET status = ? WHERE id = ?");
    $updateStmt->execute([$action, $id_demande]);

    // Redirection après action pour éviter la répétition
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}

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



    <h1 class=title>Bienvenue, membre de l'Officio Prefectus,</h1>
<h2 class=title>Vous avez franchi les portes de l'un des ordres les plus respectés et redoutés de l'Imperium. En rejoignant nos rangs, vous devenez une pièce essentielle de la machine de l'Empereur, une sentinelle vigilante contre le chaos et l'hérésie.<br>
Ici, sous l'étendard de l'ordre et de la discipline, nous veillons à ce que l'autorité impériale soit respectée, à chaque instant, dans chaque secteur. Votre dévouement à la loi impériale et votre loyauté inébranlable envers le Trône d'Or seront votre guide.<br>
Les devoirs qui vous attendent sont nombreux, les responsabilités immenses. Mais sachez ceci : vous ne marchez pas seul.<br> 
    Derrière vous, l'ombre du passé glorieux de l'Officio Prefectus et à vos côtés, vos frères et sœurs d'armes, prêts à défendre l'Imperium à tout prix.<br>
Qu'aucune faiblesse ne ternisse votre âme et que la lumière de l'Empereur vous éclaire dans chaque décision.<br>

À partir de cet instant, vous êtes plus qu'un soldat, vous êtes un gardien du futur de l'humanité.</h2>
<div class="container">
    <?php if (!empty($message)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <h2 class="tab-title" onclick="showTabContent('members')">Fichier de L'astra Militarum</h2>
    <h2 class="tab-title" onclick="showTabContent('plaintes')">Gestion des Plaintes</h2>
    <h2 class="tab-title" onclick="showTabContent('demandes')">Gestion des Demandes</h2>

    <!-- Members Section -->
    <div class="tab-content" id="members" style="display: none;">
        <form method="get">
            <input type="text" name="search_user" placeholder="Rechercher par nom" value="<?php echo htmlspecialchars($searchUser); ?>">
            <button type="submit">Rechercher</button>
        </form>
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
                            <button type="submit" name="view_pdf" class="btn-view-pdf">PDF</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Plaintes Section -->
    <div class="tab-content" id="plaintes" style="display: none;">
        <form method="get">
            <input type="text" name="search_plainte_user" placeholder="Rechercher par nom" value="<?php echo htmlspecialchars($searchPlainteUser); ?>">
            <input type="text" name="search_plainte_status" placeholder="Rechercher par statut" value="<?php echo htmlspecialchars($searchPlainteStatus); ?>">
            <button type="submit">Rechercher</button>
        </form>
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
                            <button type="submit" name="action" value="lu" class="btn-success">Marquer comme lu</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Demandes Section -->
    <div class="tab-content" id="demandes" style="display: none;">
        <form method="get">
            <input type="text" name="search_demande_user" placeholder="Rechercher par nom" value="<?php echo htmlspecialchars($searchDemandeUser); ?>">
            <input type="text" name="search_demande_status" placeholder="Rechercher par statut" value="<?php echo htmlspecialchars($searchDemandeStatus); ?>">
            <button type="submit">Rechercher</button>
        </form>
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
                        <form action="officio.php" method="post" style="display:inline;">
                            <input type="hidden" name="id_demande" value="<?php echo $demande['id']; ?>">
                            <button type="submit" name="action" value="accepter" class="btn-success">Accepter</button>
                            <button type="submit" name="action" value="rejeter" class="btn-danger">Rejeter</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
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
