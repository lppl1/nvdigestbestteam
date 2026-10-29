<<<<<<< HEAD
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
=======
<?php
require_once("auth.php")
?>


<script src="auth.js" defer></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<h1 id=title>Welcome to our club!</h1>
<div id=welcome>Welcome!</div>

<p>
  <a href="market/market.php">Market</a> |
  <a href="library/library.php">My Library</a>
  <a href="forum/forum.php">Discussion Forum</a> |
  <a href="profile.php">My Profile</a>

</p>


<form action="logout.php" method="post">
  <button type="submit">Log out</button>
</form>

>>>>>>> be59ee9dfc8380a038049c6c0b58266fd8455569
