<?php
require_once("nvdigestmq/rabbitMQLib.inc");
header("Content-Type: application/json");


$allowed = ["search_games", "get_game", "watch_add", "watch_remove", "watch_list", "library_add", "library_list", "library_remove","submit_review","show_reviews","thread_list","create_thread", "thread_posts","reply","my_squads", "create_squad", "join_squad", "squad_details", "squad_nominate"];
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
if ($type === "show_reviews"){
	$request["game_id"] = (int)($_POST["game_id"] ?? 0);
}
if ($type=="create_thread"){
	$request["session_key"]=$_COOKIE["session_key"] ?? "";
        $request["game_id"]=$_POST["game_id"];
	$request["title"]=$_POST["title"];
	$request["body"]=$_POST["body"];
}
if ($type === "my_squads"){
	$request["session_key"] = $_COOKIE["session_key"] ?? "";
}
if ($type === "create_squad"){
	$request["session_key"] = $_COOKIE["session_key"] ?? "";
	$request["name"] = $_POST["name"] ?? '';
	$request["budget"] = (float)($_POST["budget"] ?? 0);
}
if ($type === "join_squad"){
	$request["session_key"] = $_COOKIE["session_key"] ?? "";
	$request["code"] = $_POST["code"] ?? "";
}
if ($type === "squad_details"){
	$request["session_key"] = $_COOKIE["session_key"] ?? "";
	$request["squad_id"] = (int)($_POST["squad_id"] ?? 0);
}
if ($type === "squad_nominate"){
	$request["session_key"] = $_COOKIE["session_key"] ?? "";
	$request["squad_id"] = (int)($_POST["squad_id"] ?? 0);
	$request["game_id"] = (int)($_POST["game_id"] ?? 0);
	$request["target_price"] = $_POST["target_price"] ?? null;
}
if ($type === "watch_add") {
	$request["session_key"] = $_COOKIE["session_key"] ?? "";
	$request["game_id"] = (int)($_POST["game_id"] ?? 0);
	$request["target_price"] = (float)($_POST["target_price"] ?? 0);
}
if ($type === "watch_list") {
	$request["session_key"] = $_COOKIE["session_key"] ?? "";
}
if ($type === "watch_remove") {
	$request["session_key"] = $_COOKIE["session_key"] ?? "";
	$request["game_id"] = (int)($_POST["game_id"] ?? 0);
}
$client = new rabbitMQClient("nvdigestmq/nvRabbitMQ.ini", "nvdServer");
$response = $client->send_request($request);

echo json_encode($response);
exit();

?>
