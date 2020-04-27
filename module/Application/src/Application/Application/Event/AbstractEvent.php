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

    protected $docVersion;

    protected $revisionNo;

    /**
     *
     * @return string
     */
    public function getTrigger()
    {
        return $this->trigger;
    }

    public function __construct($target, $entityId, $entityToken, $docVersion, $revisionNo, $trigger, $params)
    {
        $this->target = $target;
        $this->trigger = $trigger;
        $this->params = $params;

        $this->entityId = $entityId;
        $this->entityToken = $entityToken;
        $this->docVersion = $docVersion;
        $this->revisionNo = $revisionNo;
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

    /**
     *
     * @return mixed
     */
    public function getEntityId()
    {
        return $this->entityId;
    }

    /**
     *
     * @return mixed
     */
    public function getEntityToken()
    {
        return $this->entityToken;
    }

    /**
     *
     * @return string
     */
    public function getDocVersion()
    {
        return $this->docVersion;
    }

    /**
     *
     * @return string
     */
    public function getRevisionNo()
    {
        return $this->revisionNo;
    }
}
