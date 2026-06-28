<?php

include 'config/cards.php';
include 'config/functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if(!isset($cards[$id]))
{
    die("Karte nicht gefunden.");
}

$card = $cards[$id];

$owners = getOwners($id);
$ownerCount = count($owners);

$data = loadCardsData();
$totalUsers = count($data['Users']);

$ownershipRate = 0;

if($totalUsers > 0)
{
    $ownershipRate = round(($ownerCount / $totalUsers) * 100);
}

if($ownerCount <= 2)
{
    $rarity = "🌈 Legendär";
}
elseif($ownerCount <= 5)
{
    $rarity = "🔥 Episch";
}
elseif($ownerCount <= 10)
{
    $rarity = "⭐ Selten";
}
else
{
    $rarity = "⚪ Gewöhnlich";
}

?>

<!DOCTYPE html>
<html lang="de">

<head>

<meta charset="UTF-8">

<title>
<?php echo $card['name']; ?>
</title>

<link rel="stylesheet" href="assets/css/style.css?v=4">

</head>

<body>

<header>
    <div class="logo">
        🎴 <?php echo $card['name']; ?>
    </div>
</header>

<?php include 'nav.php'; ?>

<div class="container">

    <div class="card-detail">

        <div class="card-image">

            <img
                src="assets/cards/<?php echo $card['image']; ?>"
                alt="<?php echo $card['name']; ?>"
            >

        </div>

        <div class="card-info">

            <h1>
                <?php echo $card['name']; ?>
            </h1>

            <h2>
                <?php echo $card['title']; ?>
            </h2>

            <p class="rarity">
                <?php echo $rarity; ?>
            </p>

            <br>

            <p>
                <?php echo $card['description']; ?>
            </p>

            <br>

            <div class="stats-box">

                <strong>Karten-ID:</strong>
                #<?php echo $id; ?>

                <br><br>

                <strong>Besitzer:</strong>
                <?php echo $ownerCount; ?>

                <br><br>

                <strong>Besitzquote:</strong>
                <?php echo $ownershipRate; ?>%

            </div>

        </div>

    </div>

    <br><br>

    <h2>Besitzer dieser Karte</h2>

    <?php

    if(count($owners) == 0)
    {
        echo "<p>Noch niemand besitzt diese Karte.</p>";
    }
    else
    {
        foreach($owners as $owner)
        {
            echo "
            <div class='owner-row'>
                <a href='profile.php?user={$owner}'>
                    {$owner}
                </a>
            </div>
            ";
        }
    }

    ?>

</div>

</body>

</html>