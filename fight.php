<?php

session_start();

if (!isset($_SESSION['twitch_user'])) {
    die("Bitte zuerst einloggen.");
}

include 'config/cards.php';
include 'config/battle_stats.php';

$currentUser = $_SESSION['twitch_user'];

$enemy = $_POST['enemy'] ?? '';
$team = $_POST['team'] ?? [];

if (empty($enemy)) {
    die("Kein Gegner gewählt.");
}

if (empty($team)) {
    die("Keine Karten ausgewählt.");
}

$data = json_decode(
    file_get_contents(__DIR__ . '/data/cards.json'),
    true
);

$users = $data['Users'] ?? [];

if (!isset($users[$enemy])) {
    die("Gegner nicht gefunden.");
}

/*
|--------------------------------------------------------------------------
| Gegnerteam erstellen
|--------------------------------------------------------------------------
*/

$enemyCards = $users[$enemy];

shuffle($enemyCards);

$enemyTeam = array_slice(
    $enemyCards,
    0,
    min(3, count($enemyCards))
);

/*
|--------------------------------------------------------------------------
| Kampfstärke berechnen
|--------------------------------------------------------------------------
*/

function calculatePower($cardIds, $battleStats)
{
    $attack = 0;
    $health = 0;

    foreach ($cardIds as $cardId)
    {
        if (!isset($battleStats[$cardId]))
        {
            continue;
        }

        $attack += $battleStats[$cardId]['attack'];
        $health += $battleStats[$cardId]['health'];
    }

    return [
        'attack' => $attack,
        'health' => $health,
        'power'  => $attack + $health
    ];
}

$playerStats = calculatePower(
    $team,
    $battleStats
);

$enemyStats = calculatePower(
    $enemyTeam,
    $battleStats
);

/*
|--------------------------------------------------------------------------
| Sieger bestimmen
|--------------------------------------------------------------------------
*/

if ($playerStats['power'] >= $enemyStats['power'])
{
    $winner = $currentUser;
    $loser = $enemy;
}
else
{
    $winner = $enemy;
    $loser = $currentUser;
}

/*
|--------------------------------------------------------------------------
| Kampf speichern
|--------------------------------------------------------------------------
*/

$battlesFile = __DIR__ . '/data/battles.json';

$battles = [];

if (file_exists($battlesFile))
{
    $battles = json_decode(
        file_get_contents($battlesFile),
        true
    ) ?: [];
}

$battles[] = [

    'winner' => $winner,
    'loser' => $loser,

    'player_power' =>
        $playerStats['power'],

    'enemy_power' =>
        $enemyStats['power'],

    'date' =>
        date('Y-m-d H:i:s')
];

file_put_contents(
    $battlesFile,
    json_encode(
        $battles,
        JSON_PRETTY_PRINT
    )
);

/*
|--------------------------------------------------------------------------
| Ranking speichern
|--------------------------------------------------------------------------
*/

$rankingFile = __DIR__ . '/data/rankings.json';

$rankings = [];

if (file_exists($rankingFile))
{
    $rankings = json_decode(
        file_get_contents($rankingFile),
        true
    ) ?: [];
}

foreach ([$currentUser, $enemy] as $user)
{
    if (!isset($rankings[$user]))
    {
        $rankings[$user] = [
            'wins' => 0,
            'losses' => 0
        ];
    }
}

$rankings[$winner]['wins']++;
$rankings[$loser]['losses']++;

file_put_contents(
    $rankingFile,
    json_encode(
        $rankings,
        JSON_PRETTY_PRINT
    )
);

?>

<!DOCTYPE html>
<html lang="de">

<head>

<meta charset="UTF-8">

<title>⚔️ Kampfergebnis</title>

<link rel="stylesheet" href="assets/css/style.css?v=14">
<link rel="stylesheet" href="assets/css/style-v2.css?v=2">
<link rel="stylesheet" href="assets/css/battle.css?v=4">

</head>

<body>

<header>
    <div class="logo">
        🎴 DeMerzli Sammelkarten
    </div>
</header>

<?php include 'nav.php'; ?>

<div class="container battle-result">

    <div class="winner-banner">

        🏆 SIEGER

        <br>

        <?php echo strtoupper($winner); ?>

    </div>

    <div class="battle-layout">

        <div class="team-side">

            <h2>⚔️ Dein Team</h2>

            <div class="team-cards">

                <?php foreach($team as $cardId): ?>

                    <div class="battle-card">

                        <img
                        src="assets/cards/<?=
                        $cards[$cardId]['image'];
                        ?>">

                        <div class="card-name">
                            <?= $cards[$cardId]['name']; ?>
                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        </div>

        <div class="battle-vs">
            VS
        </div>

        <div class="team-side">

            <h2>🛡️ Gegner</h2>

            <div class="team-cards">

                <?php foreach($enemyTeam as $cardId): ?>

                    <div class="battle-card">

                        <img
                        src="assets/cards/<?=
                        $cards[$cardId]['image'];
                        ?>">

                        <div class="card-name">
                            <?= $cards[$cardId]['name']; ?>
                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        </div>

    </div>

    <div class="battle-stats">

        <h2>📊 Kampfstatistik</h2>

        <div class="stat-row">

            <span>⚔️ Angriff</span>

            <strong>

                <?= $playerStats['attack']; ?>

                vs

                <?= $enemyStats['attack']; ?>

            </strong>

        </div>

        <div class="stat-row">

            <span>❤️ Herzen</span>

            <strong>

                <?= $playerStats['health']; ?>

                vs

                <?= $enemyStats['health']; ?>

            </strong>

        </div>

        <div class="stat-row">

            <span>🔥 Kampfstärke</span>

            <strong>

                <?= $playerStats['power']; ?>

                vs

                <?= $enemyStats['power']; ?>

            </strong>

        </div>

    </div>

    <div class="battle-log">

        <h2>📜 Kampfbericht</h2>

        <p>

            <?= ucfirst($winner); ?>

            gewinnt das Duell gegen

            <?= ucfirst($loser); ?>

        </p>

        <p>

            Endstand:

            <?= $playerStats['power']; ?>

            :

            <?= $enemyStats['power']; ?>

        </p>

    </div>

    <br>

    <a
    href="battle.php"
    class="hero-btn">

        ⚔️ Neues Duell

    </a>

</div>

</body>
</html>