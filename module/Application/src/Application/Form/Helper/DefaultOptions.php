<?php
namespace Application\Form\Helper;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DefaultOptions
{

    public static function createResultPerPageOption()
    {
        $o = [];

        $tmp1 = [
            'value' => 10,
            'label' => 10
        ];

        $o[] = $tmp1;

        $tmp1 = [
            'value' => 15,
            'label' => 15
        ];

        $o[] = $tmp1;

        $tmp1 = [
            'value' => 20,
            'label' => 20
        ];

        $o[] = $tmp1;

        $tmp1 = [
            'value' => 50,
            'label' => 50
        ];

        $o[] = $tmp1;

        return $o;
    }
}
