<?php
//dmz fetches the deals from cheapshark and sends to db through rabbitmq
require_once(__DIR__ . '/path.inc');
require_once(__DIR__ . '/get_host_info.inc');
require_once(__DIR__ . '/rabbitMQLib.inc');
require_once(__DIR__ . '/dmzFunctions.php');

$mq = new rabbitMQClient(MQ_INI, "nvdServer");

//stores 
$stores = fetchStores();
if ($stores) {
	echo "stores: " . json_encode($mq->send_request(["type" => "save_stores", "data" => $stores])) . "\n";
}

//whats the "deal"-io
for ($page = 0; $page < 5; $page++) {
	$deals = fetchDeals($page);
	if ($deals) {
	echo "Dealio Page $page : " . json_encode($mq->send_request(["type" => "save_deals", "data" => $deals])) . "\n";
	}
	sleep(1);
}

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

//RAWG description 
$missing = $mq->send_request(["type" => "get_missing_details"]);
foreach (is_array($missing) ? $missing : [] as $g) {
		if ($details = fetchRawg($g['title'])) {
			$mq->send_request(["type" => "save_details", "game_id" => $g['id'], "details" => $details]);
			echo "added details for " . $g['title'] . "\n";
		}
		sleep(1);
}
?>

