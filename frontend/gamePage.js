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
$("#gameTitle").text(data.title);


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

//Submit Review
$("#review").on("submit",function(e){
e.preventDefault();

const reviewData=
{
type: "submit_review",
rating:$("#rating").val(),
game_id:id,
body:$("#body").val(),
}

$.ajax({
        url:"searchData.php", 
        type:"POST", 
        data: reviewData, 
        dataType:"json",
        success: function(success,status,jqXHR) 
        {
    console.log("Data received:",success);
        if(success){
        alert("Successfully submitted review!")
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


$("#showReviews").on("click",function(){
const getReviews={
type: "show_reviews", 
game_id: id
};
$.ajax({
        url:"searchData.php", 
        type:"POST", 
        data: getReviews, 
        dataType:"json",
        success: function(reviews,status,jqXHR) 
        {
	console.log("Data received:",reviews);
  	viewReviews(reviews);
        },
        error: function(jqXHR,textStatus,error){
        console.log("ERROR: ",error);
        console.log("ERROR: ",textStatus);
        console.log("ERROR: ",jqXHR);

        }


});




});

function viewReviews(reviews){
$("#reviewList").empty();
reviews.forEach(function(r){
	$("#reviewList").append("<p><b>" + r.username + "</b> (" + r.rating + "/5): " + r.body + "</p>");
});

}
$("#price_watch").on("submit", function (e) {
	e.preventDefault();
	$.ajax({
		url: "searchData.php",
		type: "POST",
		data: {
			type: "watch_add",
			game_id: id,
			target_price: $("#targetPrice").val()
		},
		dataType: "json",
		success: function (ok) {
			if (ok === true) {
				alert("Price watch saved!");
			} else {
				alert("Could not save. Are you logged in?");
			}
		}
	});
});
$(document).on("click", ".buyGame", function(){
    $.ajax({
        url: "searchData.php",
        type: "POST",
        data: {type: "purchase", game_id: id, store_id: $(this).data("store")},
        dataType: "json",
        success: function(ok){
            alert(ok ? "Purchased! Added to your library." : "Not enough money, or you already own it.");
        }

