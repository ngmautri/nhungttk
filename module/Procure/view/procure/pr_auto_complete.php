<?php

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *         ===================================
 */

/**@var \Procure\Application\DTO\Po\PoDetailsDTO $headerDTO ;*/
/**@var \Procure\Application\DTO\Po\PORowDetailsDTO $dto ;*/
$pr_row_id_val = null;
$pr_row_number = null;
$pr_row_url_val = null;

if ($dto != null) {

    $format = '$("#pr_row_id" ).val(\'%s\');';
    $pr_row_id_val = sprintf($format, $dto->getPrRow());

    if ($dto->getPrRow() != null) {
        $pr_row_url = '/procure/pr/view1?&entity_id=%s';
        $pr_row_url = sprintf($pr_row_url, $dto->getPr());
        $format = '$("#pr_row_url").text(\'%s\'); $("#pr_row_detail").show();';
        $pr_row_url_val = sprintf($format, $pr_row_url);
        $pr_row_number = sprintf("PR Row #%s", $dto->getPrRowIndentifer());
    }
}
?>
<script>

// select pr item
$( "#pr_item_name" ).autocomplete({
    source: "/procure/pr-search/auto-complete",
    minLength: 2,
    select: function( event, ui ) {

	      $( "#pr_row_id" ).val(ui.item.hit.id);         
          $( "#quantity" ).val(ui.item.hit.quantity);          
          $( "#item_id" ).val(ui.item.hit.item);  
          $( "#item_name" ).val(ui.item.hit.itemName);
          $('#item_url').text('/inventory/item/show1?tab_idx=1&entity_id='+ui.item.hit.item+'&token='+ui.item.hit.itemToken);
          $('#pr_row_url').text('/procure/pr-row/show1?entity_id='+ui.item.hit.id+'&token='+ui.item.hit.token);             

          $('#global-notice').show();
          $('#global-notice').html('"' + ui.item.hit.itemName + '" selected');
          $('#global-notice').fadeOut(5000);

          	$('#item_detail').show();
           	$('#pr_row_detail').show();
           	$( "#quantity" ).focus();

       	var url = '/procure/pr-row/get-row?id='+ui.item.hit.id;
		  		$.get(url, {   	}, function(data, status) {
          		//alert(data.pr_convert_factor);
          		$('#purchase_uom_convert_factor').text('Ordered: '+ data.pr_qty + ' '+ data.pr_uom + ' = ' + data.pr_converted_standard_qty +  ' '+ data.item_standard_uom);
           		$('#rowUnit').val(data.pr_uom);
           	});
          

       }
    })
    .autocomplete( "instance" )._renderItem = function( ul, item ) {
          var img = '<img class="img-rounded" width="75" height="75" src="'+item.item_thumbnail+'"/>';
          var item_detail = item.hit.itemSKU +  ' | ' + item.hit.itemName 
          var item_detail_html = '<span style="font-size: 9pt; padding-top:0px;">' + item_detail + '</span><br>';
          var item_detail_html = item_detail_html + '<span style="color:gray; font-size: 9pt; padding-top:0px;">' + item.hit.itemManufacturerCode + '</span>';
          var pr_detail= item.hit.docNumber + ' | Qty: ' + item.hit.quantity + ' |' + item.hit.rowName; 
          var pr_detail_html = '<span style="padding-top:7px; font-size: 9.5pt; color:navy;font-weight: bold;">' + pr_detail + '</span>';
          var n_html = '<span style="color:graytext;font-size: 8pt; border-bottom: thin solid gray; align:right">' + item.n + '</span>';
          var tbl_html = '<table style="Padding-left:5px" ><tr><td>'+img+'</td><td style="padding: 1pt 1pt 1pt 5pt">' + item_detail_html + '<br><br>' + pr_detail_html + '<br>'+n_html +'</td></tr></table>'
          return $( '<li>')
          .append('<div style="border-bottom: thin solid gray; padding-bottom:2px; Padding-right:5px">'+tbl_html+'</div>')
          .appendTo( ul );
  };

<?php
echo $pr_row_id_val;
echo $pr_row_url_val;
?>

function showSelectedPrRow(){
	var url= $('#pr_row_url').text();
   	showJqueryDialog('<?php

    echo $pr_row_number;
    ?>' ,'1800',$(window).height()-95, url,'j_loaded_data', true);
}


</script>