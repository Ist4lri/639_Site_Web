<!DOCTYPE html>
<html lang="fr">
<head>
    <?php include 'php/header.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NoMoreWaste</title>
    
    <style>
        h1 {
            font-size: 4rem;
            margin-bottom: 50px;
            text-align: center;
        }
        nav {
            display: flex;
            justify-content: center;
            gap: 50px;
            margin-bottom: 50px;
        }
        .join a {
            text-decoration: none;
            color: #007BFF;
            font-size: 1.5rem;
            padding: 10px 20px;
            border: 2px solid #007BFF;
            border-radius: 5px;
            transition: background-color 0.3s, color 0.3s;
            display: block;
            text-align: center;
        }
        .join a:hover {
            background-color: #007BFF;
            color: #fff;
        }
        .des {
            text-align: center;
            font-size: 1.2rem;
            color: #555;
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <h1>639th</h1>

    <nav>
        <div class="join">
            <a href="benevoles.php">Nous Rejoindre</a>
            <div class="des">Devenez bénévole</div>
        </div>
        <div class="join">
            <a href="comm.php">Devenir Partenaire</a>
            <div class="des">Devenez partenaire</div>
        </div>
    </nav>

</body>
</html>
