<?php
namespace Procure\Application\Event\Handler\PO;

use Application\Application\Event\AbstractEventHandler;
use Application\Entity\MessageStore;
use Procure\Domain\Event\Po\PoAmendmentEnabled;
use Procure\Domain\PurchaseOrder\POSnapshot;
use Ramsey\Uuid\Uuid;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PoAmendmentEnabledHandler extends AbstractEventHandler implements EventSubscriberInterface
{

    /**
     *
     * @return string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            PoAmendmentEnabled::class => 'onEnabledForAmmendment'
        ];
    }

   /**
    * 
    * @param PoAmendmentEnabled $ev
    */
    public function onEnabledForAmmendment(PoAmendmentEnabled $ev)
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
        if ($class !== null) {
            $className = $class->getShortName();
        }

        $message = new MessageStore();

        $message->setRevisionNo($rootSnapshot->getRevisionNo());
        $message->setVersion($rootSnapshot->getDocVersion());

        /* if (! $changeLog1 == null) {
            $message->setChangeLog(json_encode($changeLog1));
        } */
        
        $message->setChangeLog(sprintf("P/O #%s is enabled for %s", $rootSnapshot->getId(), $rootSnapshot->getDocStatus() ));

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