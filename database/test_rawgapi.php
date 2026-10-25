<?php
require_once("dbConnection.php");
$db = dbConnection();

$key = "5646c9cd207d466dba2ac1994e39f74f" //rawg api key

//games that haven't been matched with rawg 
	$games = $db->query("SELECT id, title FROM games WHERE rawgame_id IS NULL LIMIT 10")->fetch_all(MYSQLI_ASSOC);

foreach ($games as $game) {
	//search rawg by title to find rawg id
}
?>

