<?php
	require_once("dbFunctions.php");
	require_once('../nvdigestmq/path.inc'); //This is an extra place for php to look for files. Important because of AMQP
	require_once('../nvdigestmq/get_host_info.inc'); //Sole purpose is to read .ini config files like host.ini and turns 
							       //into a usable PHP array (host, port, username, password, vhost, exchange, queue)
	require_once('../nvdigestmq/rabbitMQLib.inc');


	function requestRouter($request){   //This listens for a request whether it be login or register
		if ($request ['type'] == 'login') { //If the request type was login
			return doLogin($request['username'], $request['password']);  //It would run through the login function
		}
		else if ($request ['type'] == 'register') {  //If request type was register
			return doRegister($request['username'], $request['password'], $request['email']); //It would go through register logic
		}
		else if ($request ['type'] == 'validate_session') {
			return doValidate($request['session_key']);
		}
		else if ($request ['type'] == 'logout') {
			return doLogout($request['session_key']); 
		}
		else if ($request ['type'] == 'search_games') {
			return doSearchGames($request['term'] ?? '');
		}
		else if ($request['type'] == 'get_game') {
    			return doGetGame((int)($request['game_id'] ?? 0));
		}
		else if ($request['type'] == 'library_add') {
			return doLibraryAdd($request['session_key'] ?? '', (int)($request['game_id'] ?? 0));
		}
		else if ($request['type'] == 'library_remove') {
			return doLibraryRemove($request['session_key'] ?? '', (int)($request['game_id'] ?? 0));
		}
		else if ($request['type'] == 'library_list') {
			return doLibraryList($request['session_key'] ?? '');
		}
		else if ($request['type'] == 'review_add') {
			return doReviewAdd($request['session_key'] ?? '', (int)($request['game_id'] ?? 0), (int)($request['rating'] ?? 0), $request['body'] ?? '');
		}
		else if ($request['type'] == 'thread_list') {
			return doThreadList((int)($request['game_id'] ?? 0));
		}
		else if ($request['type'] == 'create_thread'){
			reutrn doCreateThread($request['session_key'], (int)($request['game_id'] ?? 0), $request['title'], $request['body']);
		}
		else if ($request['type'] == 'thread_posts'){
			return doThreadPosts((int)$request['thread_id']);
		}
		else if ($request['type'] == 'reply'){
			return doThreadReply($request['session_key'], (int)$request['thread_id'], $request['body'];
		}

	}	
	$server = new rabbitMQServer("../nvdigestmq/nvRabbitMQ.ini", "nvdServer"); //This line grabs server infromation
	$server->process_requests('requestRouter'); //This line initiates the connection to the server

	/*$login = doLogin("bryan", "password");
		var_dump($login);
	*/
?>

