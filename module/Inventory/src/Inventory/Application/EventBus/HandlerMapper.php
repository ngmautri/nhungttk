<?php
namespace Inventory\Application\Eventbus;

use Application\Domain\EventBus\Handler\Mapper\FullNameHandlerMapper;
use Application\Service\AbstractService;
use Inventory\Application\EventBus\Handler\Item\CalculateCostOnWhGiPosted;
use Inventory\Application\EventBus\Handler\Item\CreateFiFoLayerOnWhGrPosted;
use Inventory\Application\EventBus\Handler\Item\CreateIndexOnItemCreated;
use Inventory\Application\EventBus\Handler\Item\UpdateIndexOnItemUpdated;

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
            CalculateCostOnWhGiPosted::class,
            CreateFiFoLayerOnWhGrPosted::class,
            CreateIndexOnItemCreated::class,
            UpdateIndexOnItemUpdated::class
        ];

        $this->handlers = $handlers;
    }

    public function getHandlerMapper()
    {
        $this->setUpMapper();
        return new FullNameHandlerMapper($this->handlers);
    }
}
