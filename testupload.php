<?php

$data = [
    "Users" => [
        "demerzli" => [1,2,3,4]
    ]
];

$response = file_get_contents(
    "https://demerzli.tv/api/uploadcards.php",
    false,
    stream_context_create([
        "http" => [
            "method" => "POST",
            "header" => "Content-type: application/x-www-form-urlencoded",
            "content" => http_build_query([
                "secret" => "DEMERZLI_SECRET_2026",
                "json" => json_encode($data)
            ])
        ]
    ])
);

echo $response;