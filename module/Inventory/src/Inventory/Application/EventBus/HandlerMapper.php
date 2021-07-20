<?php
namespace Inventory\Application\EventBus;

use Application\Domain\EventBus\Handler\Mapper\FullNameHandlerMapper;
use Application\Service\AbstractService;
use Inventory\Application\EventBus\Handler\Item\OnItemCreatedCreateIndex;
use Inventory\Application\EventBus\Handler\Item\OnItemUpdatedUpdateIndex;
use Inventory\Application\EventBus\Handler\Transaction\OnWhGiForPoReturnPostedCalculateCost;
use Inventory\Application\EventBus\Handler\Transaction\OnWhGiPostedCalculateCost;
use Inventory\Application\EventBus\Handler\Transaction\OnWhGoodsExchagePostedCreateTrx;
use Inventory\Application\EventBus\Handler\Transaction\OnWhGrPostedCreateFiFoLayer;
use Inventory\Application\EventBus\Handler\Transaction\OnWhOpenBalancePostedCloseFifoLayer;
use Inventory\Application\EventBus\Handler\Transaction\OnWhOpenBalancePostedCloseTrx;
use Inventory\Application\EventBus\Handler\Transaction\OnWhTransferLocationPostedCreateTrx;
use Inventory\Application\EventBus\Handler\Transaction\OnWhTransferPostedCreateTrx;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class HandlerMapper extends AbstractService
{

    protected $queuedHandlers;

    protected $handlers;

    protected function setUpMapper()
    {
        $handlers = [
            OnWhGiPostedCalculateCost::class,
            OnWhGiForPoReturnPostedCalculateCost::class,

            OnWhGrPostedCreateFiFoLayer::class,

            OnItemCreatedCreateIndex::class,
            OnItemUpdatedUpdateIndex::class,

            OnWhOpenBalancePostedCloseFifoLayer::class,
            OnWhOpenBalancePostedCloseTrx::class,

            OnWhGoodsExchagePostedCreateTrx::class,

            OnWhTransferLocationPostedCreateTrx::class,
            OnWhTransferPostedCreateTrx::class
        ];

        $this->handlers = $handlers;
    }

    public function getHandlerMapper()
    {
        $this->setUpMapper();
        return new FullNameHandlerMapper($this->handlers);
    }
}
