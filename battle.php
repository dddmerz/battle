<?php
session_start();

require_once 'config/cards.php';

require_once 'includes/Fighter.php';
require_once 'includes/BattleFunctions.php';
require_once 'includes/BattleLogger.php';
require_once 'includes/BattleEngine.php';

if (!isset($_SESSION['twitch_user'])) {
    header("Location: login.php");
    exit;
}

$username = strtolower($_SESSION['twitch_user']);

$arenaFile   = __DIR__ . '/data/arena.json';
$playersFile = __DIR__ . '/data/players.json';
$cardsFile   = __DIR__ . '/data/cards.json';

$arena = json_decode(file_get_contents($arenaFile), true) ?: [];
$players = json_decode(file_get_contents($playersFile), true) ?: [];
$cardsData = json_decode(file_get_contents($cardsFile), true) ?: [];

$id = (int)($_GET['id'] ?? 0);

$fight = null;
$fightIndex = null;

foreach ($arena as $index => $entry) {

    if (($entry['id'] ?? 0) == $id) {

        $fight = $entry;
        $fightIndex = $index;
        break;
    }
}

if (!$fight) {
    die("Kampf nicht gefunden.");
}

if (
    ($fight['status'] ?? '') != 'open'
    &&
    !isset($fight['battle_log'])
) {
    die("Kampf bereits beendet.");
}

if (($fight['creator'] ?? '') == $username) {
    die("Du kannst deinen eigenen Kampf nicht annehmen.");
}

if (!isset($players[$username])) {

    $players[$username] = [

        "xp" => 0,
        "wins" => 0,
        "losses" => 0,
        "draws" => 0

    ];
}

$ownedCards = $cardsData["Users"][$username] ?? [];

if (
    isset($_POST["startBattle"])
    &&
    !isset($fight["battle_log"])
) {

    $opponentCards = array_values(array_unique([

        (int)$_POST["card1"],
        (int)$_POST["card2"],
        (int)$_POST["card3"]

    ]));

    if (count($opponentCards) != 3) {
        die("Bitte 3 verschiedene Karten auswählen.");
    }

    $creatorCards = $fight["creator_cards"];

    $engine = new BattleEngine();

    $battleLog = [];

    $creatorWins = 0;
    $opponentWins = 0;

    for ($i = 0; $i < 3; $i++) {

        $creator = new Fighter(
            $creatorCards[$i],
            $cards,
            $battleStats
        );

        $opponent = new Fighter(
            $opponentCards[$i],
            $cards,
            $battleStats
        );

        $result = $engine->fight(
            $creator,
            $opponent
        );

        $battleLog[] = [

            "round" => $i + 1,

            "creator_card" => $creatorCards[$i],

            "opponent_card" => $opponentCards[$i],

            "winner" => $result["winner"],

            "creator_hp" => $result["creator"]["hp"],

            "opponent_hp" => $result["opponent"]["hp"],

            "log" => $result["log"]

        ];

        if ($result["winner"] == "creator") {

            $creatorWins++;

        } else {

            $opponentWins++;

        }

    }

    if ($creatorWins > $opponentWins) {

        $winner = $fight["creator"];
        $loser = $username;

    } elseif ($opponentWins > $creatorWins) {

        $winner = $username;
        $loser = $fight["creator"];

    } else {

        $winner = "draw";

    }

    // XP

    if ($winner != "draw") {

        foreach ([$winner, $loser] as $player) {

            if (!isset($players[$player])) {

                $players[$player] = [

                    "xp" => 0,
                    "wins" => 0,
                    "losses" => 0,
                    "draws" => 0

                ];
            }

        }

        $players[$winner]["xp"] += 25;
        $players[$winner]["wins"]++;

        $players[$loser]["xp"] += 10;
        $players[$loser]["losses"]++;

    } else {

        foreach ([$fight["creator"], $username] as $player) {

            if (!isset($players[$player]["draws"])) {

                $players[$player]["draws"] = 0;

            }

            $players[$player]["xp"] += 15;
            $players[$player]["draws"]++;

        }

    }

    $arena[$fightIndex]["creator_score"] = $creatorWins;
    $arena[$fightIndex]["opponent_score"] = $opponentWins;

    $arena[$fightIndex]["winner"] = $winner;

    $arena[$fightIndex]["opponent"] = $username;

    $arena[$fightIndex]["opponent_cards"] = $opponentCards;

    $arena[$fightIndex]["battle_log"] = $battleLog;

    $arena[$fightIndex]["status"] = "finished";

    file_put_contents(
        $arenaFile,
        json_encode($arena, JSON_PRETTY_PRINT)
    );

    file_put_contents(
        $playersFile,
        json_encode($players, JSON_PRETTY_PRINT)
    );

    $fight = $arena[$fightIndex];
}

<!DOCTYPE html>
<html lang="de">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Arena Kampf</title>

<link rel="stylesheet" href="assets/css/style.css?v=10">
<link rel="stylesheet" href="assets/css/arena.css?v=10">

