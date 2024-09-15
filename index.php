<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    include 'php/db.php';
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

try {
    $sqlSpecialties = "
        SELECT 
            s.nom, 
            s.ab,
            COUNT(u.id) AS places_occupees, 
            s.total
        FROM spe s 
        LEFT JOIN utilisateurs u ON s.id = u.spe_id 
        GROUP BY s.id, s.nom, s.ab, s.total
    ORDER BY s.id;
";
    $stmtSpecialties = $pdo->query($sqlSpecialties);
    $specialties = $stmtSpecialties->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur SQL spécialités : " . $e->getMessage());
}

try {
    $sqlInstructors = "
        SELECT 
            u.nom AS utilisateur_nom, 
            s.nom AS specialite_nom 
        FROM utilisateurs u 
        JOIN spe s ON u.spe_id = s.id 
        WHERE u.gerance = 1;
    ";
    $stmtInstructors = $pdo->query($sqlInstructors);
    $instructors = $stmtInstructors->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur SQL instructeurs : " . $e->getMessage());
}

$sqlGradedUsers = "
    SELECT nom, grade 
    FROM utilisateurs 
    WHERE grade IN ('Sergent', 'Adjudant', 'Lieutenant', 'Major', 'Colonel') 
    ORDER BY grade DESC";
$stmtGradedUsers = $pdo->query($sqlGradedUsers);
$gradedUsers = $stmtGradedUsers->fetchAll(PDO::FETCH_ASSOC);


$isLoggedIn = isset($_SESSION['utilisateur']);
$userName = $isLoggedIn ? $_SESSION['nom_utilisateur'] : '';
?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>639ème Régiment Cadien</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/header.css">
    <style>
        .table-section {
    padding: 5px;
    width: 40%; 
    margin: 3px auto; 
    color: #9ed79d; 
    text-align: center;
}

.table-section h3 {
    cursor: pointer;
    color: #3bd237;
    margin-bottom: 3px;
    font-size: 1.5em;
    background-color: rgba(0, 0, 0, 0.6); 
    padding: 10px;
    border-radius: 8px; 
    backdrop-filter: blur(5px); 
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}


.table-section table {
    width: 100%; 
    border-collapse: collapse;
    margin: 0 auto; 
}

table, th, td {
    border: 2px solid #9ed79d; 
    padding: 10px;
    color: #9ed79d; 
    background-color: #211;
}

th {
    background-color: #222; 
    font-weight: bold;
}

  .servo-mortis {
    position: absolute;
    right: 10px;
    top: 150px;
    width: 150px;
    height: auto;
    cursor: pointer;
    animation: float 3s ease-in-out infinite;
}

.Tooltip {
    position: absolute;
    right: 125px; 
    top: 120px;
    background-color: #222222;
    color: #00F529;
    padding: 0px;
    border-radius: 8px;
    font-size: 14px;
    border: 0.5px solid #00F529; 
    opacity: 0;
    font-weight: bold;
    transition: opacity 0.3s ease;
}

@keyframes float {
    0% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-20px);
    }
    100% {
        transform: translateY(0);
    }
}
.tooltip2 {
    position: absolute;
    right: 125px; 
    top: 120px;
    background-color: #222222;
    color: #00F529;
    padding: 0px;
    border-radius: 8px;
    font-size: 14px;
    border: 0.5px solid #00F529; 
    opacity: 0;
    font-weight: bold;
    transition: opacity 0.3s ease;
}
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: #424242;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #424242;
            color: #9ed79d;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
        }

        .close {
            color: red;
            float: right;
            font-size: 28px;
            cursor: pointer;
        }

  
    </style>
</head>
<body>

<header class="head">
    <div class="head-logo">
        <a href="index.php">
            <img src="src/assets/Logo.png" alt="Logo 639">
        </a>
        <?php if ($isLoggedIn): ?>
            <span class="head-username">Bonjour, <?php echo htmlspecialchars($userName); ?></span>
        <?php endif; ?>
    </div>
    <div class="head-logo2">
        <img src="src/assets/TitreSite.png" alt="639 Régiment cadien"
    </div>
    <nav class="head-nav">
        <?php if ($isLoggedIn): ?>
            <a href="php/profil_utilisateur.php">Profil</a>
            <a href="php/officier.php">Officier</a>
            <a href="php/sous-officier.php">Sous-Officier</a>
            <a href="php/Dec.php">Déconnexion</a>
        <?php else: ?>
            <a href="php/connection.php">Connexion</a>
            <a href="php/ins.php">Inscription</a>
        <?php endif; ?>
    </nav>
</header>

