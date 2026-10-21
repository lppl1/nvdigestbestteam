<?php
require_once("dbConnection.php");
$db = dbConnection();

//saving stores first before pulling prices
$ch = curl_init("https://www.cheapshark.com/api/1.0/stores"); //opens a connection to cheapshark store list
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // returns result as string
curl_setopt($ch, CURLOPT_USERAGENT, "it490"); //tells server who's requesting the data
$response = curl_exec($ch); 
$stores = json_decode($response, true); //turns json into php array

foreach ($stores as $store){
	$store_id = (int)$store['storeID']; //cheapshark store id
	$store_name = $store['storeName']; // store name
	$stmt = $db->prepare( //prepares insert in stores table and if store exists, just updates name
		"INSERT INTO stores (id, name) VALUES (?, ?)
		ON DUPLICATE KEY UPDATE name = VALUES(name)"
	);
	$stmt->bind_param("is", $store_id, $store_name);
	$stmt->execute(); //runs it
}
//deal fetch for games and prices for multiple pages
for ($page = 0; $page < 5; $page++){

	$ch = curl_init("https://www.cheapshark.com/api/1.0/deals?pageSize=10&pageNumber=" . $page); //This initializes the connection to the url
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //Returns result as string
	curl_setopt($ch, CURLOPT_USERAGENT, "it490"); //Tells server who is requesting data
	$response = curl_exec($ch);

	$deals = json_decode($response, true);

	foreach ($deals as $deal) {
    		$id    = (int)$deal['gameID'];
		$title = $deal['title'];
		$steamAppID = (int)$deal['steamAppID']; //steam id for game
		if ($steamAppID == 0) 
			$steamAppID = null; //null better than 0 for unknown game id

		$metacritic = (int)$deal['metacriticScore']; // metacritic score
		if ($metacritic == 0)
			$metacritic = null; //same thing here

		$image_URL = $deal['thumb']; //link to game's thumbnail image

		$released = null;
		if ($deal['releaseDate'] > 0){ //defaults to null if no release date and only converts if real date given
			$released = date('Y-m-d', $deal['releaseDate']); //converts unix timestamp to SQL timestamp
		}

    		$stmt = $db->prepare(
        		"INSERT INTO games (cheapshark_id, title, steamapp_id, metacritic, image_url, released) VALUES (?, ?, ?, ?, ?, ?) 
	 		ON DUPLICATE KEY UPDATE title = VALUES(title), steamapp_id = VALUES(steamapp_id), metacritic = VALUES(metacritic), image_url = VALUES(image_url), released = VALUES(released), updated_at = NOW()"
    		); //updated this section
    		$stmt->bind_param("isiiss", $id, $title, $steamAppID, $metacritic, $image_URL, $released);
		$stmt->execute();

		//adding this to account for saving prices
		$stmt = $db->prepare(
			"SELECT id FROM games WHERE cheapshark_id =?"
		);
		$stmt->bind_param("i", $id);
		$stmt->execute();
		$game_id = $stmt ->get_result()->fetch_assoc()['id']; //our native game id
		$store_id = (int)$deal['storeID']; //which store deal is from
		$sale_price = $deal['salePrice']; //discounted price
		$normal_price = $deal['normalPrice'];
		$deal_id = $deal['dealID'];
		$deal_rating = $deal['dealRating']; //all this for giving us price, deal, cheapshark deal id, then deal score
	//new prepare statement for game_prices table
		$stmt = $db->prepare(
			"INSERT INTO game_prices (game_id, store_id, sale_price, normal_price, deal_id, deal_rating)
			VALUES (?, ?, ?, ?, ?, ?)
			ON DUPLICATE KEY UPDATE sale_price = VALUES(sale_price), normal_price = VALUES(normal_price), deal_id = VALUES(deal_id), 
			deal_rating = VALUES(deal_rating)"
		);
		$stmt->bind_param("iiddsd", $game_id, $store_id, $sale_price, $normal_price, $deal_id, $deal_rating);
		$stmt->execute();

    		echo "saved: " . $title . "\n";
	}
	sleep(1);
}
?>
