<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Krieg</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background-image: url('../src/assets/Battle.png');
            background-size: cover;
            background-position: center;
            font-family: Arial, sans-serif;
            color: white;
        }

        h1 {
            position: absolute;
            top: 20px;
            text-align: center;
            width: 100%;
            font-size: 2rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
        }

        video {
            width: 60%;
            height: auto;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.5);
            z-index: 2;
        }

        a {
            margin-top: 20px;
            color: white;
            font-size: 1.2rem;
            text-decoration: none;
            z-index: 2;
            background-color: rgba(0, 0, 0, 0.5);
            padding: 10px 20px;
            border-radius: 5px;
        }

        a:hover {
            background-color: rgba(0, 0, 0, 0.7);
        }

        /* Ajouter un filtre sombre sur l'arrière-plan pour plus de lisibilité */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1;
        }
    </style>
</head>
<body>
    <h1>ici on danse sur le champs de bataille</h1>

    <video controls>
        <source src="../src/assets/Trenches.mp4" type="video/mp4">
        Votre navigateur ne supporte pas la lecture de vidéos.
    </video>

    <a href="effectif.php">Voir les effectifs</a>
</body>
</html>
