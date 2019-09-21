<?php
namespace Application\Domain\Shared;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AggregateRoot
{

    protected $recordedEvents;

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
