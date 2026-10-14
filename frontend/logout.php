<?php
require_once(__DIR__ . "/../nvdigestmq/rabbitMQLib.inc");

if (isset($_COOKIE["session_key"])) {
    $client = new rabbitMQClient(__DIR__ . "/../nvdigestmq/nvRabbitMQ.ini", "nvdServer");
    $client->send_request(["type" => "logout", "session_key" => $_COOKIE["session_key"]]);
}

setcookie("session_key", "", time() - 3600, "/");   // delete the cookie
header("Location: login.html");
exit;
?>
