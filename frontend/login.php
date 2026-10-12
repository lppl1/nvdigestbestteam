<?php
require_once("nvdigestmq/rabbitMQLib.inc");
$request = $_POST;
//JQuery Response array
$jqresponse=
[
"type" => "",
"success" => "",
"validate" =>""
];



$client = new rabbitMQClient("nvdigestmq/nvRabbitMQ.ini","nvdServer");
if(isset($request["type"]))
{
	$jqresponse["type"]=$_POST["type"];
	$jqresponse["success"]="true";

	$response=$client->send_request($request);
	
 	$jqresponse["validate"]=$response;
 	
 	echo json_encode($jqresponse);
 	exit();

 
}
else
{
$jqresponse["type"]=$_POST["type"];
$jqresponse["success"]="false";
$jqresponse["validate"]="Error";
}
?>
