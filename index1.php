<?php

include 'config/functions.php';
include 'config/cards.php';

$data = loadCardsData();

$users = $data['Users'];

$totalUsers = count($users);

$totalCards = 0;
$collectors = 0;

foreach($users as $userCards)
{
    $count = count($userCards);

    $totalCards += $count;

    if($count >= 10)
    {
        $collectors++;
    }
}

/*
|--------------------------------------------------------------------------
| Top Sammler berechnen
|--------------------------------------------------------------------------
*/

$leaderboard = [];

foreach($users as $username => $userCards)
{
    $leaderboard[$username] = count($userCards);
}

arsort($leaderboard);

$topCollectors =
array_slice(
    $leaderboard,
    0,
    5,
    true
);
?>
<!DOCTYPE html>
<html lang="de">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1.0">

<title>
DeMerzli Community
</title>

<link rel="stylesheet" href="assets/css/style.css?v=14">
<link rel="stylesheet" href="assets/css/style-v2.css?v=2">

</head>

<body>

<header>

    <div class="logo">
        🎴 DeMerzli
    </div>

</header>

<?php include 'nav.php'; ?>
<section class="hero-v2">

    <div class="hero-overlay">

        <h1>DEMERZLI</h1>

        <h2>
            Streamer • Community • Sammelkarten
        </h2>

        <p>
            Sammle Community-Karten,
            entdecke Geheimnisse und werde
            Teil der DeMerzli Community.
        </p>

        <div class="hero-buttons">

            <a
                href="https://www.twitch.tv/demerzli"
                target="_blank"
                class="hero-btn twitch">

                🔴 LIVE AUF TWITCH

            </a>

            <a
                href="cards.php"
                class="hero-btn cards">

                🎴 SAMMELKARTEN

            </a>

            <a
                href="https://discord.gg/demerzli"
                target="_blank"
                class="hero-btn discord">

                💬 DISCORD

            </a>

        </div>

    </div>

</section>
<div class="container">

    <div class="home-stats">

        <div class="home-stat">

            <div class="icon">
                🎴
            </div>

            <div class="number">
                <?php echo $totalCards; ?>
            </div>

            <div class="label">
                Karten gezogen
            </div>

        </div>

        <div class="home-stat">

            <div class="icon">
                👥
            </div>

            <div class="number">
                <?php echo $totalUsers; ?>
            </div>

            <div class="label">
                Sammler
            </div>

        </div>

        <div class="home-stat">

            <div class="icon">
                🌈
            </div>

            <div class="number">
                <?php echo $collectors; ?>
            </div>

            <div class="label">
                Sammlerkönige
            </div>

        </div>

        <div class="home-stat">

            <div class="icon">
                📚
            </div>

            <div class="number">
                11
            </div>

            <div class="label">
                Karten
            </div>

        </div>

    </div>
    <section class="showcase">

    <h2>
        🎴 BELIEBTE KARTEN
    </h2>

    <div class="showcase-cards">

<?php

$featuredCards =
[
    1,
    2,
    3,
    999
];

foreach($featuredCards as $cardId)
{
?>

<a
href="card.php?id=<?php echo $cardId; ?>"
class="showcase-card">

    <img
        src="assets/cards/<?php echo $cards[$cardId]['image']; ?>"
        alt="<?php echo $cards[$cardId]['name']; ?>">

</a>

<?php
}
?>

    </div>

    <a
        href="cards.php"
        class="showcase-button">

        Alle Karten ansehen →

    </a>

</section>
<section class="top-collectors">

    <h2>
        🏆 TOP SAMMLER
    </h2>

    <div class="collector-list">

<?php

$place = 1;

foreach($topCollectors as $username => $cardCount)
{
?>

    <div class="collector-row">

        <div class="collector-place">

<?php

if($place == 1)
{
    echo "🥇";
}
elseif($place == 2)
{
    echo "🥈";
}
elseif($place == 3)
{
    echo "🥉";
}
else
{
    echo "#" . $place;
}

?>

        </div>

        <div class="collector-name">
            <?php echo ucfirst($username); ?>
        </div>

        <div class="collector-cards">
            <?php echo $cardCount; ?>/10
        </div>

    </div>

<?php

$place++;

}

?>

    </div>

    <a
        href="leaderboard.php"
        class="showcase-button">

        Zur Topliste →

    </a>

</section>
<section class="social-section">

<h2>
📺 DEMERZLI COMMUNITY
</h2>

<div class="social-grid">

<a
href="https://www.twitch.tv/demerzli"
target="_blank"
class="social-card twitch">

<h3>📺 Twitch</h3>

<p>
Live Streams
</p>

</a>

<a
href="https://www.youtube.com/@demerzlitv"
target="_blank"
class="social-card youtube">

<h3>▶️ YouTube</h3>

<p>
Videos & Clips
</p>

</a>

<a
href="https://www.instagram.com/demerzli_tv/"
target="_blank"
class="social-card instagram">

<h3>📸 Instagram</h3>

<p>
Behind the Scenes
</p>

</a>

<a
href="https://www.tiktok.com/@demerzli"
target="_blank"
class="social-card tiktok">

<h3>🎵 TikTok</h3>

<p>
Shorts & Highlights
</p>

</a>

<a
href="https://discord.gg/demerzli"
target="_blank"
class="social-card discord">

<h3>💬 Discord</h3>

<p>
Community Chat
</p>

</a>

</div>

</section>
<section class="stream-schedule">

<h2>
📅 STREAMZEITEN
</h2>

<div class="schedule-grid">

<div class="schedule-card">
Montag<br>
17:30 Uhr
</div>

<div class="schedule-card">
Mittwoch<br>
17:30 Uhr
</div>

<div class="schedule-card">
Freitag<br>
17:30 Uhr
</div>

<div class="schedule-card">
Sonntag<br>
15:00 Uhr
</div>

</div>

</section>
<section class="secret-card">

    <div class="secret-left">

        <img
        src="assets/cards/locked.png"
        alt="Geheime Karte">

    </div>

    <div class="secret-right">

        <h2>
            🌈 DIE GEHEIME SAMMELKARTE
        </h2>

        <p>

            Nur die besten Sammler
            werden sie finden.

        </p>

        <p>

            Bist du bereit für die
            ultimative Herausforderung?

        </p>

        <a
            href="wiki.php"
            class="hero-btn cards">

            Mehr erfahren

        </a>

    </div>

</section>
</div>

<footer class="footer">

    <p>

        © <?php echo date('Y'); ?>
        DeMerzli

    </p>

</footer>

</body>
</html>