<?php
//dmz fetches the deals from cheapshark and sends to db through rabbitmq
require_once('path.inc');
require_once('get_host_inc.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("nvRabbitMQ.ini", "nvdServer");
//save stores before pulling prices
$ch = curl_init("https://www.cheapshark.com/api/1.0/stores");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, "it490");
$response = curl_exec($ch);
$stores = json_decode($response, true); 

$client->publish(["type" => "stores", "data" => $stores]); //sends store list to dbListener

for ($page = 0; $page < 5; $page++){
	$ch = curl_init("https://www.cheapshark.com/api/1.0/deals?pageSize=10&pageNumber=" . $page);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, "it490");
	$response = curl_exec($ch);
	$deals = json_decode($response, true);
	$client->publish(["type" => "deals". "data" => $deals]); //sends page w/ details to dbListener

	sleep(1); //anti-cheapshark blocking mechanism
}
?>

