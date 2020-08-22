<?php
namespace Application\Application\Helper;

use Application\Entity\FinAccount;
use Application\Entity\FinCostCenter;
use Application\Entity\NmtApplicationCurrency;
use Application\Entity\NmtApplicationDepartment;
use Application\Entity\NmtApplicationIncoterms;
use Application\Entity\NmtApplicationPmtTerm;
use Application\Entity\NmtApplicationUom;
use Application\Entity\NmtInventoryItemGroup;
use Application\Entity\NmtInventoryWarehouse;
use Inventory\Domain\Item\Contracts\ItemType;
use Inventory\Domain\Item\Contracts\MonitorMethod;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class OptionList
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

    public static function createWarehouseOption($list, $id)
    {
        if ($list == null) {
            return null;
        }
        $option = "";

        foreach ($list as $l) {

            /**
             *
             * @var NmtInventoryWarehouse $l ;
             */

            if ($id == null) {
                $option = $option . sprintf('<option value="%s">%s</option>', $l->getId(), $l->getWhName());
            } else {
                if ($id == $l->getId()) {
                    $option = $option . sprintf('<option selected value="%s">%s</option>', $l->getId(), $l->getWhName());
                } else {
                    $option = $option . sprintf('<option value="%s">%s</option>', $l->getId(), $l->getWhName());
                }
            }
        }
        return $option;
    }

    public static function createGLAccountOption($list, $id)
    {
        if ($list == null) {
            return null;
        }

        $option = "";

        foreach ($list as $l) {

            /**
             *
             * @var FinAccount $l ;
             */

            if ($id == null) {
                $option = $option . sprintf('<option value="%s">%s - %s</option>', $l->getId(), $l->getAccountNumber(), $l->getDescription());
            } else {
                if ($id == $l->getId()) {
                    $option = $option . sprintf('<option selected value="%s">%s - %s</option>', $l->getId(), $l->getAccountNumber(), $l->getDescription());
                } else {
                    $option = $option . sprintf('<option value="%s">%s - %s</option>', $l->getId(), $l->getAccountName(), $l->getDescription());
                }
            }
        }
        return $option;
    }

    public static function createCostCenterOption($list, $id)
    {
        if ($list == null) {
            return null;
        }

        $option = "";

        foreach ($list as $l) {

            /**
             *
             * @var FinCostCenter $l ;
             */

            if ($id == null) {
                $option = $option . sprintf('<option value="%s">%s - %s</option>', $l->getId(), $l->getCostCenterName(), $l->getDescription());
            } else {
                if ($id == $l->getId()) {
                    $option = $option . sprintf('<option selected value="%s">%s - %s</option>', $l->getId(), $l->getCostCenterName(), $l->getDescription());
                } else {
                    $option = $option . sprintf('<option value="%s">%s - %s</option>', $l->getId(), $l->getCostCenterName(), $l->getDescription());
                }
            }
        }
        return $option;
    }

    public static function createPmtTermOption($list, $id)
    {
        if ($list == null) {
            return null;
        }

        $option = "";

        foreach ($list as $l) {

            /**
             *
             * @var NmtApplicationPmtTerm $l ;
             */

            if ($id == null) {
                $option = $option . sprintf('<option value="%s">%s - %s</option>', $l->getId(), $l->getPmtTermName(), $l->getPmtTermCode());
            } else {
                if ($id == $l->getId()) {
                    $option = $option . sprintf('<option selected value="%s">%s - %s</option>', $l->getId(), $l->getPmtTermName(), $l->getPmtTermCode());
                } else {
                    $option = $option . sprintf('<option value="%s">%s - %s</option>', $l->getId(), $l->getPmtTermName(), $l->getPmtTermCode());
                }
            }
        }
        return $option;
    }

    public static function createCurrencyOption($list, $id)
    {
        if ($list == null) {
            return null;
        }

        $option = "";

        foreach ($list as $l) {

            /**
             *
             * @var NmtApplicationCurrency $l ;
             */

            if ($id == null) {
                $option = $option . sprintf('<option value="%s">%s - %s</option>', $l->getId(), $l->getCurrency(), $l->getCurrencyNumericCode());
            } else {
                if ($id == $l->getId()) {
                    $option = $option . sprintf('<option selected value="%s">%s - %s</option>', $l->getId(), $l->getCurrency(), $l->getCurrencyNumericCode());
                } else {
                    $option = $option . sprintf('<option value="%s">%s - %s</option>', $l->getId(), $l->getCurrency(), $l->getCurrencyNumericCode());
                }
            }
        }
        return $option;
    }

    public static function createIncotermOption($list, $id)
    {
        if ($list == null) {
            return null;
        }

        $option = "";

        foreach ($list as $l) {

            /**
             *
             * @var NmtApplicationIncoterms $l ;
             */

            if ($id == null) {
                $option = $option . sprintf('<option value="%s">%s - %s</option>', $l->getId(), $l->getIncoterm(), $l->getIncoterm1());
            } else {
                if ($id == $l->getId()) {
                    $option = $option . sprintf('<option selected value="%s">%s - %s</option>', $l->getId(), $l->getIncoterm(), $l->getIncoterm1());
                } else {
                    $option = $option . sprintf('<option value="%s">%s - %s</option>', $l->getId(), $l->getIncoterm(), $l->getIncoterm1());
                }
            }
        }
        return $option;
    }

    public static function createDepartmentOption($list, $id)
    {
        if ($list == null) {
            return null;
        }

        $option = "";

        foreach ($list as $l) {

            /**
             *
             * @var NmtApplicationDepartment $l ;
             */

            if ($id == null) {
                $option = $option . sprintf('<option value="%s">%s  %s</option>', $l->getNodeId(), $l->getNodeName(), "");
            } else {
                if ($id == $l->getNodeId()) {
                    $option = $option . sprintf('<option selected value="%s">%s  %s</option>', $l->getNodeId(), $l->getNodeName(), "");
                } else {
                    $option = $option . sprintf('<option value="%s">%s  %s</option>', $l->getNodeId(), $l->getNodeName(), "");
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
             * @var NmtApplicationUom $l ;
             */

            if ($id == null) {
                $option = $option . sprintf('<option value="%s">%s  %s</option>', $l->getId(), $l->getUomCode(), "");
            } else {
                if ($id == $l->getId()) {
                    $option = $option . sprintf('<option selected value="%s">%s  %s</option>', $l->getId(), $l->getUomCode(), "");
                } else {
                    $option = $option . sprintf('<option value="%s">%s  %s</option>', $l->getId(), $l->getUomCode(), "");
                }
            }
        }
        return $option;
    }

    public static function createItemGroupOption($list, $id)
    {
        if ($list == null) {
            return null;
        }

        $option = "";

        foreach ($list as $l) {

            /**
             *
             * @var NmtInventoryItemGroup $l ;
             */

            if ($id == null) {
                $option = $option . sprintf('<option value="%s">%s  %s</option>', $l->getId(), $l->getGroupName(), "");
            } else {
                if ($id == $l->getId()) {
                    $option = $option . sprintf('<option selected value="%s">%s  %s</option>', $l->getId(), $l->getGroupName(), "");
                } else {
                    $option = $option . sprintf('<option value="%s">%s  %s</option>', $l->getId(), $l->getGroupName(), "");
                }
            }
        }
        return $option;
    }

    public static function createItemTypeOption($id)
    {
        $list = ItemType::getSupportedTypeArray();
        if ($list == null) {
            return null;
        }

        $option = "";

        foreach ($list as $l => $v) {

            if ($id == null) {
                $option = $option . sprintf('<option value="%s">%s  %s</option>', $l, $v, "");
            } else {
                if ($id == $l) {
                    $option = $option . sprintf('<option selected value="%s">%s  %s</option>', $l, $v, "");
                } else {
                    $option = $option . sprintf('<option value="%s">%s  %s</option>', $l, $v, "");
                }
            }
        }
        return $option;
    }

    public static function createItemMonitorOption($id)
    {
        $list = MonitorMethod::getSupportedMethodArray();
        if ($list == null) {
            return null;
        }

        $option = "";

        foreach ($list as $l => $v) {

            if ($id == null) {
                $option = $option . sprintf('<option value="%s">%s  %s</option>', $l, $v, "");
            } else {
                if ($id == $l) {
                    $option = $option . sprintf('<option selected value="%s">%s  %s</option>', $l, $v, "");
                } else {
                    $option = $option . sprintf('<option value="%s">%s  %s</option>', $l, $v, "");
                }
            }
        }
        return $option;
    }

    public static function createPerPageOption($id)
    {
        $list = [
            '20' => 20,
            '30' => 30,
            '50' => 50,
            '100' => 100,
            '200' => 200
        ];

        if ($list == null) {
            return null;
        }

        $option = "";

        foreach ($list as $l => $v) {

            if ($id == null) {
                $option = $option . sprintf('<option value="%s">%s  %s</option>', $l, $v, "");
            } else {
                if ($id == $l) {
                    $option = $option . sprintf('<option selected value="%s">%s  %s</option>', $l, $v, "");
                } else {
                    $option = $option . sprintf('<option value="%s">%s  %s</option>', $l, $v, "");
                }
            }
        }
        return $option;
    }
}
