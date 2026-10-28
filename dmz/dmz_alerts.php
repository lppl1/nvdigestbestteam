<?php
require_once(__DIR__ . '/path.inc');
require_once(__DIR__ . '/get_host_info.inc');
require_once(__DIR__ . '/rabbitMQlib.inc');
require_once(__DIR__ . '/dmzFunctions.php');

$client = new rabbitMQClient("nvRabbitMQ.ini", "dmzServer");

//alert_id, email, username, title, store, sale_price, target_price, deal_id, alert_type ('price' | 'budget')
$alerts = $client->send_request(["type" => "get_triggered_alerts"]);

if (!is_array($alerts) || count($alerts) === 0) {
	echo "No alerts to send.\n";
	exit;
}

foreach ($alerts as $a) {
	$link = !empty($a['deal_id'])
		? "https://www.cheapshark.com/redirect?dealID=" . urlencode($a['deal_id']) : "";
	if (($a['alert_type'] ?? 'price') === 'budget') {
		$subject = "DealDigest: " . $a['title'] . " fits your budget";
		$body = "Hey " . $a['username'] . ",\n\n"
			. $a['title'] . " is now \$" . $a['sale_price'] . " at " .  $a['store']
			. ", which keeps you within your budget. \n";
	} else {
		$subject = "DealDigest: " . $a['title'] . " hit your target price";
		$body = "Hey " . $a['username'] . ",\n\n"
			. $a['title'] . " is now \$" . $a['target_price'] . ").\n";
	}
	if ($link !== "") {
		$body .= "\nUnlock Your Deal Here ;) $link\n";
	}
	$body .= "\n- DealDigest\n";
	
	if (sendEmail($a['email'], $subject, $body)) {
		$client->send_request(["type" => "mark_alert_sent", "alert_id" => (int)$a['alert_id']]);
		echo "sent alert  " . $a['alert_id'] . " to " . $a['email'] . "\n";
	}
	sleep(1);
}
?>

