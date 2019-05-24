<?php
namespace Inventory\Application\Event\Listener;

use Doctrine\ORM\EntityManager;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Inventory\Domain\Event\ItemCreatedEvent;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemCreatedEventListener implements ListenerAggregateInterface
{

    protected $listeners = array();

    protected $events;

    protected $doctrineEM;

    /**
     *
     * @param EventManagerInterface $events
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(ItemCreatedEvent::EVENT_NAME, array(
            $this,
            'onItemCreated'
        ), 200);
    }

    /**
     *
     * @param EventInterface $e
     */
    public function onItemCreated(EventInterface $e)
    {
        $item = $e->getParam('item');
        var_dump($item);
    }

    public function detach(EventManagerInterface $events)
    {}

    /**
     *
     * @return mixed
     */
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    /**
     *
     * @param mixed $doctrineEM
     */
    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
    }
}