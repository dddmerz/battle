<?php

include 'config/cards.php';
include 'config/functions.php';

$data = loadCardsData();

$users = $data['Users'];

$totalUsers = count($users);

$totalCardsDrawn = 0;

$cardCounts = [];

foreach($cards as $id => $card)
{
    if($id != 999)
    {
        $cardCounts[$id] = 0;
    }
}

$collectors = 0;

$largestCollectionUser = "";
$largestCollectionSize = 0;

foreach($users as $username => $userCards)
{
    $count = count($userCards);

    $totalCardsDrawn += $count;

    if($count >= 10)
    {
        $collectors++;
    }

    if($count > $largestCollectionSize)
    {
        $largestCollectionSize = $count;
        $largestCollectionUser = $username;
    }

    foreach($userCards as $cardId)
    {
        if(isset($cardCounts[$cardId]))
        {
            $cardCounts[$cardId]++;
        }
    }
}

arsort($cardCounts);

$mostPopularId = array_key_first($cardCounts);
$mostPopularCount = current($cardCounts);

asort($cardCounts);

$rarestId = array_key_first($cardCounts);
$rarestCount = current($cardCounts);

?>

<!DOCTYPE html>
<html lang="de">

<head>

<meta charset="UTF-8">

<title>Statistiken</title>

<link rel="stylesheet" href="assets/css/style.css?v=4">

</head>

<body>

<header>
    <div class="logo">
        📊 Karten Statistiken
    </div>
</header>

<?php include 'nav.php'; ?>

<div class="container">

    <div class="stats-grid">

        <div class="stats-card">
            <h2>👥 Sammler</h2>
            <p><?php echo $totalUsers; ?></p>
        </div>

        <div class="stats-card">
            <h2>🎴 Gezogene Karten</h2>
            <p><?php echo $totalCardsDrawn; ?></p>
        </div>

        <div class="stats-card">
            <h2>🌈 Sammlerkönige</h2>
            <p><?php echo $collectors; ?></p>
        </div>

        <div class="stats-card">
            <h2>🏆 Größte Sammlung</h2>
            <p>
                <?php echo $largestCollectionUser; ?>
                <br>
                <?php echo $largestCollectionSize; ?>/10
            </p>
        </div>

        <div class="stats-card">
            <h2>🔥 Beliebteste Karte</h2>
            <p>
                <?php echo $cards[$mostPopularId]['name']; ?>
                <br>
                <?php echo $mostPopularCount; ?> Besitzer
            </p>
        </div>

        <div class="stats-card">
            <h2>💎 Seltenste Karte</h2>
            <p>
                <?php echo $cards[$rarestId]['name']; ?>
                <br>
                <?php echo $rarestCount; ?> Besitzer
            </p>
        </div>

    </div>

</div>

</body>

</html>