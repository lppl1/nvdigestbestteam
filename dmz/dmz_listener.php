<?php
//Me want it to listen like the gov do
require_once __DIR__ . '/../nvdigestmq/path.inc';
require_once __DIR__ . '/../nvdigestmq/get_host_info.inc';
require_once __DIR__ . '/../nvdigestmq/rabbitMQLib.inc';
require_once(__DIR__ . '/dmzFunctions.php');

function dmzRouter($req) {
	switch ($req['type'] ?? '') {
	case 'lookup_game':	return fetchGamePrices($req['cheapshark_id'] ?? 0);
	//maybe this will be the good fix
	case 'search_external':	
		$deals = searchDeals($req['term'] ?? '');
		if (is_array($deals) && $deals){
			(new rabbitMQClient(MQ_INI, 'dmzServer'))->publish(['type' => 'save_deals', 'data' => $deals]);
		}
		return true;
	case 'game_details':	return fetchRawg($req['title'] ?? '');
	case 'fetch_rawg': return fetchRawg($req['title'] ?? '');
	}
	return False;
}

(new rabbitMQServer(MQ_INI, "dmzLookupServer"))->process_requests('dmzRouter');
?>
