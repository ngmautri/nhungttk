<?php
namespace Procure\Application\Event\Handler\PO;

use Application\Application\Event\AbstractEventHandler;
use Application\Entity\MessageStore;
use Procure\Domain\Event\Po\PoPosted;
use Procure\Domain\PurchaseOrder\POSnapshot;
use Ramsey\Uuid\Uuid;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Procure\Infrastructure\Doctrine\DoctrinePOQueryRepository;

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
        $class = null;
        if ($class !== null) {
            $className = $class->getShortName();
        }

        $message = new MessageStore();

        $queryRep = new DoctrinePOQueryRepository($this->getDoctrineEM());

        // time to check version - concurency
        $currentVersion = $queryRep->getVersion($rootSnapshot->getId());

        $message->setRevisionNo($currentVersion);
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