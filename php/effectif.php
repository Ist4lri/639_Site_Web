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
?>
<?php include 'header.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Effectif</title>
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
            margin: 140px 0;
            display: flex;
            justify-content: center;
            gap: 15px;
        }
        .menu a {
            padding: 10px 20px;
            background-color: #555;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .menu a.active {
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
    </style>
    <script>
        // Script JavaScript pour gérer l'affichage du tableau selon le grade cliqué
        function showGrade(grade) {
            const tabs = document.querySelectorAll('.grade-tab');
            const menuLinks = document.querySelectorAll('.menu a');
            
            tabs.forEach(tab => {
                if (tab.dataset.grade === grade) {
                    tab.classList.add('active');
                } else {
                    tab.classList.remove('active');
                }
            });

            menuLinks.forEach(link => {
                if (link.dataset.grade === grade) {
                    link.classList.add('active');
                } else {
                    link.classList.remove('active');
                }
            });
        }

        // Afficher la section Conscrit par défaut au chargement
        window.onload = function() {
            showGrade('Conscrit');
        };
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
    <!-- <a data-grade="Commandant" onclick="showGrade('Commandant')">Commandant</a> -->
    <!-- <a data-grade="Colonel" onclick="showGrade('Colonel')">Colonel</a> -->
    <!-- <a data-grade="Général" onclick="showGrade('Général')">Général</a> -->
    <a data-grade="Major" onclick="showGrade('Major')">Major</a>
</div>

<?php
// Regrouper les utilisateurs par grade et afficher dans des tableaux séparés
$grades = ['Conscrit', 'Garde', 'Garde-Vétéran', 'Caporal', 'Sergent', 'Lieutenant','capitaine', 'Major'];

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

            // Combiner la formation et la formation hiérarchique dans la même cellule
            $formation = $utilisateur['formation'] ?? 'Aucune';
            $formationHierarchique = $utilisateur['formation_hierarchique'] ?? 'Aucune';
            echo "<td>" . htmlspecialchars($formation . '/' . $formationHierarchique) . "</td>";
            
            echo "</tr>";
        }
    }

    echo "</tbody></table></div>";
}


?>
<a href="krieg.php">
    <img class="Krieg" src="../src/assets/DK.png" alt="Image">
</a>

</body>
</html>
