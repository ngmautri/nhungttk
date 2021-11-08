<?php
namespace Application\Application\Helper;

use Application\Application\Helper\Contracts\OptionHelperInterface;
use Application\Domain\Shared\Uom\Uom;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class OptionHelper implements OptionHelperInterface
{

    public static function createYearOption($yy)
    {
        $option = "";
        $y1 = date("Y");

        for ($y = 2014; $y <= $y1; $y ++) {

            if ($yy == null) {
                $option = $option . sprintf('<option value="%s">%s</option>', $y, $y);
            } else {
                if ($y == $yy) {
                    $option = $option . sprintf('<option selected value="%s">%s</option>', $y, $y);
                } else {
                    $option = $option . sprintf('<option value="%s">%s</option>', $y, $y);
                }
            }
        }
        return $option;
    }

    public static function createMonthOption($mm)
    {
        $option = "";

        for ($m = 1; $m <= 12; $m ++) {

            if ($mm == null) {
                $option = $option . sprintf('<option value="%s">%s</option>', $m, $m);
            } else {
                if ($m == $mm) {
                    $option = $option . sprintf('<option selected value="%s">%s</option>', $m, $m);
                } else {
                    $option = $option . sprintf('<option value="%s">%s</option>', $m, $m);
                }
            }
        }
        return $option;
    }

    public static function createUoMOption($list, $id)
    {
        if ($list == null) {
            return null;
        }

        $option = "";

        foreach ($list as $l) {

            /**
             *
             * @var Uom $l ;
             */

            if ($id == null) {
                $option = $option . sprintf('<option value="%s">%s (%s)</option>', $l->getUomName(), ucwords($l->getUomName()), strtolower($l->getUomCode()));
            } else {
                if ($id == $l->getUomName()) {
                    $option = $option . sprintf('<option selected value="%s">%s (%s)</option>', $l->getUomName(), ucwords($l->getUomName()), strtolower($l->getUomCode()));
                } else {
                    $option = $option . sprintf('<option value="%s">%s (%s)</option>', $l->getUomName(), ucwords($l->getUomName()), strtolower($l->getUomCode()));
                }
            }
        }
        return $option;
    }

    public static function createUomNameOption($list, $id)
    {
        if ($list == null) {
            return null;
        }

        $option = "";

        foreach ($list as $l) {

            /**
             *
             * @var Uom $l ;
             */

            if ($id == null) {
                $option = $option . sprintf('<option value="%s">%s (%s)</option>', $l->getUomName(), ucwords($l->getUomName()), strtolower($l->getUomCode()));
            } else {
                if ($id == $l->getUomName()) {
                    $option = $option . sprintf('<option selected value="%s">%s (%s)</option>', $l->getUomName(), ucwords($l->getUomName()), strtolower($l->getUomCode()));
                } else {
                    $option = $option . sprintf('<option value="%s">%s (%s)</option>', $l->getUomName(), ucwords($l->getUomName()), strtolower($l->getUomCode()));
                }
            }
        }
        return $option;
    }
}
