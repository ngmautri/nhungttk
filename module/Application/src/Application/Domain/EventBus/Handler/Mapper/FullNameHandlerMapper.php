<?php
namespace Application\Domain\EventBus\Handler\Mapper;

use Application\Domain\EventBus\Event\EventInterface;
use Application\Domain\EventBus\Exception\NoHandlerException;
use Application\Domain\EventBus\Handler\EventHandlerInterface;
use Application\Domain\EventBus\Handler\EventHandlerPriorityInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class FullNameHandlerMapper implements EventHandlerMapperInterface
{

    /**
     *
     * @var array
     */
    protected $listeners = [];

    /**
     * An array of strings representing the FQN of the Event Handler.
     *
     * @param string[] $handlers
     */
    public function __construct(array $handlers)
    {
        foreach ($handlers as $handler) {
            if (false === class_exists($handler, true)) {
                throw new \RuntimeException(sprintf('Class %s does not exist.', $handler));
            }

            if (false === in_array(EventHandlerInterface::class, class_implements($handler, true))) {
                throw new \RuntimeException(sprintf('Class %s does not implement the %s interface.', $handler, EventHandlerInterface::class));
            }

            $priority = EventHandlerPriorityInterface::LOW_PRIORITY;
            if (false !== in_array(EventHandlerPriorityInterface::class, class_implements($handler, true))) {
                /* @var EventHandlerPriorityInterface $handler */
                $priority = $handler::priority();
            }

            /* @var EventHandlerInterface $handler */
            $this->listeners[$handler::subscribedTo()][$priority][] = $handler;
        }
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\EventBus\Handler\Mapper\EventHandlerMapperInterface::handlerName()
     */
    public function handlerName(EventInterface $event)
    {
        $eventClass = get_class($event);

        if (empty($this->listeners[$eventClass])) {
            throw new NoHandlerException(sprintf('Event %s has no EventHandler defined.', $eventClass));
        }

        ksort($this->listeners[$eventClass], SORT_NUMERIC);

        return $this->listeners[$eventClass];
    }
}