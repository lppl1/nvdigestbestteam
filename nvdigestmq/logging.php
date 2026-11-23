<?php
require_once(__DIR__ . '/path.inc');
require_once(__DIR__ . '/get_host_info.inc');
require_once(__DIR__ . '/rabbitMQLib.inc');

// $section: logDB, logFrontend or logDMZ
function logError($section, $component, $message) {
    try {
	$ini = __DIR__ . '/nvRabbitMQ.ini';
	//I think remove this section since we don't need env since host is in every log line + hosts are gonna be renamed anyway to like db-prod etc. etc.
       //$env = parse_ini_file($ini, tru)['environment']['NAME'] ?? 'unknown';
        $client = new rabbitMQClient($ini, $section);
        $client->publish([
            'type' => 'log',
           // 'env' => $env,
            'host' => gethostname(),
            'component' => $component,
            'message' => $message,
            'time' => date('c')
        ]);
    } catch (Throwable $e) {
        error_log("logError failed: " . $e->getMessage());
    }
}
