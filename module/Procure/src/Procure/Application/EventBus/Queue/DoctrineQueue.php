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
            $entityId = null;
            $entityToken = null;
            $docVersion = null;
            $docVesion = null;
            $triggeredBy = null;
            $rowToken = null;
            $rowId = null;

            if ($event instanceof AbstractEvent) {
                $entityId = $event->getEntityId();
                $entityToken = $event->getEntityToken();
                $docVersion = $event->getDocVersion();
                $docVesion = $event->getRevisionNo();
                $triggeredBy = $event->getTrigger();
                $params = $event->getParams();

                if (isset($params['rowId'])) {
                    $rowId = $params['rowId'];
                }

                if (isset($params['rowToken'])) {
                    $rowToken = $params['rowToken'];
                }

                if ($rowId !== null) {
                    $changeLog_final["rowId"] = $rowId;
                }

                if ($rowToken !== null) {
                    $changeLog_final["rowToken"] = $rowToken;
                }

                if (isset($params['changeLog'])) {
                    $changeLog = $params['changeLog'];

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

            $message = new MessageStore();

            $message->setChangeLog(json_encode($changeLog_final));
            $message->setTriggeredBy($triggeredBy);
            $message->setRevisionNo($docVesion);
            $message->setVersion($docVersion);
            $message->setEntityId($entityId);
            $message->setEntityToken($entityToken);
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