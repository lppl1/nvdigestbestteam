<?php
	require_once("dbConnection.php");


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

	function doRegister($username, $password, $email){
		$db = dbConnection();
		$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
		//This line will add the hashedpassword into password column
		$insertSql = "INSERT INTO app_users (username, password, email) VALUES (?, ?, ?)";
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

?>


