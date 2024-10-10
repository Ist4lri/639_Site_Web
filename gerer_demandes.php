<?php
session_start();

include 'php/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $demandeId = $_POST['demande_id'];
    $action = $_POST['action'];

    // Récupérer les informations de la demande
    $stmt = $pdo->prepare("SELECT * FROM demande_grade WHERE id = :id");
    $stmt->execute([':id' => $demandeId]);
    $demande = $stmt->fetch();

    if ($demande) {
        if ($action === 'accepter') {
            // Accepter la demande : mettre à jour le grade de l'utilisateur
            $nouveauGrade = $demande['nouveau_grade'];
            $utilisateurId = $demande['utilisateur_id'];

            $stmt = $pdo->prepare("UPDATE utilisateurs SET grade = :nouveau_grade WHERE id = :id");
            $stmt->execute([
                ':nouveau_grade' => $nouveauGrade,
                ':id' => $utilisateurId
            ]);

            // Mettre à jour le statut de la demande à "approuvé"
            $stmt = $pdo->prepare("UPDATE demande_grade SET statut = 'approuvé' WHERE id = :id");
            $stmt->execute([':id' => $demandeId]);
        } elseif ($action === 'rejeter') {
            // Rejeter la demande : mettre à jour le statut de la demande à "rejeté"
            $stmt = $pdo->prepare("UPDATE demande_grade SET statut = 'rejeté' WHERE id = :id");
            $stmt->execute([':id' => $demandeId]);
        }
    }
}

// Récupérer toutes les demandes en attente
$stmt = $pdo->query("SELECT d.id, d.utilisateur_id, d.nouveau_grade, d.statut, u.nom AS utilisateur_nom, u.email AS utilisateur_email 
                     FROM demande_grade d
                     JOIN utilisateurs u ON d.utilisateur_id = u.id
                     WHERE d.statut = 'en attente'");
$demandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gérer les demandes de grade</title>
    <link rel="icon" type="image/x-icon" href="src/assets/Logo_639th_2.ico">
    <link rel="stylesheet" href="css/tab.css">
</head>
<body>
    <h2>Gérer les demandes de changement de grade</h2>

    <?php if (empty($demandes)): ?>
        <p>Aucune demande en attente.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID Demande</th>
                    <th>Nom Utilisateur</th>
                    <th>Email Utilisateur</th>
                    <th>Nouveau grade</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($demandes as $demande): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($demande['id']); ?></td>
                        <td><?php echo htmlspecialchars($demande['utilisateur_nom']); ?></td>
                        <td><?php echo htmlspecialchars($demande['utilisateur_email']); ?></td>
                        <td><?php echo htmlspecialchars($demande['nouveau_grade']); ?></td>
                        <td class="actions">
                            <form action="gerer_demandes.php" method="post">
                                <input type="hidden" name="demande_id" value="<?php echo htmlspecialchars($demande['id']); ?>">
                                <input type="hidden" name="action" value="accepter">
                                <input type="submit" value="Accepter">
                            </form>
                            <form action="gerer_demandes.php" method="post">
                                <input type="hidden" name="demande_id" value="<?php echo htmlspecialchars($demande['id']); ?>">
                                <input type="hidden" name="action" value="rejeter">
                                <input type="submit" value="Rejeter" class="reject">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
