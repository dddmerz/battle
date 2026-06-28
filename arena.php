<?php

header('Content-Type: application/json');

include '../config/cards.php';

$arena = json_decode(
    file_get_contents('../data/arena.json'),
    true
);

$open = [];

foreach($arena as $fight)
{
    if($fight['status'] !== 'open')
    {
        continue;
    }

    $card1 =
        $cards[$fight['creator_cards'][0]]['name']
        ?? 'Unbekannt';

    $card2 =
        $cards[$fight['creator_cards'][1]]['name']
        ?? 'Unbekannt';

    $open[] = [

        'id' => $fight['id'],

        'creator' => $fight['creator'],

        'cards' => [

            $card1,
            $card2,
            '???'

        ]
    ];
}

echo json_encode(
    $open,
    JSON_PRETTY_PRINT
);