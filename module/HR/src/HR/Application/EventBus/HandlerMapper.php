<?php
namespace HR\Application\EventBus;

use Application\Domain\EventBus\Handler\Mapper\FullNameHandlerMapper;
use Application\Service\AbstractService;
use HR\Application\EventBus\Handler\Individual\OnIndividualCreatedCreateIndex;
use HR\Application\EventBus\Handler\Individual\OnIndividualUpdatedUpdateIndex;

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
            OnIndividualCreatedCreateIndex::class,
            OnIndividualUpdatedUpdateIndex::class
        ];

        $this->handlers = $handlers;
    }

    public function getHandlerMapper()
    {
        $this->setUpMapper();
        return new FullNameHandlerMapper($this->handlers);
    }
}
