<?php
namespace Inventory\Domain\Transaction\GI;

use Inventory\Domain\Transaction\GoodsIssue;
use Inventory\Domain\Transaction\Contracts\GoodsIssueInterface;
use Inventory\Domain\Transaction\Contracts\TrxFlow;
use Inventory\Domain\Transaction\Contracts\TrxType;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GIforCostCenter extends GoodsIssue implements GoodsIssueInterface
{

    public function __construct()
    {
        $this->movementType = TrxType::GI_FOR_COST_CENTER;
        $this->movementFlow = TrxFlow::WH_TRANSACTION_OUT;
    }
}