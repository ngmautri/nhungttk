<?php
namespace Procure\Application\Event\Handler\PO;

use Application\Application\Event\AbstractEventHandler;
use Application\Entity\MessageStore;
use Procure\Domain\Event\Po\PoAmendmentAccepted;
use Procure\Domain\PurchaseOrder\POSnapshot;
use Ramsey\Uuid\Uuid;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Procure\Infrastructure\Doctrine\DoctrinePOQueryRepository;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PoAmendmentAcceptedHandler extends AbstractEventHandler implements EventSubscriberInterface
{

    /**
     *
     * @return string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            PoAmendmentAccepted::class => 'onAcceptedForAmmendment'
        ];
    }

    /**
     *
     * @param PoAmendmentAccepted $ev
     */
    public function onAcceptedForAmmendment(PoAmendmentAccepted $ev)
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

        $class = new \ReflectionClass($rootSnapshot);
        $className = null;
        if ($class !== null) {
            $className = $class->getShortName();
        }

        $message = new MessageStore();

        $queryRep = new DoctrinePOQueryRepository($this->getDoctrineEM());

        // time to check version - concurency
        $verArray = $queryRep->getVersionArray($rootSnapshot->getId());
        $currentRevisionNo = null;
        $currentVersion = null;

        if ($verArray != null) {
            if (isset($verArray["docVersion"])) {
                $currentVersion = $verArray["docVersion"];
            }

            if (isset($verArray["revisionNo"])) {
                $currentRevisionNo = $verArray["revisionNo"];
            }
        }

        $message->setRevisionNo($currentRevisionNo);
        $message->setVersion($currentVersion);
        $message->setCreatedBy($rootSnapshot->getLastchangeBy());

        $message->setEntityId($rootSnapshot->getId());
        $message->setEntityToken($rootSnapshot->getToken());
        $message->setQueueName("procure.po");
        $message->setChangeLog(sprintf("Amendment for P/O  #%s is %s!", $rootSnapshot->getId(), $rootSnapshot->getDocStatus()));
        $message->setClassName($className);
        $message->setTriggeredBy($ev->getTrigger());
        $message->setUuid(Uuid::uuid4());
        $message->setMsgBody(json_encode((array) $rootSnapshot));
        $message->setCreatedOn(new \DateTime());
        $message->setEventName(get_class($ev));
        $this->getDoctrineEM()->persist($message);
        $this->getDoctrineEM()->flush();
    }
}