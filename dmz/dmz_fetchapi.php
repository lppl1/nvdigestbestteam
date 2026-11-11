<?php
//dmz fetches the deals from cheapshark and sends to db through rabbitmq
require_once __DIR__ . '/../nvdigestmq/path.inc';
require_once __DIR__ . '/../nvdigestmq/get_host_info.inc';
require_once __DIR__ . '/../nvdigestmq/rabbitMQLib.inc';
require_once(__DIR__ . '/dmzFunctions.php');

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
for ($page = 0; $page < 5; $page++) {
	$deals = fetchDeals($page);
	if ($deals) {
	echo "Dealio Page $page : " . json_encode($mq->send_request(["type" => "save_deals", "data" => $deals])) . "\n";
	}
	else {
		echo "fetchDeals returned nothing\n";
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
}
?>

