<?php
namespace Procure\Application\Event\Handler;

use Application\Application\Event\AbstractEventHandler;
use Application\Entity\MessageStore;
use Procure\Domain\Event\PoHeaderCreated;
use Procure\Infrastructure\Doctrine\DoctrinePOQueryRepository;
use Ramsey\Uuid\Uuid;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Procure\Domain\Event\PoHeaderUpdated;

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

        $class = new \ReflectionClass($rootEntity);
        $class = null;
        if ($class !== null) {
            $className = $class->getShortName();
        }

        $message = new MessageStore();
        $message->setEntityId($ev->getTarget());
        $message->setEntityToken($rootEntity->getToken());
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