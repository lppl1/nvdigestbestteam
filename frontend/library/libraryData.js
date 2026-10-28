$(document).ready(function () {
    loadLibrary();
});

function loadLibrary() {
    $.ajax({
        url: "../searchData.php",
        type: "POST",              // use the same GET/POST as search.js
        data: { type: "library_list" },
        dataType: "json",
        success: function (games) {
            var body = $("#libraryTable tbody");
            body.empty();

            if (games === false) {
                $("#libraryMsg").text("Please log in to see your library.");
                return;
            }
            if (games.length === 0) {
                $("#libraryMsg").text("Your library is empty.");
                return;
            }
            $("#libraryMsg").text("");

            games.forEach(function (g) {
                var row = $("<tr>");

                var img = $("<img>").attr("src", g.image_url).attr("width", 80);
                row.append($("<td>").append(img));

                var link = $("<a>").attr("href", "games.php?id=" + g.id).text(g.title);
                row.append($("<td>").append(link));

                row.append($("<td>").text(g.status));

                var btn = $("<button>").text("Remove").on("click", function () {
                    removeGame(g.id);
                });
                row.append($("<td>").append(btn));

                body.append(row);
            });
        }
    });
}

function removeGame(gameId) {
    $.ajax({
        url: "../searchData.php",
        type: "POST",
        data: { type: "library_remove", game_id: gameId },
        dataType: "json",
        success: function () {
            loadLibrary();   // reload the list after removing
        }
    });
}
