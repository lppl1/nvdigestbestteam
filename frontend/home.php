<?php require_once("auth.php"); ?>
<!DOCTYPE html>
<html>
<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body>
	<button id="logoutButton">Log Out</button>
	<h1>Welcome to NVDigest</h1>
	<p>One stop for security:)</p>

<script>
	$("#logoutButton").on("click", function () {
	$.post("login.php", { type: "logout" }).always(function () {
		window.location.replace("login.html");
		});
	});
</script>
</body>
</html>
