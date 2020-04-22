<?php
namespace Procure\Application\Helper;

use Application\Entity\FinAccount;
use Application\Entity\FinCostCenter;
use Application\Entity\NmtApplicationCurrency;
use Application\Entity\NmtApplicationDepartment;
use Application\Entity\NmtApplicationIncoterms;
use Application\Entity\NmtApplicationPmtTerm;
use Application\Entity\NmtInventoryWarehouse;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class OptionList
{

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
}
