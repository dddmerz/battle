<?php
include 'config/functions.php';
include 'config/cards.php';

$data = loadCardsData();
$users = $data['Users'] ?? [];

$totalUsers = count($users);
$totalCards = 0;
$collectors = 0;

foreach($users as $cardsOwned){
    $totalCards += count($cardsOwned);
    if(count($cardsOwned) >= 10){ $collectors++; }
}

$leaderboard = [];
foreach($users as $name => $cardsOwned){
    $leaderboard[$name] = count($cardsOwned);
}
arsort($leaderboard);
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>DeMerzli Community</title>
<link rel="stylesheet" href="assets/css/style.css?v=14">
<link rel="stylesheet" href="assets/css/style-v2.css?v=2">
</head>
<body>

<?php include 'nav.php'; ?>

<section class="hero-v3">
    <div class="hero-content">
        <h1>DEMERZLI</h1>
        <h2>Streamer • Community • Sammelkarten</h2>
        <p>Sammle Community-Karten, entdecke Geheimnisse und werde Teil der Community.</p>

        <div class="hero-buttons">
            <a href="https://www.twitch.tv/demerzli" class="hero-btn">🔴 Twitch</a>
            <a href="cards.php" class="hero-btn">🎴 Sammelkarten</a>
            <a href="https://discord.gg/demerzli" class="hero-btn">💬 Discord</a>
        </div>
    </div>
</section>

<div class="container">

<section class="stats-section">
<div class="stat-card"><span>🎴</span><h3><?php echo $totalCards; ?></h3><p>Karten gezogen</p></div>
<div class="stat-card"><span>👥</span><h3><?php echo $totalUsers; ?></h3><p>Sammler</p></div>
<div class="stat-card"><span>🌈</span><h3><?php echo $collectors; ?></h3><p>Sammlerkönige</p></div>
<div class="stat-card"><span>📚</span><h3><?php echo count($cards); ?></h3><p>Karten</p></div>
</section>

<div class="main-grid">

<section class="panel">
<h2>🎴 Beliebte Karten</h2>
<div class="showcase-grid">
<?php foreach([1,2,3,999] as $id): ?>
<a href="card.php?id=<?php echo $id; ?>">
<img src="assets/cards/<?php echo $cards[$id]['image']; ?>">
</a>
<?php endforeach; ?>
</div>
</section>

<section class="panel">
<h2>🏆 Top Sammler</h2>
<?php $i=1; foreach(array_slice($leaderboard,0,5,true) as $user=>$count): ?>
<div class="top-row">
<span><?php echo $i; ?>.</span>
<span><?php echo ucfirst($user); ?></span>
<span><?php echo $count; ?>/10</span>
</div>
<?php $i++; endforeach; ?>
</section>

</div>

<section class="community-panel">
<h2>📺 DeMerzli Community</h2>
<div class="community-grid">
<a href="https://www.twitch.tv/demerzli">📺 Twitch</a>
<a href="https://www.youtube.com/@demerzlitv">▶️ YouTube</a>
<a href="https://www.instagram.com/demerzli_tv/">📸 Instagram</a>
<a href="https://www.tiktok.com/@demerzli">🎵 TikTok</a>
<a href="https://discord.gg/demerzli">💬 Discord</a>
</div>
</section>

<section class="secret-panel">
<h2>🌈 Die geheime Sammelkarte</h2>
<p>Nur wahre Sammler werden sie entdecken.</p>
<a href="wiki.php" class="hero-btn">Mehr erfahren</a>
</section>

</div>
</body>
</html>