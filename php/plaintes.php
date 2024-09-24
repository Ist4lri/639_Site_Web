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

// Action sur "Accepter", "Rejeter" ou "Lu"
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    if ($_POST['action'] === 'accepter' || $_POST['action'] === 'rejeter') {
        // Gestion des demandes
        $id_demande = $_POST['id_demande'];
        $action = $_POST['action'] === 'accepter' ? 'Accepter' : 'Rejeter';

        $updateStmt = $pdo->prepare("UPDATE demande SET status = ? WHERE id = ?");
        $updateStmt->execute([$action, $id_demande]);

        // Redirection après action pour éviter la répétition
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit();
    } elseif ($_POST['action'] === 'lu') {
        // Gestion des plaintes
        $id_plainte = $_POST['id_plainte'];

        // Met à jour le statut de la plainte à "Lu"
        $updateStmt = $pdo->prepare("UPDATE plaintes SET status = 'Lu' WHERE id = ?");
        if ($updateStmt->execute([$id_plainte])) {
            echo "Plainte marquée comme lue avec succès.";
        } else {
            $errorInfo = $updateStmt->errorInfo();
            echo "Échec de la mise à jour : " . $errorInfo[2];
        }

        // Redirection après action pour éviter la répétition
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit();
    }
}

// Traitement pour soumettre une plainte
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['plainte']) && !empty($_POST['plainte'])) {
    $plainteText = trim($_POST['plainte']);

    if (!empty($plainteText)) {
        $stmt = $pdo->prepare("INSERT INTO plaintes (id_utilisateur, plainte, status, date_creation) VALUES (?, ?, 'Attente', NOW())");
        $stmt->execute([$currentUser['id'], $plainteText]);
        $message = "Votre plainte a été soumise avec succès.";
        header("Location: plaintes.php");
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

    <?php include 'headero.php' ?>

<?php if (!empty($message)): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<!-- Plaintes Section -->
<h2>Gestion des Plaintes</h2>
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
                <form action="plaintes.php" method="post" style="display:inline;">
                    <input type="hidden" name="id_plainte" value="<?php echo $plainte['id']; ?>">
                    <button type="submit" name="action" value="lu" class="btn-success">Marquer comme lu</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Demandes Section -->
<h2>Gestion des Demandes</h2>
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
                <form action="plaintes.php" method="post" style="display:inline;">
                    <input type="hidden" name="id_demande" value="<?php echo $demande['id']; ?>">
                    <button type="submit" name="action" value="accepter" class="btn-success">Accepter</button>
                    <button type="submit" name="action" value="rejeter" class="btn-danger">Rejeter</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
