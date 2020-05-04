<?php
namespace Application\Application\EventBus\Contract;

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
     */
    public function __construct(EntityManager $doctrineEM, $eventBusService)
    {
        $this->doctrineEM = $doctrineEM;
        $this->eventBusService = $eventBusService;
    }

    /**
     *
     * @return \Procure\Application\Eventbus\EventBusService
     */
    public function getEventBusService()
    {
        return $this->eventBusService;
    }

    /**
     *
     * @param \Procure\Application\Eventbus\EventBusService $eventBusService
     */
    public function setEventBusService(\Procure\Application\Eventbus\EventBusService $eventBusService)
    {
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
     * @param EntityManager $doctrineEM
     */
    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
    }
}
