const urlParam=new URLSearchParams(window.location.search);
const id= urlParam.get("id");
$(document).ready(function() 
{
loadGame();
})


function loadGame()
{
const getDetails={
id: id,
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

img.src=data.image_url;
dev.innerHTML+=data.developer;
pub.innerHTML+=data.publisher;
released.innerHTML+=data.release_date;
rating.innerHTML+=data.metacritic;



}
//Table function. Will fix.
function updateTable(data){

}

//Add to library function
$("#addGame").on("click",function(e)
{	e.preventDefault();
	const getDetails={
	id: id,
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
