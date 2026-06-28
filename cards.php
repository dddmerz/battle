<?php

include 'config/cards.php';

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Kartenübersicht</title>
    <link rel="stylesheet" href="assets/css/style.css?v=9">
</head>

<body>

<header>
    <div class="logo">🎴 Kartenübersicht</div>
</header>

<?php include 'nav.php'; ?>

<div class="container">

    <div class="card-grid">

        <?php foreach($cards as $id => $card): ?>

            <a href="card.php?id=<?php echo $id; ?>" class="card-link">

                <div class="card unlocked">

                    <img
                        src="assets/cards/<?php echo $card['image']; ?>"
                        alt="<?php echo htmlspecialchars($card['name']); ?>"
                    >

                    <div class="card-title">
                        <?php echo htmlspecialchars($card['name']); ?>
                    </div>

                    <div class="card-subtitle">
                        <?php echo htmlspecialchars($card['title']); ?>
                    </div>

                </div>

            </a>

        <?php endforeach; ?>

    </div>

</div>

</body>
</html>