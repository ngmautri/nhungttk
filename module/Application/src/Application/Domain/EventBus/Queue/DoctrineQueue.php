<?php
namespace Application\Domain\EventBus\Queue;

use Application\Domain\EventBus\Event\EventInterface;
use Application\Entity\MessageStore;
use Application\Infrastructure\Doctrine\MessageStoreRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Ramsey\Uuid\Uuid;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DoctrineQueue implements QueueInterface
{

    protected $doctrineEM;

    protected $queueName;

    public function __construct($doctrineEM, $queueName)
    {
        $this->doctrineEM = $doctrineEM;
        $this->queueName = $queueName;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\EventBus\Queue\QueueInterface::pop()
     */
    public function pop()
    {
        $rep = new MessageStoreRepository($this->getDoctrineEM());
        $sql = 'SELECT * FROM message_store
WHERE message_store.sent_on IS NULL and message_store.queue_name="%s"';

        $sql = \sprintf($sql, $this->queueName);
        echo $sql;

        try {
            $rsm = new ResultSetMappingBuilder($this->getDoctrineEM());
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\MessageStore', 'message_store');
            $query = $this->getDoctrineEM()->createNativeQuery($sql, $rsm);
            $result = $query->getResult();

            $events = [];

            if ($result) {

                $rep = new MessageStoreRepository($this->getDoctrineEM());
                // $rep->setSentDate($result);

                foreach ($result as $r) {
                    $events[] = \unserialize($r->getMsgBody());
                }

                return $events;
            }
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\EventBus\Queue\QueueInterface::push()
     */
    public function push(EventInterface $event)
    {
        $message = new MessageStore();

        $message->setRevisionNo(1);
        $message->setVersion(1);
        $message->setEntityId(1);
        $message->setEntityToken(1);
        $message->setQueueName($this->queueName);

        $message->setUuid(Uuid::uuid4());
        $message->setMsgBody(\serialize($event));
        $message->setCreatedOn(new \DateTime());
        $message->setEventName(get_class($event));
        $this->getDoctrineEM()->persist($message);
        $this->getDoctrineEM()->flush();
    }

    public function hasElements()
    {}

    public function name()
    {
        return $this->queueName;
    }

    /**
     *
     * @return mixed
     */
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }
}