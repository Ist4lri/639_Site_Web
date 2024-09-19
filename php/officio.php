<div class="container">
    <!-- Membres de l'Officio Prefectus -->
    <h2 onclick="toggleSection('membersSection')" style="cursor: pointer;">MEMBRES DE L'OFFICIO PREFECTUS</h2>
    <div id="membersSection">
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

    <!-- Gestion des plaintes -->
    <h2 onclick="toggleSection('plaintesSection')" style="cursor: pointer;">Gestion des Plaintes</h2>
    <div id="plaintesSection" style="display:none;">
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

    <!-- Gestion des demandes -->
    <h2 onclick="toggleSection('demandesSection')" style="cursor: pointer;">Gestion des Demandes</h2>
    <div id="demandesSection" style="display:none;">
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
</div>
