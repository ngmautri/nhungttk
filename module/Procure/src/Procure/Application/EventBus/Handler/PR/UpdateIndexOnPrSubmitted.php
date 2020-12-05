<?php
namespace Procure\Application\EventBus\Handler\PR;

use Application\Application\EventBus\Contracts\AbstractEventHandler;
use Application\Domain\EventBus\Handler\EventHandlerPriorityInterface;
use Procure\Application\Service\PR\PRService;
use Procure\Application\Service\Search\ZendSearch\PR\PrSearchIndexImpl;
use Procure\Domain\Event\Pr\PrPosted;
use Procure\Domain\PurchaseRequest\PRSnapshot;
use Procure\Infrastructure\Doctrine\PRQueryRepositoryImpl;

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
        $this->logInfo(\sprintf("PrPosted - Event Target - %s", \get_class($event->getTarget())));
        if (! $event->getTarget() instanceof PRSnapshot) {
            return;
        }

        $indexer = new PrSearchIndexImpl();
        $rep = new PRQueryRepositoryImpl($this->getDoctrineEM());
        $entity = $rep->getRootEntityByTokenId($event->getTarget()
            ->getId(), $event->getTarget()
            ->getToken());

        $indexer->setLogger($this->getLogger());
        $indexer->createDoc($entity->makeSnapshot());

        $this->logInfo(\sprintf("Search index for PR#%s created!", $event->getTarget()
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