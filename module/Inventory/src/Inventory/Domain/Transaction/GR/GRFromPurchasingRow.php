<?php
namespace Inventory\Domain\Transaction\GR;

use Application\Domain\Shared\Command\CommandOptions;
use Inventory\Domain\Transaction\GenericTrx;
use Inventory\Domain\Transaction\TrxRow;
use Inventory\Domain\Warehouse\Transaction\TransactionFlow;
use Procure\Domain\GoodsReceipt\GRRow;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GRFromPurchasingRow extends TrxRow
{

    /**
     *
     * @param GenericTrx $rootEntity
     * @param GRRow $sourceObj
     * @param CommandOptions $options
     * @throws InvalidArgumentException
     * @return \Inventory\Domain\Transaction\GR\GRFromPurchasingRow
     */
    public static function createFromPurchaseGrRow(GenericTrx $rootEntity, GRRow $sourceObj, CommandOptions $options)
    {
        if (! $sourceObj instanceof GRRow) {
            throw new InvalidArgumentException("PO document is required!");
        }
        if ($options == null) {
            throw new InvalidArgumentException("No Options is found");
        }

        /**
         *
         * @var GRFromPurchasingRow $instance ;
         */
        $instance = new self();

        $instance = $sourceObj->convertTo($instance);

        // $instance->setDocType(Constants::PROCURE_DOC_TYPE_INVOICE_PO); // important.
        $instance->setGrRow($sourceObj->getId()); // Important
        $instance->setFlow(TransactionFlow::WH_TRANSACTION_IN);
        $instance->setWh($instance->getWarehouse());

        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();
        $instance->initRow($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));

        return $instance;
    }

    /**
     *
     * @param GenericTrx $rootEntity
     * @param GRRow $sourceObj
     * @param CommandOptions $options
     * @throws InvalidArgumentException
     * @return \Inventory\Domain\Transaction\GR\GRFromPurchasingRow
     */
    public static function createFromPurchaseGrRowReversal(GenericTrx $rootEntity, GRRow $sourceObj, CommandOptions $options)
    {
        if (! $sourceObj instanceof GRRow) {
            throw new InvalidArgumentException("PO document is required!");
        }
        if ($options == null) {
            throw new InvalidArgumentException("No Options is found");
        }

        /**
         *
         * @var GRFromPurchasingRow $instance ;
         */
        $instance = new self();

        $instance = $sourceObj->convertTo($instance);

        // $instance->setDocType(Constants::PROCURE_DOC_TYPE_INVOICE_PO); // important.
        $instance->setGrRow($sourceObj->getId()); // Important
        $instance->setFlow(TransactionFlow::WH_TRANSACTION_OUT);
        $instance->setWh($instance->getWarehouse());

        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();
        $instance->initRow($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));

        return $instance;
    }
}