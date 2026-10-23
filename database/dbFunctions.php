<?php
	require_once("dbConnection.php");


	function doLogin($username, $password){
		$db = dbConnection();
		$selectSql = "SELECT username, password FROM users WHERE username=?";
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
			$updateSql = "UPDATE users SET session_key = ?, session_created = NOW()  WHERE username = ?";
				if($stmt = $db->prepare($updateSql)){
					$stmt->bind_param("ss", $hex, $username);
					$stmt->execute();
						return $hex;
			}
			else {
				return False;
			}

        }
        else {
            return False;
        }
    }
        else
        return False;
	
}
function doLogout($session_key) {
	$db = dbConnection();
	$updateSql = "UPDATE users SET session_key = NULL WHERE session_key = ?";
		if($stmt = $db->prepare($updateSql)) {
			$stmt->bind_param("s", $session_key);
			$stmt->execute();
			return True;
		}
		else {
			return False;
		}
}

		
function doValidate($session_key) {
	$db = dbConnection();
	$selectSql = "SELECT username FROM users WHERE session_key = ? AND session_created > NOW() - INTERVAL 2 HOUR";
	if($stmt = $db->prepare($selectSql)){
		$stmt->bind_param("s", $session_key);
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
		if ($row == null) {
			return False;
		}
			return True;
	}
	else {
		return False;
	}
}


	function doRegister($username, $password, $email){
		$db = dbConnection();
		$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
		//This line will add the hashedpassword into password column
		$insertSql = "INSERT INTO users (username, password, email) VALUES (?, ?, ?)";
			if($stmt = $db->prepare($insertSql)) {
		$stmt->bind_param("sss", $username, $hashedPassword, $email);
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
	function doSearchGames($term){
		$db = dbConnection();
		$like = "%" . $term . "%";

		$sql = "SELECT id, title, image_url, metacritic FROM games WHERE title LIKE ? LIMIT 50";

		        if($stmt = $db->prepare($sql)){
                	$stmt->bind_param("s", $like);
               		$stmt->execute();
               		$result = $stmt->get_result();
			return $result->fetch_all(MYSQLI_ASSOC);
			}
			return array();

}
	function doGetGame($game_id){
		$db = dbConnection();
		$sql = "SELECT * FROM games WHERE id = ?";
    if($stmt = $db->prepare($sql)){
        $stmt->bind_param("i", $game_id);
        $stmt->execute();
        $game = $stmt->get_result()->fetch_assoc();
        if ($game === null) {
            return False;
        }
    } else {
        return False;
    }
    $sql = "SELECT s.name AS store, p.sale_price, p.normal_price, p.deal_id FROM game_prices p JOIN stores s ON s.id = p.store_id WHERE p.game_id = ? ORDER BY p.sale_price ASC";
    $game['prices'] = array();
    if($stmt = $db->prepare($sql)){
        $stmt->bind_param("i", $game_id);
        $stmt->execute();
        $game['prices'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    return $game;
}
	function sessionUser($db, $session_key){
    		$sql = "SELECT id FROM users WHERE session_key = ? AND session_created > NOW() - INTERVAL 2 HOUR";
    		if($stmt = $db->prepare($sql)){
        		$stmt->bind_param("s", $session_key);
        		$stmt->execute();
        		$row = $stmt->get_result()->fetch_assoc();
        	if ($row === null) {
            	return null;
        }
        	return $row['id'];
    }
    	return null;
}	
	function doLibraryAdd($session_key, $game_id){
    		$db = dbConnection();
    		$user_id = sessionUser($db, $session_key);
    		if ($user_id === null) {
        		return False;                      
    }
    		$sql = "INSERT INTO user_library (user_id, game_id) VALUES (?, ?)";
			try {
				$stmt = $db->prepare($sql);
				$stmt->bind_param("ii", $user_id, $game_id);
				$stmt->execute();
				return True;

    		if($stmt = $db->prepare($sql)){
        		$stmt->bind_param("si", $username, $game_id); 
        	try {
            		$stmt->execute();
            		return True;
        }
        	catch (Exception $e) {
            	return False;                   
        }
  }

?>


