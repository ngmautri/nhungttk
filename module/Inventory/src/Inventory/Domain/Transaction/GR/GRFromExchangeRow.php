<?php
namespace Inventory\Domain\Transaction\GR;

use Application\Domain\Shared\Command\CommandOptions;
use Inventory\Domain\Transaction\GenericTrx;
use Inventory\Domain\Transaction\TrxRow;
use Procure\Domain\GoodsReceipt\GRRow;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GRFromExchangeRow extends TrxRow
{

    /**
     *
     * @param GenericTrx $rootEntity
     * @param GRRow $sourceObj
     * @param CommandOptions $options
     * @throws InvalidArgumentException
     * @return \Inventory\Domain\Transaction\GR\GRFromPurchasingRow
     */
    public static function createFromGIRow(GenericTrx $rootEntity, TrxRow $sourceObj, CommandOptions $options)
    {
        if (! $sourceObj instanceof GenericTrx) {
            throw new InvalidArgumentException("GenericTrx document is required!");
        }
        if ($options == null) {
            throw new InvalidArgumentException("No Options is found");
        }

        /**
         *
         * @var GRFromExchangeRow $instance ;
         */
        $instance = new self();

        $instance = $sourceObj->convertTo($instance);

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
    public static function createFromGIRowReversal(GenericTrx $rootEntity, TrxRow $sourceObj, CommandOptions $options)
    {
        if (! $sourceObj instanceof GenericTrx) {
            throw new InvalidArgumentException("GenericTrx is required!");
        }
        if ($options == null) {
            throw new InvalidArgumentException("No Options is found");
        }

        /**
         *
         * @var GRFromExchangeRow $instance ;
         */
        $instance = new self();

        $instance = $sourceObj->convertTo($instance);
        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();
        $instance->initRow($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));

        return $instance;
    }
}