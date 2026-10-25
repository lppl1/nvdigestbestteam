$(document).ready(function () {
    loadLibrary();
});

<<<<<<< HEAD
$(document).ready(function(){

//Ajax call to request library games
$.ajax({
        url:"searchData.php", 
        type:"POST", 
        data: {
	type :"library_view"
	}, 
        dataType:"text",
        success: function(library,status,jqXHR) 
        {
    console.log("Data received:",library);
 	//showLibrary(library);

        },
        error: function(jqXHR,textStatus,error){
        console.log("ERROR: ",error);
        console.log("ERROR: ",textStatus);
        console.log("ERROR: ",jqXHR);
        }


});

})





//Will send a POST request to update the status in the DB
$('select').on('change',function(){

})

function showLibrary(library){
console.log(library);
library.forEach(function(game,index){
$("#library").append("<tr><td>"+game.title+"</td> </tr>");
=======
function loadLibrary() {
    $.ajax({
        url: "../market/searchData.php",
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

                var link = $("<a>").attr("href", "../games.html?id=" + g.id).text(g.title);
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
        url: "../market/searchData.php",
        type: "POST",
        data: { type: "library_remove", game_id: gameId },
        dataType: "json",
        success: function () {
            loadLibrary();   // reload the list after removing
        }
    });
>>>>>>> 80a285f88fca42c419cac193a624a5f928846437
}

);


}

