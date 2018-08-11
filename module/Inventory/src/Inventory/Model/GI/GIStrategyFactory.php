<?php
namespace Inventory\Model\GI;


/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GIStrategyFactory
{

    public static function getGIStrategy($tTransaction)
    {
        switch ($tTransaction) {
            case \Application\Model\Constants::PROCURE_TRANSACTION_TYPE_GRNI:
                return new GIforRepair();
            case \Application\Model\Constants::PROCURE_TRANSACTION_TYPE_GRIR:
                throw new \Exception("Unknown handler!");
            case \Application\Model\Constants::PROCURE_TRANSACTION_TYPE_IRNG:
                throw new \Exception("Unknown handler!");
            default:
                throw new \Exception("Unknown Transaction Type");
        }
    }
}