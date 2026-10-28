//This file deals with individual threads

const urlParam=new URLSearchParams(window.location.search);
const id= urlParam.get("id");

$(document).ready(function() 
{



})



//Create a new reply
$("#reply").on("submit",function(e){
e.preventDefault();
console.log("HEY!!!");
addReply={
type:"reply",
thread_id:id,
body:$("#body").val()
};
$.ajax({
        url:"../searchData.php", 
        type:"POST", 
        data: addReply, 
        dataType:"json",
        success: function(data,status,jqXHR) 
        {
    console.log("Data received:",data);
	if(data){alert("Successfully Sent!");
	showReplies();
}
        else{"Something's wrong. try again!"} 
	},
        error: function(jqXHR,textStatus,error){
        console.log("ERROR: ",error);
        console.log("ERROR: ",textStatus);
        console.log("ERROR: ",jqXHR);

        }


});



});

function showReplies(data){
//Request for reply data
var getPosts={
type:"thread_posts",
thread_id:id
};

$.ajax({
        url:"../searchData.php", 
        type:"POST", 
        data: getPosts, 
        dataType:"json",
        success: function(data,status,jqXHR) 
        {
    console.log("Data received:",data);
	showReplies(data);
         },
        error: function(jqXHR,textStatus,error){
        console.log("ERROR: ",error);
        console.log("ERROR: ",textStatus);
        console.log("ERROR: ",jqXHR);

        }


});


var table = $("#all_posts tbody");
table.empty();
data.forEach(function(data){
var row=$('<tr></tr>');
var user=data.username;
var date=data.created;
var post=data.body;
row.append($("<td></td>").append(user).append("<br><p class='date'>"+date+"</p>"));
row.append($("<td></td>").append(post));
table.append(row);

});
 







data.forEach(function(reply){
console.log(reply);

});





}

