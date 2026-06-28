<?php

session_start();

if(!isset($_SESSION['twitch_user']))
{
    header("Location: login.php");
    exit;
}

$user = $_SESSION['twitch_user'];

header("Location: profile.php?user=".$user);
exit;