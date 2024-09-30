<?php
session_start();

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['id_utilisateur'])) {
    // Si l'utilisateur n'est pas connecté, redirigez-le vers la page de connexion
    header("Location: connection.php");
    exit();
}

// Inclure la connexion à la base de données
include 'db.php';

// Si l'utilisateur est connecté, vous pouvez afficher ses informations
$userId = $_SESSION['id_utilisateur'];
$nomUtilisateur = $_SESSION['nom_utilisateur'];

$factionStmt = $pdo->prepare("SELECT * FROM personnages WHERE id_utilisateur = :id_utilisateur AND faction = 'Ecclesiarchie' AND validation = 'Accepter'");
$factionStmt->execute(['id_utilisateur' => $userId]);
$faction = $factionStmt->fetch();

$message = '';
// Système de recherche par type d'entretien et nom (si applicable)
$searchQuery = "";
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $searchType = isset($_GET['search_type']) ? $_GET['search_type'] : '';
    $searchName = isset($_GET['search_name']) ? $_GET['search_name'] : '';

    // Filtrer par type d'entretien ou par nom
    $sql = "SELECT * FROM demande_eccle WHERE id_utilisateur = ?";
    $params = [$userId];

    if (!empty($searchType)) {
        $sql .= " AND type_entretien LIKE ?";
        $params[] = '%' . $searchType . '%';
    }

    if (!empty($searchName)) {
        $sql .= " AND description LIKE ?";
        $params[] = '%' . $searchName . '%';
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $demandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Récupérer toutes les demandes de l'utilisateur connecté s'il n'y a pas de recherche
    $stmt = $pdo->prepare("SELECT * FROM demande_eccle WHERE id_utilisateur = ?");
    $stmt->execute([$userId]);
    $demandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adeptus Ministorum</title>
    <link rel="stylesheet" href="../css/ecclesiarchie.css">
</head>
<body>
    <style>
@font-face {
    font-family: 'Inquisitor';
    src: url('../css/fonts/Inquisitor.otf') format('opentype');
    font-weight: normal;
    font-style: normal;
    
}
        body {
            background-image: url('../src/assets/Bougie.png');
            background-repeat: no-repeat;
            background-position: center bottom;
            background-attachment: fixed;
            background-size: cover;   
        }
    </style>

<header>
    <div class="head-logo2">
        <a href="../index.php">
            <img src="../src/assets/Banderole.png" alt="639 Régiment cadien">
        </a>
    </div>

    <nav class="head-nav">
        <?php if ($faction): ?>
            <!-- Si l'utilisateur fait partie de l'église -->
            <a href="parloir.php">Parloir</a>
        <?php else: ?>
            <!-- Si l'utilisateur n'est pas dans l'église -->
            <a href="profil_utilisateur.php">Profil</a>
            <a href="Dec.php">Déconnexion</a>
        <?php endif; ?>
    </nav>
</header>

<div class="container">
    <h3 class="pensee transition" style="font-family: 'Inquisitor', Serif;
    color: #928c10;
    font-size: 1.6em; 
    text-align: center; 
    margin: 5px;
    letter-spacing: 3px">COUCOU</h3>

<style>
    .pensee.transition {
        transition: opacity 1s ease-in-out;
    }

    .pensee.hide {
        opacity: 0;
    }
</style>


   <script>
    const pensees = <?php echo json_encode($pensees); ?>;

console.log("Pensees récupérées:", pensees);

function afficherPenseeAleatoire() {
    if (pensees.length > 0) {
        const indexAleatoire = Math.floor(Math.random() * pensees.length);
        const penseeElement = document.querySelector('.pensee');
        
        penseeElement.classList.add('hide');
        
        setTimeout(() => {
            penseeElement.textContent = `"${pensees[indexAleatoire]}"`;
            
            penseeElement.classList.remove('hide');
        }, 1000); 
    } else {
        console.log("Aucune pensée disponible");
        document.querySelector('.pensee').textContent = "Aucune pensée disponible";
    }
}
afficherPenseeAleatoire();
setInterval(afficherPenseeAleatoire, 10000);
</script>
    <?php if ($faction): ?>
        <h1 style="font-family: 'Inquisitor', Serif;
font-size: 1.6em; 
    text-align: center; 
    margin: 5px;
    letter-spacing: 3px">Bienvenue, Prêcheur toi la voie de l'Empereur</h1>
        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

    <?php else: ?>
        <div class="actions">
            <h2>Faites votre demande</h2>
            <form action="eglise.php" method="post">
                <label for="request_type">Type d'entretien :</label>
                <select id="request_type" name="request_type" required>
                    <option value="arsenal">Arsenal</option>
                    <option value="confession">Confession</option>
                    <option value="demande">Demande</option>
                </select>

                <label for="request_description">Description de la demande :</label>
                <textarea id="request_description" name="request_description" rows="5" placeholder="Veuillez décrire en détail votre demande." required></textarea>

                <button type="submit" class="btn-request">Envoyer la demande</button>
            </form>
        </div>
        <!-- Affichage des demandes précédentes -->
        <?php if (!empty($demandes)): ?>
            <h2>Vos demandes précédentes</h2>
            <table border="1">
                <thead>
                    <tr>
                        <th>Type d'entretien</th>
                        <th>Description</th>
                        <th>Statut</th>
                        <th>Date de création</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($demandes as $demande): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($demande['type_entretien']); ?></td>
                            <td><?php echo htmlspecialchars($demande['description']); ?></td>
                            <td><?php echo htmlspecialchars($demande['status']); ?></td>
                            <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($demande['date_creation']))); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Aucune demande soumise pour l'instant.</p>
        <?php endif; ?>
    <?php endif; ?>
</div>

</body>
</html>
