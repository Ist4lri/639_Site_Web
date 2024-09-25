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
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
        }

        /* Dark gray background for all containers */
        .container {
            background-color: #333;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        /* Carousel styling */
        .carousel-container {
            background-color: #333; /* Dark gray background */
            width: 60%;
            margin: 0 auto 70px auto; /* Centered and with 70px margin-bottom */
            overflow: hidden;
            height: 150px;
            position: relative;
            border-radius: 8px;
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

        /* Carousel indicators (points) */
        .carousel-indicators {
            display: flex;
            justify-content: center;
            margin-top: 10px;
        }

        .carousel-indicators div {
            width: 15px;
            height: 15px;
            background-color: #3bd237; /* Fluorescent green */
            border-radius: 50%;
            margin: 0 5px;
            cursor: pointer;
        }

        .carousel-indicators .active {
            background-color: #00FF00; /* Bright green for active */
        }

        /* News container */
        .news-container {
            margin-top: 40px;
            width: 60%;
            margin: 40px auto;
            padding: 20px;
            background-color: #333; /* Dark gray background */
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
            color: #fff;
        }

        /* Form styling */
        .admin-form {
            margin-top: 40px;
            width: 60%;
            margin: 40px auto;
            padding: 20px;
            background-color: #333; /* Dark gray background */
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
<div class="carousel-container container">
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

    <!-- Carousel indicators (points) -->
    <div class="carousel-indicators">
        <div class="active" onclick="setCurrentSlide(0)"></div>
        <div onclick="setCurrentSlide(1)"></div>
        <div onclick="setCurrentSlide(2)"></div>
        <div onclick="setCurrentSlide(3)"></div>
        <div onclick="setCurrentSlide(4)"></div>
        <div onclick="setCurrentSlide(5)"></div>
        <div onclick="setCurrentSlide(6)"></div>
        <div onclick="setCurrentSlide(7)"></div>
    </div>
</div>

<!-- Latest News -->
<div class="news-container container">
    <?php foreach ($newsItems as $news): ?>
        <div class="news-item">
            <div class="news-date"><?= htmlspecialchars($news['date']) ?></div>
            <div class="news-text"><?= htmlspecialchars($news['texte']) ?></div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Admin form for adding new news item -->
<?php if ($isAdmin): ?>
    <div class="admin-form container">
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
let carouselIndex = 0;
const images = document.querySelectorAll('.carousel-images img');
const indicators = document.querySelectorAll('.carousel-indicators div');
const totalImages = images.length;

// Move to a specific slide
function setCurrentSlide(index) {
    carouselIndex = index;
    updateCarousel();
}

// Update the carousel position and the active indicator
function updateCarousel() {
    document.querySelector('.carousel-images').style.transform = `translateX(-${carouselIndex * 12.5}%)`;
    indicators.forEach((indicator, idx) => {
        if (idx === carouselIndex) {
            indicator.classList.add('active');
        } else {
            indicator.classList.remove('active');
        }
    });
}

// Auto-slide functionality
function moveCarousel() {
    carouselIndex++;
    if (carouselIndex >= totalImages) {
        carouselIndex = 0;
    }
    updateCarousel();
}

setInterval(moveCarousel, 3000); // Change image every 3 seconds
</script>

</body>
</html>
