<?php
namespace Inventory\Application\EventBus\Handler\Item;

use Application\Application\EventBus\Contracts\AbstractEventHandler;
use Application\Domain\EventBus\Handler\EventHandlerPriorityInterface;
use Inventory\Application\Service\Search\ZendSearch\Item\ItemSearchIndexImplV1;
use Inventory\Domain\Event\Item\ItemCreated;
use Inventory\Domain\Item\ItemSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class OnItemCreatedCreateIndex extends AbstractEventHandler
{

    /**
     *
     * @param ItemCreated $event
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function __invoke(ItemCreated $event)
    {
        try {
            if (! $event->getTarget() instanceof ItemSnapshot) {
                Throw new \InvalidArgumentException("ItemSnapshot not given for new index.");
            }

            $indexer = new ItemSearchIndexImplV1();
            $indexer->setLogger($this->getLogger());
            $indexer->createDoc($event->getTarget());

            $itemId = $event->getTarget()->getId();
            $format = "Item %s created -> search index created";
            $this->logInfo(\sprintf($format, $itemId));
        } catch (\Exception $e) {
            $this->logException($e);
            throw $e;
        }
    }

    public static function priority()
    {
        return EventHandlerPriorityInterface::HIGH_PRIORITY;
    }

    public static function subscribedTo()
    {
        return ItemCreated::class;
    }
}