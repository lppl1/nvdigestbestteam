
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
}

);


}

