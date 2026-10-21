
$("#gameSearch").on("submit",function(e)
{
e.preventDefault();
var searchData=$(this).serialize();

//Send form data without page reload
$.ajax({
	url:"searchData.php", 
	type:"POST", 
	data: {
	type: "search_games",
	term: document.getElementById("term").value
	}, 
	dataType:"text",
	success: function(data,status,jqXHR) 
	{
    console.log("Data received:",data);
  
	
	},
	error: function(jqXHR,textStatus,error){
	console.log("ERROR: ",error);
	console.log("ERROR: ",textStatus);
	console.log("ERROR: ",jqXHR);
	}


});
})
