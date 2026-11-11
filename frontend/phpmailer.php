<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';


$mail = new PHPMailer(true);

$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'ADD YOUR GMAIL OR MAKE A NEW ONE FOR THIS PROJECT';
$mail->Password = 'Generate app password on Gmail check the CodeLucky Video I sent';
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = 587; //Check your firewall

//Sets sender and recipient we will have to grab the users email from database
$mail->setFrom('send@example.com', 'Sender Name');
$mail->addAddress('recipient@example.com');

//This will be the email content
foreach ($alerts as $a) {
        $link = !empty($a['deal_id'])
                ? "https://www.cheapshark.com/redirect?dealID=" . urlencode($a[>
        if (($a['alert_type'] ?? 'price') === 'budget') {
                $subject = "DealDigest: " . $a['title'] . " fits your budget";
                $body = "Hey " . $a['username'] . ",\n\n"
                        . $a['title'] . " is now \$" . $a['sale_price'] . " at >
                        . ", which keeps you within your budget. \n";
        } else {
                $subject = "DealDigest: " . $a['title'] . " hit your target pri>
                $body = "Hey " . $a['username'] . ",\n\n"
                        . $a['title'] . " is now \$" . $a['target_price'] . ").>
        }
        if ($link !== "") {
                $body .= "\nUnlock Your Deal Here ;) $link\n";
        }
        $body .= "\n- DealDigest\n";

        if (sendEmail($a['email'], $subject, $body)) {
                $client->send_request(["type" => "mark_alert_sent", "alert_id" >
                echo "sent alert  " . $a['alert_id'] . " to " . $a['email'] . ">
        }
        sleep(1);
}


//Error logging and sending
try {
	$mail->send();
	echo 'Email sent successfully!';
}
	catch (Exception $e) {
		echo 'Email failed: ' . $mail->ErrorInfo;
	}


?>


