<?php
namespace Application\Application\Helper\Form;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class FormHelper
{

    const POST_PRE = "<!--#POST_PRE-->";

    const POST_AFTER = "<!--#POST_AFTER-->";

    public static function echoMessage($msg, $label = FormHelperConst::B_LABEL_DEFAULT)
    {
        $format = '<h5><span class="label label-%s" style="margin: 5pt 1pt 5pt 1pt; font-size:10pt;">%s</span></h5>';
        echo sprintf($format, $label, $msg);
    }

    public static function drawLine()
    {
        return '<hr style="margin: 5pt 1pt 5pt 1pt;">';
    }

    public static function createTabs($list)
    {
        if ($list == null) {
            return;
        }

        $tabs = '';
        foreach ($list as $l) {

            $tabs = $tabs . \sprintf('<li>%s</li>', $l);
        }

        return $tabs;
    }

    public static function createDropDownBtn($list, $other_pre = '', $other_after = '')

    {
        if ($list == null) {
            return;
        }
        $dropDown = '<div tyle="font-size: 9pt; margin-bottom: 2pt; padding: 3pt 5pt 3pt 5pt;">';
        $dropDown = $dropDown . '<div class="dropdown">';
        $dropDown = $dropDown . $other_pre;
        $dropDown = $dropDown . self::POST_PRE;
        $dropDown = $dropDown . '<button style="color: black; padding: 3pt 5pt 3pt 5pt;; color: black; font-size: 9.5pt" class="btn btn-default dropdown-toggle btn-sm" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">';
        $dropDown = $dropDown . '<i class="fa fa-download" aria-hidden="true"></i>&nbsp;Download&nbsp;<span class="caret"></span></button>';

        $dropDown = $dropDown . '<ul style="font-size: 9.5pt; style="color: black;" class="dropdown-menu" aria-labelledby="dropdownMenu1">';

        foreach ($list as $l) {

            if ($l == FormHelperConst::DIVIDER) {
                $dropDown = $dropDown . '<li role="separator" class="divider"></li>';
            } else {
                $dropDown = $dropDown . \sprintf('<li>%s</li>', $l);
            }
        }

        $dropDown = $dropDown . '</ul>';
        $dropDown = $dropDown . $other_after;
        $dropDown = $dropDown . self::POST_AFTER;
        $dropDown = $dropDown . '</div></div>';

        return $dropDown;
    }

    public static function preAppendToCurrentToolbar($currentToolBar, $element)
    {
        return str_replace(self::POST_AFTER, $element, $currentToolBar);
    }

    public static function createCustomDropDownMenu($list, $id)
    {
        if ($list == null) {
            return;
        }

        $dropDown = \sprintf('<ul style="font-size: 9.5pt; margin-bottom: 2pt; padding: 3pt 5pt 3pt 5pt;" class="dropdown-menu" aria-labelledby="%s">', $id);

        foreach ($list as $l) {

            if ($l == "divider") {
                $dropDown = $dropDown . '<li role="separator" class="divider"></li>';
            } else {
                $dropDown = $dropDown . \sprintf('<li>%s</li>', $l);
            }
        }

        $dropDown = $dropDown . '</ul>';

        return $dropDown;
    }

    public static function createButton($name, $title, $url, $icon)
    {
        $btn = '<a style="font-size: 8.5pt; margin-bottom: 2pt; padding: 3pt 5pt 3pt 5pt;" class="btn btn-default btn-sm" href="%s" title="%s" >%s</a>';
        $btn1 = '<a style="font-size: 9.5pt; margin-bottom: 2pt; padding: 3pt 5pt 3pt 5pt;" class="btn btn-default btn-sm" href="%s" title="%s" ><small><i class="%s"></i></small> %s</a>';

        if ($icon !== null) {
            return sprintf($btn1, $url, $title, $icon, $name);
        } else {
            return sprintf($btn, $url, $title, $name);
        }
    }

    public static function createButtonForJS($name, $onclick, $title)
    {
        $btn = '<a title="%s" class="btn btn-default btn-sm" style="color: black; margin-bottom: 1pt; margin-left: 2pt; padding: 3pt 5pt 3pt 5pt;" id="myLink" href="#" onclick="%s">%s</a>';
        return sprintf($btn, $title, $onclick, $name);
    }

    public static function createLink($url, $name, $title = null, $icon = null)
    {
        $btn = '<a href="%s" title="%s" >%s</a>';
        $btn1 = '<a href="%s" title="%s" ><small><i class="%s"></i></small> %s</a>';

        if ($icon != null) {
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
 <span style="font-size:7.5pt; padding: 0px 0px 1px 1px; color:%s;">%s%s %s</span>
</div>
</div>', $progress_cls, $completion, "%", $completion, $color, $completion, "%", $caption);

        return $progress_div;
    }
}
