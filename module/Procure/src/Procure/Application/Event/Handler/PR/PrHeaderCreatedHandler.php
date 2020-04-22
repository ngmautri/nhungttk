<?php
namespace Procure\Application\Event\Handler\PR;

use Application\Application\Event\AbstractEventHandler;
use Application\Entity\MessageStore;
use Procure\Domain\AccountPayable\APSnapshot;
use Procure\Domain\Event\Ap\ApHeaderCreated;
use Ramsey\Uuid\Uuid;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Procure\Domain\Event\Pr\PrHeaderCreated;
use Procure\Domain\PurchaseRequest\PRSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PrHeaderCreatedHandler extends AbstractEventHandler implements EventSubscriberInterface
{

    /**
     *
     * @return string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            PrHeaderCreated::class => 'onCreated'
        ];
    }

    /**
     *
     * @param ApHeaderCreated $ev
     */
    public function onCreated(PrHeaderCreated $ev)
    {

        /**
         *
         * @var PRSnapshot $rootSnapshot ;
         */
        $rootSnapshot = $ev->getTarget();

        if ($rootSnapshot == null) {
            return;
        }

        $class = new \ReflectionClass($rootSnapshot);
        $className = null;

        if ($class !== null) {
            $className = $class->getShortName();
        }

        $message = new MessageStore();

        $message->setRevisionNo($rootSnapshot->getRevisionNo());
        $message->setVersion($rootSnapshot->getDocVersion());
        $message->setEntityId($rootSnapshot->getId());
        $message->setEntityToken($rootSnapshot->getToken());
        $message->setQueueName("procure.pr");

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