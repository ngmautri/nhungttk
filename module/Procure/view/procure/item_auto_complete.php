<?php

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *         ===================================
 */
$item_id_val = null;
$item_url_val = null;

if ($dto != null) {

    $format = '$("#item_id" ).val(\'%s\');';
    $item_id_val = sprintf($format, $dto->getItem());

    $item_url = '/inventory/item/show1?tab_idx=0&entity_id=%s&token=%s';
    $item_url = sprintf($item_url, $dto->getItem(), $dto->getItemToken());
    $format = '$("#item_url").text(\'%s\'); $("#item_detail").show();';
    $item_url_val = sprintf($format, $item_url);
}
?>
<script>

// if select item, then delete all pr item
$( "#item_name" ).autocomplete({
    source: "/inventory/item-search/auto-complete2",
    minLength: 2,
    select: function( event, ui ) {

         // set pr row empty
        $( "#pr_row_id" ).val("");
        $( "#pr_row_url" ).val("");
        $( "#pr_row_detail" ).hide();
        $( "#pr_item_name" ).val("");
        $( "#item_id" ).val(ui.item.hit.id);

        $('#item_url').text('/inventory/item/show1?tab_idx=7&entity_id='+ui.item.hit.id+'&token='+ui.item.hit.token);

      $('#global-notice').show();
         $('#global-notice').html('"' + ui.item.value + '" selected');
         $('#global-notice').fadeOut(5000);
         //$("#jquery_dialog").dialog("close");

         $('#item_detail').show();
         $( "#quantity" ).focus();
     }
  })
  .autocomplete( "instance" )._renderItem = function( ul, result ) {

    var img = '<img class="img-rounded" width="80" height="80" src="'+result.item_thumbnail+'"/>';
    var item_detail = result.hit.itemSku +  ' | ' + result.hit.itemName ;
    var item_detail_html = '<span style="font-size: 10pt; padding-top:0px; color:navy;font-weight: bold;">' + item_detail + '</span><br>';
    var item_detail_html = item_detail_html + '<span style="color:gray; font-size: 9pt; padding-top:0px;">' + result.hit.manufacturerCode + '</span>';
    var item_detail_html = item_detail_html + '<span style="color:gray; font-size: 9pt; padding-top:0px;"> | ' + result.hit.manufacturerModel + '</span>';
    
    var n_html = '<span style="color:graytext;font-size: 8pt; border-bottom: thin solid gray; align:right">' + result.n + '</span>';
    var tbl_html = '<table style="Padding-left:5px" ><tr><td>'+img+'</td><td style="padding: 1pt 1pt 1pt 5pt">' + item_detail_html + '<br><br>'+n_html +'</td></tr></table>'

         return $( '<li>')
         .append('<div style="border-bottom: thin solid gray; padding-bottom:2px; Padding-right:5px">'+tbl_html+'</div>')
         .appendTo( ul );

    };

<?php
echo $item_id_val;
echo $item_url_val;
?>

function showSelectedItem(){
   var url= $('#item_url').text();   
   showJqueryDialog('Item Detail','1650',$(window).height()-40, url,'j_loaded_data', true);
}

function selectItem(id, token,target, name, target_name, context = null){
   var target_id = '#' + target;
   $(target_id).val(id);

    var target_name_id = '#' + target_name;
   $(target_name_id).val(name);
   $('#modal1.close').click();
   $('#global-notice').show();
   $('#global-notice').html('"' + name + '" selected');
   $('#global-notice').fadeOut(5000);
   $("#jquery_dialog").dialog("close");


    $("#item_id" ).val(id);
      $('#item_url').text('/inventory/item/show1?tab_idx=9&entity_id='+id+'&token='+token);

      $('#item_detail').show();
      $("#quantity").focus();

   var url = '/inventory/item/get-item?id='+id;
      $('#rowUnit').val("Loading...");
      $('#purchase_uom_convert_text').text("Loading...");

   $.get(url, {      }, function(data, status) {
         //alert(data.purchase_uom_code);
         $('#rowUnit').val(data.purchase_uom_code);
         $('#standard_uom_id').text(data.uom_code);
         $('#conversionFactor').val(data.purchase_uom_convert_factor);
            $('#purchase_uom_convert_text').text($( "#rowUnit" ).val() + " = " +  $('#conversionFactor').val() + " " + $( "#standard_uom_id" ).text());

      });
}

</script>