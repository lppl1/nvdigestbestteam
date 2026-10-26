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

//tracked games price watch
$tracked = $client->send_request(["type" => "get_tracked"]);
if (is_array($tracked)) {
	foreach ($tracked as $row) {
		$info = fetchGamePrices($row['cheapshark_id']);
		if ($info && !empty($info['deals'])) {
			$client->send_request([
				"type"	=> "save_prices",
				"cheapshark_id"	=> $row['cheapshark_id'],
				"deals"	=> $info['deals'],
			]);
		echo "price updates for " . $row['cheapshark_id'] . "\n";
		}
		sleep(1);
	}
}

//RAWG description 
$missing = $client->send_request(["type" => "get_missing_details"]);
if (is_array($missing)) {
	foreach ($missing as $g) {
		$detals = fetchRawg($g['title']);
		if ($details) {
			$client->send_request(["type" => "save_details", "game_id" => $g['id'}, "details" => $details]);
			echo "added details for" . $g['title'] . "\n";
		}
		sleep(1);
	}
}
?>