<div class="intro_content">
    <?php if ($isLoggedIn): ?>
    <p class="tooltip2">N'oubliez pas de voter <br> cliquez MOI!!</p>
    <a href="https://top-serveurs.net/arma3/vote/fr-w40k-le-639th-regiment-cadian" target="_blank">
    <img src="/src/assets/ServosMortis.png" alt="Servo Mortis" class="servo-mortis">
    </a>
        <h1>Bienvenue dans la Garde Impériale, soldat du 639ème Régiment Cadien.</h1>
        <p>Nous espérons de vous une efficacité et une assiduité exemplaire. 
            Les Cadiens, originaires de Cadia, sont parmi les régiments les plus disciplinés et les plus redoutables de l'Imperium. 
            Leur monde natal, situé dans le secteur Cadien à proximité de l'Œil de la Terreur, a fait d'eux des combattants acharnés, 
            habitués à résister aux forces chaotiques depuis leur plus jeune âge.</p>
        <p>Faites honneur à vos familles et à la mémoire de Cadia. Montrez à l'Imperium que Cadia tient toujours, à travers vous.</p>
        <h1> Pour l'Empereur, pour Cadia ! </h1>
        <div class="table-section">
            
            <!-- gradés -->
            <h3 onclick="toggleTable('graded-users')">Nos gradés</h3>
            <table id="graded-users" style="display:none;">
                <tr>
                    <th> Nos Gradés </th>
                </tr>
                <?php foreach ($gradedUsers as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['nom']) . ' {' . htmlspecialchars($user['grade']) . '}' ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <div class="table-section">
            <!-- spécialités -->
            <h3 onclick="toggleTable('remaining-places')">Spécialités Disponible</h3>
            <table id="remaining-places" style="display:none;">
                <tr>
                    <th>Spécialité</th>
                    <th>Places restantes</th>
                </tr>
               <?php 
foreach ($specialties as $specialty):
    if ($specialty['nom'] === 'Fusilier' || $specialty['nom'] === 'Commandement') {
        continue; 
    }
    ?>
    <tr>
        <td><?= htmlspecialchars($specialty['nom']) ?></td>
        <td><?= ($specialty['places_occupees']) . '/' . $specialty['total'] ?></td>
    </tr>
<?php endforeach; ?>
            </table>
        </div>

        <div class="table-section">
            <h3 onclick="toggleTable('instructors')">Les instructeurs</h3>
            <table id="instructors" style="display:none;">
                <tr>
                    <th>Les Gérants</th>
                </tr>
                <?php foreach ($instructors as $instructor): ?>
                    <tr>
                    <td><?= htmlspecialchars($instructor['utilisateur_nom']). '{' .htmlspecialchars($instructor['specialite_nom']). '}'  ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

    <?php else: ?>
    <p class="Tooltip">Bonjour je suis Servo-Mortis, <br>cliquez-moi pour que je vous présente les pages</p>
    <img src="/src/assets/ServosMortis.png" alt="Servo Mortis" class="servo-mortis" onclick="playAudio()">
    <audio id="servo-mortis-audio" src="/src/assets/ServoMortis.mp3"></audio>
        <h1>Le 639th régiment</h1>
        <h3>Une communauté A l&apos;écoute et présente.</h3>
            <p>
                Ici, au régiment, on est environ une soixantaine a se battre pour l&apos;empereur ! Lorsque tu nous rejoindras, tu pourras trouver des personnes présentes
                pour te guider et répondre à toutes tes questions. Tu trouveras également des frères d&apos;armes, qui te soutiendront en tout lieu, et qui sauront te 
                guider afin que tu passes une bonne soirée, et surtout, que tu ne sois pas perdu au milieu du champ de bataille. De plus, si jamais tu as le moindre soucis,
                il y a une incroyable équipe de support qui seront ravis de t&apos;aider, tous dirigé par Cicéron, le chef des ressources humaines. Si tu as le moindre problème
                avec qui que ce soit, il saura trouver une solution afin que tu puisses continuer à être dans une bonne atmosphère. Tu as également Sieger, le responsable modeur,
                qui a la charge de gérer le mod personnel de la team, ainsi que ce qui touche a du code. Enfin tu as le grand manitou, Jager, qui est le leader de la team.
                Il s&apos;occupe de tout ce qui est administratif, et gère le serveur administrativement et financièrement parlant.
            </p>
        <h3>Un serveur français Milsim</h3>
            <p>
                Nous sommes une team Française <img src="src/assets/drapeau_fr.png" alt="Drapeau Français"/> pratiquant le Milsim
                semi-sérieux. Lors de nos opérations, nous avons toujours un temps ou nous pouvons délirer, parler, et échanger un peu, mais durant les opérations, un RP sérieux
                est demandé.  Cependant, un fort esprit de camaraderie est présent, ce qui permettra de combattre en étant entouré d&apos;amis, et permettra de supporter les ordres
                blasant des gradés (Ou des comissaires...). Mais tes futurs frères d&apos;armes sauront te montrer on fait pour survivre en s&apos;amusant, et en passant une bonne soirée.
                Enfin, sache que tu n&apos;es pas obligé de connaitre toutes l&apos;histoire de Warhammer pour pouvoir nous rejoindre : A vrai dire, c&apos;est même mieux si tu ne connais pas grand
                chose, vus qu&apos;un garde est assez ignorants des desseins qui se trouvent au dessus de lui...
            </p>
        <h3>Comment nous rejoindre ?</h3>
            <p>
                Pour nous rejoindre, rien de plus simple : Il suffit que tu rejoindre notre serveur discord ! Puis, après que tu ais lu notre réglement, tu pourras passer un entretien
                afin de savoir comment t&apos;acceuillir. Tu seras ensuite formé, même si tu ne connais rien à Arma3 : nous pouvons prendre en charge des néophytes totaux, du moment que vous
                la volonté de servir l&apos;empereur.
            </p>
        <h4>Alors ? prêt à nous rejoindre ?</h4>
       <div class="Discord"><a href="https://discord.gg/HUwHpEZBZx"><img src="../src/assets/BoutonDiscord0.png" alt="Marksman"></a></div>
    <?php endif; ?>
