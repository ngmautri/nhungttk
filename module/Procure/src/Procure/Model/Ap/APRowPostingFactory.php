<?php
namespace Procure\Model\Ap;


/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class APRowPostingFactory
{

    /**
     * 
     * @param string $tTransaction
     * @throws \Exception
     * @return \Procure\Model\Ap\GRNIStrategy|\Procure\Model\Ap\GRIRStrategy
     */
    public static function getPostingStrategy($tTransaction)
    {
        switch ($tTransaction) {
            case \Application\Model\Constants::PROCURE_TRANSACTION_TYPE_GRNI:
                return new GRNIStrategy();
            case \Application\Model\Constants::PROCURE_TRANSACTION_TYPE_GRIR:
                return new GRIRStrategy();
            case \Application\Model\Constants::PROCURE_TRANSACTION_TYPE_IRNG:
                throw new \Exception("Unknown handler!");
            default:
                throw new \Exception("Unknown Transaction Type");
        }
    }
    
    
    /**
     * 
     * @param string $tTransaction
     * @throws \Exception
     * @return \Procure\Model\Ap\GRNIReservalStrategy|\Procure\Model\Ap\GRIRReversalStrategy
     */
    public static function getReversalStrategy($tTransaction)
    {
        switch ($tTransaction) {
            case \Application\Model\Constants::PROCURE_TRANSACTION_TYPE_GRNI:
                return new GRNIReservalStrategy();
            case \Application\Model\Constants::PROCURE_TRANSACTION_TYPE_GRIR:
                return new GRIRReversalStrategy();
            case \Application\Model\Constants::PROCURE_TRANSACTION_TYPE_IRNG:
                throw new \Exception("Unknown handler!");
            default:
                throw new \Exception("Unknown Transaction Type");
        }
    }
}