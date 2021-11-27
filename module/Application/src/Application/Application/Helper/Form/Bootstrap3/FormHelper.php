<?php
namespace Application\Application\Helper\Form\Bootstrap3;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class FormHelper
{

    // horizonal
    const SIZE_SM = '.form-control-sm';

    // horizonal
    const SIZE_LG = 'form-control-lg';

    const FORM_CONTROL = 'form-control';

    const FORM_GROUP = 'form-group';

    const FORM_HORIZONTAL = 'form-horizontal';

    const FORM_INLINE = 'form-inline';

    public static function createFormGroup($content)
    {
        $format = '<div class="form-group">
                    %s
                  </div>';
        return sprintf($format, $content);
    }
}
