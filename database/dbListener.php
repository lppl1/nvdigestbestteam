<?php
	require_once("dbConnection.php");
	require_once('rabbitmqphp_example/path.inc'); //This is an extra place for php to look for files. Important because of AMQP
	require_once('rabbitmqphp_example/get_host_info.inc'); //Sole purpose is to read .ini config files like host.ini and turns 
							       //into a usable PHP array (host, port, username, password, vhost, exchange, queue)
	require_once('rabbitmqphp_example/rabbitMQLib.inc');


function doLogin($username, $password){
	$db = dbConnection();
	$selectSql = "SELECT username, password FROM app_users WHERE username=?";
	    if($stmt = $db->prepare($selectSql)) {
        	$stmt->bind_param("s", $username);
        	$stmt->execute();
            		echo "Successfully selected user";
        	$result = $stmt->get_result();
		$row = $result->fetch_assoc();
		if ($row === null) {
			return False;
		}
	if(password_verify($password, $row['password'])){
		//If the password was correct a unique randomly generated key will be added to the session_key column of the database
		$session_key = random_bytes(32);
		$hex = bin2hex($session_key);
		$updateSql = "UPDATE app_users SET session_key = ? WHERE username = ?";
			if($stmt = $db->prepare($updateSql)){
				$stmt->bind_param("ss", $hex, $username);
				$stmt->execute();
				return True;
			}
			else {
				return False;
			}
		return True;

        }
        else {
            return False;
        }
    }
    else
        return False;
}		

	function doRegister($username, $password){
		$db = dbConnection();
		$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
		//This line will add the hashedpassword into password column
		$insertSql = "INSERT INTO app_users (username, password) VALUES (?, ?)";
			if($stmt = $db->prepare($insertSql)) {
		$stmt->bind_param("ss", $username, $hashedPassword);
			try {
				$stmt->execute();
				return True;
			}
		catch (Exception $e) {
			return False;
		}

			}
	
		else {
			return False;
		}
	}

	function requestRouter($request){   //This listens for a request whether it be login or register
		if ($request ['type'] == 'login') { //If the request type was login
			return doLogin($request['username'], $request['password']);  //It would run through the login function
		}
		else if ($request ['type'] == 'register') {  //If request type was register
			return doRegister($request['username'], $request['password']); //It would go through register logic
		}
	}

	$server = new rabbitMQServer("rabbitmqphp_example/testRabbitMQ.ini", "testServer"); //This line grabs server infromation
	$server->process_requests('requestRouter'); //This line initiates the connection to the server
	

	/*$login = doLogin("bryan", "password");
		var_dump($login);
	*/

?>

