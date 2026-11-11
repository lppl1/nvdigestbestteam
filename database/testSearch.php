<?php
ini_set('amqp.read_timeout', '10');
require_once('../nvdigestmq/path.inc');
require_once('../nvdigestmq/get_host_info.inc');
require_once('../nvdigestmq/rabbitMQLib.inc');

$term = $argv[1] ?? 'hearts of iron';
$testClient = new rabbitMQClient('../nvdigestmq/nvRabbitMQ.ini', 'nvdServer');

