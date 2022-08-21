<?php
namespace HR\Application\EventBus\Handler\Individual;

use Application\Application\EventBus\Contracts\AbstractEventHandler;
use Application\Domain\EventBus\Handler\EventHandlerPriorityInterface;
use HR\Application\Service\Search\ZendSearch\Individual\IndividualSearchIndexImpl;
use HR\Domain\Employee\IndividualSnapshot;
use HR\Domain\Event\Employee\IndividualUpdated;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class OnIndividualUpdatedUpdateIndex extends AbstractEventHandler
{

    /**
     *
     * @param IndividualUpdated $event
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function __invoke(IndividualUpdated $event)
    {
        try {

            $target = $event->getTarget();

            if (! $target instanceof IndividualSnapshot) {
                Throw new \InvalidArgumentException("IndividualSnapshot not given for updating index.");
            }

            $indexer = new IndividualSearchIndexImpl();
            $indexer->setLogger($this->getLogger());
            $indexer->createDoc($target);

            $itemId = $event->getTarget()->getId();
            $format = "Individual %s updated -> search index updated";
            $this->logInfo(\sprintf($format, $itemId));
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
        return IndividualUpdated::class;
    }
}