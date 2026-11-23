#!/usr/bin/php
<?php
date_default_timezone_set('America/New_York');
require_once(__DIR__ . '/path.inc');
require_once(__DIR__ . '/get_host_info.inc');
require_once(__DIR__ . '/rabbitMQLib.inc');
//hopefully this works the first time and we don't have to edit it 20x
function writeLog($msg){
	file_put_contents('/var/log/dealdigest/combined.log', date('Y-m-d H:i:s') . ' ' . json_encode($msg) . "\n", FILE_APPEND);
	return true;
}
$server = new rabbitMQServer(__DIR__ . '/nvRabbitMQ.ini', $argv[1]);
$server->process_requests('writeLog');
