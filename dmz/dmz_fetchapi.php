<?php
//dmz fetches the deals from cheapshark and sends to db through rabbitmq
require_once('path.inc');
require_once('get_host_inc.inc');
require_once('rabbitMQLib.inc');
require_once('dmzFunctions.php');

$client = new rabbitMQClient("nvRabbitMQ.ini", "nvdServer");

//stores 
$stores = fetchStores();
if ($stores) {
	$res = $client->send_request(["type" => "save_stores", "data", => $stores]);
	echo "stores saved: " . var_export($res, true) . "\n";
}

//whats the "deal"-io
for ($page = 0; $page < 5; $page++) {
	$deals = fetchDeals($page);
	if ($deals) {
	$res = $client->send_request(["type" => "save_deals", "data" => $deals]);
	echo "deals page $page saved: " . var_export($res, true) . "\n";
	}
	sleep(1);
}
?>

