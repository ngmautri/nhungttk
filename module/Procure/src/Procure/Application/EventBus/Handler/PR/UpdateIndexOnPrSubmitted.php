<?php
namespace Procure\Application\EventBus\Handler\PR;

use Application\Application\EventBus\Contracts\AbstractEventHandler;
use Application\Domain\EventBus\Handler\EventHandlerPriorityInterface;
use Procure\Application\Service\PR\PRService;
use Procure\Application\Service\Search\ZendSearch\PR\PrSearchIndexImpl;
use Procure\Domain\Event\Pr\PrPosted;
use Procure\Domain\PurchaseRequest\PRSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class UpdateIndexOnPrSubmitted extends AbstractEventHandler
{

    /**
     *
     * @param PrPosted $event
     */
    public function __invoke(PrPosted $event)
    {
        if (! $event->getTarget() instanceof PRSnapshot) {
            return;
        }

        $indexer = new PrSearchIndexImpl();
        $indexer->createDoc($event->getTarget());
        $this->getLogger()->info(\sprintf("Search index for PR#%s created!", $event->getTarget()
            ->getId()));

        // Clear Cache.
        $key = \sprintf(PRService::PR_KEY_CACHE, $event->getTarget()->getId());
        $this->getCache()->deleteItem($key);
        $this->getLogger()->info(\sprintf("%s deleted from cache!", $key));
    }

    public static function priority()
    {
        return EventHandlerPriorityInterface::HIGH_PRIORITY;
    }

    public static function subscribedTo()
    {
        return PrPosted::class;
    }
}