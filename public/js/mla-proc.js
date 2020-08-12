function searchPrItem() {
    // select pr item
    $("#pr_item_name").autocomplete(
	    {
		source : "/procure/pr-search/auto-complete",
		minLength : 2,
		select : function(event, ui) {

		    $("#pr_row_id").val(ui.item.hit.id);

		    $("#quantity").val(ui.item.hit.quantity);

		    $("#item_id").val(ui.item.hit.item);
		    $("#item_name").val(ui.item.hit.itemName);
		    $('#item_url').text(
			    '/inventory/item/show1?tab_idx=1&entity_id='
				    + ui.item.hit.item + '&token='
				    + ui.item.hit.itemToken);
		    $('#pr_row_url').text(
			    '/procure/pr-row/show1?entity_id=' + ui.item.hit.id
				    + '&token=' + ui.item.hit.token);

		    $('#global-notice').show();
		    $('#global-notice').html(
			    '"' + ui.item.hit.itemName + '" selected');
		    $('#global-notice').fadeOut(5000);

		    $('#item_detail').show();
		    $('#pr_row_detail').show();
		    $("#quantity").focus();

		    var url = '/procure/pr-row/get-row?id=' + ui.item.hit.id;
		    $.get(url, {}, function(data, status) {
			//alert(data.pr_convert_factor);
			$('#purchase_uom_convert_factor').text(
				'Ordered: ' + data.pr_qty + ' ' + data.pr_uom
					+ ' = '
					+ data.pr_converted_standard_qty + ' '
					+ data.item_standard_uom);
			$('#rowUnit').val(data.pr_uom);
		    });

		}
	    }).autocomplete("instance")._renderItem = function(ul, item) {
	var img = '<img class="img-rounded" width="75" height="75" src="'
		+ item.item_thumbnail + '"/>';
	var item_detail = item.hit.itemSKU + ' | ' + item.hit.itemName
	var item_detail_html = '<span style="font-size: 9pt; padding-top:0px;">'
		+ item_detail + '</span><br>';
	var item_detail_html = item_detail_html
		+ '<span style="color:gray; font-size: 9pt; padding-top:0px;">'
		+ item.hit.itemManufacturerCode + '</span>';
	var pr_detail = item.hit.docNumber + ' | Qty: ' + item.hit.quantity
		+ ' |' + item.hit.rowName;
	var pr_detail_html = '<span style="padding-top:7px; font-size: 9.5pt; color:navy;font-weight: bold;">'
		+ pr_detail + '</span>';
	var n_html = '<span style="color:graytext;font-size: 8pt; border-bottom: thin solid gray; align:right">'
		+ item.n + '</span>';
	var tbl_html = '<table style="Padding-left:5px" ><tr><td>' + img
		+ '</td><td style="padding: 1pt 1pt 1pt 5pt">'
		+ item_detail_html + '<br>' + pr_detail_html + '<br>' + n_html
		+ '</td></tr></table>'
	return $('<li>')
		.append(
			'<div style="border-bottom: thin solid gray; padding-bottom:2px; Padding-right:5px">'
				+ tbl_html + '</div>').appendTo(ul);
    };

}

function OrderHistoryDialog(sp_id, article_id) {
    // $( "#dialog" ).text(t);
    $("#dialog").dialog({
	width : 950,
	height : 550,
	title : "Order History",
	modal : true,
	dialogClass : 'dialogClass'
    });

    $('#search_term').keypress(function(event) {

	if (event.keyCode === 10 || event.keyCode === 13)
	    event.preventDefault();
	//showDialog();
    });

    loadHistory(sp_id, article_id);
}
/**
 * function on calendar dialog
 */
function loadHistory(sp_id, article_id) {

    $("#search_result").html('Loading....');

    $
	    .ajax({
		url : "/procurement/pr/history?out=json&spartpart_id" + sp_id
			+ "article_id" + article_id,
		success : function(text) {
		    var obj = eval(text);
		    var n_hits = obj.length;
		    // alert(n_hits);
		    // var html = "No asset found"
		    var s;
		    var i;
		    s = '';

		    if (n_hits > 0) {

			s = s
				+ '<div><table class="table table-striped table-bordered"><thead><tr><td><b>ID</b></td><td><b>NAME</b></td><td><b>KEY-WORDS</b></td><td><b>ACTION</b></td><td><b>DETAIL</b></td></thead></tr>';
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