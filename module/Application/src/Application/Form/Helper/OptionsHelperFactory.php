<?php
namespace Application\Form\Helper;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class OptionsHelperFactory
{

    public static function createDepartmentOptions($list)
    {
        if ($list == null) {
            return null;
        }

        $tmp = [];
        foreach ($list as $l) {

            $tmp1 = array(
                $l->getDepartmentCode() => $l->getDepartmentShowName()
            );

            $tmp = \array_merge($tmp, $tmp1);
        }
        return $tmp;
    }

    public static function createDepartmentOptions1($list)
    {
        if ($list == null) {
            return null;
        }

        $tmp = [];
        foreach ($list as $l) {

            $tmp1 = [
                'value' => $l->getDepartmentCode(),
                'label' => $l->getDepartmentShowName()
            ];

            $tmp[] = $tmp1;
        }
        // var_dump($tmp);
        return $tmp;
    }
}
