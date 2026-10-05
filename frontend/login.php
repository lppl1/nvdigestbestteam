<?php
require_once("nvdigestmq/rabbitMQLib.inc");
require_once("nvdigestmq/nvRabbitMQ.ini");
$request = $_POST;
$client = new rabbitMQClient("nvdigestmq/nvRabbitMQ.ini","nvdServer");
if(isset($request["type"]))
{
	$response=$client->send_request($request);
	if($request["type"]=="register")
	{

		header("Location: login.html");
		exit;
	}
	else if($request["type"]=="login"){
		header("Location: index.html");
		exit;

	}
	

}
?>
