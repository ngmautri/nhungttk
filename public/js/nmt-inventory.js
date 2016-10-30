function showWHDialog(wh) {
	// $( "#dialog" ).text(t);
	$("#dialog").dialog({
		width : 750,
		height : 550,
		title : "Select Warehouses: " + wh,
		modal : true,
		dialogClass : 'dialogClass'
	});
	
	$('#search_term').keypress(function(event){

	    if (event.keyCode === 10 || event.keyCode === 13) 
	        event.preventDefault();
	    	//showDialog();
	  });
	
	
	loadWHList(wh);
}

/**
 * function on calendar dialog
 */
function loadWHList(wh) {
	
	$("#search_result").html('Loading....');

	$.get("/inventory/warehouse/select-list", {
		wh : wh,
	}, function(data, status) {
		$("#search_result").html(data);
	});
}

function selectWH(wh,wh_name,wh_id) {
	
	if(wh=="SOURCE_WH"){
		$("#source_wh_id").val(wh_id);
		$("#source_wh_name").val(wh_name);
	}else if(wh=="TARGET_WH"){
		$("#target_wh_id").val(wh_id);
		$("#target_wh_name").val(wh_name);
		}
	
	$("#dialog").dialog("close");
	$("#search_result").html('');
}

/**
 * function on calendar dialog
 */
function showBalance(article_id, article_name ) {
	
	$("#dialog1").dialog({
		width : 550,
		height : 350,
		title : "Check Balance: " + article_name,
		modal : true,
		dialogClass : 'dialogClass'
	});
	
	$("#dialog1").html('Loading....');
	
	$.get("/inventory/transaction/balance", {
		article_id : article_id,
	}, function(data, status) {
		$("#dialog1").html(data);
	});
}
