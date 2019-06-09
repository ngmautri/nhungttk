<?php
namespace Inventory\Domain\Warehouse\Transaction\GI;
use Inventory\Domain\Warehouse\Transaction\GoodsIssue;
use Inventory\Domain\Warehouse\Transaction\GoodsIssueInterface;

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
    public function specificRowValidation($row, $notification = null, $isPosting = false)
    {}


}