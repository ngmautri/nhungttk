<?php
namespace Application\Application\Eventbus;

use Application\Application\EventBus\Handler\Department\OnDepartmentInsertedSaveToDB;
use Application\Application\EventBus\Handler\Department\OnDepartmentInsertedSaveToLog;
use Application\Domain\EventBus\Handler\Mapper\FullNameHandlerMapper;
use Application\Service\AbstractService;

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
            OnDepartmentInsertedSaveToDB::class,
            OnDepartmentInsertedSaveToLog::class
        ];

        $this->handlers = $handlers;
    }

    public function getHandlerMapper()
    {
        $this->setUpMapper();
        return new FullNameHandlerMapper($this->handlers);
    }
}
