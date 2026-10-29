<?php
	require_once("dbFunctions.php");
	require_once('../nvdigestmq/path.inc'); //This is an extra place for php to look for files. Important because of AMQP
	require_once('../nvdigestmq/get_host_info.inc'); //Sole purpose is to read .ini config files like host.ini and turns 
							       //into a usable PHP array (host, port, username, password, vhost, exchange, queue)
	require_once('../nvdigestmq/rabbitMQLib.inc');



function routeDmz($request) {
    switch ($request['type'] ?? '') {
        case 'save_stores':         return doSaveStores($request['data']);
        case 'save_deals':          return doSaveDeals($request['data']);
        case 'get_tracked':         return doGetTracked();
        case 'save_prices':         return doSavePrices($request['cheapshark_id'], $request['deals']);
        case 'get_missing_details': return doGetMissingDetails();
        case 'save_details':        return doSaveDetails($request['game_id'], $request['details']);
    }
    return false;
}

function dmzRouter($request) {
    try { return routeDmz($request); }
    catch (Throwable $e) { error_log("dmz listener: " . $e->getMessage()); return false; }
}

$server = new rabbitMQServer('../nvdigestmq/nvRabbitMQ.ini', 'dmzServer');
$server->process_requests('dmzRouter');
