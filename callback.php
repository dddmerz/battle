<?php

session_start();

require_once 'config/twitch.php';

if(!isset($_GET['code']))
{
    die("Kein Login-Code erhalten.");
}

$token_url = "https://id.twitch.tv/oauth2/token";

$post = [
    'client_id' => TWITCH_CLIENT_ID,
    'client_secret' => TWITCH_CLIENT_SECRET,
    'code' => $_GET['code'],
    'grant_type' => 'authorization_code',
    'redirect_uri' => TWITCH_REDIRECT_URI
];

$ch = curl_init($token_url);

curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);

$data = json_decode($response, true);

curl_close($ch);

if(!isset($data['access_token']))
{
    die("Token konnte nicht abgerufen werden.");
}

$accessToken = $data['access_token'];

$ch = curl_init("https://api.twitch.tv/helix/users");

curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Client-ID: ".TWITCH_CLIENT_ID,
    "Authorization: Bearer ".$accessToken
]);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$userResponse = curl_exec($ch);

curl_close($ch);

$userData = json_decode($userResponse, true);

if(empty($userData['data'][0]))
{
    die("Benutzerdaten konnten nicht geladen werden.");
}

$_SESSION['twitch_user'] =
    strtolower($userData['data'][0]['login']);

header("Location: me.php");
exit;