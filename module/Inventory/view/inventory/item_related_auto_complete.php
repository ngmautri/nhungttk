<?php

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *         ===================================
 */
$item_id_val = null;
$item_url_val = null;

if ($dto != null) {

    $format = '$("#related_item_id" ).val(\'%s\');';
    $item_id_val = sprintf($format, $dto->getIssueFor());

    $item_url = '/inventory/item/show1?tab_idx=0&entity_id=%s&token=%s';
    $item_url = sprintf($item_url, $dto->getIssueFor(), $dto->getItemToken());
    $format = '$("#related_item_url").text(\'%s\'); $("#related_item_detail").show();';
    $item_url_val = sprintf($format, $item_url);
}
?>
<script>

// if select item, then delete all pr item
$( "#related_item_name" ).autocomplete({
    source: "/inventory/item-search/auto-complete2",
    minLength: 2,
    select: function( event, ui ) {

         // set pr row empty
        $( "#related_item_id" ).val(ui.item.hit.id);

        $('#related_item_url').text('/inventory/item/show1?tab_idx=7&entity_id='+ui.item.hit.id+'&token='+ui.item.hit.token);

        $('#global-notice').show();
         $('#global-notice').html('"' + ui.item.value + '" selected');
         $('#global-notice').fadeOut(5000);
         //$("#jquery_dialog").dialog("close");

         $('#related_item_detail').show();
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

function showSelectedRelatedItem(){
   	var url=  $('#related_item_url').text();
  	showJqueryDialog('Item Detail','1750',$(window).height()-40, url,'j_loaded_data', true);
}



</script>