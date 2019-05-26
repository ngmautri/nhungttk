<?php
namespace Inventory\Application\Event\Listener;

use Application\Entity\NmtInventoryChangeLog;
use Doctrine\ORM\EntityManager;
use Inventory\Domain\Event\ItemCreatedEvent;
use Inventory\Domain\Event\ItemUpdatedEvent;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;
use Zend\Math\Rand;
use Ramsey;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemLoggingListener implements ListenerAggregateInterface
{

    protected $listeners = array();

    protected $events;

    protected $doctrineEM;

    /**
     *
     * @param EventManagerInterface $events
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(ItemCreatedEvent::EVENT_NAME, array(
            $this,
            'onItemCreatedLog'
        ), 200);

        $this->listeners[] = $events->attach(ItemUpdatedEvent::EVENT_NAME, array(
            $this,
            'onItemUpdatedLog'
        ), 200);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Zend\EventManager\ListenerAggregateInterface::detach()
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     *
     * @param EventInterface $e
     */
    public function onItemCreatedLog(EventInterface $e)
    {}

    /**
     *
     * @param EventInterface $e
     */
    public function onItemUpdatedLog(EventInterface $e)
    {
        var_dump($e->getParams());
        $log_priority = 6;
        $log_message = $e->getParam('message');
        $objectId = $e->getParam('objectId');
        $objectToken = $e->getParam('objectToken');
        $changeArray = $e->getParam('changeArray');
        $changeBy = $e->getParam('changeBy');
        $changeOn = $e->getParam('changeOn');
        $revisionNumber = $e->getParam('revisionNumber');
        $changeValidFrom = $e->getParam('changeValidFrom');

        $filename = 'inventory_change_log_' . date('F') . '_' . date('Y') . '.txt';
        $log = new Logger();

        $writer = new Stream('./data/log/' . $filename);
        $log->addWriter($writer);
        // $log->log(Logger::INFO, $log_message);

        $detail = $log_message;
        foreach ($changeArray as $key => $value) {
            $detail_1 = $detail . " {" . $key . "}{Object ID: " . $objectId . "}";
            // $log->log(Logger::INFO, $key . " ID: " . $objectId);
            foreach ($value as $k => $v) {
                $detail_1 = $detail_1 . "{" . $k . ":" . $v . "}";
            }
            $log->log($log_priority, $detail_1);
        }

        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            'id' => $changeBy
        ));

        foreach ($changeArray as $key => $value) {

            // update database
            $entity = new NmtInventoryChangeLog();
            $entity->setObjectToken($objectToken);
            $entity->setObjectId($objectId);
            $entity->setCreatedBy($u);
            $entity->setCreatedOn($changeOn);
            $entity->setRevisionNo($revisionNumber);
            $entity->setEffectiveFrom($changeValidFrom);
            $entity->setTriggeredby($e->getTarget());

            foreach ($value as $k => $v1) {

                switch ($k) {
                    case "className":
                        $entity->setClassName($v1);
                        break;

                    case "fieldType":
                        $entity->setFieldType($v1);
                        break;

                    case "fieldName":

                        // Set all resources as inactive;
                        // Set all resources as inactive;
                        $sql = "UPDATE Application\Entity\NmtInventoryChangeLog log SET log.isValid = 0";
                        $w = sprintf(" WHERE log.objectId=%s AND log.objectToken='%s' AND log.fieldName = '%s' ", $objectId, $objectToken, $v1);
                        $sql = $sql . $w;

                        $q = $this->doctrineEM->createQuery($sql);
                        $q->execute();

                        $entity->setFieldName($v1);
                        /*
                         * $entity->setColumnName($this->doctrineEM->getClassMetadata($entity->getClassName())
                         * ->getColumnName($v1));
                         */
                        break;
                    case "oldValue":
                        $entity->setOldValue($v1);
                        break;
                    case "newValue":

                        if (is_string($v1)) {
                            if (strlen($v1) > 200) {
                                $entity->setNewValue(substr($v1, 0, 200));
                            } else {
                                $entity->setNewValue($v1);
                            }
                        } else {
                            $entity->setNewValue($v1);
                        }

                        break;
                }
            }
            $entity->setToken(Ramsey\Uuid\Uuid::uuid4()->toString());
            $entity->setIsValid(1);
            $this->doctrineEM->persist($entity);
        }

        $this->doctrineEM->flush();
    }

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