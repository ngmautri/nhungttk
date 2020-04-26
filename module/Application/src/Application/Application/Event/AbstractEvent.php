<?php
namespace Application\Application\Event;

use Application\Domain\EventBus\Event\EventInterface;
use Application\Domain\Shared\Event\EventInterface as IEvent;
use Symfony\Component\EventDispatcher\Event;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractEvent extends Event implements IEvent, EventInterface
{

    protected $target;

    protected $params;

    protected $trigger;

    protected $entityId;

    protected $entityToken;

    /**
     *
     * @return string
     */
    public function getTrigger()
    {
        return $this->trigger;
    }

    public function __construct($target, $trigger = null, $params = null)
    {
        $this->target = $target;
        $this->trigger = $trigger;
        $this->params = $params;
    }

    public function getTarget()
    {
        return $this->target;
    }

    /**
     *
     * @return string
     */
    public function getParams()
    {
        return $this->params;
    }
}
