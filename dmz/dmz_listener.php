<?php
//Me want it to listen like the gov do
require_once(__DIR__ . '/path.inc');
require_once(__DIR__ . '/get_host_info.inc');
require_once(__DIR__ . '/rabbitMQLib.inc');
require_once(__DIR__ . '/dmzFunctions.php');

function dmzRouter($req) {
	switch ($req['type'] ?? '') {
		case 'lookup_game':	return fetchGamePrices($req['cheapshark_id'] ?? 0);
		case 'search_external':	return searchDeals($req['term'] ?? '');
		case 'game_details':	return fetchRawg($req['title'] ?? '');
	}
	return False;
}

(new rabbitMQServer(MQ_INI, "dmzServer"))->process_requests('dmzRouter');
?>
