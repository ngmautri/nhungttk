<?php
namespace Procure\Application\Helper;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class FormHelper
{

    public static function createButton($name, $title, $url, $icon)
    {
        $btn = '<a href="%s" title="%s" >%s</a>';
        $btn1 = '<a href="%s" title="%s" ><small><i class="%s"></i></small> %s</a>';

        if ($icon !== null) {
            return sprintf($btn1, $url, $title, $icon, $name);
        } else {
            return sprintf($btn, $url, $title, $name);
        }
    }

    public static function createProgressDiv($completion, $caption)
    {
        $progress_cls = "";
        $color = "black";

        if ($completion == 1) {
            $progress_cls = "progress-bar-success";
        }

        if ($completion <= 0.5) {
            $progress_cls = "progress-bar-warning";
            $color = "graytext";
        }

        if ($completion < 1 && $completion > 0.5) {
            $color = "white";
        }

        $completion = round($completion * 100, 0);

        $progress_div = sprintf('<div class="progress" style="height: 18px; margin-bottom:2pt;">
<div class="progress-bar %s" role="progressbar" style="width:%s%s;" aria-valuenow="%s" aria-valuemin="0" aria-valuemax="100">
 <span style="font-size: 8pt; padding: 0px 0px 1px 1px; color:%s;">%s%s %s</span>
</div>
</div>', $progress_cls, $completion, "%", $completion, $color, $completion, "%", $caption);

        return $progress_div;
    }
}
