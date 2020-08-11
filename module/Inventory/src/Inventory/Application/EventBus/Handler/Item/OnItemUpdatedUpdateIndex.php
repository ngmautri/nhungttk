<?php
namespace Inventory\Application\EventBus\Handler\Item;

use Application\Application\EventBus\Contracts\AbstractEventHandler;
use Application\Domain\EventBus\Handler\EventHandlerPriorityInterface;
use Inventory\Application\Service\Search\ZendSearch\Item\ItemSearchIndexImpl;
use Inventory\Domain\Event\Item\ItemCreated;
use Inventory\Domain\Event\Item\ItemUpdated;
use Inventory\Domain\Item\ItemSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class OnItemUpdatedUpdateIndex extends AbstractEventHandler
{

    /**
     *
     * @param ItemCreated $event
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function __invoke(ItemUpdated $event)
    {
        try {

            if (! $event->getTarget() instanceof ItemSnapshot) {
                Throw new \InvalidArgumentException("ItemSnapshot not given for updating index.");
            }

            $indexer = new ItemSearchIndexImpl();
            $indexer->createDoc($event->getTarget());

            $format = "Index for item #%s updated!";
            $this->getLogger()->info(\sprintf($format, $event->getTarget()
                ->getId()));
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public static function priority()
    {
        return EventHandlerPriorityInterface::HIGH_PRIORITY;
    }

    public static function subscribedTo()
    {
        return ItemUpdated::class;
    }
}