<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Demandes Administratives</title>
    <link rel="stylesheet" href="css/back.css"> 
</head>
<body>
    <h1>Demandes Administratives</h1>
    <a href="back.php">Back</a>
    <a href="zeusing.php">Zeus</a>
    <a href="index.php">Accueil</a>

    <!-- Search Form -->
    <form method="GET" action="demandead.php">
        <label for="statut">Statut:</label>
        <select id="statut" name="statut">
            <option value="" <?php if ($search_statut == '') echo 'selected'; ?>>Tous</option>
            <option value="Fait" <?php if ($search_statut == 'Fait') echo 'selected'; ?>>Fait</option>
            <option value="Refusé" <?php if ($search_statut == 'Refusé') echo 'selected'; ?>>Refusé</option>
            <!-- Add more statut options as needed -->
        </select>

        <label for="utilisateur">Utilisateur:</label>
        <input type="text" id="utilisateur" name="utilisateur" value="<?php echo htmlspecialchars($search_utilisateur); ?>" placeholder="Nom utilisateur">

        <button type="submit">Rechercher</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Utilisateur</th>
                <th>Demande</th>
                <th>Statut</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($requests as $request): ?>
                <tr>
                    <td><?php echo htmlspecialchars($request['id']); ?></td>
                    <td><?php echo htmlspecialchars($request['utilisateur_nom']); ?></td>
                    <td><?php echo htmlspecialchars($request['demande']); ?></td>
                    <td><?php echo htmlspecialchars($request['statut'] ?? 'Non défini'); ?></td>
                    <td>
                        <form method="POST" action="" style="display:inline;">
                            <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($request['id']); ?>">
                            <button type="submit" name="update_statut" value="Fait">Fait</button>
                        </form>
                        <form method="POST" action="" style="display:inline;">
                            <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($request['id']); ?>">
                            <button type="submit" name="update_statut" value="Refusé">Refusé</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
