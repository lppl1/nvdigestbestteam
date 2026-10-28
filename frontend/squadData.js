function squadRequest(data, callback){
	$.ajax({
		url: "searchData.php",
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
		$("#squadTitle").text(s.name);
		$("#joinCodeText").text("Join code: " +  s.join_code);
		$("#budgetText").text("Spent $" + s.spent + " of $" + (s.monthly_budget  || "no budget"));
		//members list
		$("#memberList").empty();
		s.members.forEach(function(m){
			$("memberList").append("<li>" + m.username + "</li>");
		});
		//nominated games
		$("#gameTable").html("<tr><th>Game</th><th>Best Price</th></tr>");
		s.games.forEach(function(k){
			$("#gameTable").append("<tr><td>" + k.title + "</td><td>$" + (k.best_price || "-") + "</td></tr>");
		});
		//games within the budget
		$("#affordList").empty();
		s.can_afford.forEach(function(k){
			$("#affordList").append("<li>" + k.title + " - $" + k.best_price + "</li>");
		});
		//make sure section isn't hidden
		$("#squadDetails").show();
});
}
$(document).ready(function(){
	//list all of my squads
	squadRequest({type: "my_squads"}, function(squads){
		(squads || []).forEach(function(s){
			$("#squadList").append('<li><a href="#" class="openSquad" data-id="' + s.id + '">' + s.name + '</a></li>');
		});
	});
	$(document).on("click", ".openSquad", function(e){
		e.preventDefault(); //stops link from jumping to top of page
		showSquad($(this).data("id")); //loads squad that was selected
	});
	//creating a squad
	$("#createSquad").on("click", function(){
		squadRequest({type: "create_squad", name: $("#squadName").val(), budget: $("#squadBudget").val()}, function(code){
			if (code){
				alert("Squad created! Share this join code: " + code);
				location.reload(); //reloads to show new squad in list 
			} else {
				alert("Error! Failed to create squad.");
			}
		});
	});
	//joining a squad
	$("#joinSquad").on("click", function(){
		squadRequest({type: "join_squad", code: $("#joinCode").val()}, function(id){
			if (id){
				location.reload(); //reloads list to show joined squad
			} else {
				alert("Error! Wrong code OR already a member.");
			}
		});
	});
});
squadRequest({type: "get_wallet"}, function(balance){
        $("#walletText").text("Wallet: $" + (balance || "0.00"));
    });
