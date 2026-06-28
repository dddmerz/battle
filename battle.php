<?php
session_start();

require_once __DIR__ . '/config/cards.php';

require_once __DIR__ . '/includes/Fighter.php';
require_once __DIR__ . '/includes/BattleLogger.php';
require_once __DIR__ . '/includes/BattleFunctions.php';
require_once __DIR__ . '/includes/BattleEngine.php';

if (!isset($_SESSION['twitch_user'])) {
    header("Location: login.php");
    exit;
}

$username = strtolower($_SESSION['twitch_user']);

$arenaFile = __DIR__ . "/data/arena.json";
$playersFile = __DIR__ . "/data/players.json";
$cardsFile = __DIR__ . "/data/cards.json";

$arena = [];

if(file_exists($arenaFile)){
    $arena = json_decode(file_get_contents($arenaFile), true);
}

$players = [];

if(file_exists($playersFile)){
    $players = json_decode(file_get_contents($playersFile), true);
}

$cardsData = [];

if(file_exists($cardsFile)){
    $cardsData = json_decode(file_get_contents($cardsFile), true);
}

$id = intval($_GET["id"] ?? 0);

$fight = null;
$fightIndex = null;

foreach($arena as $index=>$entry){

    if(($entry["id"] ?? 0) == $id){

        $fight = $entry;
        $fightIndex = $index;

        break;
    }

}

if(!$fight){
    die("Kampf nicht gefunden.");
}

if(
    ($fight["status"] ?? "") != "open"
    &&
    !isset($fight["battle_log"])
){
    die("Dieser Kampf wurde bereits abgeschlossen.");
}

if(
    ($fight["creator"] ?? "") == $username
    &&
    !isset($fight["battle_log"])
){
    die("Du kannst deinen eigenen Kampf nicht annehmen.");
}

if(!isset($players[$username])){

    $players[$username] = [

        "xp"=>0,
        "wins"=>0,
        "losses"=>0,
        "draws"=>0

    ];

}

$ownedCards = [];

if(isset($cardsData["Users"][$username])){

    $ownedCards = $cardsData["Users"][$username];

}

