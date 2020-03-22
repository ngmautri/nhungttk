<?php
namespace Procure\Application\Event\Handler\PO;

use Ramsey\Uuid\Uuid;
use Application\Entity\MessageStore;
use Application\Application\Event\AbstractEventHandler;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Procure\Domain\Event\Po\PoHeaderCreated;
use Procure\Infrastructure\Doctrine\DoctrinePOQueryRepository;
use Procure\Domain\PurchaseOrder\POSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PoHeaderCreatedHandler extends AbstractEventHandler implements EventSubscriberInterface
{

    /**
     *
     * @return string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            PoHeaderCreated::class => 'onCreated'
        ];
    }

    /**
     *
     * @param PoHeaderCreated $ev
     */
    public function onCreated(PoHeaderCreated $ev)
    {

        /**
         *
         * @var POSnapshot $rootSnapshot ;
         */
        $rootSnapshot = $ev->getTarget();

        if ($rootSnapshot == null) {
            return;
        }

        $class = new \ReflectionClass($rootSnapshot);
        $class = null;
        if ($class !== null) {
            $className = $class->getShortName();
        }

        $message = new MessageStore();
        
        $message->setRevisionNo($rootSnapshot->getRevisionNo());
        $message->setVersion($rootSnapshot->getDocVersion());
        $message->setEntityId($rootSnapshot->getId());
        $message->setEntityToken($rootSnapshot->getToken());
        $message->setQueueName("procure.po");

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