<?php

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *         ===================================
 */

/**@var \Procure\Application\DTO\Po\PoDetailsDTO $headerDTO ;*/
/**@var \Procure\Application\DTO\Po\PORowDetailsDTO $dto ;*/
$po_row_id_val = null;
$po_row_url_val = null;

if ($dto != null) {

    $format = '$("#po_row_id" ).val(\'%s\');';
    $po_row_id_val = sprintf($format, $dto->getPoRow());

    $po_row_url = '/procure/po/view?&entity_id=%s';
    $po_row_url = sprintf($po_row_url, $dto->getPo());
    $format = '$("#po_row_url").text(\'%s\'); $("#po_row_detail").show();';
    $po_row_url_val = sprintf($format, $po_row_url);
}
?>
<script>


	// search po item
$( "#po_item_name" ).autocomplete({
    source: "/procure/po-search/auto-complete?vendor_id=<?php

    echo $headerDTO->getVendor();
    ?>",
    minLength: 2,
    select: function( event, ui ) {

	   	$( "#gl_account_id" ).val("");
  	   	$( "#cost_center_id").val("");
  	 	$( "#docUnitPrice").val("");



        $( "#po_row_id" ).val(ui.item.po_row_id);
        $( "#quantity" ).val(ui.item.row_quantity);
        $( "#docUnitPrice" ).val(ui.item.row_unit_price);

        $( "#item" ).val(ui.item.item_id);
        $( "#item_name" ).val(ui.item.item_name);


        $('#item_url').text('/inventory/item/show1?tab_idx=11&entity_id='+ui.item.item_id+'&token='+ui.item.item_token);
        $('#po_row_url').text('/procure/po-row/show1?entity_id='+ui.item.po_row_id+'&token='+ui.item.token);

        $('#rowUnit').val(ui.item.row_unit);
        $('#po_row_uom').text(ui.item.row_unit +' = ' + ui.item.row_conversion_factor);


         //alert(ui.item.item_id);
        $('#global-notice').show();
        $('#global-notice').html('"' + ui.item.item_name + '" selected');
        $('#global-notice').fadeOut(5000);
    	$('#item_detail').show();
     	$('#po_row_detail').show();

     	$( "#quantity" ).focus();


     	// update GL account and cost center
     	if(ui.item.inventory_account_id !==null){
         	// update GL account and cost center
     	   	$( "#glAccount" ).val(ui.item.inventory_account_id);
      	}

     // update GL account and cost center
     	if(ui.item.cost_center_id !==null){
         	// update GL account and cost center
     	   	$( "#costCenter").val(ui.item.cost_center_id);
      	}
    }
    })
    .autocomplete( "instance" )._renderItem = function( ul, item ) {
    return $( "<li>" )



.append( "<div style='padding-bottom: 15px; border-bottom: thin solid gray;'><span style='font-size: 9.5pt;font-weight: bold;'>"  + item.item_sku_key  +  " | " + item.item_name + " | Q'ty: " + item.row_quantity +
              "</span><br><span style='padding-top: 7px;color:gray;font-size: 9pt;'>"+ item.vendor_name +'<br>' + item.po_number + " | " + item.row_identifer_keyword  + " | " + item.manufacturer_code_key +"<span></div>" )

//         .append( "<div style='padding-bottom: 5px; border-bottom: thin solid gray;color:gray;font-size: 9.5pt;'>" + item.pr_number + "<br><span style='color:black;font-size: 9.5pt;'>" + item.item_sku_key  +  " | " + item.item_name + " | Q'ty: " + item.row_quantity + "<span></div>" )
      .appendTo( ul );
};


<?php
echo $po_row_id_val;
echo $po_row_url_val;
?>

function showSelectedPoRow(){
	var url= $('#po_row_url').text();
   	showJqueryDialog('PR Row Detail','1450',$(window).height()-40, url,'j_loaded_data', true);
}


</script>