<?php
require_once("dbConnection.php");
$db = dbConnection();
require_once("rawgKey.php");

//games that haven't been matched with rawg 
	$games = $db->query("SELECT id, title FROM games WHERE rawgame_id IS NULL LIMIT 30")->fetch_all(MYSQLI_ASSOC);

foreach ($games as $game) {
	//search rawg by title to find rawg id
	$url = "https://api.rawg.io/api/games?key=" .$key . "&search=" . urlencode($game['title']) . "&search_precise=true&page_size=1";
	$ch = curl_init($url); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, "it490");
	$search = json_decode(curl_exec($ch), true);
	if (empty($search['results'])){
		continue; //no match with rawg, skip game
	}
	$rawg_ID = $search['results'][0]['id'];
	//get full game details
	$ch = curl_init("https://api.rawg.io/api/games/" . $rawg_ID . "?key=" . $key);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, "it490");
	$details = json_decode(curl_exec($ch), true);
	
	$description = $details['description_raw'] ?? null;
	$developer = $details['developers'][0]['name'] ?? null;
	$publisher = $details['publishers'][0]['name'] ?? null;

	$stmt = $db->prepare("UPDATE games SET rawgame_id = ?, description = ?, developer = ?, publisher = ? WHERE id = ?");
	$stmt->bind_param("isssi", $rawg_ID, $description, $developer, $publisher, $game['id']);
	try {
		$stmt->execute();
	} catch (exception $e){
		continue; //another edition uses the same rawg id
	}
	
	foreach ($details['genres'] as $genre) {
		$stmt =$db->prepare("INSERT INTO genres (rawggenre_id, name) VALUES (?, ?) ON DUPLICATE KEY UPDATE name = VALUES(name)");
		$stmt->bind_param("is", $genre['id'], $genre['name']);
		$stmt->execute();
		$stmt = $db->prepare("SELECT id FROM genres WHERE rawggenre_id = ?"); //adds our own id
		$stmt->bind_param("i", $genre['id']);
		$stmt->execute();
		$genre_ID = $stmt->get_result()->fetch_assoc()['id'];

		$stmt = $db->prepare("INSERT IGNORE INTO game_genres (game_id, genre_id) VALUES (?, ?)");
		$stmt->bind_param("ii", $game['id'], $genre_ID);
		$stmt->execute();
	}
	echo "updated: " . $game['title'] . "/n";
	sleep(1); //anti-api-spam method	
}
?>

