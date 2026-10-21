<?php
require_once("nvdigestmq/rabbitMQLib.inc");
$request = $_POST;
//JQuery Response array
$jqresponse=
[
"type" => "",
"success" => "",
"validate" =>""
];

if (!isset($request["type"])) {
	echo json_encode($jqresponse);
	exit();
}

$client = new rabbitMQClient("nvdigestmq/nvRabbitMQ.ini","nvdServer");


if ($request["type"] === "logout") {
	$jqresponse["type"] = "logout";
	if (isset($_COOKIE["session_key"])) {
		$client->send_request([
		"type" => "logout",
		"session_key" => $_COOKIE["session_key"]
	]);
	}
	setcookie("session_key", "", time() - 3600, "/");
	$jqresponse["success"]="true";
	$jqresponse["validate"]= true;
	echo json_encode($jqresponse);
 	exit();
}

$jqresponse["success"]="true";
$jqresponse["validate"]=$client->send_request($request);
echo json_encode($jqresponse);
exit();
?>
