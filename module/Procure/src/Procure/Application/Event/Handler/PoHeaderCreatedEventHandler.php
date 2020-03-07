<?php
namespace Procure\Application\Event\Handler;

use Application\Entity\MessageStore;
use Procure\Domain\Event\PoHeaderCreatedEvent;
use Procure\Infrastructure\Doctrine\DoctrinePOQueryRepository;
use Ramsey\Uuid\Uuid;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Application\Application\Event\AbstractEventHandler;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PoHeaderCreatedEventHandler extends AbstractEventHandler implements EventSubscriberInterface
{

    /**
     *
     * @return string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            PoHeaderCreatedEvent::class => 'onCreated'
        ];
    }

    /**
     *
     * @param PoHeaderCreatedEvent $ev
     */
    public function onCreated(PoHeaderCreatedEvent $ev)
    {
        $rep = new DoctrinePOQueryRepository($this->getDoctrineEM());
        $rootEntity = $rep->getHeaderById($ev->getTarget());

        $class = new \ReflectionClass($rootEntity);
        $class = null;
        if ($class !== null) {
            $className = $class->getShortName();
        }

        $message = new MessageStore();
        $message->setQueueName("procure.po");

        $message->setClassName($className);
        $message->setTriggeredBy(get_class($ev));
        $message->setUuid(Uuid::uuid4());
        $message->setMsgBody(json_encode((array) $rootEntity->makeSnapshot()));
        $message->setCreatedOn(new \DateTime());
        $message->setEventName(get_class($ev));
        $this->getDoctrineEM()->persist($message);
        $this->getDoctrineEM()->flush();
    }
}