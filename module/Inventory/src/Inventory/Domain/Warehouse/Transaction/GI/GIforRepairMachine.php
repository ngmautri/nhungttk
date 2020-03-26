<?php
namespace Inventory\Domain\Warehouse\Transaction\GI;

use Application\Notification;
use Inventory\Domain\Event\GoodsExchangePostedEvent;
use Inventory\Domain\Service\TransactionPostingService;
use Inventory\Domain\Service\TransactionSpecificationService;
use Inventory\Domain\Service\TransactionValuationService;
use Inventory\Domain\Warehouse\Transaction\GoodsIssue;
use Inventory\Domain\Warehouse\Transaction\GoodsIssueInterface;
use Inventory\Domain\Warehouse\Transaction\TransactionFlow;
use Inventory\Domain\Warehouse\Transaction\TransactionRow;
use Inventory\Domain\Warehouse\Transaction\TransactionType;
use Inventory\Domain\Warehouse\Transaction\GR\GRFromExchange;
use Application\Domain\Shared\Specification\AbstractSpecification;

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
    protected function afterPost(TransactionSpecificationService $specificationService, TransactionValuationService $valuationService, TransactionPostingService $postingService, Notification $notification = null)
    {
        try {

            if ($notification == null)
                $notification = new Notification();

            if ($postingService == null) {
                $notification->addError("Posting service not set");
            }

            if ($notification->hasErrors())
                return $notification;

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

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Warehouse\Transaction\GenericTransaction::specificRowValidation()
     */
    protected function specificRowValidation(TransactionSpecificationService $specificationService, TransactionRow $row, Notification $notification = null, $isPosting = false)
    {
        if ($notification == null) {
            $notification = new Notification();
        }

        if ($row == null)
            $notification->addError("Row is empty");

        if ($specificationService == null) {
            $notification->addError("Speficification not set");
        }

        if ($notification->hasErrors())
            return $notification;

        if ($specificationService->getSharedSpecificationFactory()
            ->getNullorBlankSpecification()
            ->isSatisfiedBy($row->getIssueFor())) {
            $notification->addError("Machine ID is required!");
        } else {

            /**
             *
             * @var AbstractSpecification $spec ;
             */
            $spec = $specificationService->getSharedSpecificationFactory()->getItemExitsSpecification();
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

    protected function prePost(TransactionSpecificationService $specificationService, TransactionValuationService $valuationService, TransactionPostingService $postingService, Notification $notification = null)
    {}

    protected function specificValidation(TransactionSpecificationService $specificationService, Notification $notification = null)
    {}

    protected function specificHeaderValidation(TransactionSpecificationService $specificationService, Notification $notification = null)
    {}

    public function addTransactionRow(TransactionRow $transactionRow)
    {}
}