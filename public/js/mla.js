

$(document).ready(function(){
	$(document).ready(function() {
        $("#lightgallery").lightGallery(); 
    });
 
});

/**
 *	function on calendar dialog
 */
function showDialog()
{
	q =  $( "#asset" ).val();	
	
	$("#dialog").html('Please wait ...');
		$.ajax({
		  url: "/inventory/search/asset?json=1&query=" + q,
		  
		  success: function(text){
			  	var obj = eval(text	);
				var n_hits = obj.length;
				//alert(n_hits);
				//var html = "No asset found"
				var s;
				var i;
			    s="";
				if(n_hits > 0){
					s = '<table <table class="pure-table pure-table-bordered"><thead><tr><td>ID</td><td>NAME</td><td>TAG</td><td>ACTION</td><td>DETAIL</td></thead></tr>';
					for(i=0; i< n_hits; i++)
					{
					s = s + "<tr>"	
						var id  = obj[i]["id"];
						var name = obj[i]["name"];
						var tag = obj[i]["tag"];
					s = s + '<td>' +  id + '</td>';
					s = s + '<td>' +  name + '</td>';
					s = s + '<td>' +  tag	 + '</td>';
					s = s + '<td><a href="javascript:;" onclick="selectAsset(\''+ id + '\',\''+ name + '\',\''+ tag +'\')">  SELECT  </a></td>';
					s = s + '<td><a href="/inventory/asset/show?id='+ id + '" target="_blank">  DETAIL  </a></td>';

						
					s = s + "</tr>";
						
					}
					s = s + "</table>";

				}else{
					
					s="No asset found!";
				}
					
				//alert(s);
				
				$("#dialog").html(s);
		  }
	});
	
	
	//$( "#dialog" ).text(t);
	$( "#dialog" ).dialog({width :860,height:500,title: "Select asset", modal: true});
}

function selectAsset(id,name,tag){
	$( "#asset_id" ).val(id);
	$( "#asset" ).val(tag);
	$( "#dialog" ).dialog("close");
	
}


