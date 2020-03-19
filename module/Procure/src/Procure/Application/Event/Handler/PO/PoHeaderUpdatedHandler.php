<?php
namespace Procure\Application\Event\Handler\PO;

use Ramsey\Uuid\Uuid;
use Application\Application\Event\AbstractEventHandler;
use Application\Entity\MessageStore;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Procure\Infrastructure\Doctrine\DoctrinePOQueryRepository;
use Procure\Domain\Event\Po\PoHeaderUpdated;
use Procure\Domain\PurchaseOrder\POSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PoHeaderUpdatedHandler extends AbstractEventHandler implements EventSubscriberInterface
{

    /**
     *
     * @return string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            PoHeaderUpdated::class => 'onUpdated'
        ];
    }

    /**
     *
     * @param PoHeaderUpdated $ev
     */
    public function onUpdated(PoHeaderUpdated $ev)
    {
        /**
         *
         * @var POSnapshot $rootSnapshot ;
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
        $class = null;
        if ($class !== null) {
            $className = $class->getShortName();
        }

        $message = new MessageStore();

        $message->setRevisionNo($rootSnapshot->getRevisionNo());
        $message->setVersion($rootSnapshot->getRevisionNo());

        if (! $changeLog1 == null) {
            $message->setChangeLog(json_encode($changeLog1));
        }

        $message->setEntityId($rootSnapshot->getId());
        $message->setEntityToken($rootSnapshot->getToken());
        $message->setQueueName("procure.po");

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