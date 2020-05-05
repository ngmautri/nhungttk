<?php
namespace Application\Application\EventBus\Contracts;

use Application\Domain\EventBus\EventBusServiceInterface;
use Application\Domain\EventBus\Handler\EventHandlerInterface;
use Application\Domain\EventBus\Handler\EventHandlerPriorityInterface;
use Doctrine\ORM\EntityManager;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractEventHandler implements EventHandlerInterface, EventHandlerPriorityInterface
{

    protected $doctrineEM;

    protected $eventBusService;

    /**
     *
     * @param EntityManager $doctrineEM
     * @param EventBusServiceInterface $eventBusService
     */
    public function __construct(EntityManager $doctrineEM, EventBusServiceInterface $eventBusService)
    {
        $this->doctrineEM = $doctrineEM;
        $this->eventBusService = $eventBusService;
    }

    /**
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    /**
     *
     * @return \Application\Domain\EventBus\EventBusServiceInterface
     */
    public function getEventBusService()
    {
        return $this->eventBusService;
    }
}