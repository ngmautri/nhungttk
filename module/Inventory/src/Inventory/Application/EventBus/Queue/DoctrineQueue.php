<?php
namespace Procure\Application\EventBus\Queue;

use Application\Application\Event\AbstractEvent;
use Application\Domain\EventBus\Event\EventInterface;
use Application\Domain\EventBus\Queue\QueueInterface;
use Application\Entity\MessageStore;
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
        try {
            $changeLog = null;
            $changeLog1 = [];
            $changeLog_final = [];
            $rowToken = null;
            $rowId = null;

            $message = new MessageStore();

            if ($event instanceof AbstractEvent) {

                $message->setTriggeredBy($event->getTriggeredBy());
                $message->setRevisionNo($event->getTargetRrevisionNo());
                $message->setVersion($event->getTargetDocVersion());
                $message->setEntityId($event->getTargetId());
                $message->setEntityToken($event->getTargetToken());
                $message->setCreatedBy($event->getUserId());

                if ($event->hasParam("rowId")) {
                    $rowId = $event->getParam("rowId");
                }

                if ($event->hasParam("rowToken")) {
                    $rowToken = $event->getParam("rowToken");
                }

                if ($rowId !== null) {
                    $changeLog_final["rowId"] = $rowId;
                }

                if ($rowToken !== null) {
                    $changeLog_final["rowToken"] = $rowToken;
                }

                if ($event->hasParam('changeLog')) {
                    $changeLog = $event->getParam('changeLog');

                    if (count($changeLog) > 0) {
                        foreach ($changeLog as $k => $v) {

                            if (! \is_array($v)) {
                                $changeLog1[] = $v;
                                continue;
                            }

                            $changeLog2 = array();

                            foreach ($v as $k1 => $v1) {

                                if ($k1 == "className") {
                                    continue;
                                }

                                $changeLog2[] = [
                                    $k1 => $v1
                                ];
                            }
                            $changeLog1[] = $changeLog2;
                        }
                    }

                    $changeLog_final["changeLog"] = $changeLog1;
                }
            }

            $message->setChangeLog(json_encode($changeLog_final));
            $message->setQueueName($this->queueName);
            $message->setUuid(Uuid::uuid4());
            $message->setMsgBody(\serialize($event));
            $message->setCreatedOn(new \DateTime());
            $message->setEventName(get_class($event));
            $this->getDoctrineEM()->persist($message);
            $this->getDoctrineEM()->flush();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\EventBus\Queue\QueueInterface::hasElements()
     */
    public function hasElements()
    {
        $sql = sprintf('SELECT COUNT(*) AS totalCount FROM %s WHERE event_status = \'pending\';', $this->queueName);
        $sql = \sprintf($sql, $this->queueName);
        echo $sql;

        try {
            $rsm = new ResultSetMappingBuilder($this->getDoctrineEM());
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\MessageStore', 'message_store');
            $query = $this->getDoctrineEM()->createNativeQuery($sql, $rsm);
            $result = $query->getResult();
        } catch (NoResultException $e) {
            return null;
        }
        return 0 !== (int) $result['totalCount'];
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\EventBus\Queue\QueueInterface::name()
     */
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