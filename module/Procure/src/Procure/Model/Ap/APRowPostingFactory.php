<?php
namespace Procure\Model\Ap;


/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class APRowPostingFactory
{

    public static function getPostingStrategy($tTransaction)
    {
        switch ($tTransaction) {
            case \Application\Model\Constants::PROCURE_TRANSACTION_TYPE_GRNI:
                return new GRNI_Strategy();
            case \Application\Model\Constants::PROCURE_TRANSACTION_TYPE_GRIR:
                return new GRIR_Strategy();
            case \Application\Model\Constants::PROCURE_TRANSACTION_TYPE_IRNG:
                throw new \Exception("Unknown handler!");
            default:
                throw new \Exception("Unknown Transaction Type");
        }
    }
}