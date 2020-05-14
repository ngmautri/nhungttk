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

    protected $defaultParams;

    protected $params;

    protected $targetId;

    protected $targetToken;

    protected $targetDocVersion;

    protected $targetRrevisionNo;

    protected $userId;

    protected $triggeredBy;

    public function __construct($target, DefaultParameter $defaultParams, $params)
    {
        $this->target = $target;
        $this->defaultParams = $defaultParams;
        $this->params = $params;
    }

    public function hasParam($key)
    {
        if ($this->params == null) {
            return false;
        }
        return \array_key_exists($key, $this->params);
    }

    public function getParam($key)
    {
        if ($this->hasParam($key)) {
            return $this->params[$key];
        }

        return null;
    }

    /**
     *
     * @return mixed
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     *
     * @return \Application\Application\Event\DefaultParameter
     */
    public function getDefaultParams()
    {
        return $this->defaultParams;
    }

    /**
     *
     * @return mixed
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     *
     * @return mixed
     */
    public function getTargetId()
    {
        if ($this->defaultParams == null) {
            return null;
        }
        return $this->defaultParams->getTargetId();
    }

    /**
     *
     * @return mixed
     */
    public function getTargetToken()
    {
        if ($this->defaultParams == null) {
            return null;
        }
        return $this->defaultParams->getTargetToken();
    }

    /**
     *
     * @return mixed
     */
    public function getTargetDocVersion()
    {
        if ($this->defaultParams == null) {
            return null;
        }
        return $this->defaultParams->getTargetDocVersion();
    }

    /**
     *
     * @return mixed
     */
    public function getTargetRrevisionNo()
    {
        if ($this->defaultParams == null) {
            return null;
        }
        return $this->defaultParams->getTargetRrevisionNo();
    }

    /**
     *
     * @return mixed
     */
    public function getUserId()
    {
        if ($this->defaultParams == null) {
            return null;
        }
        return $this->defaultParams->getUserId();
    }

    /**
     *
     * @return mixed
     */
    public function getTriggeredBy()
    {
        if ($this->defaultParams == null) {
            return null;
        }
        return $this->defaultParams->getTriggeredBy();
    }
}
