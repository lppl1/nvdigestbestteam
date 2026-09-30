<?php

	function dbConnection(){
		$mysqli = new mysqli("localhost","bryan","password","personaldb");

		// Check connection
		if ($mysqli -> connect_errno) {
  			echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
			exit();
		}
		return $mysqli;
	}
	echo "Connected Successfully";
?>
