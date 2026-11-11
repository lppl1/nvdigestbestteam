<?php
require_once(__DIR__ . '/dmzFunctions.php');
$c = dmzConfig();

echo "user     = [" . $c['SMTP_USER'] . "]\n";
echo "pass len = " . strlen($c['SMTP_PASS']) . "  (Google app passwords are exactly 16)\n";
echo "pass has anything other than a-z: " . (preg_match('/[^a-z]/', $c['SMTP_PASS']) ? "YES (wrong)" : "no (good)") . "\n\n";

$mail = new \PHPMailer\PHPMailer\PHPMailer(true);
$mail->SMTPDebug = 2;
$mail->Debugoutput = function ($str, $level) {
	// hide the base64 login lines for safe paste
	if (preg_match('/^CLIENT -> SERVER: [A-Za-z0-9+\/=]{8,}\s*$/', $str)) $str = "CLIENT -> SERVER: [hidden]\n";
	echo $str;
};
try {
	$mail->isSMTP();
	$mail->Host       = $c['SMTP_HOST'];
	$mail->Port       = (int)$c['SMTP_PORT'];
	$mail->SMTPAuth   = true;
	$mail->Username   = $c['SMTP_USER'];
	$mail->Password   = $c['SMTP_PASS'];
	$mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
	$mail->setFrom($c['SMTP_USER'], 'DealDigest');
	$mail->addAddress($c['SMTP_USER']);
	$mail->Subject = 'DealDigest SMTP test';
	$mail->Body    = 'It works.';
	$mail->send();
	echo "\nSENT OK\n";
} catch (Throwable $e) {
	echo "\nFAILED: " . $e->getMessage() . "\n";
}
