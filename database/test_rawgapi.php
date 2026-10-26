<?php
require_once("dbConnection.php");
$db = dbConnection();

$key = "5646c9cd207d466dba2ac1994e39f74f" //rawg api key

//games that haven't been matched with rawg 
	$games = $db->query("SELECT id, title FROM games WHERE rawgame_id IS NULL LIMIT 10")->fetch_all(MYSQLI_ASSOC);

foreach ($games as $game) {
	//search rawg by title to find rawg id
	$url = "https://api.rawg.io/api/games?key=" .key . "&search=" . urlencode($game['title']) . "&search_precise=true&page_size=1";
	$ch = curl_init($url); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, "it490");
	$search = json_decode(curl_exec($ch), true);
	if (empty($search['results'])){
		continue; //no match with rawg, skip game
	}
	$rawg_ID = $search['results'][0]['id']
	//get full game details
	$ch = curl_init("https://api.rawg.io/api/games/" . $rawg_ID . "?key=" . $key);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, "it490");
	$details = json_decode(curl_exec($ch), true);

		
}
?>

