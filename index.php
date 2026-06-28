<?php

include 'config/functions.php';

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

?>

<!DOCTYPE html>
<html lang="de">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1.0">

<title>
DeMerzli Sammelkarten
</title>

<link rel="stylesheet" href="assets/css/style.css?v=9">

</head>

<body>

<header>

    <div class="logo">
        🎴 DeMerzli Sammelkarten
    </div>

</header>

<?php include 'nav.php'; ?>

<div class="hero-banner">
    <img src="assets/images/banner.png" alt="DeMerzli Sammelkarten">
</div>


<div class="container">

    <div class="hero">

        <h1>
            Willkommen bei den
            DeMerzli Sammelkarten
        </h1>

        <p>
            Sammle alle 10 Karten
            und schalte die legendäre
            Sammlerkönig-Karte frei.
        </p>

        <br>

        <a
            class="login-btn"
            href="login.php">

            🎮 Mit Twitch anmelden

        </a>
        <br><br>
<h2 style="text-align:center;margin-bottom:20px;">
🌐 Folge mir auch hier
</h2>

<div class="social-links">

    <a href="https://www.twitch.tv/demerzli" target="_blank">
        🎮 Twitch
    </a>

    <a href="https://www.youtube.com/@demerzlitv" target="_blank">
        ▶️ YouTube
    </a>

    <a href="https://www.tiktok.com/@demerzli" target="_blank">
        🎵 TikTok
    </a>

    <a href="https://www.instagram.com/demerzli_tv/" target="_blank">
        📸 Instagram
    </a>

    <a href="https://discord.gg/demerzli" target="_blank">
        💬 Discord
    </a>

</div>

<br><br>

<h2 style="text-align:center;margin-bottom:20px;">
📅 Streamzeiten
</h2>

<div class="stats-grid">

    <div class="stats-card">
        <h2>Montag</h2>
        <p>17:30 Uhr</p>
    </div>

    <div class="stats-card">
        <h2>Mittwoch</h2>
        <p>17:30 Uhr</p>
    </div>

    <div class="stats-card">
        <h2>Freitag</h2>
        <p>17:30 Uhr</p>
    </div>

    <div class="stats-card">
        <h2>Sonntag</h2>
        <p>15:00 Uhr</p>
    </div>

</div>
    </div>

    <br><br>

    <div class="stats-grid">

        <div class="stats-card">

            <h2>
                👥 Sammler
            </h2>

            <p>
                <?php echo $totalUsers; ?>
            </p>

        </div>

        <div class="stats-card">

            <h2>
                🎴 Gezogene Karten
            </h2>

            <p>
                <?php echo $totalCards; ?>
            </p>

        </div>

        <div class="stats-card">

            <h2>
                🌈 Sammlerkönige
            </h2>

            <p>
                <?php echo $collectors; ?>
            </p>

        </div>

    </div>

    <br><br>

    <h2>
        🔎 Profil suchen
    </h2>

    <form
        action="profile.php"
        method="get">

        <input
            type="text"
            name="user"
            placeholder="Twitch Name">

        <button type="submit">
            Profil öffnen
        </button>

    </form>

    <br><br>

    <div class="quick-links">

        <a href="cards.php">
            🎴 Kartenübersicht
        </a>

        <a href="leaderboard.php">
            🏆 Topliste
        </a>

        <a href="stats.php">
            📊 Statistiken
        </a>

    </div>

</div>
<div class="wiki-warning">

🚀 Season 2 befindet sich bereits in Vorbereitung.

Neue Karten.
Neue Legenden.
Neue Sammelziele.

Bleib gespannt!

</div>

</body>

</html>