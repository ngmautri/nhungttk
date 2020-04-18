<?php
namespace Procure\Application\Event\Handler\PO;

use Application\Application\Event\AbstractEventHandler;
use Application\Entity\MessageStore;
use Procure\Domain\Event\Po\PoPosted;
use Procure\Domain\PurchaseOrder\POSnapshot;
use Procure\Infrastructure\Doctrine\POQueryRepositoryImpl;
use Ramsey\Uuid\Uuid;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PoPostedHandler extends AbstractEventHandler implements EventSubscriberInterface
{

    /**
     *
     * @return string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            PoPosted::class => 'onPosted'
        ];
    }

    /**
     *
     * @param PoPosted $ev
     */
    public function onPosted(PoPosted $ev)
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

        $queryRep = new POQueryRepositoryImpl($this->getDoctrineEM());

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

        $message->setEntityId($rootSnapshot->getId());
        $message->setEntityToken($rootSnapshot->getToken());
        $message->setQueueName("procure.po");
        $message->setChangeLog(sprintf("P/O #%s is %s!", $rootSnapshot->getId(), $rootSnapshot->getDocStatus()));
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