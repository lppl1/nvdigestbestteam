<?php
require_once("dbConnection.php");
$db = dbConnection();

$ch = curl_init("https://www.cheapshark.com/api/1.0/deals?pageSize=10"); //This initializes the connection to the url
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //Returns result as string
curl_setopt($ch, CURLOPT_USERAGENT, "it490"); //Tells server who is requesting data
$response = curl_exec($ch);

$deals = json_decode($response, true);

foreach ($deals as $deal) {
    $id    = (int)$deal['gameID'];
    $title = $deal['title'];

    $stmt = $db->prepare(
        "INSERT INTO games (cheapshark_id, title) VALUES (?, ?)
         ON DUPLICATE KEY UPDATE title = VALUES(title)"
    );
    $stmt->bind_param("is", $id, $title);
    $stmt->execute();

    echo "saved: " . $title . "\n";
}
?>
