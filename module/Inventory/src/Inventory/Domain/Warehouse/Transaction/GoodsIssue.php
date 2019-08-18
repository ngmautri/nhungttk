<?php
namespace Inventory\Domain\Warehouse\Transaction;

use Application\Notification;
use Inventory\Domain\Event\GoodsIssuePostedEvent;
use Inventory\Domain\Service\TransactionPostingService;
use Inventory\Domain\Service\TransactionSpecificationService;
use Inventory\Domain\Service\TransactionValuationService;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class GoodsIssue extends GenericTransaction
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Warehouse\Transaction\GenericTransaction::doPost()
     */
    protected function doPost(TransactionSpecificationService $specificationService, TransactionValuationService $valuationService, TransactionPostingService $postingService, Notification $notification = null)
    {
        try {

            if ($notification == null)
                $notification = new Notification();

            if ($specificationService == null)
                $notification->addError("Specification service (COGS) not found!");

            if ($valuationService == null)
                $notification->addError("Valuation service (COGS) not found!");

            if ($postingService == null)
                $notification->addError("Posting service  not found!");

            if ($notification->hasErrors())
                return $notification;

            // 1.validate transaction
            $notification = $this->validate($specificationService, $notification, true);

            if ($notification->hasErrors())
                return $notification;

            // 2. caculate cogs
            foreach ($this->transactionRows as $row) {

                /**
                 *
                 * @var \Inventory\Domain\Warehouse\Transaction\TransactionRow $row ;
                 */
                $cogs = $valuationService->getFifoService()->calculateCOGS($this, $row);

                echo ($cogs);
                $snapshot = $row->makeSnapshot();
                $snapshot->cogsLocal = $cogs;
                $row->makeFromSnapshot($snapshot);
            }

            $postingService->getTransactionCmdRepository()->post($this, true);
        } catch (\Exception $e) {
            $notification->addError($e->getMessage());
        }

        return $notification;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Warehouse\Transaction\GenericTransaction::raiseEvent()
     */
    protected function raiseEvent()
    {
        // Recording Events
        $this->recordedEvents[] = new GoodsIssuePostedEvent($this);
    }
   
    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Warehouse\Transaction\GenericTransaction::specificRowValidationByFlow()
     */
    protected function specificRowValidationByFlow(TransactionSpecificationService $specificationService, TransactionRow $row, Notification $notification = null, $isPosting = false)
    {
        if ($notification == null)
            $notification = new Notification();

        if ($specificationService == null)
            $notification->addError("Specification service (COGS) not found!");

        if ($row == null)
            $notification->addError("Row is empty");

        if ($notification->hasErrors())
            return $notification;

        // Now doing validation.

        /**
         *
         * @var OnhandQuantitySpecification $specDomain
         */
        $specDomain = $specificationService->getDomainSpecificationFactory()->getOnhandQuantitySpecification();

        $subject = array(
            "warehouseId" => $this->warehouse,
            "itemId" => $row->getItem(),
            "movementDate" => $this->movementDate,
            "docQuantity" => $row->getDocQuantity()
        );

        if (! $specDomain->isSatisfiedBy($subject))
            $notification->addError(sprintf("Can not issue this quantity %s on %s (WH #%s ,  Item #%s)", $row->getDocQuantity(), $this->movementDate, $this->warehouse, $row->getItem()));

        return $notification;
    }
}