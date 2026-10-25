<?php
if (file_exists(__DIR__ . '/vendor/autoload.php')) 
	require_once(__DIR__ . '/vendor/autoload.php');
use PHPMailer\PHPMailer\PHPMailer;

const CHEAPSHARK = "https://www.cheapshark.com/api/1.0/";
const RAWG	 = "https://api.rawg.io/api/";
const MQ_INI	 = __DIR__ . "/nvRabbitMQ.ini";
const USER_AGENT = "DealDigest/1.0 (IT490 Semester Project);

function dmzConfig() {
	static $cfg = null;
	return $cfg ??= parse_ini_file(__DIR__ . "/dmz_config.ini");
}
function httpGet($url) {
	$ch = curl_init($url);
	curl_setopt_array($ch, [$CURLOPT_RETURNTRANSFER => true,  CURLOPT_USERAGENT => USER_AGENT, CURLOPT_TIMEOUT => 15]);
	$response = curl_exec($ch);
	$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	
	return ($response !== false && $code ===200 ? json_decode($response, true) : null; 
}

//the frugal shark
fetchStores()
fetchDeals()
fetchGamePrices()
searchDeals()


//What's up RAWG
fetchRawg

//annoying emails
sendAlertEmail
?>
