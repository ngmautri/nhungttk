<?php
namespace Application\Application\EventBus;

use Application\Application\EventBus\Handler\Department\OnDepartmentInsertedSaveToDB;
use Application\Application\EventBus\Handler\Department\OnDepartmentInsertedSaveToLog;
use Application\Application\EventBus\Handler\Department\OnDepartmentMovedSaveToDB;
use Application\Application\EventBus\Handler\Department\OnDepartmentMovedSaveToLog;
use Application\Application\EventBus\Handler\Department\OnDepartmentRemovedSaveToDB;
use Application\Application\EventBus\Handler\Department\OnDepartmentRemovedSaveToLog;
use Application\Application\EventBus\Handler\Department\OnDepartmentRenamedSaveToDB;
use Application\Application\EventBus\Handler\Department\OnDepartmentRenamedSaveToLog;
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
            OnDepartmentInsertedSaveToLog::class,

            OnDepartmentMovedSaveToLog::class,
            OnDepartmentMovedSaveToDB::class,

            OnDepartmentRenamedSaveToLog::class,
            OnDepartmentRenamedSaveToDB::class,

            OnDepartmentRemovedSaveToLog::class,
            OnDepartmentRemovedSaveToDB::class
        ];

        $this->handlers = $handlers;
    }

    public function getHandlerMapper()
    {
        $this->setUpMapper();
        return new FullNameHandlerMapper($this->handlers);
    }
}
