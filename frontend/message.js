$("#info").on("submit",function(e)
{
e.preventDefault();

	$.post("login.php", $(this).serialize(), function(data) {
		if (data.type==="register" && data.validate===true) {
			location.replace("login.html");
		}
		else if (data.type==="login" && data.validate.length===64) {
			document.cookie="session_key=" + data.validate + "; path=/";
			location.replace("home.php");
		}
		else{
			$("#message").text("Failed Login! Try Login Again!");
		}
	}, "json").fail(function () {
		$("#message").text("Server error.");
	});
});
