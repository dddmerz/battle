<?php

$secret = "DEMERZLI_SECRET_2026";

if (!isset($_POST['secret']))
{
    http_response_code(403);
    exit("Kein Zugriff");
}

if ($_POST['secret'] !== $secret)
{
    http_response_code(403);
    exit("Falscher Schlüssel");
}

if (!isset($_POST['json']))
{
    http_response_code(400);
    exit("Keine Daten");
}

file_put_contents(
    __DIR__ . "/../data/cards.json",
    $_POST['json']
);

echo "OK";