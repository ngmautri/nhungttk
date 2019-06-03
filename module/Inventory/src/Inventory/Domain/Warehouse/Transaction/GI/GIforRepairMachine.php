<?php
namespace Inventory\Domain\Warehouse\Transaction\GI;
use Inventory\Domain\Warehouse\Transaction\GenericTransaction;
use Inventory\Domain\Warehouse\Transaction\GoodsIssueInterface;
use Inventory\Domain\Warehouse\Transaction\GoodsIssue;

/**
 * Machine ID is required, exchange part.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GIforRepairMachine extends GoodsIssue implements GoodsIssueInterface
{
    public function specificValidation($notification = null)
    {}
    public function addTransactionRow($transactionRowDTO)
    {}

}