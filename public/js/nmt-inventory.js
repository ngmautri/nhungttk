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


function autocompleteItem(query_div, item_id,  item_url, item_detail){
	
	var query_div1 =  '#' + query_div;
	var item_id1 =  '#' + item_id;
	var item_url1 =  '#' + item_url;
	var item_detail1 =  '#' + item_detail;
	
	alert(query_div1);

	 $( query_div1 ).autocomplete({
              source: "/inventory/item-search/auto-complete",
              minLength: 2,
              select: function( event, ui ) {

                // set pr row empty
                $( item_id1 ).val(ui.item.id);

                $(item_url1).text('/inventory/item/show1?tab_idx=7&entity_id='+ui.item.id+'&token='+ui.item.token);
         	 	 $('#global-notice').show();
            	 $('#global-notice').html('"' + ui.item.value + '" selected');
            	 $('#global-notice').fadeOut(5000);
            	//$("#jquery_dialog").dialog("close");
         	  	 $(item_detail1).show();
             }

      })
      .autocomplete( "instance" )._renderItem = function( ul, item ) {
          	  var serial_no = "";
          	  if(item.item_serial!=""){
          		  serial_no = " : " + item.item_serial;
          	  }

          	  var img = '<img class="img-rounded" width="60" height="60" src="'+item.item_thumbnail+'"/>';
                    

               return $( "<li>" )
                 .append( "<div style='padding-bottom: 5px; border-bottom: thin solid gray;'><table><tr><td>"+img+"</td><td><span style='padding-left:5px; font-size: 9.5pt;font-weight: bold;'>" + item.value + "</span><br><span style='padding-left:5px; color:gray;font-size: 9pt;'>" + item.item_sku + serial_no +"<span></td></tr></table></div>" )
                 .appendTo( ul );
     };
      
}
