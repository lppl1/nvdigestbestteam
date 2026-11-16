<?php
//Me want it to listen like the gov do
require_once __DIR__ . '/../nvdigestmq/path.inc';
require_once __DIR__ . '/../nvdigestmq/get_host_info.inc';
require_once __DIR__ . '/../nvdigestmq/rabbitMQLib.inc';
require_once(__DIR__ . '/dmzFunctions.php');

function dmzRouter($req) {
	$type = $req['type'] ?? '';
	echo date('c') . " request: $type\n";
 
	switch ($type) {
		case 'search_external':
			$term = trim($req['term'] ?? '');
			if ($term === '') return false;
			$deals = searchDeals($term);
			if (empty($deals)) return ["saved" => 0, "deals" => []];
			$saved = saveToDb(["type" => "save_deals", "data" => $deals]);
			return ["saved" => $saved, "deals" => $deals];
 
		case 'lookup_game':
			$id = (int)($req['cheapshark_id'] ?? 0);
			$info = fetchGamePrices($id);
			if (!$info) return false;
			if (!empty($info['deals'])) {
				saveToDb(["type" => "save_prices", "cheapshark_id" => $id, "deals" => $info['deals']]);
			}
			$steamId = $info['info']['steamAppID'] ?? null;
			$info['steam'] = $steamId ? fetchSteamDetails($steamId) : null;
			return $info;
 
		
		case 'game_details':
		case 'steam_details':
			$details = fetchSteamDetails($req['steam_app_id'] ?? 0);
			if ($details && !empty($req['game_id'])) {
				saveToDb(["type" => "save_game_details", "game_id" => (int)$req['game_id'], "details" => $details]);
			}
			return $details ?: false;
 
		case 'send_email':
			return sendEmail($req['to'] ?? '', $req['subject'] ?? 'DealDigest', $req['body'] ?? '');
	}
	return false;
}
 
echo "DMZ lookup listener running..." . PHP_EOL;
(new rabbitMQServer(MQ_INI, "dmzLookupServer"))->process_requests('dmzRouter');
?>
