<?php
namespace Application\Application\Helper\Form\Bootstrap3;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class CollapseHelper
{

    const DEFAULT_DOWN = '<span class="glyphicon glyphicon-collapse-down"></span> ';

    const DEFAULT_UP = '<span class="glyphicon glyphicon-collapse-down"></span> ';

    const DEFAULT_JS = '<script>
$(document).ready(function(){

  $("#%s").on("hide.bs.collapse", function(){
    $("#btn_9999999").html(\'%s\');
  });

  $("#%s").on("show.bs.collapse", function(){
    $("#btn_9999999").html(\'%s\');
  });
});
</script>';

    public static function drawBasisCollapse($showTitle, $hideTitle, $collapseId, $content, $hide = TRUE)
    {
        $format = '<button id="btn_9999999" type="button" class="btn btn-default" data-toggle="collapse" data-target="#%s">%s</button>
        <div id="%s" class="collapse%s">
 <div style="border: 1px; padding: 3px; font-size: 9pt; background: url(/images/bg1.png); background-repeat: repeat-x; background-color: #FFFFFF;">
        %s
</div>
        </div>';

        $in = '';
        if ($hide) {
            $title = $showTitle;
        } else {
            $in = " in";
            $title = $hideTitle;
        }

        $c = sprintf($format, $collapseId, $title, $collapseId, $in, $content);

        $js = sprintf(self::DEFAULT_JS, $collapseId, $showTitle, $collapseId, $hideTitle);

        return $c . "\n" . $js;
    }
}
