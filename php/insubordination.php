<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insubordination</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(180deg, #444 0%, #000 100%);
            color: white;
            font-family: 'Arial', sans-serif;
            text-align: center;
        }

        .container {
            position: relative;
            width: 100%;
            max-width: 360px; /* Limite la taille du conteneur pour éviter le scroll */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background-color: rgba(0, 0, 0, 0.8);
            padding: 10px;
        }

        .container img {
            max-width: 88%;
            height: auto;
            display: block;
        }

        h1 {
            margin: 1px 0;
            font-size: 2.5rem; /* Plus grand et gras */
            color: #ff4747; /* Rouge vif pour l'effet d'avertissement */
            font-weight: bold;
        }

        p {
            font-size: 1.3rem; /* Ajuste la taille du texte */
            font-weight: bold; /* Texte en gras */
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Je sens de l'insubordination...</h1>
        <p>Vous n'êtes pas censé accéder à ces données !</p>
        <a href="../index.php">Retourner dans les rangs</a>
        <img src="../src/assets/Commissaire.jpg" alt="Insubordination">

    </div>
</body>
</html>
