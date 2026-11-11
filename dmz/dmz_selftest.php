<?php
// Run on the DMZ VM:  php dmz_selftest.php [you@example.com]
// Checks everything EXCEPT the DB listener, so you can find problems one layer at a time.
require_once(__DIR__ . '/dmzFunctions.php');

function check($label, $ok, $hint = '') {
	echo ($ok ? "[ OK ] " : "[FAIL] ") . $label . ($ok || $hint === '' ? "" : "\n         -> $hint") . "\n";
	return $ok;
}

echo "== PHP ==\n";
check("PHP version " . PHP_VERSION, version_compare(PHP_VERSION, '7.4', '>='), "need 7.4+");
foreach (['curl', 'bcmath', 'mbstring', 'sockets'] as $ext) {
	check("extension $ext", extension_loaded($ext), "sudo apt install php-$ext");
}
check("vendor/autoload.php", file_exists(__DIR__ . '/vendor/autoload.php'), "composer require php-amqplib/php-amqplib phpmailer/phpmailer");
check("PHPMailer class", class_exists('PHPMailer\PHPMailer\PHPMailer'), "composer require phpmailer/phpmailer");
foreach (['path.inc', 'get_host_info.inc', 'rabbitMQLib.inc'] as $f) {
	check("shared file $f", file_exists(dirname(MQ_INI) . "/$f"), "copy it from your group repo into this folder");
}

echo "\n== Config ==\n";
$cfg = dmzConfig();
check("dmz_config.ini readable", is_array($cfg), "file missing or has a syntax error");
check("RAWG_KEY set", !empty($cfg['RAWG_KEY']) && $cfg['RAWG_KEY'] !== 'your_rawg_api_key', "put your real key in dmz_config.ini");
check("SMTP_USER set", !empty($cfg['SMTP_USER']) && $cfg['SMTP_USER'] !== 'youraddress@gmail.com', "put your real address in dmz_config.ini");

echo "\n== RabbitMQ (TCP only) ==\n";
$ini = file_exists(MQ_INI) ? parse_ini_file(MQ_INI, true) : null;
check("nvRabbitMQ.ini readable", is_array($ini), "file missing");
if (is_array($ini)) {
	foreach (['dmzServer'] as $sec) {
		check("section [$sec] exists", isset($ini[$sec]), "add the section to nvRabbitMQ.ini");
	}
	$h = $ini['dmzServer']['BROKER_HOST'] ?? ''; $p = (int)($ini['dmzServer']['BROKER_PORT'] ?? 5672);
	$sock = @fsockopen($h, $p, $en, $es, 5);
	check("broker reachable at $h:$p", (bool)$sock, "wrong IP, RabbitMQ stopped, or firewall: on broker VM run sudo ufw allow from DMZ_IP to any port 5672");
	if ($sock) fclose($sock);
}

echo "\n== Internet APIs ==\n";
$stores = fetchStores();
check("CheapShark stores (" . (is_array($stores) ? count($stores) : 0) . ")", !empty($stores), "no outbound HTTPS, DNS problem, or rate limited");
$deals = fetchDeals(0, 3);
check("CheapShark deals", !empty($deals), "rate limited? wait a minute and retry");
$r = fetchRawg("Portal 2");
check("RAWG details" . (!empty($r['publisher']) ? " (publisher: {$r['publisher']})" : ""), !empty($r), "bad RAWG_KEY or no access to api.rawg.io");

if (!empty($argv[1])) {
	echo "\n== Email ==\n";
	check("test email to {$argv[1]}", sendAlertEmail($argv[1], "DealDigest DMZ test", "<p>It works.</p>"),
		"for Gmail use an App Password (needs 2-step verification), port 587; check fetch.log / php error log");
}
echo "\nDone.\n";
?>
