<?php

require_once 'config/twitch.php';

$url = "https://id.twitch.tv/oauth2/authorize?" .
http_build_query([
    'client_id' => TWITCH_CLIENT_ID,
    'redirect_uri' => TWITCH_REDIRECT_URI,
    'response_type' => 'code',
    'scope' => 'user:read:email'
]);

header("Location: $url");
exit;