<?php
if (file_exists(__DIR__ . '/vendor/autoload.php')) 
	require_once(__DIR__ . '/vendor/autoload.php');
use PHPMailer\PHPMailer\PHPMailer;

const CHEAPSHARK = "https://www.cheapshark.com/api/1.0/";
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
	curl_close($ch);
	return ($response !== false && $code === 200) ? json_decode($response, true) : null; 
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


//annoying emails
function sendAlertEmail($to, $subject, $html) {
	$c = dmzConfig();
	try {
		$mail = new PHPMAILER(true);
		$mail->isSMTP();
		$mail->Host	= $c['SMTP_HOST'];
		$mail->Port	= (int)$c['SMTP_PORT'];
		$mail->SMTPAuth	= true;
		$mail->Username = $c['SMTP_USER'];
		$mail->Password = $c['SMTP_PASS'];
		$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
		$mail->setFrom($c['FROM_EMAIL'], $c['FROM_NAME']);
		$mail->addAddress($to);
		$mail->isHTML(true);
		$mail->Subject = $subject;
		$mail->Body = $html;
		$mail->AltBody = strip_tags($html);
		return $mail->send();
	} catch (Throwable $e) {
		echo "Email failed:( " . $e->getMessage() . "\n";
		return false;
	}
}
?>
