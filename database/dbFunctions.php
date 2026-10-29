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
				$new_id = $db->insert_id;
				$db->query("INSERT INTO wallets (user_id, balance) VALUES (" . (int)$new_id . ", 100.00)");
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
   		 $sql = "SELECT s.name AS store, p.store_id, p.sale_price, p.normal_price, p.deal_id FROM game_prices p JOIN stores s ON s.id = p.store_id WHERE p.game_id = ? ORDER BY p.sale_price ASC";
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
		}
		catch (Exception $e) {
			return False;
		}
	}

	function doLibraryRemove($session_key, $game_id){
		$db = dbConnection();
		$user_id = sessionUser($db, $session_key);
		if ($user_id === null) {
			return False;
		}
		$sql = "DELETE FROM user_library WHERE user_id = ? AND game_id = ?";
		try {
			$stmt = $db->prepare($sql);
			$stmt->bind_param("ii", $user_id, $game_id);
			$stmt->execute();
			return $stmt->affected_rows > 0;
		}
		catch (Exception $e) {
			return False;
		}
	}

	function doLibraryList($session_key){
		$db = dbConnection();
		$user_id = sessionUser($db, $session_key);
		if ($user_id === null) {
			return False;
		}
		$sql = "SELECT g.id, g.title, g.image_url, l.status FROM user_library l JOIN games g ON g.id = l.game_id WHERE l.user_id = ? ORDER BY l.added DESC";
		try {
			$stmt = $db->prepare($sql);
			$stmt->bind_param("i", $user_id);
			$stmt->execute();
			return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
		}
		catch (Exception $e) {
			return False;
		}
	}
	function doAddReview($session_key, $game_id, $rating, $body){
		$db = dbConnection();
		$user_id = sessionUser($db, $session_key);
		if ($user_id === null){
			return false;
		}
		if ($rating < 1 || $rating > 5){
			return false;
		}
		$sql = "INSERT INTO reviews (user_id, game_id, rating, body) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE rating = VALUES(rating), body = VALUES(body)";
		try{
			$stmt = $db->prepare($sql);
			$stmt->bind_param("iiis", $user_id, $game_id, $rating, $body);
			$stmt->execute();
			return true;
		} catch (exception $e) {
			return false;
		}
	}
	function showReview($game_id){
		$db = dbConnection();
		$sql = "SELECT u.username, r.rating, r.body FROM reviews r JOIN users u ON u.id = r.user_id WHERE r.game_id = ?";
		$stmt = $db->prepare($sql);
		$stmt->bind_param("i", $game_id);
		$stmt->execute();
		return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
	}
	function doThreadList($game_id){
		$db = dbConnection();

		if ($game_id == 0){
			$sql = "SELECT t.id, t.title, t.created_at, u.username FROM forum_threads t JOIN users u ON u.id = t.author_id WHERE t.game_id IS NULL ORDER BY t.created_at DESC";
		} else {
			$sql = "SELECT t.id, t.title, t.created_at, u.username FROM forum_threads t JOIN users u ON u.id = t.author_id WHERE t.game_id = ? ORDER BY t.created_at DESC";
		}
		try{
			$stmt = $db->prepare($sql);
			if ($game_id != 0){
				$stmt->bind_param("i", $game_id);
			}
			$stmt->execute();
			return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
		} catch (exception $e){
			return false;
		}	
	}
	
	function doCreateThread($session_key, $game_id, $title, $body){
		$db = dbConnection();
		$user_id = sessionUser($db, $session_key);
		if ($user_id === null){
			return false;
		}
		if ($game_id == 0){
			$game_id = null;
		}
		try {
			$stmt = $db->prepare("INSERT INTO forum_threads (author_id, game_id, title) VALUES (?, ?, ?)");
			$stmt->bind_param("iis", $user_id, $game_id, $title);
			$stmt->execute();
			$thread_id = $db->insert_id;
			$stmt = $db->prepare("INSERT INTO forum_posts (thread_id, author_id, body) VALUES (?, ?, ?)");
			$stmt->bind_param("iis", $thread_id, $user_id, $body);
			$stmt->execute();
			return $thread_id;
		} catch (exception $e){
			return false;
		}
	}
	
	function doThreadPosts($thread_id){
		$db = dbConnection();
		$sql = "SELECT p.id, p.body, p.created, u.username FROM forum_posts p JOIN users u ON u.id = p.author_id WHERE p.thread_id = ? ORDER BY p.created ASC";
		$stmt = $db->prepare($sql);
		$stmt->bind_param("i", $thread_id);
		$stmt->execute();
		return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
	}

	function doThreadReply($session_key, $thread_id, $body){
		$db = dbConnection();
		$user_id = sessionUser($db, $session_key);
		if ($user_id === null){
			return false;
		}
		try {
			$stmt = $db->prepare("INSERT INTO forum_posts (thread_id, author_id, body) VALUES (?, ?, ?)");
			$stmt->bind_param("iis", $thread_id, $user_id, $body);
			$stmt->execute();
			return true;
		} catch (exception $e){
			return false;
		}
	}

	//is this person in teh squad
	function isMember($db, $squad_ID, $user_id){
		$stmt = $db->prepare("SELECT 1 FROM squad_members WHERE squad_id = ? AND user_id = ?");
		$stmt->bind_param("ii", $squad_ID, $user_id);
		$stmt->execute();
		return $stmt->get_result()->fetch_assoc() !== null;
	}

	function doCreateSquad($session_key, $name, $budget){
		$db = dbConnection();
		$user_id = sessionUser($db, $session_key);
		if ($user_id === null){
			return false;
		}
		$code = strtoupper(bin2hex(random_bytes(3))); //random 6 character join code
		try { 	
			$stmt = $db->prepare("INSERT INTO squads (name, owner_id, join_code, monthly_budget) VALUES (?, ?, ?, ?)");
			$stmt->bind_param("sisd", $name, $user_id, $code, $budget);
			$stmt->execute();
			$squad_ID = $db->insert_id;
			$stmt = $db->prepare("INSERT INTO squad_members (squad_id, user_id) VALUES (?, ?)");
			$stmt->bind_param("ii", $squad_ID, $user_id);
			$stmt->execute();
			return $squad_ID;
		} catch (exception $e){
			return false;
		}
	}

	function doJoinSquad($session_key, $code){
		$db = dbConnection();
		$user_id = sessionUser($db, $session_key);
		if ($user_id === null){
			return false;
		}
		$stmt = $db->prepare("SELECT id from squads WHERE join_code = ?"); //finding squad with join code
		$stmt->bind_param("s", $code);
		$stmt->execute();
		$row = $stmt->get_result()->fetch_assoc();
		if ($row === null){
			return false; //if no squad has this code
		}
		$squad_ID = $row['id'];
		try {
			$stmt = $db->prepare("INSERT INTO squad_members (squad_id, user_id) VALUES (?, ?)");
			$stmt->bind_param("ii", $squad_ID, $user_id);
			$stmt->execute();
			return $squad_ID;
		} catch (exception $e) {
			return false;
		}
	}

	function doMySquads($session_key){
		$db = dbConnection();
		$user_id = sessionUser($db, $session_key);
		if ($user_id === null){
			return false;
		}
		$sql = "SELECT s.id, s.name, s.join_code, s.monthly_budget FROM squads s JOIN squad_members m ON m.squad_id = s.id WHERE m.user_id = ? ORDER BY s.name";
		$stmt = $db->prepare($sql);
		$stmt->bind_param("i", $user_id);
		$stmt->execute();
		return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
	}

	function doSNominate($session_key, $squad_ID, $game_id, $target_price){
		$db = dbConnection();
		$user_id = sessionUser($db, $session_key);
		if ($user_id === null || !isMember($db, $squad_ID, $user_id)){
			return false;
		}
		try { 
			$stmt = $db->prepare("INSERT INTO squad_games (squad_id, game_id, added_by, target_price) VALUES (?, ?, ?, ?)");
			$stmt->bind_param("iiid", $squad_ID, $game_id, $user_id, $target_price);
			$stmt->execute();
			return true;
		} catch (exception $e) {
			return false;
		}
	}

	function doSDetails($session_key, $squad_ID){
		$db = dbConnection();
		$user_id = sessionUser($db, $session_key);
		if ($user_id === null || !isMember($db, $squad_ID, $user_id)){
			return false;
		}
		$stmt = $db->prepare("SELECT id, name, join_code, monthly_budget FROM squads WHERE id = ?");
		$stmt->bind_param("i", $squad_ID);
		$stmt->execute();
		$squad = $stmt->get_result()->fetch_assoc();
		$stmt = $db->prepare("SELECT COALESCE(SUM(price_paid_, 0) AS spent FROM orders WHERE squad_id = ? AND created_at >= DATE_FORMAT(NOW(), '%Y-%m-01')");
		$stmt->bind_param("i", $squad_ID);
		$stmt->execute();
		$squad['spent'] = (float)$stmt->get_result()->fetch_assoc()['spent'];
		$stmt = $db->prepare("SELECT u.username FROM squad_members m JOIN users u ON u.id = m.user_id WHERE m.squad_id = ?");
		$stmt->bind_param("i", $squad_ID);
		$stmt->execute();
		$squad['members'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
		$sql = "SELECT g.id, g.title, (SELECT MIN(p.sale_price) FROM game_prices p WHERE p.game_id = sg.game_id) AS best_price FROM squad_games sg JOIN games g ON g.id = sg.game_id WHERE sg.squad_id = ? ORDER BY best_price ASC";
		$stmt = $db->prepare($sql);
		$stmt->bind_param("i", $squad_ID);
		$stmt->execute();
		$squad['games'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
		$squad['can_afford'] = array();
		$remaining  = (float)$squad['monthly_budget'] - $squad['spent'];
		foreach ($squad['games'] as $g){
			if ($g['best_price'] !== null && (float)$g['best_price'] <= $remaining){
				$squad['can_afford'][] = $g;
				$remaining -= (float)$g['best_price'];
			}
		}
		return $squad;
	}
	function doWatchAdd($session_key, $game_id, $target_price){
	$db = dbConnection();
	$user_id = sessionUser($db, $session_key);
	if ($user_id === null) {
		return False;
	}
	if ($target_price <= 0) {
		return False;
	}
	$sql = "INSERT INTO price_watch (user_id, game_id, target_price)
	        VALUES (?, ?, ?)
	        ON DUPLICATE KEY UPDATE target_price = VALUES(target_price)";
	try {
		$stmt = $db->prepare($sql);
		$stmt->bind_param("iid", $user_id, $game_id, $target_price);
		$stmt->execute();
		return True;
	}
	catch (Exception $e) {
		return False;
	}
}

	function doWatchList($session_key){
		$db = dbConnection();
		$user_id = sessionUser($db, $session_key);
		if ($user_id === null) {
			return False;
		}
		$sql = "SELECT g.id, g.title, g.image_url, w.target_price, MIN(p.sale_price) AS current_price FROM price_watch w JOIN games g ON g.id = w.game_id LEFT JOIN game_prices p ON p.game_id = g.id WHERE w.user_id		     = ? GROUP BY g.id, g.title, g.image_url, w.target_price ORDER BY g.title";
		try {
			$stmt = $db->prepare($sql);
			$stmt->bind_param("i", $user_id);
			$stmt->execute();
			return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
		}	
		catch (Exception $e) {
			return False;
		}
}

	function doWatchRemove($session_key, $game_id){
		$db = dbConnection();
		$user_id = sessionUser($db, $session_key);
		if ($user_id === null) {
			return False;
		}
		$sql = "DELETE FROM price_watch WHERE user_id = ? AND game_id = ?";
		try {
			$stmt = $db->prepare($sql);
			$stmt->bind_param("ii", $user_id, $game_id);
			$stmt->execute();
			return $stmt->affected_rows > 0;
		}
		catch (Exception $e) {
			return False;
		}

	}
	
	function doPurchase($session_key, $game_id, $store_id, $squad_ID = null){
		$db = dbConnection();
		$user_id = sessionUser($db, $session_key);
		if ($user_id === null){
			return false;
		}
		if ($squad_ID !== null && !isMember($db, $squad_ID, $user_id)){
			return false;
		}
		try {
			$db->begin_transaction(); //everything happens together or not at all
			$stmt = $db->prepare("SELECT 1 FROM user_library WHERE user_id = ? AND game_id = ? AND source = 'purchase'");
			$stmt->bind_param("ii", $user_id, $game_id);
			$stmt->execute();
			if ($stmt->get_result()->fetch_assoc() !== null){
				$db->rollback();
				return false;
			}
			if ($store_id == 0){
				$stmt = $db->prepare("SELECT store_id, sale_price FROM game_price WHERE game_id = ? ORDER BY sale_price ASC LIMIT 1");
				$stmt->bind_param("i", $game_id);
			} else {
				$stmt = $db->prepare("SELECT store_id, sale_price FROM game_prices WHERE game_id = ? and store_id =?");
				$stmt->bind_param("ii", $game_id, $store_id);
			}
			$price = (float)$row['sale_price'];
			$store_id = (int)$row['store_id'];
			//squad budget check
			if ($squad_ID !== null){
				$stmt = $db->prepare("SELECT s.monthly_budget, (SELECT COALESCE(SUM(price_paid), 0) FROM orders WHERE squad_id = s.id AND created_at >= DATE_FORMAT(NOW(), '%Y-%m-01')) AS spent FROM squads s WHERE s.id = ?");
				$stmt->bind_param("i", $squad_ID);
				$stmt->execute();
				$squad = $stmt->get_result()->fetch_assoc();
				if ($squad['monthly_budget'] !== null && (float)$squad['spent'] + $price > (float)$squad['monthly_budget']){
					$db->rollback();
					return false;
				}
			}

			$stmt = $db->prepare("SELECT balance FROM wallets WHERE user_id = ? FOR UPDATE");
			$stmt->bind_param("i", $user_id);
			$stmt->execute();
			$wallet = $stmt->get_result()->fetch_assoc();
			if ($wallet === null || (float)$wallet['balance'] < $price){
				$db->rollback();
				return false; //no wallet or not enough money
			}

			//step 1, take money
			$stmt = $db->prepare("UPDATE wallets SET balance = balance - ? WHERE user_id = ?");
			$stmt->bind_param("di", $price, $user_id);
			$stmt->execute();
			//step 2,  record order happening
			$stmt = $db->prepare("INSERT INTO orders (user_id, game_id, store_id, price_paid) VALUES (?, ?, ?, ?)");
			$stmt->bind_param("iiid", $user_id, $game_id, $store_id, $price);
			$stmt->execute();
			$order_id = $db->insert_id;
			//step 3, add to user library
			$stmt = $db->prepare("INSERT INTO user_library (user_id, game_id, source, order_id) VALUES (?, ?, 'purchase', ?) ON DUPLICATE KEY UPDATE source = 'purchase', order_id = VALUES(order_id)");
			$stmt->bind_param("iii", $user_id, $game_id, $order_id);
			$stmt->execute();
			$db->commit(); //saves all 3 changes
			return true;
		} catch (exception $e){
			$db->rollback(); //undo everything if any step failed
			return false;
		}
	}

	function doGetWallet($session_key){
		$db = dbConnection();
		$user_id = sessionUser($db, $session_key);
		if ($user_id === null){
			return false;
		}
		$stmt = $db->prepare("SELECT w.balance, us.monthly_budget, (SELECT COALESCE(SUM(price_paid), 0) FROM orders WHERE user_id = ? AND created_at >= DATE_FORMAT(NOW(), '%Y-%m-01')) AS spent FROM wallets w LEFT JOIN user_settings us ON us.user_id = w.user_id WHERE w.user_id = ?");
		$stmt->bind_param("ii", $user_id, $user_id);
		$stmt->execute();
		$row = $stmt->get_result()->fetch_assoc();
		return $row === null ? false : $row;
	}
	
	function doSetWallet($session_key, $amount){
		$db = dbConnection();
		$user_id = sessionUser($db, $session_key);
		if ($user_id === null || $amount < 0){
			return false;
		}
		$stmt = $db->prepare("INSERT INTO wallets (user_id, balance) VALUES (?, ?) ON DUPLICATE KEY UPDATE balance = VALUES(balance)");
		$stmt->bind_param("id", $user_id, $amount);
		$stmt->execute();
		return true;
	}
	function doSettings($session_key, $monthly_budget, $email_opt_in){
		$db = dbConnection();
		$user_id = sessionUser($db, $session_key);
		if ($user_id === null){
			return false;
		}
		$monthly_budget = (float)$monthly_budget;
		$email_opt_in = $email_opt_in ? 1 : 0;
		$stmt = $db->prepare("INSERT INTO user_settings (user_id, monthly_budget, email_opt_in) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE monthly_budget = VALUES(monthly_budget), email_opt_in = VALUES(email_opt_in)");
		$stmt->bind_param("idi", $user_id, $monthly_budget, $email_opt_in);
		$stmt->execute();
		return true;
	}
		function doGetTracked(){
		$db = dbConnection();
		$sql = "SELECT DISTINCT g.cheapshark_id FROM price_watch w JOIN games g ON g.id = w.game_id WHERE w.is_active = 1 AND g.cheapshark_id IS NOT NULL";
		try {
			$stmt = $db->prepare($sql);
			$stmt->execute();
			return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
		} catch (Exception $e) {
			return [];
		}
	}

	function doGetMissingDetails(){
		$db = dbConnection();
		$sql = "SELECT id, title FROM games WHERE description IS NULL ORDER BY id LIMIT 20";
		try {
			$stmt = $db->prepare($sql);
			$stmt->execute();
			return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
		} catch (Exception $e) {
			return [];
		}
	}

	function doSaveDetails($game_id, $details){
		$db = dbConnection();
		$game_id = (int)$game_id;
		$rawg_id = (int)($details['rawg_id'] ?? 0);
		$description = $details['description'] ?? '';
		$developer = $details['developer'] ?? null;
		$publisher = $details['publisher'] ?? null;
		$sql = "UPDATE games SET rawgame_id = ?, description = ?, developer = ?, publisher = ? WHERE id = ?";
		try {
			$stmt = $db->prepare($sql);
			$stmt->bind_param("isssi", $rawg_id, $description, $developer, $publisher, $game_id);
			$stmt->execute();
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
		function doSaveDeals($deals){
		if (!is_array($deals)) { return false; }
		$db = dbConnection();
		$saved = 0;
		try {
			$gameStmt = $db->prepare("INSERT INTO games (cheapshark_id, steamapp_id, title, image_url, released, metacritic) VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE steamapp_id = VALUES(steamapp_id), title = VALUES(title), image_url = VALUES(image_url), metacritic = VALUES(metacritic)");
			$findStmt = $db->prepare("SELECT id FROM games WHERE cheapshark_id = ?");
			$priceStmt = $db->prepare("INSERT INTO game_prices (game_id, store_id, deal_id, sale_price, normal_price, deal_rating) VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE deal_id = VALUES(deal_id), sale_price = VALUES(sale_price), normal_price = VALUES(normal_price), deal_rating = VALUES(deal_rating)");
		} catch (Exception $e) {
			return false;
		}
		foreach ($deals as $d) {
			try {
				$cs = (int)($d['gameID'] ?? 0);
				if ($cs <= 0) { continue; }
				$steam = ((int)($d['steamAppID'] ?? 0)) ?: null;
				$title = $d['title'] ?? '';
				$thumb = $d['thumb'] ?? null;
				$released = (!empty($d['releaseDate']) && (int)$d['releaseDate'] > 0) ? date('Y-m-d', (int)$d['releaseDate']) : null;
				$meta = ((int)($d['metacriticScore'] ?? 0)) ?: null;
				$gameStmt->bind_param("iisssi", $cs, $steam, $title, $thumb, $released, $meta);
				$gameStmt->execute();

				$findStmt->bind_param("i", $cs);
				$findStmt->execute();
				$row = $findStmt->get_result()->fetch_assoc();
				if (!$row) { continue; }
				$game_id = (int)$row['id'];

				$store_id = (int)($d['storeID'] ?? 0);
				$deal_id = $d['dealID'] ?? '';
				$sale = (float)($d['salePrice'] ?? 0);
				$normal = (float)($d['normalPrice'] ?? 0);
				$rating = isset($d['dealRating']) ? (float)$d['dealRating'] : null;
				$priceStmt->bind_param("iisddd", $game_id, $store_id, $deal_id, $sale, $normal, $rating);
				$priceStmt->execute();
				$saved++;
			} catch (Exception $e) {
				error_log("doSaveDeals: " . $e->getMessage());
			}
		}
		return $saved;
	}

	function doSavePrices($cheapshark_id, $deals){
		if (!is_array($deals)) { return false; }
		$db = dbConnection();
		$cs = (int)$cheapshark_id;
		$saved = 0;
		try {
			$findStmt = $db->prepare("SELECT id FROM games WHERE cheapshark_id = ?");
			$findStmt->bind_param("i", $cs);
			$findStmt->execute();
			$row = $findStmt->get_result()->fetch_assoc();
			if (!$row) { return false; }
			$game_id = (int)$row['id'];
			$priceStmt = $db->prepare("INSERT INTO game_prices (game_id, store_id, deal_id, sale_price, normal_price) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE deal_id = VALUES(deal_id), sale_price = VALUES(sale_price), normal_price = VALUES(normal_price)");
		} catch (Exception $e) {
			return false;
		}
		foreach ($deals as $d) {
			try {
				$store_id = (int)($d['storeID'] ?? 0);
				$deal_id = $d['dealID'] ?? '';
				$sale = (float)($d['price'] ?? 0);
				$normal = (float)($d['retailPrice'] ?? 0);
				$priceStmt->bind_param("iisdd", $game_id, $store_id, $deal_id, $sale, $normal);
				$priceStmt->execute();
				$saved++;
			} catch (Exception $e) {
				error_log("doSavePrices: " . $e->getMessage());
			}
		}
		return $saved;
	}	
	function doSaveStores($stores){
		if (!is_array($stores)) { return false; }
		$db = dbConnection();
		$saved = 0;
		try {
			$stmt = $db->prepare("INSERT INTO stores (id, name) VALUES (?, ?) ON DUPLICATE KEY UPDATE name = VALUES(name)");
		} catch (Exception $e) {
			return false;
		}
		foreach ($stores as $s) {
			try {
				$id = (int)($s['storeID'] ?? 0);
				$name = $s['storeName'] ?? '';
				if ($id <= 0) { continue; }
				$stmt->bind_param("is", $id, $name);
				$stmt->execute();
				$saved++;
			} catch (Exception $e) {
				error_log("doSaveStores: " . $e->getMessage());
			}
		}
		return $saved;
	}
?>
