<?php
namespace Inventory\Domain\Warehouse\Transaction\GI;

use Inventory\Domain\Service\TransactionPostingService;
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
use Inventory\Domain\Warehouse\Transaction\GR\GRFromExchange;
use Inventory\Domain\Warehouse\Transaction\TransactionRowSnapshot;
use Inventory\Domain\Event\GoodsIssuePostedEvent;
use Inventory\Domain\Event\GoodsExchangePostedEvent;

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
     * {@inheritdoc}
     * @see \Inventory\Domain\Warehouse\Transaction\GenericTransaction::afterPost()
     */
    protected function afterPost(TransactionPostingService $postingService = null, $notification = null)
    {
        try {
            $sourceWH = $postingService->getWhQueryRepository()->getById($this->getWarehouse());

            /**
             *
             * @var GenericLocation $location
             */
            $location = $sourceWH->getReturnLocation();

            /**
             *
             * @var TransactionSnapshot $newSnapshot
             */
            $newSnapshot = clone ($this->makeSnapshot());

            $newSnapshot->id = null;
            $newSnapshot->uuid = null;

            $newSnapshot->remarks = "automatically generated.";
            $newSnapshot->tartgetLocation = $location->getId();

            $newTrx = new GRFromExchange();
            $newTrx->makeFromSnapshot($newSnapshot);

            // create row
            foreach ($this->getRows() as $row) {

                // var_dump($row);

                /**
                 *
                 * @var TransactionRowSnapshot $newRowSnapshot ;
                 */
                $newRowSnapshot = clone ($row->makeSnapshot());

                $newRowSnapshot->id = null;
                $newRowSnapshot->token = null;
                $newRowSnapshot->mvUuid = null;

                $newRowSnapshot->whLocation = $location->getId();
                $newRowSnapshot->cogsDoc = 0;
                $newRowSnapshot->cogsLocal = 0;
                $newRowSnapshot->flow = TransactionFlow::WH_TRANSACTION_IN;
                $newRowSnapshot->transactionType = TransactionType::GR_FROM_EXCHANGE;
                $newRowSnapshot->docType = TransactionType::GR_FROM_EXCHANGE;

                $newTransactionRow = new TransactionRow();
                $newTransactionRow->makeFromSnapshot($newRowSnapshot);
                $newTrx->addRow($newTransactionRow);
            }

            $postingService->getTransactionCmdRepository()->store($newTrx, true, true);

            // Recording Events
            $this->recoredEvents[] = new GoodsExchangePostedEvent($newTrx);
        } catch (\Exception $e) {
            $notification->addError($e->getMessage());
        }

        return $notification;
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

        if ($this->sharedSpecificationFactory->getNullorBlankSpecification()->isSatisfiedBy($row->getIssueFor())) {
            $notification->addError("Machine ID is required!");
        } else {

            /**
             *
             * @var AbstractSpecificationForCompany $spec ;
             */
            $spec = $this->sharedSpecificationFactory->getItemExitsSpecification();
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
    
    /**
     * 
     * {@inheritDoc}
     * @see \Inventory\Domain\Warehouse\Transaction\GenericTransaction::prePost()
     */
    protected function prePost(TransactionPostingService $postingService = null, $notification = null)
    {}

 
}