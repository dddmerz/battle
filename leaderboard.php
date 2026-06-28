<?php

include 'config/functions.php';

$data = loadCardsData();
$users = $data['Users'];

$players = json_decode(
    file_get_contents(__DIR__ . '/data/players.json'),
    true
) ?: [];

$leaderboard = [];

foreach($users as $username => $cards)
{
    $realCards = array_filter($cards, function($card){
        return $card != 999;
    });

    $cardCount = count($realCards);

    $wins = $players[$username]['wins'] ?? 0;
    $xp = $players[$username]['xp'] ?? 0;

    $level = floor($xp / 100) + 1;

    $points =
        ($cardCount * 10)
        +
        ($wins * 5)
        +
        ($level * 2);

    $leaderboard[] = [
    'user' => $username,
    'count' => $cardCount,
    'wins' => $wins,
    'losses' => $players[$username]['losses'] ?? 0,
    'xp' => $xp,
    'level' => $level,
    'points' => $points
];
}

usort($leaderboard, function($a, $b){
    return $b['points'] <=> $a['points'];
});

?>

<!DOCTYPE html>
<html lang="de">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>🏆 Topliste</title>

<link rel="stylesheet" href="assets/css/style.css?v=11">

</head>

<body>

<header>
    <div class="logo">
        🏆 Topliste
    </div>
</header>

<?php include 'nav.php'; ?>

<div class="container">

<h1 style="text-align:center;margin-bottom:30px;">
🏆 Die besten Spieler
</h1>

<?php

$rank = 1;

foreach($leaderboard as $entry)
{
    $percent = ($entry['count'] / 10) * 100;

    $medal = "🎴";

    if($rank == 1) $medal = "🥇";
    if($rank == 2) $medal = "🥈";
    if($rank == 3) $medal = "🥉";

?>

<div class="leaderboard-card">

    <div class="leaderboard-top">

        <div class="leaderboard-rank">
            <?= $medal ?> #<?= $rank ?>
        </div>

        <div class="leaderboard-user">
            <a href="profile.php?user=<?= urlencode($entry['user']) ?>">
                <?= htmlspecialchars($entry['user']) ?>
            </a>
        </div>

        <div class="leaderboard-score">
            🏆 <?= $entry['points'] ?>
        </div>

    </div>

    <div class="progress-bar">
        <div
            class="progress-fill"
            style="width:<?= $percent ?>%">
        </div>
        
    </div>


    <div class="leaderboard-details">

    <span>🎴 Karten: <?= $entry['count'] ?>/10</span>

    <span>⚔️ Siege: <?= $entry['wins'] ?></span>
    
    <span>💀 Niederlagen: <?= $entry['losses'] ?></span>

    <span>⭐ Level: <?= $entry['level'] ?></span>

</div>

</div>

<?php

    $rank++;
}

?>

</div>

</body>
</html>