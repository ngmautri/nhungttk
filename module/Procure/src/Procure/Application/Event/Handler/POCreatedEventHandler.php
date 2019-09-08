<?php
namespace Procure\Application\Event\Handler;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Inventory\Domain\Event\ItemCreatedEvent;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class POCreatedEventHandler implements EventSubscriberInterface
{

    protected $itemId;

    protected $doctrineEM;

    public function __construct($itemId, $doctrineEM)
    {
        $this->itemId = $itemId;
        $this->doctrineEM = $doctrineEM;
    }

    /**
     *
     * @return string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            ItemCreatedEvent::EVENT_NAME => 'onItemCreated'
        ];
    }

    /**
     *
     * @param ItemCreatedEvent $event
     */
    public function onItemCreated(ItemCreatedEvent $event)
    {
        $searcher = new \Inventory\Application\Service\Search\Solr\ItemSearchService();
        $searcher->setDoctrineEM($this->doctrineEM);
        $searcher->updateItemIndex($this->itemId, true, false);
    }
}