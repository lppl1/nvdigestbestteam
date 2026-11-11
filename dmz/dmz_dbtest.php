<?php
require_once(__DIR__ . '/../nvdigestmq/path.inc'); 
require_once(__DIR__ . '/../nvdigestmq/get_host_info.inc');
require_once(__DIR__ . '/../nvdigestmq/rabbitMQLib.inc'); 
require_once(__DIR__ . '/dmzFunctions.php');
$mq = new rabbitMQClient(MQ_INI, "nvdServer");
var_dump($mq->send_request(["type" => "get_tracked"]));
?>
