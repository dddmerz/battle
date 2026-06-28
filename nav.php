<?php

if (session_status() === PHP_SESSION_NONE)
{
    session_start();
}

$current = basename($_SERVER['PHP_SELF']);

?>

<nav class="navbar">

    <a
        class="<?= $current == 'index.php' ? 'active' : '' ?>"
        href="index.php">
        🏠 Startseite
    </a>

    <a
        class="<?= $current == 'cards.php' ? 'active' : '' ?>"
        href="cards.php">
        🎴 Karten
    </a>
    
    <a
        class="<?= $current == 'leaderboard.php' ? 'active' : '' ?>"
        href="leaderboard.php">
        🏆 Topliste
    </a>

    <a
    class="<?= $current == 'stats.php' ? 'active' : '' ?>"
    href="stats.php">
    📊 Statistiken
</a>

<a
    class="<?= $current == 'wiki.php' ? 'active' : '' ?>"
    href="wiki.php">
    📖 DeMerzli Wiki
</a>

    <?php if(isset($_SESSION['twitch_user'])): ?>

        <a
            class="<?= $current == 'me.php' ? 'active' : '' ?>"
            href="me.php">
            👤 Mein Profil
        </a>

        <a href="logout.php">
            🚪 Logout
        </a>

    <?php else: ?>

        <a
            class="<?= $current == 'login.php' ? 'active' : '' ?>"
            href="login.php">
            🎮 Login
        </a>
        <span class="user-welcome">
    👋 <?= htmlspecialchars($_SESSION['twitch_user']) ?>
 
</span>

    <?php endif; ?>

</nav>