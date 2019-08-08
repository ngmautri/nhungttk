<?php
namespace Inventory\Domain\Warehouse\Transaction\GI;

use Inventory\Domain\Warehouse\Transaction\GoodsIssue;
use Inventory\Domain\Warehouse\Transaction\GoodsIssueInterface;
use Inventory\Domain\Warehouse\Transaction\TransactionFlow;
use Inventory\Domain\Warehouse\Transaction\TransactionRow;
use Inventory\Domain\Warehouse\Transaction\TransactionType;
use Application\Notification;
use Inventory\Domain\Warehouse\Transaction\GenericTransaction;
use Inventory\Domain\Warehouse\Transaction\TransactionSnapshot;
use Inventory\Domain\Warehouse\Transaction\GR\GRFromTransferLocation;
use Inventory\Domain\Warehouse\GenericWarehouse;
use Inventory\Domain\Warehouse\Location\GenericLocation;

/**
 * Machine ID is required, exchange part.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GIforRepairMachine extends GoodsIssue implements GoodsIssueInterface
{

    public function specify()
    {
        $this->movementType = TransactionType::GI_FOR_REPAIR_MACHINE_WITH_EX;
        $this->movementFlow = TransactionFlow::WH_TRANSACTION_OUT;
    }

    /**
     * 
     * {@inheritDoc}
     * @see \Inventory\Domain\Warehouse\Transaction\GenericTransaction::afterPost()
     */
    protected function afterPost(GenericWarehouse $sourceWh, GenericWarehouse $targetWh = null)
    {
        // returning items
        
        // check if warehouse has returning location
        
        // create new transaction
        
        /**
         *
         * @var GenericLocation $location
         */
        $location = $sourceWh->getReturnLocation();
                
        /**
         *
         * @var TransactionSnapshot $newSnapshot
         */
        $newSnapshot = clone($this->makeSnapshot());
        $newSnapshot->remarks ="automatically generated.";
        $newSnapshot->tartgetLocation = $location->getId();
                
        $newTrx = new GRFromTransferLocation();
        $newTrx->makeFromSnapshot($newSnapshot);
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \Inventory\Domain\Warehouse\Transaction\GenericTransaction::prePost()
     */
    protected function prePost(GenericWarehouse $sourceWh, GenericWarehouse $targetWh = null)
    {}
    

    public function prePost1()
    {}
    
    public function afterPost1()
    {
       
        
    }

    public function specificValidation($notification = null)
    {}

    public function specificHeaderValidation($notification = null)
    {}

    /**
     *
     * @param TransactionRow $row
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Warehouse\Transaction\GenericTransaction::specificRowValidation()
     */
    public function specificRowValidation($row, $notification = null, $isPosting = false)
    {
        if ($notification == null) {
            $notification = new Notification();
        }

        if ($row == null)
            $notification->addError("Row is empty");

        if ($this->sharedSpecificationFactory == null)
            $notification->addError("Validator is not found");

        if ($this->sharedSpecificationFactory->getNullorBlankSpecification()->isSatisfiedBy($row->getIssueFor())) {
            $notification->addError("Machine ID is required!");
        } else {

            /**
             *
             * @var AbstractSpecificationForCompany $spec ;
             */
            $spec = $this->sharedSpecificationFactory->getIsFixedAssetSpecification();
            $subject = array(
                "companyId" => $this->company,
                "itemId" => $row->getIssueFor()
            );

            if (! $spec->isSatisfiedBy($subject)) {
                $notification->addError("Item issued for not exits #" . $row->getIssueFor());
            }

            if ($row->getIssueFor() == $row->getItem()) {
                $notification->addError("Same item");
            }
        }

        return $notification;
    }
 
}