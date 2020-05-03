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

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Transaction\Contracts\GoodsIssueInterface::specify()
     */
    public function specify()
    {
        $this->movementType = TrxType::GI_FOR_COST_CENTER;
        $this->movementFlow = TrxFlow::WH_TRANSACTION_OUT;
    }
}