<?php
namespace Procure\Application\EventBus\Handler\PR;

use Application\Application\EventBus\Contracts\AbstractEventHandler;
use Application\Domain\EventBus\Handler\EventHandlerPriorityInterface;
use Procure\Application\Service\PR\PRService;
use Procure\Application\Service\Search\ZendSearch\PR\PrSearchIndexImpl;
use Procure\Domain\Event\Pr\PrPosted;
use Procure\Domain\PurchaseRequest\PRDoc;
use Procure\Domain\PurchaseRequest\PRSnapshot;
use Procure\Infrastructure\Persistence\Domain\Doctrine\PRQueryRepositoryImplV1;

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
        $rep = new PRQueryRepositoryImplV1($this->getDoctrineEM());
        $target = $event->getTarget();

        /**
         *
         * @var PRDoc $entity
         */
        $entity = $rep->getRootEntityByTokenId($target->getId(), $target->getToken());
        $this->logInfo(\sprintf("%s-%s", $target->getId(), $target->getToken()));

        $indexer->setLogger($this->getLogger());
        $indexingResult = $indexer->createIndexDoc($entity);

        if (! $indexingResult->getIsSuccess()) {
            $m = "Search Indexing failed. %s";
            throw new \RuntimeException(sprintf($m, $indexingResult->getMessage()));
        }

        $this->logInfo(\sprintf("%s", $indexingResult->getMessage()));

        // Clear Cache.
        $key = \sprintf(PRService::PR_KEY_CACHE, $target->getId());
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