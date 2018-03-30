<?php
namespace Application\Listener;

use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
// use Zend\EventManager\AbstractListenerAggregate;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;
use Doctrine\ORM\EntityManager;
use Application\Entity\NmtHrChangeLog;
use Zend\Math\Rand;
use Application\Entity\NmtHrContractLog;
use Application\Entity\NmtInventoryLog;
use Application\Entity\NmtProcureChangeLog;
use Application\Entity\NmtInventoryChangeLog;
use Application\Entity\NmtProcureLog;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class LoggingListener implements ListenerAggregateInterface
{

    const CHAR_LIST = "__0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ__";

    protected $listeners = array();

    protected $events;

    protected $doctrineEM;

    /**
     *
     * @param EventManagerInterface $events
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach('system.log', array(
            $this,
            'onSystemLogging'
        ), 200);
        
        $this->listeners[] = $events->attach('hr.change.log', array(
            $this,
            'onHRLogging'
        ), 200);
        
        $this->listeners[] = $events->attach('authenticate.log', array(
            $this,
            'onAuthenticationLogging'
        ), 200);
        
        $this->listeners[] = $events->attach('inventory.activity.log', array(
            $this,
            'onInventoryActivityLogging'
        ), 200);
        
        $this->listeners[] = $events->attach('hr.contract.log', array(
            $this,
            'onContractLogging'
        ), 200);
        
        $this->listeners[] = $events->attach('inventory.change.log', array(
            $this,
            'onInventoryChangeLogging'
        ), 200);
        
        // PROCURE ACT LOG
        $this->listeners[] = $events->attach('procure.activity.log', array(
            $this,
            'onProcureActivityLogging'
        ), 200);
        
        // PROCURE CHANGE LOG
        $this->listeners[] = $events->attach('procure.change.log', array(
            $this,
            'onProcureChangeLogging'
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
    public function onAuthenticationLogging(EventInterface $e)
    {
        // $log_priority = $e->getParam('priotiry');
        $log_message = $e->getParam('message');
        
        $filename = 'authentication_log_' . date('F') . '_' . date('Y') . '.txt';
        $log = new Logger();
        $writer = new Stream('./data/log/' . $filename);
        $log->addWriter($writer);
        $log->log(Logger::INFO, $log_message);
    }

    /**
     *
     * @param EventInterface $e
     */
    public function onSystemLogging(EventInterface $e)
    {
        $log_priority = $e->getParam('priotiry');
        $log_message = $e->getParam('message');
        
        $filename = 'system_log_' . date('F') . '_' . date('Y') . '.txt';
        $log = new Logger();
        $writer = new Stream('./data/log/' . $filename);
        $log->addWriter($writer);
        $log->log(Logger::INFO, $log_message);
    }

    /**
     *
     * @param EventInterface $e
     */
    public function onHRLogging(EventInterface $e)
    {
        $log_priority = $e->getParam('priotiry');
        $log_message = $e->getParam('message');
        $objectId = $e->getParam('objectId');
        $objectToken = $e->getParam('objectToken');
        $changeArray = $e->getParam('changeArray');
        $changeBy = $e->getParam('changeBy');
        $changeOn = $e->getParam('changeOn');
        
        $filename = 'hr_change_log_' . date('F') . '_' . date('Y') . '.txt';
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
            $log->log(Logger::INFO, $detail_1);
        }
        
        foreach ($changeArray as $key => $value) {
            
            // update database
            $entity = new NmtHrChangeLog();
            $entity->setObjectToken($objectToken);
            $entity->setObjectId($objectId);
            $entity->setCreatedBy($changeBy);
            $entity->setCreatedOn($changeOn);
            
            foreach ($value as $k => $v1) {
                
                switch ($k) {
                    case "className":
                        $entity->setClassName($v1);
                        break;
                    case "fieldName":
                        $entity->setFieldName($v1);
                        $entity->setColumnName($this->doctrineEM->getClassMetadata($entity->getClassName())
                            ->getColumnName($v1));
                        break;
                    case "oldValue":
                        $entity->setOldValue($v1);
                        break;
                    case "newValue":
                        $entity->setNewValue($v1);
                        break;
                }
            }
            $entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
            $this->doctrineEM->persist($entity);
        }
        
        $this->doctrineEM->flush();
    }

    /**
     * ON Contract Log
     *
     * @param EventInterface $e
     */
    public function onContractLogging(EventInterface $e)
    {
        $log_priority = $e->getParam('priotiry');
        $log_message = $e->getParam('message');
        $objectId = $e->getParam('objectId');
        $objectToken = $e->getParam('objectToken');
        $changeArray = $e->getParam('changeArray');
        $changeBy = $e->getParam('changeBy');
        $changeOn = $e->getParam('changeOn');
        $revisionNumber = $e->getParam('revisionNumber');
        $changeValidFrom = $e->getParam('changeValidFrom');
        
        $filename = 'hr_contract_log_' . date('F') . '_' . date('Y') . '.txt';
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
            $log->log(Logger::INFO, $detail_1);
        }
        
        foreach ($changeArray as $key => $value) {
            
            // update database
            $entity = new NmtHrContractLog();
            $entity->setObjectToken($objectToken);
            $entity->setObjectId($objectId);
            $entity->setCreatedBy($changeBy);
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
                        $sql = "UPDATE Application\Entity\NmtHrContractLog log SET log.isValid = 0";
                        
                        $w = sprintf(" WHERE log.objectId=%s AND log.objectToken='%s' AND log.fieldName = '%s' ", $objectId, $objectToken, $v1);
                        
                        $sql = $sql . $w;
                        
                        $q = $this->doctrineEM->createQuery($sql);
                        $q->execute();
                        
                        $entity->setFieldName($v1);
                        $entity->setColumnName($this->doctrineEM->getClassMetadata($entity->getClassName())
                            ->getColumnName($v1));
                        break;
                    case "oldValue":
                        $entity->setOldValue($v1);
                        break;
                    case "newValue":
                        $entity->setNewValue($v1);
                        break;
                }
            }
            $entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
            $entity->setIsValid(1);
            $this->doctrineEM->persist($entity);
        }
        
        $this->doctrineEM->flush();
    }

    /**
     * Inventory Activity Loag
     *
     * @param EventInterface $e
     */
    public function onInventoryActivityLogging(EventInterface $e)
    {
        $log_priority = $e->getParam('priority');
        $log_message = $e->getParam('message');
        $createdBy = $e->getParam('createdBy');
        $createdOn = $e->getParam('createdOn');
        
        $filename = 'inventory_activity_log_' . date('F') . '_' . date('Y') . '.txt';
        $log = new Logger();
        $writer = new Stream('./data/log/' . $filename);
        $log->addWriter($writer);
        $log->log($log_priority, $log_message);
        
        // update DB
        $entity = new NmtInventoryLog();
        $entity->setPriority($log_priority);
        $entity->setMessage($log_message);
        $entity->setTriggeredby($e->getTarget());
        $entity->setCreatedBy($createdBy);
        $entity->setCreatedOn($createdOn);
        $entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
        $this->doctrineEM->persist($entity);
        $this->doctrineEM->flush();
    }

    /**
     * ON Inventory Change Log
     *
     * @param EventInterface $e
     */
    public function onInventoryChangeLogging(EventInterface $e)
    {
        $log_priority = $e->getParam('priority');
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
        
        foreach ($changeArray as $key => $value) {
            
            // update database
            $entity = new NmtInventoryChangeLog();
            $entity->setObjectToken($objectToken);
            $entity->setObjectId($objectId);
            $entity->setCreatedBy($changeBy);
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
                        $sql = "UPDATE Application\Entity\NmtInventoryChangeLog log SET log.isValid = 0";
                        
                        $w = sprintf(" WHERE log.objectId=%s AND log.objectToken='%s' AND log.fieldName = '%s' ", $objectId, $objectToken, $v1);
                        
                        $sql = $sql . $w;
                        
                        $q = $this->doctrineEM->createQuery($sql);
                        $q->execute();
                        
                        $entity->setFieldName($v1);
                        $entity->setColumnName($this->doctrineEM->getClassMetadata($entity->getClassName())
                            ->getColumnName($v1));
                        break;
                    case "oldValue":
                        $entity->setOldValue($v1);
                        break;
                    case "newValue":
                        $entity->setNewValue($v1);
                        break;
                }
            }
            $entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
            $entity->setIsValid(1);
            $this->doctrineEM->persist($entity);
        }
        
        $this->doctrineEM->flush();
    }

    /**
     * Procure Activity Loag
     *
     * @param EventInterface $e
     */
    public function onProcureActivityLogging(EventInterface $e)
    {
        $log_priority = $e->getParam('priority');
        $log_message = $e->getParam('message');
        $createdBy = $e->getParam('createdBy');
        $createdOn = $e->getParam('createdOn');
        
        $filename = 'procure_activity_log_' . date('F') . '_' . date('Y') . '.txt';
        $log = new Logger();
        $writer = new Stream('./data/log/' . $filename);
        $log->addWriter($writer);
        $log->log($log_priority, $log_message);
        
        // update DB
        $entity = new NmtProcureLog();
        $entity->setPriority($log_priority);
        $entity->setMessage($log_message);
        $entity->setTriggeredby($e->getTarget());
        $entity->setCreatedBy($createdBy);
        $entity->setCreatedOn($createdOn);
        $entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
        $this->doctrineEM->persist($entity);
        $this->doctrineEM->flush();
    }

    /**
     * ON Procure Log
     *
     * @param EventInterface $e
     */
    public function onProcureChangeLogging(EventInterface $e)
    {
        // $log_priority = $e->getParam('priotiry');
        $log_message = $e->getParam('message');
        $objectId = $e->getParam('objectId');
        $objectToken = $e->getParam('objectToken');
        $changeArray = $e->getParam('changeArray');
        $changeBy = $e->getParam('changeBy');
        $changeOn = $e->getParam('changeOn');
        $revisionNumber = $e->getParam('revisionNumber');
        $changeValidFrom = $e->getParam('changeValidFrom');
        
        $filename = 'procure_change_log_' . date('F') . '_' . date('Y') . '.txt';
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
            $log->log(Logger::INFO, $detail_1);
        }
        
        foreach ($changeArray as $key => $value) {
            
            // update database
            $entity = new NmtProcureChangeLog();
            $entity->setObjectToken($objectToken);
            $entity->setObjectId($objectId);
            $entity->setCreatedBy($changeBy);
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
                        $sql = "UPDATE Application\Entity\NmtProcureChangeLog log SET log.isValid = 0";
                        
                        $w = sprintf(" WHERE log.objectId=%s AND log.objectToken='%s' AND log.fieldName = '%s' ", $objectId, $objectToken, $v1);
                        
                        $sql = $sql . $w;
                        
                        $q = $this->doctrineEM->createQuery($sql);
                        $q->execute();
                        
                        $entity->setFieldName($v1);
                        $entity->setColumnName($this->doctrineEM->getClassMetadata($entity->getClassName())
                            ->getColumnName($v1));
                        break;
                    case "oldValue":
                        $entity->setOldValue($v1);
                        break;
                    case "newValue":
                        /**
                         *
                         * @todo text
                         */
                        $entity->setNewValue($v1);
                        break;
                }
            }
            $entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
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