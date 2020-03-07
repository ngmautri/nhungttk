<?php
namespace Application\Domain\Shared;

use Application\Notification;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractEntity
{

    private $notification;

    private $recordedEvents;

    /**
     *
     * @param object $e
     */
    public function addEvent($e)
    {
        if ($e == null) {
            return;
        }

        $events = $this->getRecordedEvents();
        $events[] = $e;
        $this->recordedEvents = $events;
    }

    /**
     *
     * @return boolean
     */
    public function hasErrors()
    {
        return $this->getNotification()->hasErrors();
    }

    /**
     *
     * @return array|\Application\multitype:
     */
    public function getErrors()
    {
        return $this->getNotification()->getErrors();
    }

    /**
     *
     * @param string $err
     * @return \Application\Domain\Shared\AbstractEntity
     */
    public function addError($err)
    {
        if ($err == null)
            return $this;

        $notification = $this->getNotification();
        $notification->addError($err);
        $this->notification = $notification;
        return $this;
    }

    /**
     *
     * @param array $errs
     * @return \Application\Domain\Shared\AbstractEntity
     */
    public function addErrorArray(array $errs)
    {
        if (count($errs) == 0) {
            return $this;
        }

        $notification = $this->getNotification();

        foreach ($errs as $err) {
            $notification->addError($err);
        }

        $this->notification = $notification;

        return $this;
    }

    /**
     *
     * @param string $mes
     * @return \Application\Domain\Shared\AbstractEntity
     */
    public function addWaring($mes)
    {
        if ($mes == null)
            return $this;

        $notification = $this->getNotification();
        $this->notification = $notification->addWarning($mes);
        return $this;
    }

    /**
     *
     * @param string $mes
     * @return \Application\Domain\Shared\AbstractEntity
     */
    public function addSuccess($mes)
    {
        if ($mes == null)
            return $this;

        $notification = $this->getNotification();
        $this->notification = $notification->addSuccess($mes);
        return $this;
    }

    /**
     *
     * @return \Application\Notification
     */
    public function getNotification()
    {
        if (! $this->notification instanceof Notification) {
            return new Notification();
        }

        return $this->notification;
    }

    /**
     *
     * @param Notification $notification
     */
    public function setNotification(Notification $notification)
    {
        $this->notification = $notification;
    }

    /**
     *
     * @return array|NULL
     */
    public function getRecordedEvents()
    {
        if ($this->recordedEvents == null) {
            return array();
        }

        return $this->recordedEvents;
    }

    /**
     */
    public function clearNotification()
    {
        $this->notification = null;
    }

    public function getErrorMessage($html = true)
    {
        return $this->getNotification()->errorMessage($html);
    }

    /**
     *
     * @return array
     */
    public function clearEvents()
    {
        $this->recordedEvents = null;
    }

    /**
     *
     * @return array
     */
    public function registerEvent($event)
    {
        $this->recordedEvents[] = $event;
    }
}
