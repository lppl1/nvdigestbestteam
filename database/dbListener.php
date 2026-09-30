<?php
	require_once("dbFunctions.php");
	require_once('../rabbitmq/rabbitmqphp_example/path.inc'); //This is an extra place for php to look for files. Important because of AMQP
	require_once('../rabbitmq/rabbitmqphp_example/get_host_info.inc'); //Sole purpose is to read .ini config files like host.ini and turns 
							       //into a usable PHP array (host, port, username, password, vhost, exchange, queue)
	require_once('../rabbitmq/rabbitmqphp_example/rabbitMQLib.inc');


	function requestRouter($request){   //This listens for a request whether it be login or register
		if ($request ['type'] == 'login') { //If the request type was login
			return doLogin($request['username'], $request['password']);  //It would run through the login function
		}
		else if ($request ['type'] == 'register') {  //If request type was register
			return doRegister($request['username'], $request['password']); //It would go through register logic
		}
	}

	$server = new rabbitMQServer("../rabbitmq/rabbitmqphp_example/testRabbitMQ.ini", "testServer"); //This line grabs server infromation
	$server->process_requests('requestRouter'); //This line initiates the connection to the server

	/*$login = doLogin("bryan", "password");
		var_dump($login);
	*/

?>

