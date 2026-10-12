<?php
require_once("nvdigestmq/rabbitMQLib.inc");


$client = new rabbitMQClient("nvdigestmq/nvRabbitMQ.ini","nvdServer");
if(isset($_COOKIE["session_key"])){
//manually creating the message to send
$request=[
"type"=> "validate_session",
"session_key"=>$_COOKIE["session_key"]
]

$response=$client->send_request($request);
if($response=="true"){
exit();
}
else if($response=="false"){
header("Location:login.html");
exit();
}

}

else{
header("Location:login.html");
exit();
}
?>
