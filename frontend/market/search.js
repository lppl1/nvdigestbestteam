$("#gameSearch").on("submit", function (e) {
    e.preventDefault();

    $.ajax({
        url: "searchData.php",
        type: "POST",
        data: {
            type: "search_games",
            term: $("#term").val()
        },
        dataType: "json",
        success: function (games) {
            var $table = $("#gamesTable");
            $table.find("tr.result").remove(); 

            if (!Array.isArray(games) || games.length === 0) {
                $table.append('<tr class="result"><td colspan="2">No games found</td></tr>');
                return;
            }

            games.forEach(function (g) {
                var $row = $('<tr class="result"></tr>');
                var $link = $("<a></a>")
                    .attr("href", "../games.html?id=" + encodeURIComponent(g.id))
                    .text(g.title);

                $row.append($("<td></td>").append($link));
                $row.append($("<td></td>").text(g.metacritic === null ? "N/A" : g.metacritic));
                $table.append($row);
            });
        },
        error: function (jqXHR, textStatus, error) {
            console.log("ERROR: ", error);
            console.log("ERROR: ", textStatus);
            console.log("ERROR: ", jqXHR);
        }
    });
});
