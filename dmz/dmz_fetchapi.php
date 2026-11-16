<?php
//dmz fetches the deals from cheapshark and sends to db through rabbitmq
require_once __DIR__ . '/../nvdigestmq/path.inc';
require_once __DIR__ . '/../nvdigestmq/get_host_info.inc';
require_once __DIR__ . '/../nvdigestmq/rabbitMQLib.inc';
require_once(__DIR__ . '/dmzFunctions.php');

const DEAL_PAGES = 5;
const STEAM_BATCH = 100;

$mq = new rabbitMQClient(MQ_INI, "dmzServer");

//stores 
$stores = fetchStores();
if ($stores) {
	echo "stores: " . json_encode($mq->send_request(["type" => "save_stores", "data" => $stores])) . "\n";
}
	else {
		echo "fetchStores returned nothing\n";
       	}

//whats the "deal"-io
for ($page = 0; $page < DEAL_PAGES; $page++) {
	$deals = fetchDeals($page);
	if (empty($deals)) {
		echo "fetchDeals returned nothing on page $page, stopping\n";
		break;
	}
	echo "Dealio Page $page : " . json_encode($mq->send_request(["type" => "save_deals", "data" => $deals])) . "\n";
	sleep(1);
}
//steam fill in missing game info
$missing = $mq->send_request(["type" => "get_missing_details", "limit" => STEAM_BATCH]);
$found = 0;
foreach (is_array($missing) ? $missing : [] as $row) {
	$details = fetchSteamDetails($row['steam_app_id'] ?? 0);
	
	$mq->send_request([
		"type"    => "save_game_details",
		"game_id" => (int)$row['game_id'],
		"details" => $details,
	]);
	if ($details) $found++;
	usleep(1500000); 
}
echo "steam details: $found found, " . (is_array($missing) ? count($missing) : 0) . " checked\n";

//tracked games price watch
$tracked = $mq->send_request(["type" => "get_tracked"]);
	foreach (is_array($tracked) ? $tracked : [] as $row) {
		$info = fetchGamePrices($row['cheapshark_id']);
		if (!empty($info['deals'])) {
			$mq->send_request([
				"type"	=> "save_prices",
				"cheapshark_id"	=> $row['cheapshark_id'],
				"deals"	=> $info['deals'],
			]);
		echo "price updates for " . $row['cheapshark_id'] . "\n";
		}
		sleep(1);

	}
?>

