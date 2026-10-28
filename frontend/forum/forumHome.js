$(document).ready(function() 
{
dispDiscussions();
})

//Function is meant to request data from the from the server
function dispDiscussions(){
const getThreads={
type:"thread_list",

};

$.ajax({
        url:"../searchData.php", 
        type:"POST", 
        data: getThreads, 
        dataType:"json",
        success: function(threadData,status,jqXHR) 
        {
    	console.log("Data received:",threadData);
	updateBoard(threadData)

 
        },
        error: function(jqXHR,textStatus,error){
        console.log("ERROR: ",error);
        console.log("ERROR: ",textStatus);
        console.log("ERROR: ",jqXHR);

        }


});


}

//This is separate so I can test functionality.
function updateBoard(threadData){
//remove previous data
var table=$("#Threads tbody");
table.empty();
threadData.forEach(function(thread){
var row=$('<tr></tr>');
var link = $("<a></a>").attr("href", "thread.php?id=" + encodeURIComponent(thread.id)).text(thread.title);
row.append($("<td></td>").append(link));
table.append(row);
	
});

}


//ADD THREAD
$("#createThread").on("submit",function(e){
e.preventDefault();
threadData={
type:"create_thread",
game_id:$("#game_id").val(),
title:$("#title").val(),
body:$("#body").val()
};
$.ajax({
        url:"../searchData.php", 
        type:"POST", 
        data: threadData, 
        dataType:"json",
        success: function(threadData,status,jqXHR) 
        {
        console.log("Data received:",threadData);
	dispDiscussions();  
 
        },
        error: function(jqXHR,textStatus,error){
        console.log("ERROR: ",error);
        console.log("ERROR: ",textStatus);
        console.log("ERROR: ",jqXHR);

        }


});


});
