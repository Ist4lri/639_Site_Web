<?php
session_start();
include 'db.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['utilisateur'])) {
    header("Location: connection.php");
    exit();
}

// Récupérer l'utilisateur actuel et vérifier les permissions
$stmt = $pdo->prepare("SELECT id, spe_id, grade FROM utilisateurs WHERE email = :email");
$stmt->execute(['email' => $_SESSION['utilisateur']]);
$currentUser = $stmt->fetch();

// Vérifier si l'utilisateur est de la faction Officio Prefectus et a un personnage avec validation acceptée
if ($currentUser['spe_id'] != 3) {
    echo "<h3>Souhaitez-vous envoyer une plainte ?</h3>";
    echo "<form action='plaintes.php' method='post'>
            <textarea name='plainte' required placeholder='Votre plainte'></textarea>
            <button type='submit' class='btn btn-primary'>Envoyer la plainte</button>
          </form>";
    exit();
}

// Récupérer tous les utilisateurs de la faction Officio Prefectus
$stmt = $pdo->prepare("SELECT id, nom FROM utilisateurs WHERE spe_id = :spe_id");
$stmt->execute(['spe_id' => 3]);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Officio Prefectus - Membres</title>
    <link rel="stylesheet" href="../css/med.css">
</head>
<body>

<div class="container">
    <h2>Membres de l'Officio Prefectus</h2>

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

<?php
// Récupérer et afficher les plaintes en attente
$plaintesStmt = $pdo->query("SELECT p.id, u.nom AS utilisateur, p.plainte, p.status, p.date_plainte 
                            FROM plaintes p 
                            JOIN utilisateurs u ON p.id_utilisateur = u.id
                            WHERE p.status = 'Attente'");
$plaintes = $plaintesStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h2>Gestion des Plaintes</h2>

    <h3>Plaintes en attente</h3>
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
                <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($plainte['date_plainte']))); ?></td>
                <td>
                    <form action="plainte.php" method="post" style="display:inline;">
                        <input type="hidden" name="id_plainte" value="<?php echo $plainte['id']; ?>">
                        <button type="submit" name="action" value="lu" class="btn btn-success">Marquer comme lu</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
// Récupérer et afficher les demandes en attente
$pendingStmt = $pdo->query("SELECT d.id, u.nom AS utilisateur, d.demande, d.status 
                            FROM demande d 
                            JOIN utilisateurs u ON d.id_utilisateurs = u.id
                            WHERE d.status = 'en attente'");
$pendingDemandes = $pendingStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h2>Gestion des Demandes</h2>

    <h3>Demandes en attente</h3>
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

</body>
</html>
