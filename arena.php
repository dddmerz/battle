<?php
session_start();
require_once 'config/cards.php';

if (!isset($_SESSION['twitch_user'])) {
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Arena</title>

<link rel="stylesheet" href="assets/css/style.css">
<link rel="stylesheet" href="assets/css/arena.css">
</head>
<body>

<header>
    <div class="logo">⚔️ Arena</div>
</header>

<?php include 'nav.php'; ?>

<div class="container">

    <div class="player-box">

        <h2>🔒 Arena gesperrt</h2>

        <p>
            Du musst dich zuerst mit Twitch anmelden,
            um Kämpfe erstellen oder annehmen zu können.
        </p>

        <a href="login.php" class="arena-button">
            🎮 Mit Twitch anmelden
        </a>

    </div>

</div>

</body>
</html>
<?php
exit;
}

$username = strtolower($_SESSION['twitch_user']);

$cardsData = json_decode(file_get_contents(__DIR__.'/data/cards.json'), true) ?: [];
$arenaFile = __DIR__.'/data/arena.json';
$arena = json_decode(file_get_contents($arenaFile), true) ?: [];
$playersFile = __DIR__.'/data/players.json';
$players = json_decode(file_get_contents($playersFile), true) ?: [];

if (!isset($players[$username])) {
    $players[$username] = ['xp'=>0,'wins'=>0,'losses'=>0];
}

$ownedCards = $cardsData['Users'][$username] ?? [];
$level = floor(($players[$username]['xp'] ?? 0)/100)+1;

$hasOpenFight = false;
foreach($arena as $fight){
    if(($fight['creator'] ?? '') === $username && ($fight['status'] ?? '') === 'open'){
        $hasOpenFight = true;
        break;
    }
}

if(isset($_POST['createArena']) && !$hasOpenFight){
    $selected = array_unique([
        (int)$_POST['card1'],
        (int)$_POST['card2'],
        (int)$_POST['card3']
    ]);

    if(count($selected) === 3){
        $arena[] = [
            'id'=>time(),
            'creator'=>$username,
            'creator_cards'=>array_values($selected),
            'opponent'=>null,
            'opponent_cards'=>[],
            'status'=>'open',
            'winner'=>null,
            'created'=>time()
        ];

        file_put_contents($arenaFile, json_encode($arena, JSON_PRETTY_PRINT));
        header("Location: arena.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Arena</title>
<link rel="stylesheet" href="assets/css/style.css?v=9">
<link rel="stylesheet" href="assets/css/arena.css?v=3">
</head>
<body>

<header><div class="logo">⚔️ Arena</div></header>
<?php include 'nav.php'; ?>

<div class="container">
<div class="player-box">
<h2><?= htmlspecialchars($username) ?></h2>
<p>Level <?= $level ?></p>
<p>🏆 <?= $players[$username]['wins'] ?? 0 ?> | 💀 <?= $players[$username]['losses'] ?? 0 ?></p>
</div>


<div class="arena-layout">

<div class="arena-left">
<h2>⚔️ Offene Kämpfe</h2>

<div class="open-fights">
<?php foreach($arena as $fight): ?>
<?php if(($fight['status'] ?? '') !== 'open') continue; ?>

<?php
$c1 = $fight['creator_cards'][0];
$c2 = $fight['creator_cards'][1];
?>

<div class="fight-box">
<h3><?= htmlspecialchars($fight['creator']) ?></h3>

<div class="fight-cards">
<img src="assets/cards/<?= $cards[$c1]['image'] ?>" class="mini-card">
<img src="assets/cards/<?= $cards[$c2]['image'] ?>" class="mini-card">
<div class="secret-card">❓</div>
</div>

<?php if($fight['creator'] !== $username): ?>
<a href="battle.php?id=<?= $fight['id'] ?>" class="arena-button">Kampf annehmen</a>
<?php endif; ?>

</div>
<?php endforeach; ?>
</div>

</div>

<div class="arena-right">
<h2>🎴 Neue Herausforderung</h2>

<?php if(!$hasOpenFight): ?>

<div id="selectedCards" class="selected-preview">
Noch keine Karten gewählt
</div>

<div class="card-selection">
<?php foreach($ownedCards as $cardId): ?>
<div class="arena-card"
     data-card="<?= $cardId ?>"
     data-name="<?= htmlspecialchars($cards[$cardId]['name']) ?>">
<img src="assets/cards/<?= $cards[$cardId]['image'] ?>">
</div>
<?php endforeach; ?>
</div>

<form method="POST">
<input type="hidden" name="card1" id="card1">
<input type="hidden" name="card2" id="card2">
<input type="hidden" name="card3" id="card3">

<button type="submit" name="createArena" class="arena-button">
⚔️ Kampf eröffnen
</button>
</form>

<?php else: ?>
<div class="warning-box">Du hast bereits einen offenen Kampf.</div>
<?php endif; ?>

</div>

</div>
</div>

<script>
let selected = [];

function updatePreview(){
 const preview=document.getElementById('selectedCards');
 if(!preview) return;

 if(selected.length===0){
   preview.innerHTML='Noch keine Karten gewählt';
   return;
 }

 preview.innerHTML=selected.map(c=>'<span>'+c.name+'</span>').join(' ');
}

document.querySelectorAll('.arena-card').forEach(card=>{
 card.addEventListener('click',()=>{
   const id=card.dataset.card;
   const name=card.dataset.name;

   const exists=selected.find(x=>x.id===id);

   if(exists){
     selected=selected.filter(x=>x.id!==id);
     card.classList.remove('selected');
   }else{
     if(selected.length>=3) return;
     selected.push({id,name});
     card.classList.add('selected');
   }

   document.getElementById('card1').value=selected[0]?.id||'';
   document.getElementById('card2').value=selected[1]?.id||'';
   document.getElementById('card3').value=selected[2]?.id||'';

   updatePreview();
 });
});
</script>

</body>
</html>
