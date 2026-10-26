<?php
require_once("nvdigestmq/rabbitMQLib.inc");
header("Content-Type: application/json");


$allowed = ["search_games", "get_game", "library_add", "library_list", "library_remove","submit_review,show_reviews"];
$type = $_POST["type"] ?? "";

if (!in_array($type, $allowed, true)) {
    http_response_code(400);
    echo json_encode(false);
    exit();
}

$request = ["type" => $type];

if ($type === "search_games") {
    $request["term"] = $_POST["term"] ?? "";
}
if ($type === "get_game") {
    $request["game_id"] = (int)($_POST["game_id"] ?? 0);
}
if ($type === "library_add" || $type === "library_remove") {
    $request["session_key"] = $_COOKIE["session_key"] ?? "";
    $request["game_id"] = (int)($_POST["game_id"] ?? 0);
}
if ($type === "library_list") {
	$request["session_key"] = $_COOKIE["session_key"] ?? "";
}
if ($type === "submit_review"){
	$request["session_key"]=$_COOKIE["session_key"] ?? "";
	$request["game_id"]=$_POST["game_id"];
	$request["rating"]=$_POST["rating"];
	$request["body"]=$_POST["body"];
}
$client = new rabbitMQClient("nvdigestmq/nvRabbitMQ.ini", "nvdServer");
$response = $client->send_request($request);

echo json_encode($response);
exit();




?>
