<?php
namespace Application\Application\Event;

use Symfony\Component\EventDispatcher\Event;
use Application\Domain\Shared\Event\EventInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractEvent extends Event implements EventInterface
{

    private $target;

    private $params;

    private $trigger;

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
