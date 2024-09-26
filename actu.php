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

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualité</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/header.css">
    
    <!-- Bootstrap JS and Dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <style>
        header {
    background-color: rgba(75, 75, 75, 0.3); /* Semi-transparent background */
    color: #9ed79d;
    padding: 5px 5px; /* Padding for robust look */
    display: flex;
    align-items: center;
    position: fixed;
    width: 99.6%;
    top: 0;
    left: 0;
    z-index: 1000; /* Ensures header stays above content */
    backdrop-filter: blur(6px); /* Applies blur effect to background */
    -webkit-backdrop-filter: blur(6px); /* For Safari support */
    border-bottom: 1px solid rgba(255, 255, 255, 0.2); /* Optional: adds subtle border */
}
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
        }
        .carousel-indicators li {
            background-color: #00FF00; /* Fluorescent green for inactive indicators */
            border: 1px solid #00FF00; /* Border also green */
        }

        .carousel-indicators .active {
            background-color: #00FF00; /* Fluorescent green for active indicators */
        }

        /* Change the color of the prev and next controls to fluorescent green */
        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            background-color: #00FF00; /* Fluorescent green background */
            border-radius: 50%;
            width: 40px;
            height: 40px;
            background-size: 100%, 100%; /* Makes the arrow icon cover the button */
        }

        .carousel-control-prev-icon::after,
        .carousel-control-next-icon::after {
            content: '';
            display: block;
            width: 100%;
            height: 100%;
            background-image: none; /* Remove default icon */
        }

        .carousel-control-prev-icon {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%2300FF00' viewBox='0 0 8 8'%3E%3Cpath d='M4.5 0L0 4l4.5 4V0z'/%3E%3C/svg%3E"); /* Fluorescent green left arrow */
        }

        .carousel-control-next-icon {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%2300FF00' viewBox='0 0 8 8'%3E%3Cpath d='M3.5 0l4.5 4-4.5 4V0z'/%3E%3C/svg%3E"); /* Fluorescent green right arrow */
        }

        /* Carousel container */
        .carousel {
            margin-top: 200px;
        }

        /* Adjust the carousel height */
        .carousel-item img {
            height: 250px;
            object-fit: cover;
        }

        /* Spacer between carousel and news */
        .spacer {
            height: 70px;
        }
        h2 {
    color: #3bd237;
    margin-bottom: 3px;
    font-size: 1.5em;
    background-color: rgba(0, 0, 0, 0.6); 
    padding: 10px;
    border-radius: 8px; 
    backdrop-filter: blur(5px); 
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        width: 500px;
    margin-left: 38%;
}


        /* News container */
        .news-container {
            width: 75%;
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

        /* Admin form */
        .admin-form {
            width: 60%;
            margin-left: 21%;
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
</head>

    
<body>

<!-- Bootstrap Carousel -->
<div id="newsCarousel" class="carousel slide" data-ride="carousel">
    <ol class="carousel-indicators">
        <li data-target="#newsCarousel" data-slide-to="0" class="active"></li>
        <li data-target="#newsCarousel" data-slide-to="1"></li>
        <li data-target="#newsCarousel" data-slide-to="2"></li>
        <li data-target="#newsCarousel" data-slide-to="3"></li>
        <li data-target="#newsCarousel" data-slide-to="4"></li>
        <li data-target="#newsCarousel" data-slide-to="5"></li>
        <li data-target="#newsCarousel" data-slide-to="6"></li>
        <li data-target="#newsCarousel" data-slide-to="7"></li>
    </ol>

    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="src/assets/Battle.png" class="d-block w-100" alt="Image 1">
        </div>
        <div class="carousel-item">
            <img src="src/assets/Baionette.png" class="d-block w-100" alt="Image 2">
        </div>
        <div class="carousel-item">
            <img src="src/assets/image3.jpg" class="d-block w-100" alt="Image 3">
        </div>
        <div class="carousel-item">
            <img src="src/assets/image4.jpg" class="d-block w-100" alt="Image 4">
        </div>
        <div class="carousel-item">
            <img src="src/assets/image5.jpg" class="d-block w-100" alt="Image 5">
        </div>
        <div class="carousel-item">
            <img src="src/assets/image6.jpg" class="d-block w-100" alt="Image 6">
        </div>
        <div class="carousel-item">
            <img src="src/assets/image7.jpg" class="d-block w-100" alt="Image 7">
        </div>
        <div class="carousel-item">
            <img src="src/assets/image8.jpg" class="d-block w-100" alt="Image 8">
        </div>
    </div>

    <a class="carousel-control-prev" href="#newsCarousel" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
    </a>
    <a class="carousel-control-next" href="#newsCarousel" role="button" data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
    </a>
</div>

<!-- Spacer for 70px space between carousel and news -->
<div class="spacer"></div>

<h2>Les Actualités</h2>
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

</body>

</html>
