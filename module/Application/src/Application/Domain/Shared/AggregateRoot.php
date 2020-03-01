<?php
namespace Application\Domain\Shared;

use Application\Notification;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AggregateRoot
{

    protected $notification;

    protected $recordedEvents;

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
     * @param string $err
     * @return \Application\Domain\Shared\AggregateRoot
     */
    public function addError(string $err)
    {
        if ($err == null)
            return $this;

        $notification = $this->getNotification();
        $this->notification = $notification->addError($err);
        return $this;
    }

    /**
     *
     * @param string $mes
     * @return \Application\Domain\Shared\AggregateRoot
     */
    public function addWaring(string $mes)
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
     * @return \Application\Domain\Shared\AggregateRoot
     */
    public function addSuccess(string $mes)
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
     * @return array
     */
    public function getRecordedEvents()
    {
        return $this->recordedEvents;
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
