<?php
use Procure\Application\DTO\Qr\QrDTO;
use Procure\Application\DTO\Qr\QrRowDTO;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *         ===================================
 */

/**
 *
 * @var QrDTO $headerDTO ;
 * @var QrRowDTO $dto ;
 */

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
$( "#qr_item_name" ).autocomplete({
    source: "/procure/qr-search/auto-complete",
    minLength: 2,
    select: function( event, ui ) {

	      $( "#qr_row_id" ).val(ui.item.hit.id);
          $( "#docQuantity" ).val(ui.item.hit.docQuantity);
          $( "#pr_row_id" ).val(ui.item.hit.prRow);


          $( "#item" ).val(ui.item.hit.item);
          $( "#item_name" ).val(ui.item.hit.itemName);
          $('#item_url').text('/inventory/item/show1?tab_idx=1&entity_id='+ui.item.hit.item+'&token='+ui.item.hit.itemToken);
          $('#pr_row_url').text('/procure/pr-row/show1?entity_id='+ui.item.hit.id+'&token='+ui.item.hit.token);

          $('#global-notice').show();
          $('#global-notice').html('"' + ui.item.hit.itemName + '" selected');
          $('#global-notice').fadeOut(5000);

          $('#item_detail').show();
          //$('#pr_row_detail').show();
          $( "#docQuantity" ).focus();
          $( "#target_wh_id" ).val(ui.item.hit.warehouse);


           // update GL account and cost center
          if(ui.item.inventory_account_id !==null){
               	// update GL account and cost center
           	   	$( "#glAccount" ).val(ui.item.hit.itemInventoryGL);
          }

           // update GL account and cost center
           	if(ui.item.cost_center_id !==null){
               	// update GL account and cost center
           	   	$( "#costCenter").val(ui.item.hit.itemCostCenter);
           	}

                $( "#standardConvertFactor" ).val(1);

          $('#purchase_uom_convert_factor').text('Quoted: '+ ui.item.hit.docQuantity + ' '+ ui.item.hit.docUnit + ' = ' + ui.item.hit.convertedStandardQuantity +  ' '+ ui.item.hit.itemStandardUnitName);

          $('#docUnit').val(ui.item.hit.docUnit);
          $('#unit_price').val(ui.item.hit.docUnitPrice);

        /*   var url = '/procure/pr-row/get-row?id='+ui.item.hit.id;
		  		$.get(url, {   	}, function(data, status) {
           		$('#purchase_uom_convert_factor').text('Ordered: '+ data.pr_qty + ' '+ data.pr_uom + ' = ' + data.pr_converted_standard_qty +  ' '+ data.item_standard_uom);
           		$('#rowUnit').val(data.pr_uom);
           	}); */
       }
    })
    .autocomplete( "instance" )._renderItem = function( ul, item ) {
          var img = '<img class="img-rounded" width="75" height="75" src="'+item.item_thumbnail+'"/>';
          var item_detail = item.hit.itemSKU +  ' | ' + item.hit.itemName
          var item_detail_html = '<span style="font-size: 9pt; padding-top:0px;">' + item_detail + '</span><br>';
          var item_detail_html = item_detail_html + '<span style="color:gray; font-size: 9pt; padding-top:0px;">' + item.hit.itemManufacturerCode + '</span>';
          var item_detail_html = item_detail_html + '<span style="color:gray; font-size: 9pt; padding-top:0px;"> | ' + item.hit.vendorItemName + '</span>';

          var pr_detail= 'Quote #' + item.hit.docNumber + ' | Qty: ' + item.hit.quantity + ' |' + item.hit.vendorItemName;

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

function showSelectedQrRow(){
	var url= $('#pr_row_url').text();
   	showJqueryDialog('<?php

    echo $pr_row_number;
    ?>' ,'1800',$(window).height()-95, url,'j_loaded_data', true);
}


</script>