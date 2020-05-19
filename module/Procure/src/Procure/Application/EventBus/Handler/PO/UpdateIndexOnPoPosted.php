<?php
namespace Procure\Application\EventBus\Handler\PO;

use Application\Application\EventBus\Contracts\AbstractEventHandler;
use Application\Domain\EventBus\Handler\EventHandlerPriorityInterface;
use Procure\Application\Service\Search\ZendSearch\PO\PoSearchIndexImpl;
use Procure\Domain\Event\Po\PoPosted;
use Procure\Domain\PurchaseOrder\POSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class UpdateIndexOnPoPosted extends AbstractEventHandler
{

    /**
     *
     * @param PoPosted $event
     */
    public function __invoke(PoPosted $event)
    {
        if (! $event->getTarget() instanceof POSnapshot) {
            return;
        }

        $indexer = new PoSearchIndexImpl();
        $indexer->createDoc($event->getTarget());

        $this->getLogger()->info(\sprintf("Search index created on %s, #%s ", \get_class($event), $event->getTarget()
            ->getId()));
    }

    public static function priority()
    {
        return EventHandlerPriorityInterface::HIGH_PRIORITY;
    }

    public static function subscribedTo()
    {
        return PoPosted::class;
    }
}