</div>


<div class="explain_container">
  <img src="src/assets/NosSpécialités.png" alt="Nos spécialités" class="specialty-image">
<div class="specialties">
    <div class="row">
        <div class="specialty"><a href="php/mg.php"><img src="src/assets/BoutonMGunner0.png" alt="Machine Gunner"></a></div>
        <div class="specialty"><a href="php/at.php"><img src="src/assets/BoutonATank0.png" alt="Anti-Tank"></a></div>
        <div class="specialty"><a href="php/med.php"><img src="src/assets/BoutonMedicae0.png" alt="Médicae"></a></div>
        <div class="specialty"><a href="php/vox.php"><img src="src/assets/BoutonVOperateur0.png" alt="Vox Opérateur"></a></div>
    </div>
    <div class="row">
        <div class="specialty"><a href="php/mark.php"><img src="src/assets/BoutonMarksman0.png" alt="Marksman"></a></div>
        <div class="specialty"><a href="php/plas.php"><img src="src/assets/BoutonPlasma0.png" alt="Plasma"></a></div>
        <div class="specialty"><a href="php/kboom.php"><img src="src/assets/BoutonBreacher0.png" alt="Breacher"></a></div>
        <div class="specialty"><a href="php/etl.php"><img src="src/assets/BoutonETLourd0.png" alt="Equipier de Tir Lourd"></a></div>
    </div>
</div>

    <div class="eff"><a href="php/effectif.php"><img src="src/assets/BoutonNosEffectifs0.png" alt="Effectif"></a></div>
</div>

<div id="myModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <p>N'oubliez pas d'aller voter ! <a href="https://top-serveurs.net/arma3/vote/fr-w40k-le-639th-regiment-cadian" target="_blank">Cliquez ici pour voter</a></p>
    </div>
</div>

<script>
    function showModal() {
        var modal = document.getElementById("myModal");
        var closeBtn = document.getElementsByClassName("close")[0];

        modal.style.display = "flex";

        closeBtn.onclick = function() {
            modal.style.display = "none";
            localStorage.setItem('lastShown', Date.now()); 
        };

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
                localStorage.setItem('lastShown', Date.now()); 
            }
        };
    }


    function checkModal() {
        var lastShown = localStorage.getItem('lastShown');
        var now = Date.now();

        if (!lastShown || (now - lastShown) >= 5000) {
            showModal();
        }
    }

    window.onload = function() {
        checkModal();

        setInterval(checkModal, 5000);
    };
</script>
        
<script>
function toggleTable(tableId) {
    const table = document.getElementById(tableId);
    table.style.display = table.style.display === "none" ? "table" : "none";
}
</script>

<script>
        function playAudio() {
            var audio = document.getElementById('servo-mortis-audio');
            audio.volume=0.7;
            audio.play();
        }
    </script>

<script>
    function showTooltip() {
        const tooltip = document.querySelector('.Tooltip');
        tooltip.style.opacity = 1;

        setTimeout(() => {
            tooltip.style.opacity = 0;
        }, 7000); 
    }

    
    setInterval(showTooltip, 7000);
</script>


  <script>
    function showtooltip2() {
        const tooltip2 = document.querySelector('.tooltip2');
        tooltip2.style.opacity = 1;

        setTimeout(() => {
            tooltip2.style.opacity = 0;
        }, 8000); 
    }

    
    setInterval(showtooltip2, 3000);
</script>
</body>
</html>
