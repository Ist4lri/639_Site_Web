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
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualité</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/header.css">
    
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
    width: 500px; /* Set the width of the carousel to match the width of the images */
    margin: 0 auto;
    background-color: #333; /* Dark grey background */
    padding: 10px;
    overflow: hidden; /* Ensure that only the current image is visible */
    height: 250px;
    position: relative;
    border-radius: 8px;
}

.carousel-images {
            display: flex;
            width: 800%;
            transition: transform 0.5s ease-in-out;
        }

        .carousel-images img {
            width: 350px;
            height: 150px;
        }

/* Dots navigation */
.dots {
    text-align: center;
    margin-top: 10px;
}

.dot {
    height: 15px;
    width: 15px;
    margin: 0 5px;
    background-color: #333;
    border-radius: 50%;
    display: inline-block;
    cursor: pointer;
    border: 2px solid #0f0; /* Fluorescent green border */
}

.active-dot {
    background-color: #0f0; /* Active dot is fluorescent green */
}


        /* Space between carousel and news section */
        .spacer {
            height: 130px;
        }

        /* News container */
        .news-container {
            width: 60%;
            margin: 40px auto;
            padding: 20px;
            background-color: #333; /* Dark grey background */
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
            color: #fff; /* Light text color on dark background */
        }

        /* Form styling */
        .admin-form {
            width: 60%;
            margin: 40px auto;
            padding: 5px;
            background-color: #333; /* Dark grey background */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .admin-form h3 {
            font-size: 1.2em;
            margin-bottom: 15px;
            text-align: center;
            color: #fff;
        }

        .admin-form textarea {
            width: 90%;
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
<body>

<!-- Carousel -->
<div class="carousel-container">
    <div class="carousel-images">
        <img src="src/assets/Battle.png" alt="Image 1">
        <img src="src/assets/Baionette.png" alt="Image 2">
        <img src="src/assets/image3.jpg" alt="Image 3">
        <img src="src/assets/image4.jpg" alt="Image 4">
        <img src="src/assets/image5.jpg" alt="Image 5">
        <img src="src/assets/image6.jpg" alt="Image 6">
        <img src="src/assets/image7.jpg" alt="Image 7">
        <img src="src/assets/image8.jpg" alt="Image 8">
    </div>

    <!-- Dots navigation -->
    <div class="dots">
        <span class="dot active-dot" onclick="moveToSlide(0)"></span>
        <span class="dot" onclick="moveToSlide(1)"></span>
        <span class="dot" onclick="moveToSlide(2)"></span>
        <span class="dot" onclick="moveToSlide(3)"></span>
        <span class="dot" onclick="moveToSlide(4)"></span>
        <span class="dot" onclick="moveToSlide(5)"></span>
        <span class="dot" onclick="moveToSlide(6)"></span>
        <span class="dot" onclick="moveToSlide(7)"></span>
    </div>
</div>

<!-- Spacer for 70px space between carousel and news -->
<div class="spacer"></div>

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
    updateActiveDot();
}

function moveToSlide(index) {
    carouselIndex = index;
    document.querySelector('.carousel-images').style.transform = `translateX(-${carouselIndex * 12.5}%)`;
    updateActiveDot();
}

function updateActiveDot() {
    const dots = document.querySelectorAll('.dot');
    dots.forEach(dot => dot.classList.remove('active-dot'));
    dots[carouselIndex].classList.add('active-dot');
}

setInterval(moveCarousel, 3000); // Change image every 3 seconds
</script>

</body>
</html>
