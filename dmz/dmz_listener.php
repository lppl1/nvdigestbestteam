<?php
//Me want it to listen like the gov do
required_once(__DIR__ . '/nvdigestmq/rabbitMQLib.inc');
required_once(__DIR__ . '/get_host_info.inc');
required_once(__DIR__ . '/rabbitMQLib.inc');
required_once(__DIR__ . '/dmzFunctions.php');

function dmzRouter($req) {
	switch ($req['type' ?? '') {
		case 'lookup_game':	return fetchGamePrices($req['cheapshark_id' ?? 0);
		case 'search_external':	return searchDeals($req['term'] ?? '');
		case 'game_details':	return fetchRawg($req['title'] ?? '');
	}
	return false;
}

(new rabbitMQServer(MQ_INI, "dmzServer"))->process_requests('dmzRouter');
?>
