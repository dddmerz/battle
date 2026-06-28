<?php

$secret = "DemerzliCards2026";

if(!isset($_POST["secret"]))
{
    die("No Secret");
}

if($_POST["secret"] != $secret)
{
    die("Wrong Secret");
}

if(!isset($_POST["json"]))
{
    die("No Data");
}

file_put_contents(
    __DIR__ . "/data/cards.json",
    $_POST["json"]
);

echo "OK";

?>