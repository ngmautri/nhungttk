<?php
namespace Procure\Application\Event\Handler;

use Ramsey\Uuid\Uuid;
use Application\Application\Event\AbstractEventHandler;
use Application\Entity\MessageStore;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Procure\Infrastructure\Doctrine\DoctrinePOQueryRepository;
use Procure\Domain\Event\Po\PoHeaderUpdated;

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
        $rep = new DoctrinePOQueryRepository($this->getDoctrineEM());
        $rootEntity = $rep->getHeaderById($ev->getTarget());

        if ($rootEntity == null) {
            return;
        }

        $params = $ev->getParams();        
        $trigger= $ev->getTrigger();
      
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

        $class = new \ReflectionClass($rootEntity);
        $class = null;
        if ($class !== null) {
            $className = $class->getShortName();
        }

        $message = new MessageStore();

        $message->setRevisionNo($rootEntity->getRevisionNo());
        $message->setVersion($rootEntity->getRevisionNo());

        if (! $changeLog1 == null) {
            $message->setChangeLog(json_encode($changeLog1));
        }

        $message->setEntityId($ev->getTarget());
        $message->setEntityToken($rootEntity->getToken());
        $message->setQueueName("procure.po");

        $message->setClassName($className);
        $message->setTriggeredBy($trigger);
        $message->setUuid(Uuid::uuid4());
        $message->setMsgBody(json_encode((array) $rootEntity->makeSnapshot()));
        $message->setCreatedOn(new \DateTime());
        $message->setEventName(get_class($ev));
        $this->getDoctrineEM()->persist($message);
        $this->getDoctrineEM()->flush();
    }
}