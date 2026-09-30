<?php
require_once('nvdigestbestteam/database/dbListener.php')
$username=$_POST["reguser"]
$password=$_POST["regpass"]

doRegister($username, $password)

?>
