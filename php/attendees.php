<?php
require '../vendor/autoload.php'; // Si vous utilisez Guzzle pour faire des requêtes HTTP

// Configuration du bot Discord
$botToken = ''; // Remplacez par le token de votre bot
$guildID = '1162367370619269180'; // Remplacez par l'ID du serveur Discord où est Sesh
$eventChannelID = '1162367371210670102'; // Remplacez par l'ID du channel où se déroule l'événement

// URL de l'API Discord
$baseURL = "https://discord.com/api/v10/channels/{eventChannelID}/messages?limit=1";

// Fonction pour récupérer les membres d'un événement Sesh
function getEventAttendees($botToken, $guildID, $eventChannelID) {
    $url = "$baseURL/guilds/$guildID/members";
    
    try {
        // Utilisation de Guzzle pour la requête HTTP
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', $url, [
            'headers' => [
                'Authorization' => "Bot $botToken",
                'Content-Type' => 'application/json'
            ]
        ]);
        
        $members = json_decode($response->getBody(), true);
        
        // Filtrer les membres avec Sesh
        $attendees = array_filter($members, function ($member) use ($eventChannelID) {
            return in_array($eventChannelID, array_column($member['roles'], 'id'));
        });
        
        return $attendees;
        
    } catch (Exception $e) {
        echo 'Erreur: ' . $e->getMessage();
        return [];
    }
}

// Récupération des participants
$attendees = getEventAttendees($botToken, $guildID, $eventChannelID);

// Tri par catégories spécifiques (MJI, LTI, STI, etc.)
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
    $username = $attendee['user']['username'];
    if (preg_match('/^MJI-/', $username)) {
        $categories['MJI'][] = $username;
    } elseif (preg_match('/^LTI-/', $username)) {
        $categories['LTI'][] = $username;
    } elseif (preg_match('/^STI-/', $username)) {
        $categories['STI'][] = $username;
    } elseif (preg_match('/^CPI-/', $username)) {
        $categories['CPI'][] = $username;
    } elseif (preg_match('/^GIV-/', $username)) {
        $categories['GIV'][] = $username;
    } elseif (preg_match('/^GI-/', $username)) {
        $categories['GI'][] = $username;
    } elseif (preg_match('/^CI-/', $username)) {
        $categories['CI'][] = $username;
    } else {
        $categories['Others'][] = $username;
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
