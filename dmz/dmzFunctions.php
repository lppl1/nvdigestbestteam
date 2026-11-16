<?php
if (file_exists(__DIR__ . '/vendor/autoload.php')) 
	require_once(__DIR__ . '/vendor/autoload.php');
use PHPMailer\PHPMailer\PHPMailer;

const CHEAPSHARK = "https://www.cheapshark.com/api/1.0/";
const STEAM_STORE = "https://store.steampowered.com/api/appdetails";
const MQ_INI	 = __DIR__ . "/../nvdigestmq/nvRabbitMQ.ini";
const USER_AGENT = "DealDigest/1.0 (IT490 Semester Project)";

function dmzConfig() {
	static $cfg = null;
	return $cfg ??= parse_ini_file(__DIR__ . "/dmz_config.ini");
}
function httpGet($url) {
	$ch = curl_init($url);
	curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true,  CURLOPT_USERAGENT => USER_AGENT, CURLOPT_TIMEOUT => 15]);
	$response = curl_exec($ch);
	$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	if ($response === false || $code !== 200) {
		echo "httpGet failed (HTTP $code): $url\n";
	}
	return json_decode($response, true);	
}

//the frugal shark
function fetchStores() {
	return httpGet(CHEAPSHARK . "stores"); }
function fetchDeals($page) {
	return httpGet(CHEAPSHARK . "deals?pageSize=60&sortBy=Recent&pageNumber=" . (int)$page); }
function fetchGamePrices($id) {
	return httpGet(CHEAPSHARK . "games?id=" . (int)$id); }
function searchDeals($title){
	return httpGet(CHEAPSHARK . "deals?pageSize=60&title=" . urlencode($title)); }


//annoying emails
function sendAlertEmail($to, $subject, $body, $html = null) {
	$c = dmzConfig();
	try {
		$mail = new PHPMAILER(true);
		$mail->isSMTP();
		$mail->Host	= $c['SMTP_HOST'] ?? 'smtp.gmail.com';
		$mail->Port	= (int)($c['SMTP_PORT'] ?? 587);
		$mail->SMTPAuth	= true;
		$mail->Username = $c['SMTP_USER'] ?? '';
		$mail->Password = $c['SMTP_PASS'] ?? '';
		$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
		$mail->CharSet	= 'UTF-8';
		$mail->setFrom($c['FROM_EMAIL'] ?? $mail->Username, $c['FROM_NAME'] ?? 'DealDigest');
		$mail->addAddress($to);
		$mail->Subject  = $subject;
		if ($html !== null) {
			$mail->isHMTML(true);
			$mail->Body = $html;
			$mail->AltBody = $body;
		} else {
		$mail->Body = $body; }		
		return $mail->send();
	} catch (Throwable $e) {
		echo "Email failed:( " . $e->getMessage() . "\n";
		return false;
	}
}
function fetchSteamDetails($appId) {
	$appId = (int)$appId;
	if ($appId <= 0) return null;
 
	$json = httpGet(STEAM_STORE . "?appids=$appId&cc=us&l=en");
	if (empty($json[$appId]['success'])) return null;
	$d = $json[$appId]['data'];
 
	return [
		"steam_app_id"     => $appId,
		"title"            => $d['name'] ?? '',
		"developers"       => implode(', ', $d['developers'] ?? []),
		"publishers"       => implode(', ', $d['publishers'] ?? []),
		"release_date"     => $d['release_date']['date'] ?? '',
		"metacritic_score" => $d['metacritic']['score'] ?? null,   // many games have none
		"metacritic_url"   => $d['metacritic']['url'] ?? null,
		"description"      => trim(strip_tags($d['short_description'] ?? '')),
		"header_image"     => $d['header_image'] ?? '',
		"genres"           => implode(', ', array_column($d['genres'] ?? [], 'description')),
	];
} 

?>
