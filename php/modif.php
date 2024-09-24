<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_utilisateur'])) {
    $id_utilisateur = $_POST['id_utilisateur'];

    // Récupérer les informations médicales de l'utilisateur
    $stmt = $pdo->prepare("SELECT * FROM informations_medicales WHERE id_utilisateur = ?");
    $stmt->execute([$id_utilisateur]);
    $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);

    if (isset($_POST['update_info'])) {
        // Récupérer les nouvelles valeurs du formulaire
        $age = $_POST['age'];
        $taille = $_POST['taille'];
        $poids = $_POST['poids'];
        $groupe_sanguin = $_POST['groupe_sanguin'];
        $classe_spe = $_POST['classe_spe'];
        $monde_origine = $_POST['monde_origine'];
        $antecedents_biologiques = $_POST['antecedents_biologiques'];
        $antecedents_psychologiques = $_POST['antecedents_psychologiques'];
        $fumeurs = isset($_POST['fumeurs']) ? 1 : 0;
        $allergies = $_POST['allergies'];
        $intolerances = $_POST['intolerances'];
        $commentaires = $_POST['commentaires'];

        // Mise à jour des informations médicales
        $stmt = $pdo->prepare("UPDATE informations_medicales SET age = ?, taille = ?, poids = ?, groupe_sanguin = ?, classe_spe = ?, monde_origine = ?, antecedents_biologiques = ?, antecedents_psychologiques = ?, fumeurs = ?, allergies = ?, intolerances = ?, commentaires = ? WHERE id_utilisateur = ?");
        $stmt->execute([$age, $taille, $poids, $groupe_sanguin, $classe_spe, $monde_origine, $antecedents_biologiques, $antecedents_psychologiques, $fumeurs, $allergies, $intolerances, $commentaires, $id_utilisateur]);

        $success_message = "Les informations médicales ont été mises à jour avec succès.";
        header("Location: medicae_info.php");
        exit();
    }
} else {
    // Rediriger si l'accès à la page est incorrect
    header("Location: medicae_info.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier les Informations Médicales</title>
    <style>/* modif.css */

/* Général */
body {
    font-family: 'Arial', sans-serif;
    background-color: #3C3C3C;
    color: #333;
    margin: 0;
    padding: 0;
}

/* Conteneur principal */
.container {
    max-width: 800px;
    margin: 50px auto;
    padding: 20px;
    background-color: #7C7C7C;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
}

/* Titres */
h2 {
    text-align: center;
    color: #444;
    margin-bottom: 20px;
    font-size: 28px;
}

/* Formulaire */
form {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

/* Labels */
label {
    font-weight: bold;
    font-size: 14px;
    color: #FFFFFF;
}

/* Champs de texte et textareas */
input[type="text"],
input[type="number"],
textarea {
    width: 94%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 16px;
    color: #333;
    background-color: #f9f9f9;
    transition: border-color 0.3s ease;
}

input[type="text"]:focus,
input[type="number"]:focus,
textarea:focus {
    border-color: #1e90ff;
    outline: none;
}

/* Textarea pour les commentaires et antécédents */
textarea {
    resize: vertical;
    min-height: 100px;
}

/* Case à cocher */
input[type="checkbox"] {
    width: auto;
    transform: scale(1.2);
    margin-right: 10px;
}

/* Section de validation */
.btn {
    display: inline-block;
    padding: 10px 15px;
    font-size: 16px;
    color: #fff;
    background-color: #28a745;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    text-align: center;
}

.btn:hover {
    background-color: #218838;
}

.alert {
    padding: 15px;
    background-color: #f44336;
    color: white;
    margin-bottom: 20px;
    border-radius: 5px;
}

.alert-success {
    background-color: #4CAF50;
}

/* Pour le champ date de modification */
input[readonly] {
    background-color: #e9ecef;
    border: 1px solid #ccc;
    color: #777;
}

/* Responsiveness pour mobile */
@media (max-width: 768px) {
    .container {
        padding: 15px;
        margin: 20px;
    }

    h2 {
        font-size: 24px;
    }

    input[type="text"],
    input[type="number"],
    textarea {
        font-size: 14px;
    }

    .btn {
        padding: 10px 12px;
        font-size: 14px;
    }
}
</style>
    
</head>
<body>
    <div class="container">
        <h2>Modifier les Informations Médicales</h2>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>

        <form action="modif.php" method="post">
            <input type="hidden" name="id_utilisateur" value="<?php echo htmlspecialchars($id_utilisateur); ?>">

            <label for="date_modification">Date de modification:</label>
            <input type="text" id="date_modification" name="date_modification" value="<?php echo date('d/m/Y'); ?>" readonly>

            <label for="groupe_sanguin">Groupe Sanguin :</label>
            <input type="text" id="groupe_sanguin" name="groupe_sanguin" value="<?php echo htmlspecialchars($userInfo['groupe_sanguin'] ?? ''); ?>" required>

            <label for="taille">Taille (cm) :</label>
            <input type="number" id="taille" name="taille" value="<?php echo htmlspecialchars($userInfo['taille'] ?? ''); ?>" required>

            <label for="poids">Poids (kg) :</label>
            <input type="number" id="poids" name="poids" value="<?php echo htmlspecialchars($userInfo['poids'] ?? ''); ?>" required>

            <label for="age">Âge :</label>
            <input type="number" id="age" name="age" value="<?php echo htmlspecialchars($userInfo['age'] ?? ''); ?>" required>

            <label for="classe_spe">Classe / Spé :</label>
            <input type="text" id="classe_spe" name="classe_spe" value="<?php echo htmlspecialchars($userInfo['classe_spe'] ?? ''); ?>" required>

            <label for="monde_origine">Monde d'origine :</label>
            <input type="text" id="monde_origine" name="monde_origine" value="<?php echo htmlspecialchars($userInfo['monde_origine'] ?? ''); ?>" required>

            <label for="antecedents_biologiques">Antécédents biologiques / physiques :</label>
            <textarea id="antecedents_biologiques" name="antecedents_biologiques" rows="4" required><?php echo htmlspecialchars($userInfo['antecedents_biologiques'] ?? ''); ?></textarea>

            <label for="antecedents_psychologiques">Antécédents psychologiques :</label>
            <textarea id="antecedents_psychologiques" name="antecedents_psychologiques" rows="4" required><?php echo htmlspecialchars($userInfo['antecedents_psychologiques'] ?? ''); ?></textarea>

            <label for="fumeurs">Fumeurs :</label>
            <input type="checkbox" id="fumeurs" name="fumeurs" <?php if (!empty($userInfo['fumeurs'])) echo 'checked'; ?>>

            <label for="allergies">Allergies :</label>
            <textarea id="allergies" name="allergies" rows="2"><?php echo htmlspecialchars($userInfo['allergies'] ?? ''); ?></textarea>

            <label for="intolerances">Intolérances :</label>
            <textarea id="intolerances" name="intolerances" rows="2"><?php echo htmlspecialchars($userInfo['intolerances'] ?? ''); ?></textarea>

            <label for="commentaires">Commentaires :</label>
            <textarea id="commentaires" name="commentaires" rows="4"><?php echo htmlspecialchars($userInfo['commentaires'] ?? ''); ?></textarea>

            <button type="submit" name="update_info" class="btn btn-success">Confirmer</button>
        </form>
    </div>
</body>
</html>
