<?php
require_once("nvdigestmq/rabbitMQLib.inc");
$request = $_POST;
$client = new rabbitMQClient("nvdigestmq/nvRabbitMQ.ini","nvdServer");
if(isset($request["type"]))
{
	$response=$client->send_request($request);
	if($request["type"]=="register")
	{

		if(json_encode($response)=="true")
		{header("Location: login.html");
		exit;
		}
		
		else if(json_encode($response)=="false"){
			
			echo "<h1>Account already exists.<h1>";
			exit;
		}
	}
	else if($request["type"]=="login"){
		if(json_encode($response)!="false")
		{		
		header("Location: index.html");
		exit;
		}
		
		else 
		{
		echo "<h1>Go away.<h1>";
		exit;
		}
	}
 
 


}
?>
