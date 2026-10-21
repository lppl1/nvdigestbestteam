<?php
require_once("nvdigestmq/rabbitMQLib.inc");
$request = $_POST;
//JQuery Response array
$jqresponse=[];
$client = new rabbitMQClient("nvdigestmq/nvRabbitMQ.ini","nvdServer");

if(isset($request["type"]))
{

$response=$client->send_request($request);

echo json_encode($response);
exit();
}






?>
