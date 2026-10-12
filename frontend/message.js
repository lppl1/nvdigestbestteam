
$("#info").on("submit",function(e)
{
e.preventDefault();
var loginData=$(this).serialize();

//Send form data without page reload
$.ajax({
	url:"login.php", 
	type:"POST", 
	data: loginData, 
	dataType:"json",
	success: function(data,status,jqXHR) 
	{
    //console.log("Data received:",data);
    testCredentials(data);

	
	},
	error: function(jqXHR,textStatus,error){
	console.log("ERROR: ",error);
	console.log("ERROR: ",textStatus);
	console.log("ERROR: ",jqXHR);
	}


});


});

//ERROR POPUP RELATED
//test button. Will Delete

$("#closePopup").on("click",function()
	{
	showPopup();
	}

);
//Show warning
function showPopup()
{
$(".popup").toggle();
}
//END OF ERROR POPUPS

//Success
function testCredentials(data){
	if(data.type=="register")
	{
	if(data.validate=="true"){
	window.location.replace("login.html");
	}
	else if(data.validate=="false"){
	showPopup();
	}
	
	}
	
	if(data.type=="login")
	{
		if(data.validate!="false"){
	window.location.replace("home.php");
	document.cookie = "session_key="+data.validate;
	}
	else if(data.validate=="false"){
	showPopup();
	}
	
	}
}
