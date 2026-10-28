function loadWatches() {
	$.ajax({
		url: "searchData.php",
		type: "POST",
		data: { type: "watch_list" },
		dataType: "json",
		success: function (rows) {
			$("#watchTable tr:gt(0)").remove();

			if (rows === false) { $("#watchMsg").text("Please log in."); return; }
			if (rows.length === 0) { $("#watchMsg").text("No games on your watch list."); return; }
			$("#watchMsg").text("");

			rows.forEach(function (w) {
				var row = $("<tr>");

				var link = $("<a>").attr("href", "games.html?id=" + w.id).text(w.title);
				row.append($("<td>").append(link));

				row.append($("<td>").text("$" + w.target_price));
				row.append($("<td>").text(w.current_price === null ? "N/A" : "$" + w.current_price));

				var btn = $("<button>").text("Remove").on("click", function () {
					$.ajax({
						url: "searchData.php",
						type: "POST",
						data: { type: "watch_remove", game_id: w.id },
						dataType: "json",
						success: loadWatches
					});
				});
				row.append($("<td>").append(btn));

				$("#watchTable").append(row);
			});
		}
	});
}

$(document).ready(loadWatches);
