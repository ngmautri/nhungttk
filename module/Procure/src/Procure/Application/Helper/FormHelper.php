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
}
