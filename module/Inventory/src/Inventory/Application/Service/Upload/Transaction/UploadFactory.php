<?php
namespace Inventory\Application\Service\Upload\Transaction;

use Inventory\Domain\Transaction\Contracts\TrxType;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class UploadFactory
{

    /**
     *
     * {@inheritdoc}
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    static public function create($typeId)
    {
        $uploader = null;
        switch ($typeId) {

            case TrxType::GR_FROM_OPENNING_BALANCE:
                $uploader = new UploadGrForOpeningBalance();
                break;
            case TrxType::GI_FOR_COST_CENTER:
                $uploader = new UploadGiForCostCenter();
                break;
            case TrxType::GI_FOR_REPAIR_MACHINE_WITH_EX:
                $uploader = new UploadGiForRepairWithEx();
                break;
        }

        if ($uploader == null) {
            throw new \RuntimeException("Can not create Transaction Uploader");
        }

        return $uploader;
    }
}