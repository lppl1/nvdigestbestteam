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
function fetchStores() {
	return httpGet(CHEAPSHARK . "stores"); }
function fetchDeals($page) {
	return httpGet(CHEAPSHARK . "deals?pageSize=10&pageNumber=" . (int)$page); }
function fetchGamePrices($id) {
	return httpGet(CHEAPSHARK . "games?id=" . (int)$id); }
function searchDeals($title){
	return httpGet(CHEAPSHARK . "deals?pageSize=10&title=" . urlencode($title)); }

//What's up RAWG
function fetchRawg($title){
	$key = dmzConfig()['RAWG_KEY'];
	$found = httpGet(RAWG . "games?key=$key&page_size=1&search=" . urlencode($title));
	if (empty($found['results'])) return null;
	$id = $found ['results'][0]['id'];
	$d = httpGet(RAWG . "games/$id?key=$key");
	if(!$d) return null;
	return [
	"rawg_id"	=> $id,
	"description"	=> $d['description_raw' ?? '',
	"punlisher" 	=> $d['publishers'][0]['name'] ?? null,
	"developer"	=> $d['developers'][0]['name'] ?? null,
	"genres"	=> implode(",", array_column($d['genres'] ?? [], 'name')),
	]; 
}

//annoying emails
function sendAlertEmail($to, $subject, $body) {
	$autoload = __DIR__ . "/vendor/autoload.php";
	if (!file_exist($autoload) {
		echo "vendor/autoload.php missing; run composer require phpmailer/phpmailer\n";
		return false;
	}
	require_once($autoload);
	$cfg = dmzConfig();

	$mail = new PHPMailer\PHPMailer\PHPMailer(true);
	try {
		$mail->isSMTP();
		$mail->Host	= $cfg['SMTP_HOST'];
		$mail->Port	= (int)$cfg['SMTP_PORT'];
		$mail->SMTPAuth	= true;
		$mail->Username = $cfg['SMTP_USER'];
		$mail->Password = $cfg['SMTP_PASS'];
		$mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
		$mail->setFRom($cfg['SMTP_FROM'], $cfg['SMTP_FROM_NAME'] ?? 'DealDigest');
		$mail->addAddress($to);
		$mail->Subject = $subject;
		$mail->Body = $body;
		$mail->send();
		return true;
	} catch (Exception $e) {
		echo "Email failed:( " . $mail->ErrorInfo . "\n";
		return false;
	}
}
?>
