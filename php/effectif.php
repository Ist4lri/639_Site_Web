<?php
session_start();
include 'db.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['utilisateur'])) {
    header("Location: connection.php");
    exit();
}

// Récupérer tous les utilisateurs par grade (du conscrit au major) avec leur spécialité et formations
$sql = "SELECT u.nom, u.grade, s.nom AS specialite, f.formation, f.formation_hierarchique
        FROM utilisateurs u
        LEFT JOIN spe s ON u.spe_id = s.id
        LEFT JOIN formation f ON u.id = f.id_utilisateur
        WHERE u.grade IN ('Conscrit', 'Garde', 'Garde-Vétéran', 'Caporal', 'Sergent', 'Lieutenant', 'Capitaine', 'Commandant', 'Colonel', 'Général', 'Major')
        ORDER BY u.grade";
$stmt = $pdo->query($sql);
$utilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer tous les personnages et leurs grades
$sqlPersonnages = "SELECT nom, COALESCE(grade, 'grade_mecha') AS grade 
                   FROM personnages";
$stmtPersonnages = $pdo->query($sqlPersonnages);
$personnages = $stmtPersonnages->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include 'header.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Effectif</title>
    <link rel="icon" type="image/x-icon" href="../src/assets/Logo_639th_2.ico">
    <link rel="stylesheet" href="../css/eff.css">
    <style>
        .grade-tab {
            display: none;
            margin: 20px 0;
        }
        .grade-tab.active {
            display: block;
        }
        .menu {
            margin-top: 140px;
            display: flex;
            justify-content: center;
            gap: 15px;
        }
        .menu a, .menu button {
            padding: 10px 20px;
            background-color: #555;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            cursor: pointer;
            border: none;
        }
        .menu a.active, .menu button.active {
            background-color: #2e7d32;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        th {
            background-color: #2e7d32;
            color: white;
        }
        .personnages-tab {
            display: none;
            margin: 20px 0;
        }
        .personnages-tab.active {
            display: block;
        }
    </style>
    <script>
        // Script JavaScript pour gérer l'affichage du tableau selon le grade cliqué
        function showGrade(grade) {
            const tabs = document.querySelectorAll('.grade-tab');
            const menuLinks = document.querySelectorAll('.menu a, .menu button');
            
            tabs.forEach(tab => {
                tab.classList.toggle('active', tab.dataset.grade === grade);
            });

            menuLinks.forEach(link => {
                link.classList.toggle('active', link.dataset.grade === grade);
            });
        }

        // Afficher la section Conscrit par défaut au chargement
        window.onload = function() {
            showGrade('Conscrit');
        };

        // Afficher les personnages
        function showPersonnages() {
            const personnagesTab = document.querySelector('.personnages-tab');
            const menuLinks = document.querySelectorAll('.menu a, .menu button');

            // Masquer tous les onglets sauf celui des personnages
            document.querySelectorAll('.grade-tab').forEach(tab => tab.classList.remove('active'));
            personnagesTab.classList.add('active');

            // Désactiver tous les liens du menu
            menuLinks.forEach(link => link.classList.remove('active'));

            // Activer le bouton Personnages
            document.querySelector('.menu button[data-grade="Personnages"]').classList.add('active');
        }
    </script>
</head>
<body>

<div class="menu">
    <a data-grade="Conscrit" onclick="showGrade('Conscrit')">Conscrit</a>
    <a data-grade="Garde" onclick="showGrade('Garde')">Garde</a>
    <a data-grade="Garde-Vétéran" onclick="showGrade('Garde-Vétéran')">Garde-Vétéran</a>
    <a data-grade="Caporal" onclick="showGrade('Caporal')">Caporal</a>
    <a data-grade="Sergent" onclick="showGrade('Sergent')">Sergent</a>
    <a data-grade="Lieutenant" onclick="showGrade('Lieutenant')">Lieutenant</a>
    <a data-grade="Capitaine" onclick="showGrade('Capitaine')">Capitaine</a>
    <a data-grade="Major" onclick="showGrade('Major')">Major</a>
    <button data-grade="Personnages" onclick="showPersonnages()">Personnages</button>
</div>

<?php
// Regrouper les utilisateurs par grade et afficher dans des tableaux séparés
$grades = ['Conscrit', 'Garde', 'Garde-Vétéran', 'Caporal', 'Sergent', 'Lieutenant', 'Capitaine', 'Major'];

foreach ($grades as $grade) {
    echo "<div class='grade-tab' data-grade='$grade'>";
    echo "<h2>$grade</h2>";
    echo "<table>";
    echo "<thead><tr><th>Nom</th><th>Spécialité</th><th>Formation</th></tr></thead>";
    echo "<tbody>";

    foreach ($utilisateurs as $utilisateur) {
        if ($utilisateur['grade'] === $grade) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($utilisateur['nom']) . "</td>";
            echo "<td>" . htmlspecialchars($utilisateur['specialite'] ?? 'Pégu') . "</td>";

            $formation = $utilisateur['formation'] ?? 'Aucune';
            $formationHierarchique = $utilisateur['formation_hierarchique'] ?? 'Aucune';
            echo "<td>" . htmlspecialchars($formation . '/' . $formationHierarchique) . "</td>";
            echo "</tr>";
        }
    }
    echo "</tbody></table></div>";
}
?>

<!-- Onglet des personnages -->
<div class="personnages-tab">
    <h2>Personnages</h2>
    <table>
        <thead>
            <tr><th>Nom</th><th>Grade</th></tr>
        </thead>
        <tbody>
            <?php foreach ($personnages as $personnage): ?>
                <tr>
                    <td><?php echo htmlspecialchars($personnage['nom']); ?></td>
                    <td><?php echo htmlspecialchars($personnage['grade'] ?? 'Inconnue'); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
