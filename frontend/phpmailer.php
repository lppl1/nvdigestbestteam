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
$mail->isHTML(true);
$mail->Subject = 'Email Subject';
$mail->Body = '<h1>HTML Content</h1>';
$mail->AltBody = 'Plain text content';


//Error logging and sending
try {
	$mail->send();
	echo 'Email sent successfully!';
}
	catch (Exception $e) {
		echo 'Email failed: ' . $mail->ErrorInfo;
	}


?>


