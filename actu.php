<?php
session_start();
include 'php/db.php'; 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Fetch the latest 5 news from the `info` table
$sqlNews = "SELECT date, texte FROM info ORDER BY date DESC LIMIT 5";
$stmtNews = $pdo->query($sqlNews);
$newsItems = $stmtNews->fetchAll(PDO::FETCH_ASSOC);

// Check if user is logged in and is an admin
$isLoggedIn = isset($_SESSION['utilisateur']);
$isAdmin = $isLoggedIn && $_SESSION['role'] === 'admin';

// Handle form submission for adding new news item
if ($isAdmin && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $newText = trim($_POST['texte']);
    $newDate = date('Y-m-d');

    if (!empty($newText)) {
        $sqlInsert = "INSERT INTO info (date, texte) VALUES (:date, :texte)";
        $stmtInsert = $pdo->prepare($sqlInsert);
        $stmtInsert->execute(['date' => $newDate, 'texte' => $newText]);
        header('Location: actu.php'); // Redirect to avoid form resubmission
        exit();
    } else {
        $error = "Le texte ne peut pas être vide.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<header class="head">
    <div class="head-logo">
        <a href="index.php">
            <img src="src/assets/Logo.png" alt="Logo 639">
        </a>
    </div>
    <div class="head-logo2">
        <img src="src/assets/TitreSite.png" alt="639 Régiment cadien"
    </div>
    <nav class="head-nav">
            <a href="php/profil_utilisateur.php">Profil</a>
            <a href="php/officier.php">Officier</a>
            <a href="php/sous-officier.php">Sous-Officier</a>
            <a href="php/Dec.php">Déconnexion</a>
    </nav>
    </div>
</header>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
        }

        /* Carousel styling */
        .carousel-container {
            top: 150px;
            width: 60%;
            margin: 0 auto;
            overflow: hidden;
            height: 150px;
            position: relative;
        }

        .carousel-images {
            display: flex;
            width: 800%;
            transition: transform 0.5s ease-in-out;
        }

        .carousel-images img {
            width: 12.5%; /* 8 images => 100% / 8 */
            height: 150px;
        }

        /* News container */
        .news-container {
            margin-top: 40px;
            width: 60%;
            margin: 40px auto;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .news-item {
            border-bottom: 1px solid #ddd;
            padding: 20px 0;
        }

        .news-item:last-child {
            border-bottom: none;
        }

        .news-date {
            font-size: 0.9em;
            color: #888;
            text-align: left;
        }

        .news-text {
            text-align: center;
            font-size: 1.1em;
            color: #333;
        }

        /* Form styling */
        .admin-form {
            margin-top: 40px;
            width: 60%;
            margin: 40px auto;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .admin-form h3 {
            font-size: 1.2em;
            margin-bottom: 15px;
            text-align: center;
        }

        .admin-form textarea {
            width: 85%;
            height: 100px;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 1em;
        }

        .admin-form button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: white;
            font-size: 1em;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .admin-form button:hover {
            background-color: #218838;
        }

        .error {
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>

<!-- Carousel -->
<div class="carousel-container">
    <div class="carousel-images">
        <img src="src/assets/Battle.png" alt="Image 1">
        <img src="src/assets/image2.jpg" alt="Image 2">
        <img src="src/assets/image3.jpg" alt="Image 3">
        <img src="src/assets/image4.jpg" alt="Image 4">
        <img src="src/assets/image5.jpg" alt="Image 5">
        <img src="src/assets/image6.jpg" alt="Image 6">
        <img src="src/assets/image7.jpg" alt="Image 7">
        <img src="src/assets/image8.jpg" alt="Image 8">
    </div>
</div>

<!-- Latest News -->
<div class="news-container">
    <?php foreach ($newsItems as $news): ?>
        <div class="news-item">
            <div class="news-date"><?= htmlspecialchars($news['date']) ?></div>
            <div class="news-text"><?= htmlspecialchars($news['texte']) ?></div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Admin form for adding new news item -->
<?php if ($isAdmin): ?>
    <div class="admin-form">
        <h3>Ajouter une nouvelle actualité</h3>
        <?php if (isset($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="POST" action="actu.php">
            <textarea name="texte" placeholder="Entrez une Actualité"></textarea>
            <button type="submit">Ajouter l'actualité</button>
        </form>
    </div>
<?php endif; ?>

<script>
// Carousel functionality
let carouselIndex = 0;
const images = document.querySelectorAll('.carousel-images img');
const totalImages = images.length;

function moveCarousel() {
    carouselIndex++;
    if (carouselIndex >= totalImages) {
        carouselIndex = 0;
    }
    document.querySelector('.carousel-images').style.transform = `translateX(-${carouselIndex * 12.5}%)`;
}

setInterval(moveCarousel, 3000); // Change image every 3 seconds
</script>

</body>
</html>


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

        .officio {
    position: absolute;
    right: 20px;
    top: 240px;
    width: 150px;
    height: auto;
    cursor: pointer;
}

        .mech {
    position: absolute;
    right: 45px;
    top: 320px;
    width: 80px;
    height: auto;
    cursor: pointer;
}

        .map {
    position: fixed;
    left: 0px;
    bottom: 0px;
    width: 80px;
    height: auto;
    cursor: pointer;
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
            left: 40%;
            top: 35%;
            width: 20%;
            height: 20%;
            background-color: #424242;
            justify-content: center;
            align-items: center;
            border-radius: 10px;
            border: 2px solid #3bd237;
        }

        .modal-content {
            position: relative; 
            background-color: #424242;
            color: #9ed79d;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
        }

        .close {
            position: absolute;
            top: -35px;
            right: 5px;
            color: red;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: darkred;
        }

  
    </style>
</head>
<body>


