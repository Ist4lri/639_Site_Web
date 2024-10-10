<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Officio Prefectus Header</title>
    <link rel="icon" type="image/x-icon" href="../src/assets/Logo_639th_2.ico">
    <link rel="stylesheet" href="../css/officio.css"> <!-- Lien vers ton fichier CSS -->
    <style>

        /* Style pour le header */
        .header {
            position: relative;
            width: 100%;
            height: 100px;
            background-color: #424242; /* Couleur sombre pour le header */
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5); /* Ombre pour effet de profondeur */
            padding: 10px;
        }

        /* Image Aquila au centre */
        .header img {
            position: absolute;
            top: 10px;
            left: 15%;
            transform: translateX(-50%);
            width: 120px;
            height: auto;
        }

        /* Lien en haut à droite */
        .header .nav-links {
            position: absolute;
            right: 40px;
        }

        .header .nav-links a {
            font-family: 'Inquisitor', serif;
            color: #810000;
            text-decoration: none;
            margin-left: 20px;
            font-size: 25px;
            padding: 10px 20px;
            background-color: #252525;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .header .nav-links a:hover {
            background-color: #4F4F4F;
        }
    </style>
</head>
<body>

<!-- Header -->
<header class="header">
    <!-- Image au centre -->
    <a href=../index.php>
    <img src="../src/assets/Aquila.png" alt="Aquila" class='logo' />
    </a>
    <!-- Liens à droite -->
    <div class="nav-links">
        <a href="officio.php">Vos Bureaux</a>
        <a href="officio_info.php"> Informations Militarum</a>
        <a href="officio_main.php">Fiche Médicales</a>
        <a href="plaintes.php">Demandes</a>
        
    </div>
</header>

</body>
</html>