$error = "";
<?php
if(isset($_POST["startBattle"]) && !isset($fight["battle_log"])){

    $opponentCards = array_values(array_unique([

        intval($_POST["card1"] ?? 0),
        intval($_POST["card2"] ?? 0),
        intval($_POST["card3"] ?? 0)

    ]));

    $opponentCards = array_values(array_filter($opponentCards));

    if(count($opponentCards) !== 3){

        $error = "Bitte 3 verschiedene Karten wählen.";

    }else{

        foreach($opponentCards as $cardId){

            if(!isset($cards[$cardId]) || !isset($battleStats[$cardId])){

                $error = "Eine gewählte Karte ist ungültig.";
                break;

            }

        }

    }

    if($error == ""){

        $creatorCards = $fight["creator_cards"] ?? [];

        $engine = new BattleEngine();

        $battleLog = [];

        $creatorWins = 0;
        $opponentWins = 0;

        for($i=0;$i<3;$i++){

            $creatorCardId = intval($creatorCards[$i] ?? 0);
            $opponentCardId = intval($opponentCards[$i] ?? 0);

            if(
                !isset($cards[$creatorCardId]) ||
                !isset($battleStats[$creatorCardId]) ||
                !isset($cards[$opponentCardId]) ||
                !isset($battleStats[$opponentCardId])
            ){
                continue;
            }

            $creatorFighter = new Fighter(
                $creatorCardId,
                $cards,
                $battleStats
            );

            $opponentFighter = new Fighter(
                $opponentCardId,
                $cards,
                $battleStats
            );

            $result = $engine->run(
                $creatorFighter,
                $opponentFighter
            );

            if($result["winner"] == "creator"){

                $creatorWins++;

            }elseif($result["winner"] == "opponent"){

                $opponentWins++;

            }

            $battleLog[] = [

                "round" => $i + 1,

                "creator_card" => $creatorCardId,
                "opponent_card" => $opponentCardId,

                "winner" => $result["winner"],

                "creator" => $result["creator"],
                "opponent" => $result["opponent"],

                "log" => $result["log"]

            ];

        }

        if($creatorWins > $opponentWins){

            $winner = $fight["creator"];
            $loser = $username;

        }elseif($opponentWins > $creatorWins){

            $winner = $username;
            $loser = $fight["creator"];

        }else{

            $winner = "draw";
            $loser = null;

        }
                if($winner == "draw"){

            foreach([$fight["creator"], $username] as $player){

                if(!isset($players[$player])){

                    $players[$player] = [

                        "xp"=>0,
                        "wins"=>0,
                        "losses"=>0,
                        "draws"=>0

                    ];

                }

                if(!isset($players[$player]["draws"])){
                    $players[$player]["draws"] = 0;
                }

                $players[$player]["xp"] += 15;
                $players[$player]["draws"]++;

            }

        }else{

            foreach([$winner, $loser] as $player){

                if(!isset($players[$player])){

                    $players[$player] = [

                        "xp"=>0,
                        "wins"=>0,
                        "losses"=>0,
                        "draws"=>0

                    ];

                }

            }

            $players[$winner]["xp"] += 25;
            $players[$winner]["wins"]++;

            $players[$loser]["xp"] += 10;
            $players[$loser]["losses"]++;

        }

        $arena[$fightIndex]["creator_score"] = $creatorWins;
        $arena[$fightIndex]["opponent_score"] = $opponentWins;

        $arena[$fightIndex]["opponent"] = $username;
        $arena[$fightIndex]["opponent_cards"] = $opponentCards;

        $arena[$fightIndex]["winner"] = $winner;
        $arena[$fightIndex]["battle_log"] = $battleLog;
        $arena[$fightIndex]["status"] = "finished";

        file_put_contents(
            $arenaFile,
            json_encode($arena, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        file_put_contents(
            $playersFile,
            json_encode($players, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        $fight = $arena[$fightIndex];

    }

}

function arenaIcon($type){

    switch($type){

        case "critical":
            return "💥";

        case "heal":
            return "💚";

        case "shield":
            return "🛡️";

        case "lightning":
            return "⚡";

        case "legendary":
            return "🌈";

        default:
            return "⚔️";

    }

}
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Kampf</title>

<link rel="stylesheet" href="assets/css/style.css?v=10">
<link rel="stylesheet" href="assets/css/arena.css?v=10">
</head>

<body>

<header>
    <div class="logo">⚔️ Kampf</div>
</header>

<?php include 'nav.php'; ?>

<div class="container">

<?php if($error != ""): ?>

<div class="player-box">
    <h2>⚠️ Fehler</h2>
    <p><?= htmlspecialchars($error) ?></p>
</div>

<?php endif; ?>

<?php if(isset($fight["battle_log"])): ?>

<div class="player-box">

<h2>🏆 Kampfergebnis</h2>

<p>
<strong><?= htmlspecialchars($fight["creator"]) ?></strong>
gegen
<strong><?= htmlspecialchars($fight["opponent"] ?? $username) ?></strong>
</p>

<p>
<strong>Ergebnis:</strong>
<?= intval($fight["creator_score"] ?? 0) ?>
:
<?= intval($fight["opponent_score"] ?? 0) ?>
</p>

<?php foreach($fight["battle_log"] as $battle): ?>

<hr>

<h3>Runde <?= intval($battle["round"]) ?></h3>

<p>
<strong><?= htmlspecialchars($cards[$battle["creator_card"]]["name"]) ?></strong>
❤️ <?= intval($battle["creator"]["hp"] ?? 0) ?>/<?= intval($battle["creator"]["max_hp"] ?? 0) ?>

<br>

<strong>VS</strong>

<br>

<strong><?= htmlspecialchars($cards[$battle["opponent_card"]]["name"]) ?></strong>
❤️ <?= intval($battle["opponent"]["hp"] ?? 0) ?>/<?= intval($battle["opponent"]["max_hp"] ?? 0) ?>
</p>

<div class="battle-log">

<?php foreach(($battle["log"] ?? []) as $entry): ?>

<p class="battle-log-entry">
<?= arenaIcon($entry["type"] ?? "attack") ?>

<strong><?= htmlspecialchars($entry["actor"] ?? "") ?></strong>

→

<strong><?= htmlspecialchars($entry["target"] ?? "") ?></strong>

| Wert: <?= intval($entry["value"] ?? 0) ?>

<?php if(isset($entry["target_hp"], $entry["target_max_hp"])): ?>

| HP:
<?= intval($entry["target_hp"]) ?>/<?= intval($entry["target_max_hp"]) ?>

<?php endif; ?>
</p>

<?php endforeach; ?>

</div>

<?php endforeach; ?>

<hr>

<?php if(($fight["winner"] ?? "") == "draw"): ?>

<h3>🤝 Gesamtergebnis: Unentschieden</h3>

<?php else: ?>

<h3>🏆 Gesamtsieger: <?= htmlspecialchars($fight["winner"]) ?></h3>

<?php endif; ?>

<a href="arena.php" class="arena-button">
Zur Arena
</a>

</div>

<?php else: ?>

<h1><?= htmlspecialchars($fight["creator"]) ?> fordert dich heraus!</h1>

<?php
$c1 = intval($fight["creator_cards"][0]);
$c2 = intval($fight["creator_cards"][1]);
?>

<div class="fight-cards">

<img src="assets/cards/<?= htmlspecialchars($cards[$c1]["image"]) ?>" class="mini-card">

<img src="assets/cards/<?= htmlspecialchars($cards[$c2]["image"]) ?>" class="mini-card">

<div class="secret-card">❓</div>

</div>

<h2>Wähle deine 3 Karten</h2>

<div class="card-selection">

<?php foreach($ownedCards as $cardId): ?>

<?php if(!isset($cards[$cardId], $battleStats[$cardId])) continue; ?>

<div class="arena-card" data-card="<?= intval($cardId) ?>">

<img src="assets/cards/<?= htmlspecialchars($cards[$cardId]["image"]) ?>">

<div class="card-info">

<strong><?= htmlspecialchars($cards[$cardId]["name"]) ?></strong>

<br>

❤️ <?= intval($battleStats[$cardId]["health"]) ?>
⚔️ <?= intval($battleStats[$cardId]["attack"]) ?>

</div>

</div>

<?php endforeach; ?>

</div>

<form method="POST" id="battleForm">

<input type="hidden" name="card1" id="card1">
<input type="hidden" name="card2" id="card2">
<input type="hidden" name="card3" id="card3">

<button type="submit" name="startBattle" class="arena-button">
⚔️ Kampf starten
</button>

</form>

<script>
let selected = [];

document.querySelectorAll(".arena-card").forEach(card => {

    card.addEventListener("click", () => {

        let id = card.dataset.card;

        if(selected.includes(id)){

            selected = selected.filter(x => x !== id);
            card.classList.remove("selected");

        }else{

            if(selected.length >= 3){
                alert("Du kannst nur 3 Karten auswählen.");
                return;
            }

            selected.push(id);
            card.classList.add("selected");

        }

        document.getElementById("card1").value = selected[0] || "";
        document.getElementById("card2").value = selected[1] || "";
        document.getElementById("card3").value = selected[2] || "";

    });

});

document.getElementById("battleForm").addEventListener("submit", function(e){

    if(selected.length !== 3){

        e.preventDefault();
        alert("Bitte wähle zuerst 3 Karten aus.");

    }

});
</script>

<?php endif; ?>

</div>

</body>
</html>
