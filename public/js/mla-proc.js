
function OrderHistoryDialog(sp_id,article_id) {
	// $( "#dialog" ).text(t);
	$("#dialog").dialog({
		width : 950,
		height : 550,
		title : "Order History",
		modal : true,
		dialogClass : 'dialogClass'
	});
	
	$('#search_term').keypress(function(event){

	    if (event.keyCode === 10 || event.keyCode === 13) 
	        event.preventDefault();
	    	//showDialog();
	  });
	
	
	loadHistory(sp_id,article_id);
}
/**
 * function on calendar dialog
 */
function loadHistory(sp_id,article_id) {
	
	$("#search_result").html('Loading....');
	
	$.ajax({
				url : "/procurement/pr/history?out=json&spartpart_id"+sp_id+"article_id"+article_id,
				success : function(text) {
					var obj = eval(text);
					var n_hits = obj.length;
					// alert(n_hits);
					// var html = "No asset found"
					var s;
					var i;
					s = '';
					
					if (n_hits > 0) {
						
						s = s +'<div><table class="table table-striped table-bordered"><thead><tr><td><b>ID</b></td><td><b>NAME</b></td><td><b>KEY-WORDS</b></td><td><b>ACTION</b></td><td><b>DETAIL</b></td></thead></tr>';
						for (i = 0; i < n_hits; i++) {
							s = s + "<tr>"
							var id = obj[i]["id"];
							var name = obj[i]["name"];
							var keywords = obj[i]["keywords"];
							s = s + '<td>' + id + '</td>';
							s = s + '<td>' + name + '</td>';
							s = s + '<td>' + keywords + '</td>';
							s = s
									+ '<td><a href="javascript:;" onclick="selectVendor(\''
									+ id + '\',\'' + name + '\',\'' + keywords
									+ '\')">  Select  </a></td>';
							s = s + '<td><a href="/procurement/vendor/show?id='
									+ id
									+ '" target="_blank">  Detail  </a></td>';
							s = s + "</tr>";

						}
						s = s + "</table></div>";

					} else {

						s = "No Vendors found!";
					}

					//alert(s);

					$("#search_result").html(s);
				}
			});

}