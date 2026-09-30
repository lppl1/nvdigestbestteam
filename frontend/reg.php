<?php
require_once('rabbitmq/rabbitmqphp_example/testrabbitmq.ini')
$username=$_POST["reguser"]
$password=$_POST["regpass"]

doRegister($username, $password)

?>
