const urlParam=new URLSearchParams(window.location.search);
const id= urlParam.get("id");
$(document).ready(function() 
{
loadGame();
})


function loadGame()
{
const getDetails={
game_id: id,
type: "get_game"
};

console.log(getDetails);
//Grabbing data
$.ajax({
        url:"searchData.php", 
        type:"POST", 
        data: getDetails, 
        dataType:"json",
        success: function(data,status,jqXHR) 
        {
    console.log("Data received:",data);
	
	updatePage(data);
	updateTable(data);     
        },
        error: function(jqXHR,textStatus,error){
        console.log("ERROR: ",error);
        console.log("ERROR: ",textStatus);
        console.log("ERROR: ",jqXHR);

        }


});





}

//Function to update the page with server data before load.
function updatePage(data){
var img=$("#gameImg");
var dev=$("#gameDev");
var pub= $("#gamePub");
var released=$("#gameReleased");
var rating=$("#gameRating");

img.attr("src", data.image_url);
dev.append(data.developer);
pub.append(data.publisher);
released.append(data.released);
rating.append(data.metacritic);



}
//Table function. Will fix.
function updateTable(data){
	data.prices.forEach(function (p) {
	var row = $("<tr>");
		row.append($("<td>").text(p.store));
		row.append($("<td>").text("$" + p.sale_price));
		row.append($("<td>").text("$" + p.normal_price));
		var buy = $("<a>")
			.attr("href", "https://www.cheapshark.com/redirect?dealID=" + p.deal_id)
			.text("Buy");
		row.append($("<td>").append(buy));
		$("#pricesTable").append(row);
	});

}


//Add to library function
$("#addGame").on("click",function(e)
{	e.preventDefault();
	const getDetails={
	game_id: id,
	type: "library_add"
	};


$.ajax({
        url:"searchData.php", 
        type:"POST", 
        data: getDetails, 
        dataType:"json",
        success: function(success,status,jqXHR) 
        {
    console.log("Data received:",success);
	if(success){
	alert("Successfully added to library!")
	}
	else{
	alert("Something went wrong. Try again!")
	}

        },
        error: function(jqXHR,textStatus,error){
        console.log("ERROR: ",error);
        console.log("ERROR: ",textStatus);
        console.log("ERROR: ",jqXHR);

        }


});


});
