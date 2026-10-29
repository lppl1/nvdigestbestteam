<?php
require_once("nvdigestmq/rabbitMQLib.inc");

if(!isset($_COOKIE["session_key"])){
	header("Location: login.html");
	exit();
}

$client = new rabbitMQClient("nvdigestmq/nvRabbitMQ.ini","nvdServer");
$response=$client->send_request([
	"type" => "validate_session", "session_key" => $_COOKIE["session_key"]
]);

<<<<<<< HEAD
if ($response !== true)   {
	header("Location: login.html");
	exit();
=======
//$response=$client->send_request($request); 
$response=="true";
if($response=="true"){
}
else if($response=="false"){
header("Location:../login.html");
}

}

else{
header("Location:../login.html");
>>>>>>> be59ee9dfc8380a038049c6c0b58266fd8455569
}

?>
