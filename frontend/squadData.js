function squadRequest(data, callback){
	$.ajax({
		url: "searchdata.php",
		type: "POST",
		data: data,
		dataType: "json",
		success: callback,
		error: function(jqXHR, textStatus, error){ console.log("ERROR:", error, textStatus); }
	});
}

function showSquad(squadID){
	squadRequest({type: "squad_details", squad_id: squadID}, function(s){
		if (!s) { alert("You are not in this squad!"); return; }
		//important info
		$("#squadTitle").text(s name);
		$("#joinCodeText").text("Join code: " +  s.join_code);
		$("#budgetText").text("Spent $" + s.spent + " of $" + (s.monthly_budget  || "no budget"));
		//members list
		$("memberList").empty();
		s.members.forEach(function(m){
			$("memberList").append("<li>" + m.username + "</li>");
		});
		//nominated games
		
		//games within the budget
}
