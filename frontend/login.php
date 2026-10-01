<?php
require_once("../nvdigestmq/rabbitMQLib.inc");
require_once("../nvdigestmq/nvRabbitMQ.ini");
$request = $_POST;
$client = new rabbitMQClient("../nvdigestmq/nvRabbitMQ.ini","testServer");
$response=$client->send_request($request);
?>
