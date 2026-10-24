$(document).ready(function() 
{
loadGame();
})


function loadGame()
{
const urlParam=new URLSearchParams(window.location.search);
const id= urlParam.get("id");
const getDetails={
id: id,
type: "get_game"
};

console.log(getDetails);
//Grabbing data
$.ajax({
        url:"getGame.php", 
        type:"GET", 
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
var img=document.getElementById("gameImg");
var dev=document.getElementById("gameDev");
var pub= document.getElementById("gamePub");
var released=document.getElementById("gameReleased");
var rating=document.getElementById("gameRating");

img.src=data.image_url;
dev.innerHTML+=data.developer;
pub.innerHTML+=data.publisher;
released.innerHTML+=data.release_date;
rating.innerHTML+=data.metacritic;



}

function updateTable(data){

}

