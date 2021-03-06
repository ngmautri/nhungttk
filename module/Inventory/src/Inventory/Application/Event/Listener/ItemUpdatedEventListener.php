<?php
namespace Inventory\Application\Event\Listener;

use Application\Entity\AllMessageStore;
use Application\Entity\MessageStore;
use Doctrine\ORM\EntityManager;
use Inventory\Domain\Event\ItemUpdatedEvent;
use Inventory\Infrastructure\Doctrine\DoctrineItemRepository;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Ramsey;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemUpdatedEventListener implements ListenerAggregateInterface
{

    protected $listeners = array();

    protected $events;

    protected $doctrineEM;

    protected $messagesDoctrineEM;

    /**
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getMessagesDoctrineEM()
    {
        return $this->messagesDoctrineEM;
    }

    /**
     *
     * @param EntityManager $messagesDoctrineEM
     */
    public function setMessagesDoctrineEM(EntityManager $messagesDoctrineEM)
    {
        $this->messagesDoctrineEM = $messagesDoctrineEM;
    }

    /**
     *
     * @param EventManagerInterface $events
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(ItemUpdatedEvent::EVENT_NAME, array(
            $this,
            'onItemUpdated'
        ), 200);
    }

    /**
     *
     * @param EventInterface $e
     */
    public function onItemUpdated(EventInterface $e)
    {
        $searcher = new \Inventory\Application\Service\Search\ZendSearch\ItemSearchService();
        $searcher->setDoctrineEM($this->getDoctrineEM());

        $itemId = $e->getParam('itemId');
        $searcher->updateItemIndex($itemId, FALSE, FALSE);

        $rep = new DoctrineItemRepository($this->getDoctrineEM());
        $item = $rep->getById($itemId);

        $itemClass = new \ReflectionClass($item);
        $className = null;
        if ($itemClass !== null) {
            $className = $itemClass->getShortName();
        }

        $message = new MessageStore();
        $message->setUuid(Ramsey\Uuid\Uuid::uuid4());
        $message->setClassName($className);
        $message->setTriggeredBy($e->getTarget());
        $message->setQueueName("inventory.item");
        $message->setMsgBody(json_encode((array) $item->createDTO()));
        $message->setCreatedOn(new \DateTime());
        $message->setEventName($e->getName());
        $this->getDoctrineEM()->persist($message);
        $this->getDoctrineEM()->flush();

        /*
         * $message = new AllMessageStore();
         *
         * $message->setUuid(Ramsey\Uuid\Uuid::uuid4());
         * $message->setClassName($className);
         * $message->setTriggeredBy($e->getTarget());
         *
         *
         * $message->setMsgBody(json_encode((array) $item->createDTO()));
         * $message->setQueueName("inventory.item");
         * $message->setCreatedOn(new \DateTime());
         * $message->setEventName($e->getName());
         * $this->getMessagesDoctrineEM()->persist($message);
         * $this->getMessagesDoctrineEM()->flush();
         */

        // $exe_string = "C:\ADDIN\nmt.bat";
        // exec('c:\WINDOWS\system32\cmd.exe /c START C:\ADDIN\nmt.bat');

        // exec('c:\WINDOWS\system32\cmd.exe /C start C:\ADDIN\nmt.bat');
        // exec($exe_string);
    }

    public function detach(EventManagerInterface $events)
    {}

    /**
     *
     * @return mixed
     */
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    /**
     *
     * @param mixed $doctrineEM
     */
    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
    }
}