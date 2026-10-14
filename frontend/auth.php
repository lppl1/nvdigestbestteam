<?php
require_once("nvdigestmq/rabbitMQLib.inc");

if(!isset($_COOKIE["session_key"])){
	header("Location: login.html");
	exit();
}

$client = new rabbitMQClient("nvdigestmq/nvRabbitMQ.ini","nvdServer");
$response=$client->send_request([
	"type" => "validate_session", "session_key" => $_COOKIE["session_key"]
]);

if ($response !== true)   {
	header("Location: login.html");
	exit();
}
?>
