<?php
namespace HR\Application\EventBus\Handler\Individual;

use Application\Application\EventBus\Contracts\AbstractEventHandler;
use Application\Domain\EventBus\Handler\EventHandlerPriorityInterface;
use HR\Application\Service\Search\ZendSearch\Individual\IndividualSearchIndexImpl;
use HR\Domain\Employee\IndividualSnapshot;
use HR\Domain\Event\Employee\IndividualCreated;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class OnIndividualCreatedCreateIndex extends AbstractEventHandler
{

    /**
     *
     * @param IndividualCreated $event
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function __invoke(IndividualCreated $event)
    {
        try {
            if (! $event->getTarget() instanceof IndividualSnapshot) {
                Throw new \InvalidArgumentException("IndividualSnapshot not given for new index.");
            }

            $indexer = new IndividualSearchIndexImpl();
            $indexer->setLogger($this->getLogger());
            $indexer->createDoc($event->getTarget());

            $itemId = $event->getTarget()->getId();
            $format = "Individual %s created -> search index created";
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
        return IndividualCreated::class;
    }
}