</head>

<body>

<header>

    <div class="logo">

        ⚔️ Arena

    </div>

</header>

<?php include 'nav.php'; ?>

<div class="container">

<?php if(isset($fight["battle_log"])): ?>

<div class="player-box">

<h2>🏆 Kampfergebnis</h2>

<p>

<strong>

<?= htmlspecialchars($fight["creator"]) ?>

</strong>

gegen

<strong>

<?= htmlspecialchars($fight["opponent"]) ?>

</strong>

</p>

<p>

Endstand

<strong>

<?= $fight["creator_score"] ?>

:

<?= $fight["opponent_score"] ?>

</strong>

</p>

<?php

foreach($fight["battle_log"] as $battle):

?>

<hr>

<h3>

Runde <?= $battle["round"] ?>

</h3>

<div class="battle-round">

<div class="battle-card">

<img
src="assets/cards/<?= $cards[$battle["creator_card"]]["image"] ?>"
class="mini-card">

<h4>

<?= $cards[$battle["creator_card"]]["name"] ?>

</h4>

<p>

❤️

<?= $battle["creator_hp"] ?>

HP

</p>

</div>

<div class="battle-vs">

VS

</div>

<div class="battle-card">

<img
src="assets/cards/<?= $cards[$battle["opponent_card"]]["image"] ?>"
class="mini-card">

<h4>

<?= $cards[$battle["opponent_card"]]["name"] ?>

</h4>

<p>

❤️

<?= $battle["opponent_hp"] ?>

HP

</p>

</div>

</div>

<div class="battle-log">

<?php foreach($battle["log"] as $entry): ?>

<div class="battle-log-entry">

<?php

switch($entry["type"]){

case "attack":

echo "⚔️ ";

break;

case "critical":

echo "💥 ";

break;

case "heal":

echo "💚 ";

break;

case "shield":

echo "🛡 ";

break;

case "lightning":

echo "⚡ ";

break;

case "legendary":

echo "🌈 ";

break;

default:

echo "• ";

}

?>

<strong>

<?= htmlspecialchars($entry["attacker"]) ?>

</strong>

→

<strong>

<?= htmlspecialchars($entry["target"]) ?>

</strong>

|

<?= $entry["value"] ?>

|

HP:

<?= $entry["target_hp"] ?>

</div>

<?php endforeach; ?>

</div>

<?php endforeach; ?>

<hr>

<h2>

<?php

if($fight["winner"]=="draw"){

echo "🤝 Unentschieden";

}else{

echo "🏆 Gewinner: ".htmlspecialchars($fight["winner"]);

}

?>

</h2>

<a
href="arena.php"
class="arena-button">

Zur Arena

</a>

</div>

<?php else: ?>

<h2>

<?= htmlspecialchars($fight["creator"]) ?>

fordert dich heraus!

</h2>

<?php

$c1=$fight["creator_cards"][0];

$c2=$fight["creator_cards"][1];

?>

<div class="fight-cards">

<img
src="assets/cards/<?= $cards[$c1]["image"] ?>"
class="mini-card">

<img
src="assets/cards/<?= $cards[$c2]["image"] ?>"
class="mini-card">

<div class="secret-card">

❓

</div>

</div>

<h3>

Wähle deine 3 Karten

</h3>

<div class="card-selection">

<?php foreach($ownedCards as $cardId): ?>

<div
class="arena-card"
data-card="<?= $cardId ?>">

<img
src="assets/cards/<?= $cards[$cardId]["image"] ?>">

<div class="card-info">

<strong>

<?= $cards[$cardId]["name"] ?>

</strong>

<br>

❤️

<?= $battleStats[$cardId]["health"] ?>

&nbsp;

⚔️

<?= $battleStats[$cardId]["attack"] ?>

</div>

</div>

<?php endforeach; ?>

</div>

<form method="POST">

<input
type="hidden"
name="card1"
id="card1">

<input
type="hidden"
name="card2"
id="card2">

<input
type="hidden"
name="card3"
id="card3">

<button
type="submit"
name="startBattle"
class="arena-button">

⚔️ Kampf starten

</button>

</form>

<?php endif; ?>
<script>

let selected = [];

document.querySelectorAll(".arena-card").forEach(card => {

    card.addEventListener("click", function () {

        let id = this.dataset.card;

        if (selected.includes(id)) {

            selected = selected.filter(x => x != id);

            this.classList.remove("selected");

        } else {

            if (selected.length >= 3) {

                alert("Du kannst nur 3 Karten auswählen.");

                return;

            }

            selected.push(id);

            this.classList.add("selected");

        }

        document.getElementById("card1").value = selected[0] || "";
        document.getElementById("card2").value = selected[1] || "";
        document.getElementById("card3").value = selected[2] || "";

    });

});

document.querySelector("form")?.addEventListener("submit", function(e){

    if(selected.length != 3){

        e.preventDefault();

        alert("Bitte wähle zuerst 3 Karten aus.");

    }

});

</script>

</div>

</body>
</html>
