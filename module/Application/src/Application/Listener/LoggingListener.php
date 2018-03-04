<?php
namespace Application\Listener;

use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
//use Zend\EventManager\AbstractListenerAggregate;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;

class LoggingListener implements ListenerAggregateInterface
{

    /**
     *
     * @var array
     */
    protected $listeners = array();

    protected $events;

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
        $log_message = $e->getParam ( 'message' );
		
		$filename = 'system_log_' . date('F') . '_'.date('Y') .'.txt';
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
	    $log_message = $e->getParam ( 'message' );
	    
	    $filename = 'procure_activity_log_' . date('F') . '_'.date('Y') .'.txt';
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
	    $log_message = $e->getParam ( 'message' );
	    
	    $filename = 'inventory_activity_log_' . date('F') . '_'.date('Y') .'.txt';
	    $log = new Logger();
	    $writer = new Stream('./data/log/' . $filename);
	    $log->addWriter($writer);
	    $log->log(Logger::INFO, $log_message);
	}
	
	/**
	 *
	 * @param EventInterface $e
	 */
	public function onAuthenticationLogging(EventInterface $e)
	{
	    $log_priority = $e->getParam('priotiry');
	    $log_message = $e->getParam ( 'message' );
	    
	    $filename = 'authentication_log_' . date('F') . '_'.date('Y') .'.txt';
	    $log = new Logger();
	    $writer = new Stream('./data/log/' . $filename);
	    $log->addWriter($writer);
	    $log->log(Logger::INFO, $log_message);
	}
	
	
}