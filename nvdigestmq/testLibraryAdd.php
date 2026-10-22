#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("nvRabbitMQ.ini","nvdServer");
if (isset($argv[1]))
{
  $msg = $argv[1];
}
else
{
  $msg = "test message";
}

$request = array();
$request['type'] = "library_add";
$request['session_key'] = "   "; //PICK A SESSION KEY AND ADD IT HERE WHATEVER GAME ID YOU SELECT WILL BE SAVED UNDER THAT USER
$request['game_id'] = 1; //Add a game ID
$response = $client->send_request($request);
//$response = $client->publish($request);

echo "Game successfully chosen: ".PHP_EOL;
print_r($response);
echo "\n\n";

echo $argv[0]." END".PHP_EOL;




