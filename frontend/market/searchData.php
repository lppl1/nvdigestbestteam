<?php
require_once("nvdigestmq/rabbitMQLib.inc");
$request = $_POST;
//JQuery Response array
$jqresponse=
[
"type" => "",
"term" => ""
];
$jqresponse["type"]="search_games";
$jqresponse["term"]=$_POST["term"]

$client = new rabbitMQClient("nvdigestmq/nvRabbitMQ.ini","nvdServer");
?>
