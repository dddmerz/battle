<?php
include 'config/cards.php';
include 'config/functions.php';

$user = $_GET['user'] ?? '';

$userCards = getUserCards($user);
$userCardCounts = getUserCardCounts($user);

if(empty($userCards)){
    die("<h1>Spieler nicht gefunden</h1>");
}

$players = json_decode(file_get_contents(__DIR__.'/data/players.json'), true) ?: [];

$player = $players[strtolower($user)] ?? [
    'xp' => 0,
    'wins' => 0,
    'losses' => 0
];

$xp = $player['xp'];
$wins = $player['wins'];
$losses = $player['losses'];

$level = floor($xp / 100) + 1;
$currentXP = $xp % 100;
$xpPercent = $currentXP;

$fights = $wins + $losses;

$progress = getProgress($userCards);
$specialUnlocked = count($userCards) >= 10;

$silver = 0;
$gold = 0;
$diamond = 0;

foreach($userCardCounts as $count){
    if($count >= 25) $diamond++;
    elseif($count >= 10) $gold++;
    elseif($count >= 5) $silver++;
}

$points = (count($userCards) * 10) + ($wins * 5) + ($level * 2);
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($user) ?></title>
<link rel="stylesheet" href="assets/css/style.css?v=20">
<link rel="stylesheet" href="assets/css/style-v2.css?v=2">
</head>
<body>

<header>
<div class="logo">🎴 <?= htmlspecialchars($user) ?></div>
</header>

<?php include 'nav.php'; ?>

<div class="container">

<h1><?= htmlspecialchars($user) ?></h1>

<div class="player-box">
<h2>⭐ Level <?= $level ?></h2>

<div class="progress">
    <div class="progress-fill" style="width:<?= $xpPercent ?>%"></div>
</div>

<div class="progress-text">
    <?= $currentXP ?> / 100 XP
</div>
</div>

<div class="profile-stats">
    <div class="stat-box">🎴<div><?= count($userCards) ?></div><small>Karten</small></div>
    <div class="stat-box">🥈<div><?= $silver ?></div><small>Silber</small></div>
    <div class="stat-box">🥇<div><?= $gold ?></div><small>Gold</small></div>
    <div class="stat-box">💎<div><?= $diamond ?></div><small>Diamant</small></div>
</div>

<div class="profile-stats">
    <div class="stat-box">⚔️<div><?= $fights ?></div><small>Kämpfe</small></div>
    <div class="stat-box">🏆<div><?= $wins ?></div><small>Siege</small></div>
    <div class="stat-box">💀<div><?= $losses ?></div><small>Niederlagen</small></div>
    <div class="stat-box">⭐<div><?= $level ?></div><small>Level</small></div>
    <div class="stat-box">🏆<div><?= $points ?></div><small>Punkte</small></div>
</div>

<h2>🎴 Season 1</h2>

<div class="progress">
    <div class="progress-fill" style="width:<?= $progress ?>%"></div>
</div>

<div class="progress-text">
    <?= count($userCards) ?>/10 Karten gesammelt
</div>

<div class="card-grid">
<?php
foreach($cards as $id => $card){

    $owned = ($id == 999) ? $specialUnlocked : in_array($id, $userCards);
    $count = $userCardCounts[$id] ?? 0;

    $badge = "";
    if($count >= 25) $badge = "💎 Diamant";
    elseif($count >= 10) $badge = "🥇 Gold";
    elseif($count >= 5) $badge = "🥈 Silber";

    echo "
    <a href='card.php?id={$id}' class='card-link'>
        <div class='card ".($owned ? "unlocked" : "locked")."'>
            <img src='".($owned ? "assets/cards/".$card['image'] : "assets/cards/locked.png")."'>
            <div class='card-title'>".($owned ? $card['name'] : '???')."</div>
            <div class='card-subtitle'>".($owned ? $card['title'] : 'Noch nicht entdeckt')."</div>";

    if($owned){
        echo "<div class='card-stats'>
                <span class='copies'>x{$count}</span>".
                ($badge ? "<span class='rank-badge'>{$badge}</span>" : "").
              "</div>";
    }

    echo "</div></a>";
}
?>
</div>

</div>
</body>
</html>
