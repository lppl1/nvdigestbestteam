<?php
ini_set('amqp.read_timeout', '10');
require_once('../nvdigestmq/path.inc');
require_once('../nvdigestmq/get_host_info.inc');
require_once('../nvdigestmq/rabbitMQLib.inc');

$term = $argv[1] ?? 'hearts of iron';//testing hearts of iron 4 to see if anything is even returned with my hotfix in dmz
$testClient = new rabbitMQClient('../nvdigestmq/nvRabbitMQ.ini', 'nvdServer');
var_dump($testClient->send_request(['type' => 'search_games', 'term' => $term]));


