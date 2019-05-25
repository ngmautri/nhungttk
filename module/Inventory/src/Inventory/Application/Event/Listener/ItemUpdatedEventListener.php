<?php
namespace Inventory\Application\Event\Listener;

use Doctrine\ORM\EntityManager;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Inventory\Domain\Event\ItemCreatedEvent;
use Inventory\Domain\Item\AbstractItem;
use Inventory\Domain\Event\ItemUpdatedEvent;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemUpdatedEventListener implements ListenerAggregateInterface
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
        $this->listeners[] = $events->attach(ItemUpdatedEvent::EVENT_NAME, array(
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

        /**
         *
         * @var AbstractItem $item
         */
        $item = $e->getParam('item');

        $searcher->updateItemIndex($item->getId(), FALSE, FALSE);
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