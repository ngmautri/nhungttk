<?php
namespace Procure\Application\Event\Handler\PR;

use Application\Application\Event\AbstractEventHandler;
use Application\Entity\MessageStore;
use Procure\Domain\Event\Ap\ApHeaderUpdated;
use Procure\Domain\Event\Pr\PrHeaderUpdated;
use Procure\Domain\PurchaseRequest\PRSnapshot;
use Ramsey\Uuid\Uuid;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PrHeaderUpdatedHandler extends AbstractEventHandler implements EventSubscriberInterface
{

    /**
     *
     * @return string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            PrHeaderUpdated::class => 'onUpdated'
        ];
    }

    /**
     *
     * @param ApHeaderUpdated $ev
     */
    public function onUpdated(PrHeaderUpdated $ev)
    {
        /**
         *
         * @var PRSnapshot $rootSnapshot ;
         */
        $rootSnapshot = $ev->getTarget();

        if ($rootSnapshot == null) {
            return;
        }

        $params = $ev->getParams();
        $trigger = $ev->getTrigger();

        $changeLog = null;
        $changeLog1 = array();
        if (isset($params['changeLog'])) {
            $changeLog = $params['changeLog'];

            if (count($changeLog) > 0) {

                foreach ($changeLog as $k => $v) {

                    $changeLog2 = array();
                    foreach ($v as $k1 => $v1) {
                        if ($k1 == "className") {
                            continue;
                        }

                        $changeLog2[] = [
                            $k1 => $v1
                        ];
                        ;
                    }

                    $changeLog1[] = $changeLog2;
                }
            }
        }

        $class = new \ReflectionClass($rootSnapshot);
        $className = null;

        if ($class !== null) {
            $className = $class->getShortName();
        }

        $message = new MessageStore();

        $message->setRevisionNo($rootSnapshot->getRevisionNo());
        $message->setVersion($rootSnapshot->getDocVersion());

        if (! $changeLog1 == null) {
            $message->setChangeLog(json_encode($changeLog1));
        }

        $message->setEntityId($rootSnapshot->getId());
        $message->setEntityToken($rootSnapshot->getToken());
        $message->setQueueName("procure.pr");

        $message->setClassName($className);
        $message->setTriggeredBy($trigger);
        $message->setUuid(Uuid::uuid4());
        $message->setMsgBody(json_encode((array) $rootSnapshot));
        $message->setCreatedOn(new \DateTime());
        $message->setEventName(get_class($ev));
        $this->getDoctrineEM()->persist($message);
        $this->getDoctrineEM()->flush();
    }
}