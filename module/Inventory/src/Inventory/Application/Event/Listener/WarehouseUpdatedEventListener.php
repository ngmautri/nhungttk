<?php
namespace Inventory\Application\Event\Listener;

use Doctrine\ORM\EntityManager;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Inventory\Domain\Event\ItemCreatedEvent;
use Inventory\Domain\Item\AbstractItem;
use Inventory\Domain\Event\ItemUpdatedEvent;
use Inventory\Domain\Event\WarehouseUpdatedEvent;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class WarehouseUpdatedEventListener implements ListenerAggregateInterface
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
        $this->listeners[] = $events->attach(WarehouseUpdatedEvent::EVENT_NAME, array(
            $this,
            'onItemUpdated'
        ), 200);
    }

    /**
     *
     * @param EventInterface $e
     */
    public function onItemUpdated(EventInterface $e)
    {
        $searcher = new \Inventory\Application\Service\Search\ZendSearch\ItemSearchService();
        $searcher->setDoctrineEM($this->getDoctrineEM());

        $itemId = $e->getParam('itemId');
        $searcher->updateItemIndex($itemId, FALSE, FALSE);
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