<?php
namespace Procure\Application\Event\Handler\PO;
use Ramsey\Uuid\Uuid;
use Application\Entity\MessageStore;
use Application\Application\Event\AbstractEventHandler;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Procure\Infrastructure\Doctrine\DoctrinePOQueryRepository;
use Procure\Domain\Event\Po\PoRowAdded;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PoRowAddedHandler extends AbstractEventHandler implements EventSubscriberInterface
{
    
    /**
     *
     * @return string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            PoRowAdded::class => 'onPoRowAdded'
        ];
    }
    
  /**
   * 
   * @param PoRowAdded $ev
   */
    public function onPoRowAdded(PoRowAdded $ev)
    {
        $rep = new DoctrinePOQueryRepository($this->getDoctrineEM());
        $rootEntity = $rep->getHeaderById($ev->getTarget());
        
        $class = new \ReflectionClass($rootEntity);
        $class = null;
        if ($class !== null) {
            $className = $class->getShortName();
        }
        
        echo $className;
        
        $message = new MessageStore();
        
        $message->setRevisionNo($rootEntity->getRevisionNo());
        $message->setVersion($rootEntity->getRevisionNo());
        
        
        $message->setEntityId($ev->getTarget());
        $message->setEntityToken($rootEntity->getToken());
        $message->setQueueName("procure.po");
        
        $message->setClassName($className);
        $message->setTriggeredBy($ev->getTrigger());
        $message->setUuid(Uuid::uuid4());
        $message->setMsgBody(json_encode((array) $rootEntity->makeSnapshot()));
        $message->setCreatedOn(new \DateTime());
        $message->setEventName(get_class($ev));
        $this->getDoctrineEM()->persist($message);
        $this->getDoctrineEM()->flush();
    }
}