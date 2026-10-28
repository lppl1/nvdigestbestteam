<?php
require_once("../auth.php")
?>

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
<div id="banner"></div>
<h1 style="text-align:center">Market</h1>
<br>
<form name="gameSearch" id="gameSearch"  >
Search for games:
<input type="text" placeholder="Search" name="term" id="term">
<br>
<input type="submit">
</form>

<br>
<br>
<br>
<table id=gamesTable>
	<tr class="header">
		
		<td>
			<h3>Game</h3>
		</td>
		<td>
			<h3>Metacritic</h3>
		</td>
	</tr>
</table>
<script src="search.js" defer></script>
<script src="../loadBanner.js" defer></script>
