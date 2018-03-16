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
        
        $this->listeners[] = $events->attach('procure.activity.log', array(
            $this,
            'onProcureActivityLogging'
        ), 200);
        
        $this->listeners[] = $events->attach('inventory.activity.log', array(
            $this,
            'onInventoryActivityLogging'
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
    public function onProcureActivityLogging(EventInterface $e)
    {
        $log_priority = $e->getParam('priotiry');
        $log_message = $e->getParam('message');
        
        $filename = 'procure_activity_log_' . date('F') . '_' . date('Y') . '.txt';
        $log = new Logger();
        $writer = new Stream('./data/log/' . $filename);
        $log->addWriter($writer);
        $log->log(Logger::INFO, $log_message);
    }

    /**
     *
     * @param EventInterface $e
     */
    public function onInventoryActivityLogging(EventInterface $e)
    {
        $log_priority = $e->getParam('priotiry');
        $log_message = $e->getParam('message');
        $objectId = $e->getParam('objectId');
        $objectToken = $e->getParam('objectToken');
        $changeArray = $e->getParam('changeArray');
        $filename = 'inventory_activity_log_' . date('F') . '_' . date('Y') . '.txt';
        $log = new Logger();
        $writer = new Stream('./data/log/' . $filename);
        $log->addWriter($writer);
        $log->log(Logger::INFO, $log_message);
        
        foreach ($changeArray as $key => $value) {
            $log->log(Logger::INFO, $key . " ID: " . $objectId);
            foreach ($value as $v) {
                $log->log(Logger::INFO, $v);
            }
        }
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
        
        $filename = 'inventory_activity_log_' . date('F') . '_' . date('Y') . '.txt';
        $log = new Logger();
        
        $writer = new Stream('./data/log/' . $filename);
        $log->addWriter($writer);
        $log->log(Logger::INFO, $log_message);
        
        foreach ($changeArray as $key => $value) {
            $log->log(Logger::INFO, $key . " ID: " . $objectId);
            foreach ($value as $v) {
                $log->log(Logger::INFO, $v);
            }
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
     *
     * @param EventInterface $e
     */
    public function onAuthenticationLogging(EventInterface $e)
    {
        $log_priority = $e->getParam('priotiry');
        $log_message = $e->getParam('message');
        
        $filename = 'authentication_log_' . date('F') . '_' . date('Y') . '.txt';
        $log = new Logger();
        $writer = new Stream('./data/log/' . $filename);
        $log->addWriter($writer);
        $log->log(Logger::INFO, $log_message);
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