<?php
namespace Inventory\Application\EventBus\Handler\Item;

use Application\Application\EventBus\Contracts\AbstractEventHandler;
use Application\Domain\EventBus\Handler\EventHandlerPriorityInterface;
use Inventory\Application\Service\Search\ZendSearch\Item\ItemSearchIndexImplV1;
use Inventory\Domain\Event\Item\ItemCreated;
use Inventory\Domain\Event\Item\ItemUpdated;
use Inventory\Domain\Item\ItemSnapshot;
use Inventory\Infrastructure\Doctrine\ItemQueryRepositoryImpl;

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

            $target = $event->getTarget();

            if (! $target instanceof ItemSnapshot) {
                Throw new \InvalidArgumentException("ItemSnapshot not given for updating index.");
            }

            $rep = new ItemQueryRepositoryImpl($this->getDoctrineEM());
            $item = $rep->getRootEntityById($target->getId());
            $item->getLazyVariantCollection();

            $indexer = new ItemSearchIndexImplV1();
            $indexer->setLogger($this->getLogger());
            $indexer->createDoc($item->makeSnapshot());

            $format = "Index for item #%s updated!";
            $this->logInfo(\sprintf($format, $event->getTarget()
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