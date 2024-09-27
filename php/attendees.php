<?php
$attendees = [
    "CPI-47189 Snorri",
    "LPI-14785",
    "Fear/Ciceron/Magnus",
    "GI-22178 Rémus",
    "GIV-36931 Koda",
    "Lordis",
    "GIV-99999 Iota / Ludwig",
    "MJI-33669 JägerMeister",
    "CPI-02695 Jojin Konrad",
    "VEX/Dimatrikiv",
    "GI-24117 Paolo",
    "GI-78210-Tom",
    "GI-26023 Vaylias Ments",
    "Thaddeus /GI-48569",
    "Viktor cain",
    "PRI-Dalquiel",
    "Targus",
    "SC Gurke"
];

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
    <title>Liste des Participants</title>
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
    <h1>Participants Classés</h1>

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
