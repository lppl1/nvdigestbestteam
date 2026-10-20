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
	$store_name = $store['store_name']; // store name
	$stmt = $db->prepare( //prepares insert in stores table and if store exists, just updates name
		"INSERT INTO stores (id, name) VALUES (?, ?)
		ON DUPLICATE KEY UPDATE name = VALUES(name)"
	):
	$stmt->bind_param("is", $storeID, $store_name);
	$stmt->execute(); //runs it
}
//deal fetch for games and prices for multiple pages
for ($page = 0; $page < 5; $page++){

	$ch = curl_init("https://www.cheapshark.com/api/1.0/deals?pageSize=10"); //This initializes the connection to the url
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //Returns result as string
	curl_setopt($ch, CURLOPT_USERAGENT, "it490"); //Tells server who is requesting data
	$response = curl_exec($ch);

	$deals = json_decode($response, true);

	foreach ($deals as $deal) {
    		$id    = (int)$deal['gameID'];
		$title = $deal['title'];
		$steamAppID = (int)$deal['steamapp_id']; //steam id for game
		if ($steamAppID == 0) 
			$steamAPPID = null; //null better than 0 for unknown game id

		$metacritic = (int)$deal['metacriticScore']; // metacritic score
		if ($metacritic == 0)
			$metacritic = null; //same thing here

		$imageURL = $deal['thumb']; //link to game's thumbnail image

		$released = null;
		if ($deal['releaseDate'] > 0){ //defaults to null if no release date and only converts if real date given
			$released = date('Y-m-d', $deal['releaseDate']); //converts unix timestamp to SQL timestamp
		}

    		$stmt = $db->prepare(
        		"INSERT INTO games (cheapshark_id, title) VALUES (?, ?)
	 		ON DUPLICATE KEY UPDATE title = VALUES(title), updated_at = NOW()"
    		);
    		$stmt->bind_param("is", $id, $title);
    		$stmt->execute();

    		echo "saved: " . $title . "\n";
	}
}
?>
