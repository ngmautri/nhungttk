<?php
namespace Inventory\Domain\Warehouse\Transaction;

use Application\Notification;

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
     * @see \Inventory\Domain\Warehouse\Transaction\GenericTransaction::post()
     */
    public function post()
    {
        // 1.validate header
        $notification = $this->validate();

        if ($this->getValuationService() == null)
            $notification->addError("Valuation service (COGS) not found!");

        if ($notification->hasErrors())
            return $notification;

        /**
         * Template Method
         * ===============
         */
        $this->prePost();

        // 2. caculate cogs
        foreach ($this->transactionRows as $row) {

            /**
             *
             * @var \Inventory\Domain\Warehouse\Transaction\TransactionRow $row ;
             */

            $cogs = $this->getValuationService()->calculateCOGS($this, $row);
            var_dump($cogs);
        }

        /**
         * Template Method
         * ==============
         */
        $this->afterPost();

        // 4. store transaction

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
        $specDomain->setWarehouseId($this->warehouse);
        $specDomain->setTransactionDate($this->movementDate);
        $specDomain->setIssueQuantity($row->getDocQuantity());

        if (! $specDomain->isSatisfiedBy($row->getItem()))
            $notification->addError(sprintf("Can not issue this quantity %s (W #%s ,  Item #%s)", $this->warehouse, $row->getDocQuantity(), $row->getItem()));

        return $notification;
    }
}