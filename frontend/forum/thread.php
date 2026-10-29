<!DOCTYPE HTML>
<html>

<?php
require_once("../auth.php")
?>


	<head><title>Forum Threads</title>

	<style>
	td{
	border: 2px solid black;
	}
	table{
	border-collapse:collapse;
	margin-left:auto;
	margin-right:auto;
	width:50%;
	}
	</style>
	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>





	</head>
	<body>
		<a href="forum.php">Back to Forums</a>
		
			<!--posts from forum_posts will be within h1 element-->

		<h3>Reply?</h3>
		<form method="post" id="reply">
			<textarea name="body" id="body" placeholder"Message"></textarea>
			<br>
			<input type="submit" value="reply">
		</form>
	
		<br>
		<br>
		<table id="all_posts">
		<tbody>
		<tr id="Heading"><td><h1>User</h1></td><td><h1>Post</h1></td></tr>
		</tbody>
		</table>	


	</body>
<script src="viewThreads.js"></script>

</html>

