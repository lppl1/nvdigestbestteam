<?php
require_once("../nvdigestmq/rabbitMQLib.inc");
require_once("../nvdigestmq/nvRabbitMQ.ini");
$request = $_POST;
$client = new rabbitMQClient("../nvdigestmq/nvRabbitMQ.ini","nvdServer");
if(isset($request["account"]))
{
	$response=$client->send_request($request);
	if($request["account"]=="register")
	{

		header("Location: login.html");
		exit;
	}
	else if($request["account"]=="login"){
		header("Location: index.html");
		exit;

	}
	

}
?>
