<?php
namespace Inventory\Application\Eventbus;

use Application\Domain\EventBus\Handler\Mapper\FullNameHandlerMapper;
use Application\Service\AbstractService;
use Inventory\Application\EventBus\Handler\Item\OnItemCreatedCreateIndex;
use Inventory\Application\EventBus\Handler\Item\OnItemUpdatedUpdateIndex;
use Inventory\Application\EventBus\Handler\Transaction\OnOpenBalancePostedCloseFifoLayer;
use Inventory\Application\EventBus\Handler\Transaction\OnOpenBalancePostedCloseTrx;
use Inventory\Application\EventBus\Handler\Transaction\OnWhGiPostedCalculateCost;
use Inventory\Application\EventBus\Handler\Transaction\OnWhGrPostedCreateFiFoLayer;

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
            OnWhGrPostedCreateFiFoLayer::class,
            OnItemCreatedCreateIndex::class,
            OnItemUpdatedUpdateIndex::class,
            OnOpenBalancePostedCloseFifoLayer::class,
            OnOpenBalancePostedCloseTrx::class
        ];

        $this->handlers = $handlers;
    }

    public function getHandlerMapper()
    {
        $this->setUpMapper();
        return new FullNameHandlerMapper($this->handlers);
    }
}
