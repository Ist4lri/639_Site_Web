<?php
require '../vendor/autoload.php'; // Si vous utilisez Guzzle pour faire des requêtes HTTP

// Configuration du bot Discord
$botToken = ''; // Remplacez par le token de votre bot
$guildID = '831854428515467276'; // ID du serveur Discord
$eventChannelID = '905063808228274187'; // ID du channel où se déroule l'événement

// URL de l'API Discord pour récupérer les messages
$baseURL = "https://discord.com/api/v10/channels/$eventChannelID/messages?limit=1";

// Fonction pour récupérer le dernier message du canal
function getLastMessage($botToken, $baseURL) {
    try {
        // Utilisation de Guzzle pour la requête HTTP
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', $baseURL, [
            'headers' => [
                'Authorization' => "Bot $botToken",
                'Content-Type' => 'application/json'
            ]
        ]);

        $messages = json_decode($response->getBody(), true);
        return $messages[0]['content']; // Retourne le contenu du dernier message
    } catch (Exception $e) {
        echo 'Erreur: ' . $e->getMessage();
        return '';
    }
}

// Récupération du dernier message dans le canal
$lastMessage = getLastMessage($botToken, $baseURL);

// Extraction des participants à partir du message Sesh
$attendees = [];
if (preg_match('/Attendees \(\d+\): (.*)/', $lastMessage, $matches)) {
    $attendees = explode(',', $matches[1]); // Sépare les noms d'utilisateurs
}

// Catégorisation des participants
$categories = [
    "MJI" => [],
    "LTI" => [],
    "STI" => [],
    "CPI" => [],
    "GIV" => [],
    "GI" => [],
    "CI" => [],
    "Others" => []
];

foreach ($attendees as $attendee) {
    $attendee = trim($attendee); // Supprime les espaces inutiles
    if (preg_match('/^MJI-/', $attendee)) {
        $categories['MJI'][] = $attendee;
    } elseif (preg_match('/^LTI-/', $attendee)) {
        $categories['LTI'][] = $attendee;
    } elseif (preg_match('/^STI-/', $attendee)) {
        $categories['STI'][] = $attendee;
    } elseif (preg_match('/^CPI-/', $attendee)) {
        $categories['CPI'][] = $attendee;
    } elseif (preg_match('/^GIV-/', $attendee)) {
        $categories['GIV'][] = $attendee;
    } elseif (preg_match('/^GI-/', $attendee)) {
        $categories['GI'][] = $attendee;
    } elseif (preg_match('/^CI-/', $attendee)) {
        $categories['CI'][] = $attendee;
    } else {
        $categories['Others'][] = $attendee;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Participants Discord</title>
    <style>
        body {
            background-color: #2c2f33;
            color: #ffffff;
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        h2 {
            background-color: #7289da;
            color: #ffffff;
            padding: 10px;
            border-radius: 5px;
        }
        .category {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ffffff;
            border-radius: 5px;
        }
        .attendee {
            padding: 5px;
        }
        .others {
            background-color: #99aab5;
            border: 1px solid #ffffff;
            padding: 10px;
        }
    </style>
</head>
<body>
    <h1>Participants Classés de Discord</h1>

    <?php foreach ($categories as $category => $list): ?>
        <?php if (!empty($list) && $category !== "Others"): ?>
            <div class="category">
                <h2><?php echo htmlspecialchars($category); ?></h2>
                <?php foreach ($list as $attendee): ?>
                    <div class="attendee"><?php echo htmlspecialchars($attendee); ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>

    <h2>Autres Participants</h2>
    <div class="others">
        <?php foreach ($categories['Others'] as $attendee): ?>
            <div class="attendee"><?php echo htmlspecialchars($attendee); ?></div>
        <?php endforeach; ?>
    </div>
</body>
</html>
