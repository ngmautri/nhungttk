<?php
namespace Inventory\Domain\Warehouse\Transaction;

use Application\Notification;
use Inventory\Domain\Event\GoodsIssuePostedEvent;
use Inventory\Domain\Service\TransactionPostingService;

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
    protected function doPost(TransactionPostingService $postingService = null, $notification = null)
    {

        // 1.validate header
        $notification = $this->validate();

        if ($this->getValuationService() == null)
            $notification->addError("Valuation service (COGS) not found!");

        if ($notification->hasErrors())
            return $notification;

        // 2. caculate cogs
        foreach ($this->transactionRows as $row) {

            /**
             *
             * @var \Inventory\Domain\Warehouse\Transaction\TransactionRow $row ;
             */
            $cogs = $this->getValuationService()->calculateCOGS($this, $row);
            echo ($cogs);
            $snapshot = $row->makeSnapshot();
            $snapshot->cogsLocal = $cogs;
            $row->makeFromSnapshot($snapshot);
        }

        $postingService->getTransactionCmdRepository()->post($this, true);

        // Recording Events
        $this->recoredEvents[] = new GoodsIssuePostedEvent($this);

        return $notification;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Warehouse\Transaction\GenericTransaction::addTransactionRow()
     */
    public function addTransactionRow($transactionRowDTO)
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Warehouse\Transaction\GenericTransaction::specificRowValidationByFlow()
     */
    public function specificRowValidationByFlow($row, $notification = null, $isPosting = false)
    {
        if ($notification == null)
            $notification = new Notification();

        if ($this->domainSpecificationFactory == null)
            $notification->addError("No Domain Validator found");

        if ($row == null)
            $notification->addError("Row is empty");

        if ($notification->hasErrors())
            return $notification;

        // Now doing validation.

        /**
         *
         * @var OnhandQuantitySpecification $specDomain
         */
        $specDomain = $this->domainSpecificationFactory->getOnhandQuantitySpecification();

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